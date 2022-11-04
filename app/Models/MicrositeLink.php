<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MicrositeLink extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'microsite_id',
        'title',
        'link',
    ];

    public function microsite()
    {
        return $this->belongsTo(Microsite::class, 'microsite_id');
    }
}
