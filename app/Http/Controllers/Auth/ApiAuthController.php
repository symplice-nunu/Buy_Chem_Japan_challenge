<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class ApiAuthController extends Controller
{
    /**
     * Default device name when none is provided
     */
    private const DEFAULT_DEVICE_NAME = 'Unknown Device';

    /**
     * Authenticate user and create access token
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function login(Request $request): JsonResponse
    {
        $credentials = $this->validateLoginRequest($request);
        
        $user = $this->getUserByEmail($credentials['email']);
        
        if (! $user || ! $this->isValidPassword($user, $credentials['password'])) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $this->createUserToken($user, $this->getDeviceName($request));

        return response()->json([
            'token' => $token,
            'user' => new UserResource($user),
        ], Response::HTTP_OK);
    }

    /**
     * Logout user by revoking the current access token
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        
        return response()->json([
            'message' => 'Successfully logged out'
        ], Response::HTTP_OK);
    }

    /**
     * Validate login request data
     *
     * @param Request $request
     * @return array
     * @throws ValidationException
     */
    private function validateLoginRequest(Request $request): array
    {
        return $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
            'device_name' => ['nullable', 'string'],
        ]);
    }

    /**
     * Get user by email
     *
     * @param string $email
     * @return User|null
     */
    private function getUserByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    /**
     * Check if the provided password is valid
     *
     * @param User $user
     * @param string $password
     * @return bool
     */
    private function isValidPassword(User $user, string $password): bool
    {
        return Hash::check($password, $user->password);
    }

    /**
     * Create a new token for the user
     *
     * @param User $user
     * @param string $deviceName
     * @return string
     */
    private function createUserToken(User $user, string $deviceName): string
    {
        return $user->createToken($deviceName)->plainTextToken;
    }

    /**
     * Get device name from request or fallback to default
     *
     * @param Request $request
     * @return string
     */
    private function getDeviceName(Request $request): string
    {
        return $request->device_name ?? $request->userAgent() ?? self::DEFAULT_DEVICE_NAME;
    }
} 