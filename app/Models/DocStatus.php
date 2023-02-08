<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocStatus extends Model
{
    use HasFactory;
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [
        'id', 
        'name',
    ];
}
