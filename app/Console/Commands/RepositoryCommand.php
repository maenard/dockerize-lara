<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class RepositoryCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:repository {name} {--a}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate repository class.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $name = Str::singular($name);
        $stub_path = base_path('stubs/repository.stub');
        $repo_path = app_path("Repositories/{$name}Repository.php");

        if (File::exists($repo_path)) {
            $this->error("Repository {$name} already exists!");
        }else{
            $stub = File::get($stub_path);
            $stub = str_replace(
                ['{{ namespace }}', '{{ class }}'],
                ['App\\Repositories', $name . 'Repository'],
                $stub
            );
            File::put($repo_path, $stub);
            $this->info("Repository {$name} created successfully.");
        }

        if($this->option('a')){
            Artisan::call('make:model', ['name' => $name]);
            Artisan::call('make:request', ['name' => "$name/Index"]);
            Artisan::call('make:request', ['name' => "$name/Store"]);
            Artisan::call('make:request', ['name' => "$name/Update"]);
            Artisan::call('make:request', ['name' => "$name/Delete"]);
            Artisan::call('make:migration',
                [
                    'name' => 'create_' . lcfirst(Str::plural($name)) . '_table',
                ]
            );

            $controller_stub_path = base_path('stubs/controller.repo.stub');
            $controller_path = app_path("Http/Controllers/{$name}Controller.php");

            if(File::exists($controller_path)){
                $this->error("Controller {$name} already exists!");
            }else{
                $stub = File::get($controller_stub_path);
                $stub = str_replace(
                    ['{{ namespace }}', '{{ controller }}', '{{ name }}', '{{ repo_name }}', '{{ repo_variable_name }}'],
                    ['App\\Http\\Controllers', $name . 'Controller', $name, $name . 'Repository', lcfirst($name . 'Repository')],
                    $stub
                );

                File::put($controller_path, $stub);
                $this->info("Controller {$name} created successfully.");
            }
        }
    }
}
