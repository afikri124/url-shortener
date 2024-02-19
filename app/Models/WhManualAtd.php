<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhManualAtd extends Model
{
    use HasFactory;
    protected $fillable = [
        'id', 
        'date_start',
        'date_end',
        'username',
        'remark',
        'doc_path',
        'created_id'
    ];
}
