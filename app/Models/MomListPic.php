<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MomListPic extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'mom_list_id',
        'username',
    ];
}
