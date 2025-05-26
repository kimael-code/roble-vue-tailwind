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
                'name'            => 'root',
                'email'           => 'root@company.com',
                'password'        => Hash::make('root'),
                'remember_token'  => Str::random(10),
                'is_password_set' => false,
                'role'            => __('Superuser'),
            ],
            [
                'name'            => 'admin',
                'email'           => 'admin@company.com',
                'password'        => Hash::make('admin'),
                'remember_token'  => Str::random(10),
                'is_password_set' => false,
                'role'            => __('Systems Administrator'),
            ],
        ];

        $devUsers = [
            [
                'name'            => 'admin.dev',
                'email'           => 'admin.dev@example.com',
                'password'        => Hash::make('12345678'),
                'remember_token'  => Str::random(10),
                'is_password_set' => true,
                'role'            => __('Systems Administrator'),
            ],
        ];

        if (App::environment('local'))
        {
            foreach ($devUsers as $devUser)
            {
                array_push($users, $devUser);
            }
            unset($users[0], $users[1]);
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
