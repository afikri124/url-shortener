<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocCategory extends Model
{
    use HasFactory;
    protected $fillable = [
        'id', 
        'name',
        'activity_id'
    ];
}
