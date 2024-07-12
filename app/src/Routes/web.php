<?php
//slim load
use Psr\Http\Message\ResponseInterface as ResponseSlim;
use Psr\Http\Message\ServerRequestInterface as RequestSlim;
use Lib\slim\getParsedBody as getParsedBody;
use Slim\Routing\RouteCollectorProxy;
use DI\Container;
use Psr\Container\ContainerInterface;
use App\Middlewares\Authorization;
//container 
//midde
// use App\middleware\{
//     authorization,
//     notifyOnModifie,
//     permission
// };
//controllers load
use App\Controllers\{
    Rinha,
    swoole
};

/*
 Função responsável por pegar os dados do container que contém a instância do swooleServer
*/
/** @var \Slim\App $app */
/** @var \DI\Container $container */
// $http = $container->get('swooleServer');

$swooleServer = $container->get('swooleServer');

$container->set('swooleServer', function ()  use ($http) {
    return $http;
});
// $app->group('/v1/api/user/', function (RouteCollectorProxy $group) use ($swooleServer) {
//     $group->get('', exemple::class . ':task');
// });
// $app->post('/pessoas', function (RequestSlim $request, ResponseSlim $response, $args) use ($swooleServer) {
//     return Rinha::create($request, $response, $args, $swooleServer);
// });
// //$app->get('/pessoas', peaples::class . ':search');
// //$app->get('/pessoas/{id}', peaples::class . ':searchId');
// $app->get('/contagem-pessoas', Rinha::class . ':countPeoples');

// $app->get('/pessoas/{id}', Rinha::class . ':searchId');
// //pessoas?t=[:termo da busca]
// $app->get('/pessoas', Rinha::class . ':search');

//teste router
$app->get('/teste', function (RequestSlim $request, ResponseSlim $response, $args) use ($swooleServer) {
    //swoole task execute
    go(function () use ($swooleServer) {
        sleep(5);
        $swooleServer->task(
            'task executada'
        );
    });

    $swooleServer->task(
        'task executada'
    );
    $response->getBody()->write('teste');
    return $response; // Retorna o objeto $response
});

$app->group('/v1/api/swoole/', function (RouteCollectorProxy $group) {
    $group->get('', swoole::class . ':listAll');
    $group->get('{id}', swoole::class . ':searchId');
    $group->get('slug/{slug}', swoole::class . ':searchSlug');
    $group->post('', swoole::class . ':create');
    $group->put('{id}', swoole::class . ':update')->add(Authorization::class . ':Authorization');
    $group->delete('{id}', swoole::class . ':delete')->add(Authorization::class . ':Authorization');
});

// $app->group('/v1/api/swoole2/', function (RouteCollectorProxy $group) {
//     $group->get('', swoole::class . ':listAll');
//     $group->get('{id}', swoole::class . ':searchId');
//     $group->get('slug/{slug}', swoole::class . ':searchSlug');
//     $group->post('', swoole::class . ':create');
//     $group->put('{id}', swoole::class . ':update');
//     $group->delete('{id}', swoole::class . ':delete')->add(Authorization::class . ':Authorization');
// });


