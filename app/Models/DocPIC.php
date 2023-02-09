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

    public function department()
    {
        return $this->belongsTo(DocDepartment::class, 'department_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'pic_id');
    }
}
