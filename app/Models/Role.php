<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;
    public $timestamps = false;
    public $incrementing = false;
    protected $fillable = [
        'id', 'title',
    ];

    public function users()
  {
      return $this->belongsToMany(User::class, 'user_roles');
  }

  protected $appends = ['color'];

  public function getColorAttribute(){
        $x = "";
        if($this->id == "AD"){
            $x = "primary";
        } else if($this->id == "SD"){
            $x = "warning";
        } else if($this->id == "ST"){
            $x = "danger";
        } else if($this->id == "GS"){
            $x = "info";
        } else if($this->id == "HR"){
            $x = "success";
        } else if($this->id == "DS"){
            $x = "secondary";
        } else {
            $x = "dark";
        }
        return $x;
  }
}
