<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocStatus extends Model
{
    use HasFactory;
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [
        'id', 
        'name',
    ];

    protected $appends = ['color'];

    public function getColorAttribute(){
        $x = "";
        if($this->id == "S1"){
            $x = "info";
        } else if($this->id == "S2"){
            $x = "warning";
        } else if($this->id == "S3"){
            $x = "danger";
          }else if($this->id == "S4"){
            $x = "success";
        } else {
            $x = "muted";
        }
        return $x;
    }
}
