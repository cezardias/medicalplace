<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Salas extends Model
{
    public function imagens() {
        return $this->hasMany('App\SalasImagens', 'sala_id', 'id');
    }
}
