<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocSystem extends Model
{
    use HasFactory;
    protected $fillable = [
        'id', 
        'name',
        'deadline',
        'doc_path',
        'status_id',
        'category_id',
        'created_id',
        'updated_id',
        'remark',
        'histories'
    ];

    public function status()
    {
        return $this->belongsTo(DocStatus::class, 'status_id');
    }

    public function category()
    {
        return $this->belongsTo(DocCategory::class, 'category_id');
    }

    public function PIC()
    {
        return $this->hasMany(DocPIC::class, 'doc_id');
    } 
}
