<?php
//slim load
use Slim\Factory\AppFactory;
use Slim\Psr7\Response as slimReponse;
use Psr\Http\Message\ResponseInterface as ResponseSlim;
use Psr\Http\Message\ServerRequestInterface as RequestSlim;
use Psr\Http\Server\RequestHandlerInterface as RequestHandlerSlim;

$file = 'handler';

/**
 * Create your slim app
 */
$app = AppFactory::create();
$app->addRoutingMiddleware();

$customErrorHandle = function (
    RequestSlim $request,
    Throwable $exception,
    bool $displayErrorDetails,
    bool $logErrors,
    $logger = null
) use ($app, $file): ResponseSlim {
    $payload = ['error' => $exception->getMessage()];
    $response = $app->getResponseFactory()->createResponse();

    switch ($exception->getCode()) {
        case 404:
            $response = $response->withStatus(404);
            break;
        case 405:
            $response = $response->withStatus(405);
            break;
        default:
            var_dump($exception->getMessage());
            $response = $response->withStatus(500);
            break;
    }

    $response->getBody()->write(json_encode($payload));
    return $response->withHeader('Content-Type', 'application/json');
};

// Add Error Middleware
$errorMiddleware = $app->addErrorMiddleware(true, true, true);
if (DISPLAY_ERROR == true) {
    $errorMiddleware->setDefaultErrorHandler($customErrorHandle);
} else {
    $errorMiddleware->setDefaultErrorHandler(null);
}

$app->add(function (RequestSlim $request, RequestHandlerSlim $handler) {
    try {
        // Verifica se o cabeçalho Authorization está presente na solicitação
        $authorizationHeader = $request->getHeaderLine('authorization');
        // Verifica se o método da solicitação é OPTIONS
        if ($request->getMethod() === 'OPTIONS') {
            // Retorna uma resposta vazia com os cabeçalhos de controle de acesso apropriados
            $response = new slimReponse();
            return $response
                ->withHeader('Access-Control-Allow-Origin', '*')
                ->withHeader('Access-Control-Allow-Headers', 'Content-Type, authorization')
                ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        }

        // Passa a solicitação para o próximo middleware
        $response = $handler->handle($request);
        // Adiciona o cabeçalho Authorization à resposta
        if (!empty($authorizationHeader)) {
            $response = $response->withHeader('authorization', $authorizationHeader);
        }
        return $response;
    } catch (Exception $th) {
        var_dump($th->getMessage());
        $response = new slimReponse();
        $response->getBody()->write(json_encode(['error' => $th->getMessage()]));
        return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
    }
});


//o swoole pega o caminho com base no server.php (cuidado ao alterar)
//verificar se o diretorio existe e carregar as rotas
if (is_dir('./app/src')) {
    require './app/src/Routes/web.php';
} else {
    throw new Exception('Diretório site não encontrado');
}
