<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhUser extends Model
{
    use HasFactory;
    public $incrementing = false;
    protected $fillable = [
        'uid', 
        'username',
        'username_old',
        'name',
        'role',
        'password',
        'cardno',
        'status',
        'group_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'username', 'username');
    }

    public function group()
    {
        return $this->belongsTo(WhUserGroup::class, 'group_id', 'uid');
    }

    public function unit()
    {
        return $this->belongsTo(WhUserUnit::class, 'unit_id', 'uid');
    }

    protected $appends = ['status_name', 'role_name'];

    public function getStatusNameAttribute(){
            $x = "";
            if($this->status == 1){
                $x = "Aktif";
            } else if($this->status == 0){
                $x = "Tidak Aktif";
            }
            return $x;
    }

    public function getRoleNameAttribute(){
        $x = "";
        if($this->role == 0){
            $x = "User";
        } else if($this->role == 14){
            $x = "Admin";
        }
        return $x;
}
}
