<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Servico extends Model
{
    protected $table = 'servicos';

    protected $fillable = ['especialidade_id', 'nome', 'preco', 'status'];

    public function especialidade()
    {
        return $this->belongsTo(Especialidade::class);
    }
}
