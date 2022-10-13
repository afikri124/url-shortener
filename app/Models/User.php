<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'google_id',
        'email',
        'password',
        'job',
        'gender',
        'front_title',
        'back_title',
    ];
    protected $appends = ['name_with_title'];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    } 

    public function hasRole($role) 
    {
      return $this->roles()->where('role_id', $role)->count() == 1;
    }

    function image()
    { 
      $has_valid_avatar = false;
      if(env('APP_ENV') != 'local'){
        $hash = md5(strtolower(trim($this->email)));
        $uri = "https://klas.jgu.ac.id/employee_profile/image.php?id=".$this->username;
        if(!@getimagesize($uri)){
            $hash = md5(strtolower(trim($this->email)));
            $uri = "https://www.gravatar.com/avatar/$hash".'?d=404';
        }
        $headers = @get_headers($uri);
        if($headers != false){
          if (preg_match("|200|", $headers[0])) {
            $has_valid_avatar = true;
          }
        }
      }

      if($has_valid_avatar){
        return $uri;
      } else {
        return $this->user_avatar;
      }
    }

    public function getUserAvatarAttribute()
    { 
      if($this->gender == 'F'){
        return asset('assets/img/avatars/user-f.png');
      } else {
        return asset('assets/img/avatars/user.png');
      }
    }

    public function getNameWithTitleAttribute()
    { 
      $name_with_title = ($this->front_title==null?"":$this->front_title." ").$this->name.($this->back_title==null?"":", ".$this->back_title);
      return $name_with_title;
    }
}
