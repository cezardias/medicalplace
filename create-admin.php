<?php

// Script para criar um usuario administrador
// Uso dentro do container: php artisan tinker create-admin.php

use App\User;
use Illuminate\Support\Facades\Hash;

$email = 'admin@medicalplace.med.br';
$password = 'admin123';

$user = User::where('email', $email)->first();

if (!$user) {
    $user = new User();
    $user->name = 'Administrador';
    $user->sobrenome = 'Sistema';
    $user->email = $email;
    $user->password = Hash::make($password);
    $user->role = 'administrador'; 
    $user->status = 'ativo';
    $user->save();
    echo "Sucesso! Usuario admin criado.\n";
} else {
    $user->role = 'administrador';
    $user->password = Hash::make($password); // Garante que a senha seja admin123
    $user->save();
    echo "Usuario admin ja existia, role e senha atualizadas.\n";
}

// Criar um Medico para teste
$emailM = 'medico@medicalplace.med.br';
$userM = User::where('email', $emailM)->first();
if (!$userM) {
    $userM = new User();
    $userM->name = 'Medico';
    $userM->sobrenome = 'Teste';
    $userM->email = $emailM;
    $userM->password = Hash::make('medico123');
    $userM->role = 'medico';
    $userM->status = 'ativo';
    $userM->save();
    echo "Sucesso! Usuario medico criado (medico123).\n";
}

// Criar Usuario Cezar Dias para teste de recuperacao
$emailC = 'cezar.dias@gmail.com';
$userC = User::where('email', $emailC)->first();
if (!$userC) {
    $userC = new User();
    $userC->name = 'cezar';
    $userC->sobrenome = 'dias';
    $userC->email = $emailC;
    $userC->telefone = '61999449572';
    $userC->cpf = '00589372181';
    $userC->password = Hash::make('123456');
    $userC->role = 'medico';
    $userC->status = 'ativo';
    $userC->save();
    echo "Sucesso! Usuario cezar.dias@gmail.com criado (123456).\n";
} else {
    $userC->password = Hash::make('123456');
    $userC->save();
    echo "Usuario cezar.dias@gmail.com ja existia, senha resetada para 123456.\n";
}
