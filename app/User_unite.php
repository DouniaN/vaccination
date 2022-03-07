<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class User_unite extends Model
{
    public function unite_vaccination(){
       return $this->belongsTo(Unite_vaccination::class);
    }
    public function user(){
        return $this->belongsTo(User::class);
    }
}
