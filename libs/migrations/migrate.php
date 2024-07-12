<?php

namespace Lib\migrations;

require '../../vendor/autoload.php';
require '../database/eloquent/config.php';

use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon;

class migrate
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
                $table->timestamps();
            });
        }
    }

    public function runMigrations($path)
    {
        $migrationFiles = glob($path . '/*.php');
        foreach ($migrationFiles as $file) {
            if (basename($file) !== 'migrate.php') {
                require $file;
                $className = basename($file, '.php');
                $migrationInstance = new $className;
                if (Capsule::table('migrations')->where('migration', $className)->exists()) {
                    echo "Migração '{$className}' já foi executada. Ignorando.\n";
                } else {
                    $migrationInstance->up();
                    $tableStructure = $this->getTableStructure($className);
                    Capsule::table('migrations')->insert([
                        'migration' => $className,
                        'table_structure' => json_encode($tableStructure),
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ]);
                    echo "Migração '{$className}' executada com sucesso.\n";
                }
            }
        }
        echo "Migrations executed successfully.\n";
    }

    protected function getTableStructure($tableName)
    {
        $columns = Capsule::schema()->getColumnListing($tableName);
        $structure = [];
        foreach ($columns as $column) {
            $type = Capsule::schema()->getColumnType($tableName, $column);
            $structure[] = ['name' => $column, 'type' => $type];
        }
        return $structure;
    }

    public function runMigrationsOnly($archive)
    {
        $path = '../../app/src/migrations/';
        $file = $path . $archive . '.php';
        require $file;
        $className = basename($file, '.php');
        $migrationInstance = new $className;
        if (Capsule::table('migrations')->where('migration', $className)->exists()) {
            echo "Migração '{$className}' já foi executada. Ignorando.\n";
        } else {
            $migrationInstance->up();
            $tableStructure = $this->getTableStructure($className);
            Capsule::table('migrations')->insert([
                'migration' => $className,
                'table_structure' => json_encode($tableStructure),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
            echo "Migração '{$className}' executada com sucesso.\n";
        }
        echo "Migrations executed successfully.\n";
    }

    public function createMigrationFile($path, $fileName)
    {
        $className = ucfirst(pathinfo($fileName, PATHINFO_FILENAME));
        $filePath = $path . '/' . $fileName . '.php'; // Adiciona a extensão .php ao nome do arquivo
        if (!file_exists($filePath)) {
            $template = "<?php\n\nuse Illuminate\\Database\\Capsule\\Manager as Capsule;\n\nclass " . $className . " {\n    public function up() {\n        if (!Capsule::schema()->hasTable('{$fileName}')) {\n            Capsule::schema()->create('{$fileName}', function (\$table) {\n                \$table->increments('id');\n                \$table->string('name');\n                \$table->timestamps();\n            });\n            echo \"Tabela '{$fileName}' criada com sucesso.\\n\";\n        } else {\n            echo \"Tabela '{$fileName}' já existe. Migração ignorada.\\n\";\n        }\n    }\n\n    public function down() {\n        Capsule::schema()->dropIfExists('{$fileName}');\n    }\n}\n";
            file_put_contents($filePath, $template);
            echo "Arquivo de migração criado: $filePath\n";
        } else {
            echo "Arquivo de migração já existe: $filePath\n";
        }
    }

    public function runAllMigrations($path)
    {
        $this->runMigrations($path);
        echo "All migrations executed successfully.\n";
    }
}

if ($argc < 2) {
    echo "Usage: php migrate.php <migration_name|all|create> [<file_name>]\n";
    exit(1);
}

$migrationManager = new migrate();
if ($argv[1] === 'all') {
    $migrationManager->runAllMigrations('../../app/src/migrations');
} elseif ($argv[1] === 'create') {
    if ($argc < 3) {
        echo "Usage: php migrate.php create <file_name>\n";
        exit(1);
    }
    $migrationName = $argv[2];
    $migrationManager->createMigrationFile('../../app/src/migrations', $migrationName);
    $migrationManager->runMigrationsOnly($migrationName);
} else {
    $migrationName = $argv[1];
    $migrationManager->createMigrationFile('../../app/src/migrations', $migrationName);
}