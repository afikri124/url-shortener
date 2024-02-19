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
        'idmesin',
        'wh_manual_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'username', 'username');
    }
}
