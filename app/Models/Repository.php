<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Repository extends Model
{
    use HasFactory;
    protected $fillable = [
        'uid', 
        'user_id',
        'file_path',
        'name',
        'type',
        'published',
        'created_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
