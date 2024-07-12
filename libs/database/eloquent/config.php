<?php

namespace Lib\database\eloquent;

use Illuminate\Database\Capsule\Manager as Capsule;
use Swoole\Database\PDOConfig;
use Swoole\Database\PDOPool;

class config
{
    static private $capsule = null;
    static private $pool = null;

    static private function initialize()
    {
        //verificar se a conexão já foi inicializada
        if (self::$capsule === null) {
            self::$capsule = new Capsule;

            self::$capsule->addConnection([
                'driver'    => TYPE,
                'host'      => HOST,
                'database'  => DB,
                'username'  => USER,
                'password'  => PASS,
                'charset'   => 'utf8',
                'collation' => 'utf8_unicode_ci',
                'prefix'    => '',
                'options'   => [
                    \PDO::ATTR_PERSISTENT => true,
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_EMULATE_PREPARES => false,
                ]
            ]);

            self::$capsule->bootEloquent();

            // Configuração do pool de conexões
            $config = (new PDOConfig())
                ->withHost(HOST)
                ->withPort(PORT)
                ->withDbName(DB)
                ->withCharset('utf8')
                ->withUsername(USER)
                ->withPassword(PASS)
                ->withDriver(DRIVER);

            self::$pool = new PDOPool($config, 50); // Defina o nmero máximo de conexes
        }

        return self::$capsule;
    }

    static public function conn()
    {
        return self::initialize();
    }

    static public function getPooledConnection()
    {
        if (self::$pool === null) {
            self::initialize();
        }

        return self::$pool->get();
    }

    static public function closeConn()
    {
        if (self::$capsule) {
            self::$capsule->getConnection()->disconnect();
            self::$capsule = null;
        }

        if (self::$pool) {
            self::$pool->close();
            self::$pool = null;
        }
    }
}
