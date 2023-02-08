<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocPIC extends Model
{
    use HasFactory;
    protected $fillable = [
        'id', 
        'doc_id',
        'department_id',
        'pic_id'
    ];
}
