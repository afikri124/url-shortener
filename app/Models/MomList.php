<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MomList extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'activity_id',
        'detail',
        'target',
    ];
}
