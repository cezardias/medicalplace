<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $table = 'leads';

    protected $fillable = [
        'tipo', 'nome', 'telefone', 'email', 'cpf', 
        'crm', 'especialidade', 'turno', 'convenio', 'status'
    ];
}
