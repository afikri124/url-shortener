<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhAttendance extends Model
{
    use HasFactory;    
    public $timestamps = false;
    public $incrementing = false;
    protected $fillable = [
        'uid', 
        'username',
        'state',
        'type',
        'timestamp',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'username', 'username');
    }

    // public function user_old()
    // {
    //     return $this->belongsTo(WhUser::class, 'username', 'username_old');
    // }
}
