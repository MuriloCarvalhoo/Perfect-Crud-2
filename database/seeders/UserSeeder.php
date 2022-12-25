<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        $user = User::updateOrCreate(
            ['id' => 1],
            [
                'name' => 'Murilo Carvalho',
                'email' => 'murilocarvalho2204@gmail.com',
                'cpf' => '44020592837',
                'ativo' => 1,
                'password'  => Hash::make('123456')
            ]
        );

        $user->roles()->attach([1,2]);

    }
}
