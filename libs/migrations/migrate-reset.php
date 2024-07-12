<?php

require '../../vendor/autoload.php';
require '../database/eloquent/config.php';

use Illuminate\Database\Capsule\Manager as Capsule;

if ($argc < 2) {
    echo "Usage: php migrate-reset.php <table_name>\n";
    exit(1);
}

$tableName = $argv[1];

class MigrateReset
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
    }

    public function resetMigrations($path)
    {
        $migrationFiles = glob($path . '/*.php');
        foreach ($migrationFiles as $file) {
            $className = basename($file, '.php');
            require $file;
            $migrationInstance = new $className;
            if (Capsule::schema()->hasTable($className)) {
                $migrationInstance->down();
                echo "Tabela '{$className}' removida com sucesso.\n";
            } else {
                echo "Tabela '{$className}' não existe. Nada para reverter.\n";
            }
        }
        echo "All migrations have been reset.\n";
    }
}

$migrateReset = new MigrateReset();
$migrateReset->resetMigrations('../../app/src/migrations');