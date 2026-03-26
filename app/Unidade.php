<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Unidade extends Model
{
    protected $table = 'unidades';

    protected $fillable = ['nome', 'endereco', 'horario_funcionamento', 'status'];
}
