<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'honorific',
        'first_name',
        'last_name',
        'gender',
        'date_of_birth',
        'nationality',
        'phone_number',
        'profile_picture',
        'is_expatriate'
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'is_expatriate' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
