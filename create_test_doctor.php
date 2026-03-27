<?php

// Script para criar usuario medico de teste com saldo
// Uso dentro do container: php artisan tinker create_test_doctor.php

use App\User;
use App\Repositories\CreditosRepository;
use Illuminate\Support\Facades\Hash;

$email = 'cezar.dias@gmail.com';
$password = '123456';
$valor_credito = 100.00;

$user = User::where('email', $email)->first();

if (!$user) {
    $user = new User();
    $user->name = 'Cezar';
    $user->sobrenome = 'Dias';
    $user->email = $email;
    $user->password = Hash::make($password);
    $user->role = 'medico';
    $user->status = 'ativo';
    $user->telefone = '61999449572';
    $user->cpf = '00589372181';
    $user->save();
    echo "Sucesso! Usuario medico criado.\n";
} else {
    $user->role = 'medico';
    $user->status = 'ativo';
    $user->save();
    echo "Usuario ja existia, garantindo que seja medico e ativo.\n";
}

// Adicionar credito de 100 reais
$repo = new CreditosRepository();
$repo->grava($user->id, $valor_credito, 'credito', null);

echo "Sucesso! Adicionado R$ " . number_format($valor_credito, 2, ',', '.') . " de credito para o usuario {$email}.\n";
echo "Acesse com a senha: {$password}\n";
