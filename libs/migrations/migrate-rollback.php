<?php

require '../../vendor/autoload.php';
require '../database/eloquent/config.php';

use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon;

$tableName = $argv[1] ?? null;

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

        // Cria a tabela de controle de migrações se não existir
        if (!Capsule::schema()->hasTable('migrations')) {
            Capsule::schema()->create('migrations', function ($table) {
                $table->increments('id');
                $table->string('migration');
                $table->text('table_structure')->nullable();
                $table->timestamp('reverted_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function rollbackMigration($path, $tableName = null)
    {
        if ($tableName) {
            if ($tableName === 'lastReverted') {
                $this->rollbackLastRevertedMigration($path);
            } else {
                $this->rollbackSpecificMigration($path, $tableName);
            }
        } else {
            $this->rollbackLastMigration($path);
        }
    }

    protected function rollbackSpecificMigration($path, $tableName)
    {
        $migrationFiles = glob($path . '/*.php');
        foreach ($migrationFiles as $file) {
            $className = basename($file, '.php');
            if ($className === $tableName) {
                require $file;
                $migrationInstance = new $className;
                if (Capsule::schema()->hasTable($tableName)) {
                    // Verifica se a tabela contém dados
                    $rowCount = Capsule::table($tableName)->count();
                    if ($rowCount > 0) {
                        echo "Tabela '{$tableName}' contém dados. Operação de rollback não realizada.\n";
                        return;
                    }
                    $migrationInstance->down();
                    Capsule::table('migrations')->where('migration', $className)->update(['reverted_at' => Carbon::now()]);
                    echo "Tabela '{$tableName}' removida com sucesso.\n";
                } else {
                    echo "Tabela '{$tableName}' não existe. Nada para reverter.\n";
                }
                return;
            }
        }
        echo "Arquivo de migração para a tabela '$tableName' não encontrado.\n";
    }

    protected function rollbackLastMigration($path)
    {
        $lastMigration = Capsule::table('migrations')->whereNull('reverted_at')->orderBy('id', 'desc')->first();
        if ($lastMigration) {
            $this->rollbackSpecificMigration($path, $lastMigration->migration);
        } else {
            echo "Nenhuma migração encontrada para reverter.\n";
        }
    }

    protected function rollbackLastRevertedMigration($path)
    {
        $lastRevertedMigration = Capsule::table('migrations')->whereNotNull('reverted_at')->orderBy('reverted_at', 'desc')->first();
        if ($lastRevertedMigration) {
            $this->applyMigrationFromStructure($lastRevertedMigration->migration, $lastRevertedMigration->table_structure);
        } else {
            echo "Nenhuma migração revertida encontrada para aplicar.\n";
        }
    }

    protected function applyMigrationFromStructure($tableName, $tableStructure)
    {
        if ($tableStructure) {
            Capsule::schema()->create($tableName, function ($table) use ($tableStructure) {
                $structure = json_decode($tableStructure, true);
                foreach ($structure as $column) {
                    $table->{$column['type']}($column['name']);
                }
            });
            Capsule::table('migrations')->where('migration', $tableName)->update(['reverted_at' => null, 'updated_at' => Carbon::now()]);
            echo "Tabela '{$tableName}' aplicada com sucesso.\n";
        } else {
            echo "Estrutura da tabela '{$tableName}' não encontrada.\n";
        }
    }
}

$migrateRollback = new MigrateRollback();
$migrateRollback->rollbackMigration('../../app/src/migrations', $tableName);