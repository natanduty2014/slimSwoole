<?php

require '../../vendor/autoload.php';
require '../database/eloquent/config.php';

use Illuminate\Database\Capsule\Manager as Capsule;

if ($argc < 2) {
    echo "Usage: php migrate-rollback.php <table_name>\n";
    exit(1);
}

$tableName = $argv[1];

class MigrateRollback
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

    public function rollbackMigration($path, $tableName)
    {
        $migrationFiles = glob($path . '/*.php');
        foreach ($migrationFiles as $file) {
            $className = basename($file, '.php');
            if ($className === $tableName) {
                require $file;
                $migrationInstance = new $className;
                if (Capsule::schema()->hasTable($tableName)) {
                    $migrationInstance->down();
                    echo "Tabela '{$tableName}' removida com sucesso.\n";
                } else {
                    echo "Tabela '{$tableName}' não existe. Nada para reverter.\n";
                }
                return;
            }
        }
        echo "Arquivo de migração para a tabela '$tableName' não encontrado.\n";
    }
}

$migrateRollback = new MigrateRollback();
$migrateRollback->rollbackMigration('../../app/src/migrations', $tableName);