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

    public function docs()
    {
        return $this->hasMany(MomDoc::class, 'activity_id', 'activity_id');
    } 

    public function pics()
    {
        return $this->belongsToMany(User::class, 'mom_list_pics');
    } 


    public function hasPIC($username) 
    {
      return $this->pics()->where('username', $username)->count() == 1;
    }

    public function activity()
    {
        return $this->belongsTo(AttendanceActivity::class, 'activity_id');
    }

}
