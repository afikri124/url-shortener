<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'username',
        'activity_id',
    ];

    public function activity()
    {
        return $this->belongsTo(AttendanceActivity::class, 'activity_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'username', 'username');
    }
}
