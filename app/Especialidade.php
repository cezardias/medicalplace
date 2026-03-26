<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Especialidade extends Model
{
    protected $table = 'especialidades';

    protected $fillable = ['nome', 'status'];

    public function servicos()
    {
        return $this->hasMany(Servico::class);
    }
}
