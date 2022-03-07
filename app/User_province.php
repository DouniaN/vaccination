<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class User_province extends Model
{
    public function province(){
        return $this->belongsTo(Province::class);
     }
     public function user(){
         return $this->belongsTo(User::class);
     }
}
