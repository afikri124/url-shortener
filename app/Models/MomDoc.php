<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MomDoc extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'activity_id',
        'type',
        'doc_path',
    ];
}
