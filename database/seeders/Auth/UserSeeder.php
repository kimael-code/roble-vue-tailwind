<?php

namespace Database\Seeders\Auth;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name'            => 'admin',
                'email'           => 'admin@company.org',
                'password'        => Hash::make('admin'),
                'remember_token'  => Str::random(10),
                'is_password_set' => false,
                'role'            => 'Administrador de Sistemas',
            ],
        ];

        $devUsers = [
            [
                'name'            => 'test',
                'email'           => 'test@examplecom',
                'password'        => Hash::make('12345678'),
                'remember_token'  => Str::random(10),
                'is_password_set' => true,
                'role'            => 'Administrador de Sistemas',
            ],
        ];

        if (App::environment('local'))
        {
            foreach ($devUsers as $devUser)
            {
                array_push($users, $devUser);
            }
            array_shift($users);
        }

        foreach ($users as $user)
        {
            $createdUser = User::create([
                'name'            => $user['name'],
                'email'           => $user['email'],
                'password'        => $user['password'],
                'remember_token'  => $user['remember_token'],
                'is_password_set' => $user['is_password_set'],
            ]);
            $createdUser->assignRole($user['role']);
        }
    }
}
