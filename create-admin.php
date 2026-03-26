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
    $user->save();
    echo "Usuario admin ja existia, role atualizada para 'administrador'.\n";
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
