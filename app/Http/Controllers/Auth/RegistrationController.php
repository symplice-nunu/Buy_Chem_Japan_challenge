<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\RegistrationProgress;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Carbon\Carbon;

class RegistrationController extends Controller
{
    public function initializeRegistration()
    {
        $registration = RegistrationProgress::create([
            'registration_id' => Str::uuid(),
            'current_step' => 1,
            'expires_at' => Carbon::now()->addHours(24)
        ]);

        return response()->json([
            'registration_id' => $registration->registration_id,
            'current_step' => 1
        ]);
    }

    public function submitStep1(Request $request)
    {
        $request->validate([
            'registration_id' => 'required|uuid|exists:registration_progress,registration_id',
            'honorific' => 'nullable|in:Mr.,Mrs.,Miss,Ms.,Dr.,Prof.,Hon.',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'gender' => 'required|in:male,female',
            'date_of_birth' => 'required|date|before:today',
            'email' => ['required', 'email', 'unique:users,email', 'not_regex:/(tempmail|temp-mail|disposable)/i'],
            'nationality' => 'required|string|max:255',
            'phone_number' => 'required|string|unique:user_profiles,phone_number',
            'profile_picture' => 'nullable|image|mimes:png|max:2048'
        ]);

        $registration = RegistrationProgress::where('registration_id', $request->registration_id)
            ->where('current_step', 1)
            ->firstOrFail();

        // Handle profile picture upload
        $profilePicturePath = null;
        if ($request->hasFile('profile_picture')) {
            $profilePicturePath = $request->file('profile_picture')->store('profile-pictures', 'public');
        }

        // Set default honorific if not provided
        $honorific = $request->honorific;
        if (!$honorific) {
            $honorific = $request->gender === 'male' ? 'Mr.' : 'Ms.';
        }

        $stepData = [
            'honorific' => $honorific,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'gender' => $request->gender,
            'date_of_birth' => $request->date_of_birth,
            'email' => $request->email,
            'nationality' => $request->nationality,
            'phone_number' => $request->phone_number,
            'profile_picture' => $profilePicturePath
        ];

        $registration->update([
            'email' => $request->email,
            'current_step' => 2,
            'step_data' => array_merge($registration->step_data ?? [], ['step1' => $stepData])
        ]);

        return response()->json([
            'message' => 'Step 1 completed successfully',
            'current_step' => 2
        ]);
    }

    public function submitStep2(Request $request)
    {
        $request->validate([
            'registration_id' => 'required|uuid|exists:registration_progress,registration_id',
            'country_of_residence' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
            'apartment_name' => 'nullable|string|max:255',
            'room_number' => 'nullable|string|max:50'
        ]);

        $registration = RegistrationProgress::where('registration_id', $request->registration_id)
            ->where('current_step', 2)
            ->firstOrFail();

        $stepData = $request->only([
            'country_of_residence',
            'city',
            'postal_code',
            'apartment_name',
            'room_number'
        ]);

        // Check if user is expatriate
        $isExpatriate = $registration->step_data['step1']['nationality'] !== $request->country_of_residence;

        $registration->update([
            'current_step' => 3,
            'step_data' => array_merge($registration->step_data, [
                'step2' => array_merge($stepData, ['is_expatriate' => $isExpatriate])
            ])
        ]);

        return response()->json([
            'message' => 'Step 2 completed successfully',
            'current_step' => 3
        ]);
    }

    public function initiateEmailVerification(Request $request)
    {
        $request->validate([
            'registration_id' => 'required|uuid|exists:registration_progress,registration_id'
        ]);

        $registration = RegistrationProgress::where('registration_id', $request->registration_id)
            ->where('current_step', 3)
            ->firstOrFail();

        // Generate verification code
        $verificationCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Store verification code in step data
        $registration->update([
            'step_data' => array_merge($registration->step_data, [
                'step3' => [
                    'verification_code' => Hash::make($verificationCode),
                    'verification_sent_at' => Carbon::now()->toDateTimeString()
                ]
            ])
        ]);

        // Send verification email using EmailVerificationService
        app('email.verification')->sendVerificationCode($registration->email, $verificationCode);

        return response()->json([
            'message' => 'Verification code sent successfully'
        ]);
    }

    public function verifyEmail(Request $request)
    {
        $request->validate([
            'registration_id' => 'required|uuid|exists:registration_progress,registration_id',
            'verification_code' => 'required|string|size:6'
        ]);

        $registration = RegistrationProgress::where('registration_id', $request->registration_id)
            ->where('current_step', 3)
            ->firstOrFail();

        $verificationData = $registration->step_data['step3'];
        
        if (!Hash::check($request->verification_code, $verificationData['verification_code'])) {
            return response()->json([
                'message' => 'Invalid verification code'
            ], 422);
        }

        $registration->update([
            'current_step' => 4,
            'step_data' => array_merge($registration->step_data, [
                'step3' => array_merge($verificationData, ['verified_at' => Carbon::now()->toDateTimeString()])
            ])
        ]);

        return response()->json([
            'message' => 'Email verified successfully',
            'current_step' => 4
        ]);
    }

    public function submitStep4(Request $request)
    {
        $request->validate([
            'registration_id' => 'required|uuid|exists:registration_progress,registration_id',
            'password' => ['required', 'confirmed', Password::min(8)
                ->mixedCase()
                ->numbers()
                ->symbols()
                ->uncompromised()
            ]
        ]);

        $registration = RegistrationProgress::where('registration_id', $request->registration_id)
            ->where('current_step', 4)
            ->firstOrFail();

        $registration->update([
            'current_step' => 5,
            'step_data' => array_merge($registration->step_data, [
                'step4' => [
                    'password' => Hash::make($request->password)
                ]
            ])
        ]);

        return response()->json([
            'message' => 'Password set successfully',
            'current_step' => 5
        ]);
    }

    public function submitStep5(Request $request)
    {
        $request->validate([
            'registration_id' => 'required|uuid|exists:registration_progress,registration_id'
        ]);

        $registration = RegistrationProgress::where('registration_id', $request->registration_id)
            ->where('current_step', 5)
            ->firstOrFail();

        // Create user
        $user = User::create([
            'name' => $registration->step_data['step1']['first_name'] . ' ' . $registration->step_data['step1']['last_name'],
            'email' => $registration->step_data['step1']['email'],
            'password' => $registration->step_data['step4']['password']
        ]);

        // Create user profile
        $userProfile = UserProfile::create([
            'user_id' => $user->id,
            'honorific' => $registration->step_data['step1']['honorific'],
            'first_name' => $registration->step_data['step1']['first_name'],
            'last_name' => $registration->step_data['step1']['last_name'],
            'gender' => $registration->step_data['step1']['gender'],
            'date_of_birth' => $registration->step_data['step1']['date_of_birth'],
            'nationality' => $registration->step_data['step1']['nationality'],
            'phone_number' => $registration->step_data['step1']['phone_number'],
            'profile_picture' => $registration->step_data['step1']['profile_picture'],
            'is_expatriate' => $registration->step_data['step2']['is_expatriate']
        ]);

        // Create user address
        $userAddress = UserAddress::create([
            'user_id' => $user->id,
            'country_of_residence' => $registration->step_data['step2']['country_of_residence'],
            'city' => $registration->step_data['step2']['city'],
            'postal_code' => $registration->step_data['step2']['postal_code'],
            'apartment_name' => $registration->step_data['step2']['apartment_name'],
            'room_number' => $registration->step_data['step2']['room_number']
        ]);

        // Mark registration as completed
        $registration->update([
            'is_completed' => true
        ]);

        return response()->json([
            'message' => 'Registration completed successfully',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'profile' => $userProfile,
                'address' => $userAddress
            ]
        ]);
    }

    public function resumeRegistration(Request $request)
    {
        $request->validate([
            'registration_id' => 'required|uuid'
        ]);

        $registration = RegistrationProgress::where('registration_id', $request->registration_id)
            ->where('is_completed', false)
            ->where('expires_at', '>', Carbon::now())
            ->firstOrFail();

        return response()->json([
            'current_step' => $registration->current_step,
            'step_data' => $registration->step_data
        ]);
    }
}
