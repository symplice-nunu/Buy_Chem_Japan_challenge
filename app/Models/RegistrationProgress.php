<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistrationProgress extends Model
{
    use HasFactory;

    protected $table = 'registration_progress';

    protected $fillable = [
        'registration_id',
        'email',
        'current_step',
        'step_data',
        'is_completed',
        'expires_at'
    ];

    protected $casts = [
        'step_data' => 'array',
        'is_completed' => 'boolean',
        'expires_at' => 'datetime'
    ];
}
