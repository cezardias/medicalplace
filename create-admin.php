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
    $user->role = 'admin'; 
    $user->status = 'ativo';
    $user->save();
    echo "Sucesso! Usuario admin criado.\n";
    echo "Email: $email\n";
    echo "Senha: $password\n";
} else {
    echo "Erro: Usuario com este email ja existe.\n";
}
