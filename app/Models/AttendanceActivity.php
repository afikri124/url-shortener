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
        'sub_title',
        'date',
        'location',
        'host',
        'participant',
        'user_id',
        'notulen_username'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
