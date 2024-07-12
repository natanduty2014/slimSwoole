<?php

namespace Lib\migrations;

require '../../vendor/autoload.php';
require '../database/eloquent/config.php';

use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon;

class MigrateInstall
{
    protected $capsule;

    public function __construct()
    {
        $this->capsule = new Capsule;
        $this->capsule->addConnection([
            'driver'    => TYPE,
            'host'      => HOST,
            'database'  => DB,
            'username'  => USER,
            'password'  => PASS,
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ]);

        $this->capsule->setAsGlobal();
        $this->capsule->bootEloquent();

        // Cria a tabela de controle de migrações se não existir
        if (!Capsule::schema()->hasTable('migrations')) {
            Capsule::schema()->create('migrations', function ($table) {
                $table->increments('id');
                $table->string('migration');
                $table->timestamps();
            });
        }
    }

    public function runMigrations()
    {
        $migrationFiles = glob('../../app/src/migrations/*.php');
        foreach ($migrationFiles as $file) {
            if (basename($file) !== 'migrate-install.php') {
                require $file;
                $className = basename($file, '.php');
                $migrationInstance = new $className;
                if (Capsule::table('migrations')->where('migration', $className)->exists()) {
                    echo "Migração '{$className}' já foi executada. Ignorando.\n";
                } else {
                    $migrationInstance->up();
                    Capsule::table('migrations')->insert([
                        'migration' => $className,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ]);
                    echo "Migração '{$className}' executada com sucesso.\n";
                }
            }
        }
        echo "All migrations have been executed.\n";
    }
}

$migrateInstall = new MigrateInstall();

if ($argc < 2) {
    // Se nenhum argumento for fornecido, migra todas as migrações na pasta
    $migrateInstall->runMigrations();
    exit(0);
}

// Se um argumento for fornecido, você pode adicionar lógica adicional aqui, se necessário
echo "Argumento fornecido: " . $argv[1] . "\n";