<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'roble:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Instala ROBLE en entorno de producción';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando la instalación de la aplicación...');

        $this->updateEnvironmentFile();

        Artisan::call('config:clear');
        Artisan::call('cache:clear');

        $this->info('Ejecutando migraciones de bases de datos...');
        Artisan::call('migrate:fresh', ['--seed' => true]);

        $this->createSuperUser();

        Artisan::call('key:generate');

        $this->info('¡Instalación completada exitosamente!');
    }

    private function updateEnvironmentFile()
    {
        $this->info('Establecer variables de entorno...');

        $envPath = base_path('.env');
        $envContent = file_get_contents($envPath);

        $vars = [
            'APP_NAME' => $this->ask('Nombre de la aplicación', 'Roble'),
            'APP_URL' => $this->ask('URL de la aplicación', 'http://localhost'),
            'DB_HOST' => $this->ask('Host de base de datos', 'localhost'),
            'DB_PORT' => $this->ask('Puerto de base de datos', '5432'),
            'DB_DATABASE' => $this->ask('Nombre de la base de datos'),
            'DB_USERNAME' => $this->ask('Nombre de usuario de la base de datos'),
            'DB_PASSWORD' => $this->secret('Contraseña de la base de datos'),
            'DB_HOST_ORG' => $this->ask('Host de la base de datos de la organización', 'localhost'),
            'DB_PORT_ORG' => $this->ask('Puerto de base de datos de la organización', '5432'),
            'DB_DATABASE_ORG' => $this->ask('Nombre de la base de datos de la organización'),
            'DB_USERNAME_ORG' => $this->ask('Nombre de usuario de la base de datos de la organización'),
            'DB_PASSWORD_ORG' => $this->secret('Contraseña de la base de datos de la organización'),
            'REVERB_APP_ID' => random_int(100000, 999999),
            'REVERB_APP_KEY' => Str::random(20),
            'REVERB_APP_SECRET' => Str::random(20),
            'REVERB_HOST' => $this->ask('Host Reverb', 'localhost'),
            'REVERB_PORT' => $this->ask('Port Reverb', '8080'),
            'REVERB_SCHEME' => $this->ask('Esquema Reverb (http o https)', 'http'),
            'APP_ENV' => 'production',
            'APP_DEBUG' => false,
        ];

        if ($this->confirm('¿Quiere configurar los ajustes del servidor de correo electrónico?'))
        {
            $vars['MAIL_HOST'] = $this->ask('Host de correo electrónico');
            $vars['MAIL_PORT'] = $this->ask('Puerto de correo electrónico');
            $vars['MAIL_USERNAME'] = $this->ask('Nombre de usuario del correo electrónico');
            $vars['MAIL_PASSWORD'] = $this->secret('Contraseña de correo electrónico');
            $vars['MAIL_FROM_ADDRESS'] = $this->ask('Dirección de correo electrónico del remitente');
        }

        foreach ($vars as $key => $value)
        {
            $envContent = preg_replace("/^{$key}=.*/m", "{$key}={$value}", $envContent);
        }

        file_put_contents($envPath, $envContent);
    }

    private function createSuperUser()
    {
        $this->info('Crear Superusuario...');

        $password = $this->secret('Introduzca la contraseña para el superusuario');

        $name = 'root';
        $email = 'root@email.com';

        if ($this->confirm('Do you want to customize the superuser data?', false))
        {
            $name = $this->ask('Enter the superuser name');
            $email = $this->ask('Enter the superuser email');
        }

        User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
        ])->assignRole('superuser');
    }
}
