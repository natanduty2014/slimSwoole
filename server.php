<?php

ini_set('display_errors', 'on');
ini_set('display_startup_errors', 'on');
ini_set('memory_limit', '900M');

require __DIR__ . '/vendor/autoload.php';

use Imefisto\PsrSwoole\ServerRequest as PsrRequest;
use Imefisto\PsrSwoole\ResponseMerger;
use Nyholm\Psr7\Factory\Psr17Factory;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Lib\swoole\Utils\TaskChannelManager;


$http = new Swoole\Http\Server('0.0.0.0', getenv('HTTP_PORT'));

$uriFactory = new Psr17Factory;
$streamFactory = new Psr17Factory;
$responseFactory = new Psr17Factory;
$uploadedFileFactory = new Psr17Factory;
$responseMerger = new ResponseMerger;

$http->set([
    'enable_coroutine' => true,
    'task_enable_coroutine' => true,
    'max_coroutine' => 10000,
    'reactor_num' => 2,
    'worker_num' => 2,
    'task_worker_num' => 8,
    'hook_flags' => SWOOLE_HOOK_ALL,
    'open_tcp_nodelay' => true,
    //post size max 30mb
    'package_max_length' => 30 * 1024 * 1024,
]);

// $http->set([
//     //teste
//     'dispatch_mode' => 1,
//     'open_length_check'     => true,
//     'package_max_length' => 1024 * 1024 * 5,
//     'package_length_type'   => 'N',
//     'package_length_offset' => 8,
//     'package_body_offset'   => 16,
//     //
//     'backlog'       => 512,   // set the length of the listen queue //é uma configuração que define o tamanho da fila de conexões pendentes que aguardam para serem aceitas pelo servidor
//     //'max_request'   => 500,    // maximum number of requests per process
//     'open_tcp_keepalive' => true,
//     'tcp_keepidle' => 4, // Check if no data transmission for 4 seconds
//     'tcp_keepinterval' => 1, // Probe every 1 second
//     'tcp_keepcount' => 5, // Number of probes, close the connection if no response after 5 attempts
//     'heartbeat_idle_time'      => 120, // Represents that if a connection does not send any data to the server within 600 seconds, the connection will be forcibly closed
//     'heartbeat_check_interval' => 60,  // Represents checking every 60 seconds
//     //coroutine
//     'max_coroutine' => 3000,
//     //'enable_deadlock_check' => false,
//     //'hook_flags' => SWOOLE_HOOK_SLEEP,
//     //'enable_preemptive_scheduler' => true,
//     'enable_coroutine' => true,
//     //'task_enable_coroutine' => true, #esta tendo conflito com as task quando usar coroutine dentro das tasks
//     //end coroutine
//     //process
//     'reactor_num' => swoole_cpu_num(),
//     'worker_num' => swoole_cpu_num(),
//     'task_worker_num' => 8, //divida por 10 o numero de task por segundos (2000/10 = 200 task por segundo)
//     'task_max_request' => 500,
//     //
//     'open_tcp_nodelay' => true,
//     'socket_buffer_size' => 4 * 1024 * 1024,
//     'buffer_output_size' => 4 * 1024 * 1024,
//     //'open_http2_protocol' => true,
//     'max_wait_time' => 120,
//     'http_parse_files' => true,
//     'http_parse_post' => true,

//     //post size max 30mb

//     //async http (coroutine)
//     //'task_enable_coroutine' => true,
//     //'log_level' => 0,
//     // 'log_file' => '/public/logs/server/swoole/serverSwoole.'.getenv('HTTP_PORT').'.log',
//     // 'log_rotation' => SWOOLE_LOG_ROTATION_DAILY,
//     // 'log_date_format' => '%Y-%m-%d %H:%M:%S',
//     //'http_parse_cookie' => false,
//     //server id pid to reload
//     // 'http_compression' => true,
//     // //level 6
//     // 'http_compression_level' => 6,
//     // Enable HTTP2 protocol
//     // Source File Reloading
//     //'reload_async' => true,
//     //root past
//     // 'enable_static_handler' => true,
//     // 'document_root' => './',
//     // 'static_handler_locations' => ['/public'],
// ]);

// a swoole server is evented just like express
$http->on('start', function (Swoole\Http\Server $http) {
   // echo sprintf('Swoole http server is started at http://localhost:9602 [%s]', date('Y-m-d H:i:s') . '\n');
    //    $scanDirectories = [
    //     'site/controllers',
    //     'site/controllers/entity',
    //     'site/middleware',
    //     'site/model',
    //  ];
    //  //$swagger = \OpenApi\Generator::scan($scanDirectories);
    //  //file_put_contents('public/swagger/swagger.json', \json_encode($swagger));
});

$http->on(
    'request',
    function (
        Request $swooleRequest,
        Response $swooleResponse
    ) use (
        $uriFactory,
        $streamFactory,
        $uploadedFileFactory,
        $responseFactory,
        $responseMerger,
        $http
    ) {
        //create coroutine
       
        /**
         * create psr request from swoole request
         */
        $psrRequest = new PsrRequest(
            $swooleRequest,
            $uriFactory,
            $streamFactory,
            $uploadedFileFactory
        );

        require './bootstrap/SwooleMethod.php';
        require './bootstrap/Container.php';
        require './libs/slim/AppSlim.php';

        // Instanciar a classe AppSlim
        

        /**
         * process request (here is where slim handles the request)
         */
        $psrResponse = $app->handle($psrRequest);

        /**
         * merge your psr response with swoole response
         */
        $responseMerger->toSwoole(
            $psrResponse,
            $swooleResponse
        )->end();
    }
);
$http->on('task', function (Swoole\Http\Server $server, Swoole\Server\Task $task) {
    try {
        $data = $task->data;
        // RinhasModel::create($data, $data['uuid']);
    } catch (\Exception $e) {
        echo "catch";
    }
    $task->finish($data);
});

$http->on('finish', function (Swoole\Http\Server $server, $task_id, $data) {
    // Tarefa finalizada
});


$http->start();

