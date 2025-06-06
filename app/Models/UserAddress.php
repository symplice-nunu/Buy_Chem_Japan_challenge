<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'country_of_residence',
        'city',
        'postal_code',
        'apartment_name',
        'room_number'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
