<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    public function user_province(){
        return $this->hasMany(User_province::class);
    }
}
