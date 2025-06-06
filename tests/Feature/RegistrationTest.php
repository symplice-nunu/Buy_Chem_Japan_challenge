<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_complete_registration_flow()
    {
        Storage::fake('public');

        // Step 0: Initialize registration
        $response = $this->postJson('/api/register/init');
        $response->assertStatus(200);
        $registrationId = $response->json('registration_id');

        // Step 1: Submit personal information
        $response = $this->postJson('/api/register/step1', [
            'registration_id' => $registrationId,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'gender' => 'male',
            'date_of_birth' => '1990-01-01',
            'email' => 'john.doe@example.com',
            'nationality' => 'US',
            'phone_number' => '+1234567890',
            'profile_picture' => UploadedFile::fake()->image('avatar.png')
        ]);
        $response->assertStatus(200);
        $response->assertJson(['current_step' => 2]);

        // Step 2: Submit address details
        $response = $this->postJson('/api/register/step2', [
            'registration_id' => $registrationId,
            'country_of_residence' => 'Japan',
            'city' => 'Tokyo',
            'postal_code' => '100-0001',
            'apartment_name' => 'Sample Apartment',
            'room_number' => '101'
        ]);
        $response->assertStatus(200);
        $response->assertJson(['current_step' => 3]);

        // Step 3a: Request email verification
        $response = $this->postJson('/api/register/step3/send-verification', [
            'registration_id' => $registrationId
        ]);
        $response->assertStatus(200);

        // Get the verification code from the database
        $registration = \App\Models\RegistrationProgress::where('registration_id', $registrationId)->first();
        $verificationCode = '123456'; // We'll use a fixed code for testing
        $registration->update([
            'step_data' => array_merge($registration->step_data, [
                'step3' => [
                    'verification_code' => \Illuminate\Support\Facades\Hash::make($verificationCode),
                    'verification_sent_at' => now()->toDateTimeString()
                ]
            ])
        ]);

        // Step 3b: Submit verification code
        $response = $this->postJson('/api/register/step3/verify', [
            'registration_id' => $registrationId,
            'verification_code' => $verificationCode
        ]);
        $response->assertStatus(200);
        $response->assertJson(['current_step' => 4]);

        // Step 4: Set password
        $response = $this->postJson('/api/register/step4', [
            'registration_id' => $registrationId,
            'password' => 'SecurePassword123!',
            'password_confirmation' => 'SecurePassword123!'
        ]);
        $response->assertStatus(200);
        $response->assertJson(['current_step' => 5]);

        // Step 5: Complete registration
        $response = $this->postJson('/api/register/step5', [
            'registration_id' => $registrationId
        ]);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'user' => [
                'id',
                'name',
                'email',
                'profile' => [
                    'honorific',
                    'first_name',
                    'last_name',
                    'gender',
                    'date_of_birth',
                    'nationality',
                    'phone_number',
                    'profile_picture',
                    'is_expatriate'
                ],
                'address' => [
                    'country_of_residence',
                    'city',
                    'postal_code',
                    'apartment_name',
                    'room_number'
                ]
            ]
        ]);

        // Test resume registration
        $response = $this->getJson("/api/register/resume?registration_id={$registrationId}");
        $response->assertStatus(404); // Should fail because registration is completed
    }
}