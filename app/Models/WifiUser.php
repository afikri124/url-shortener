<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WifiUser extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'username',
        'password',
        'is_seen',
        'first_name',
        'last_name',
        'email',
        'updated_at'
    ];
}
