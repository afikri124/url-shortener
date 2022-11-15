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
}
