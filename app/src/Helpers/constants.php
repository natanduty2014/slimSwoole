<?php
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__, 'const.env'); // Ajuste o caminho conforme necessário
$dotenv->load();
//configuraçes da hora/data
//header('Content-type: text/html; charset=utf-8');
setlocale( LC_ALL, 'pt_BR.utf-8', 'pt_BR', 'Portuguese_Brazil');
date_default_timezone_set('America/Sao_Paulo');

//display errors with json for api mode
define('DISPLAY_ERROR', getenv('DISPLAY_ERROR'));
define('DISPLAY_ERROR_JSON', getenv('DISPLAY_ERROR_JSON'));


//configuração do banco de dados
define('TYPE', $_ENV['TYPE']); //mysql, pgsql, sqlsrv, sqlite
define('HOST', $_ENV['HOST']);
define('DB', $_ENV['DB']);
define('PASS', $_ENV['PASS']);
define('USER', $_ENV['USER']);
define('PORT', $_ENV['PORT']); //porta padrão do PostgreSQL: 5432 - mysql 3306 - sqlserver 1433
define('DRIVER', $_ENV['DRIVER']); //driver do banco de dados

//configuração do redis
define('REDIS_HOST', $_ENV['REDIS_HOST']);
define('REDIS_PASS', $_ENV['REDIS_PASS']);
define('REDIS_PORT', $_ENV['REDIS_PORT']);

//configuração do email
define('EMAIL_HOST', $_ENV['EMAIL_HOST']);
define('EMAIL_PORT', $_ENV['EMAIL_PORT']);
define('EMAIL_USER', $_ENV['EMAIL_USER']);
define('EMAIL_PASS', $_ENV['EMAIL_PASS']);
define('EMAIL_DEBUG', $_ENV['EMAIL_DEBUG']);
define('EMAIL_FROM', $_ENV['EMAIL_FROM']);

//project info
define('VERSION', $_ENV['VERSION']);
define('NAME', $_ENV['NAME']);
define('DESCRIPTION', $_ENV['DESCRIPTION']);

//dominio
define('DOMAIN', $_ENV['DOMAIN']);
//url base
define('URL_BASE', $_ENV['URL_BASE']);

