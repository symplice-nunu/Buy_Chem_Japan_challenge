<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;
use App\Mail\VerificationCodeMail;

class EmailVerificationService
{
    public function sendVerificationCode(string $email, string $verificationCode): void
    {
        Mail::to($email)->send(new VerificationCodeMail($verificationCode));
    }
} 