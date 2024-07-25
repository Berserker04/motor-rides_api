<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ApiFullCommand extends Command
{
    protected $signature = 'app:api-full {name}';

    protected $description = 'Create a controller, model, seeder, and resource for the API';

    public function handle()
    {
        $name = $this->argument('name');

        // Crear el modelo
        Artisan::call('make:model', [
            'name' => $name,
            '-f' => true,
        ]);

        // Crear el controlador
        Artisan::call('make:controller', [
            'name' => "Api/V1/{$name}Controller",
            '-r' => true,
        ]);

        // Crear el seeder
        Artisan::call('make:seeder', [
            'name' => "{$name}Seeder",
        ]);

        // Crear el Resource
        Artisan::call('make:resource', [
            'name' => "V1/{$name}Resource",
        ]);

        $this->info('Controller, model, seeder, and resource created successfully.');
    }
}
