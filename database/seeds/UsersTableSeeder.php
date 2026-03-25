<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            [
                'name' => 'Admin',
                'email' => 'admin@medicalplace.med.br',
                'password' => bcrypt('ekm98vbfh6'),
                'role' => 'administrador',
                'status' => 'ativo'
            ]
/*
	,
            [
                'name' => 'Secretaria',
                'email' => 'secretaria@medicalplace.com.br',
                'password' => bcrypt('123'),
                'role' => 'secretaria',
                'status' => 'ativo'
            ],
            [
                'name' => 'Medico',
                'email' => 'medico@medicalplace.com.br',
                'password' => bcrypt('123'),
                'role' => 'medico',
                'status' => 'ativo'
            ]*/
        ]);
    }
}
