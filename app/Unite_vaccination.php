<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Unite_vaccination extends Model
{
    public function user_unite(){
        return $this->hasMany(User_unite::class);
    }
}
