<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhPublicHoliday extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'id', 
        'date',
        'detail',
        'created_id'
    ];
}
