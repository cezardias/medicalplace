<?php

// Script para criar o usuario Cezar Dias com 100 reais de credito
// Uso dentro do container: php artisan tinker create-cezar-credit.php

use App\User;
use App\Creditos;
use Illuminate\Support\Facades\Hash;
use App\Repositories\CreditosRepository;

$email = 'cezar.dias@gmail.com';
$user = User::where('email', $email)->first();

if (!$user) {
    $user = new User();
    $user->name = 'Cezar';
    $user->sobrenome = 'Dias';
    $user->email = $email;
    $user->password = Hash::make('123456');
    $user->role = 'medico';
    $user->status = 'ativo';
    $user->save();
    echo "Sucesso! Usuario $email criado.\n";
} else {
    echo "Usuario $email ja existia.\n";
}

// Adicionar 100 reais de credito
// Criar entrada manual no extrato
$credito = new Creditos();
$credito->user_id = $user->id;
$credito->valor = 100.00;
$credito->tipo = 'credito';
$credito->save();

echo "Sucesso! Foram adicionados R$ 100,00 de saldo para {$user->name}.\n";
echo "Email: $email | Senha: 123456\n";
