<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceActivity extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'type',
        'title',
        'date',
        'location',
        'host',
        'participant',
        'user_id',
        'notulen_username',
        'expired'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function notulen()
    {
        return $this->belongsTo(User::class, 'notulen_username', 'username');
    }
}
