<?php

namespace App\Middleware;

use Slim\Psr7\Response as SlimResponse;
use Psr\Http\Message\ResponseInterface as ResponseSlim;
use Psr\Http\Message\ServerRequestInterface as RequestSlim;
use Psr\Http\Server\RequestHandlerInterface as RequestHandlerSlim;

class NotifyOnModify
{
    public static function req(): string
    {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'web:3002/build/astro/website/',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
        ]);
        $responseBuild = curl_exec($curl);
        curl_close($curl);
        return json_encode($responseBuild);
    }

    public static function notify(RequestSlim $request, RequestHandlerSlim $handler, $swooleServer): ResponseSlim
    {
        $response = $handler->handle($request);
        $status = $response->getStatusCode();

        if (!in_array($status, [200, 201])) {
            return $response;
        }

        if (in_array($request->getMethod(), ['PUT', 'POST', 'DELETE', 'PATCH'])) {
            var_dump("status http: " . $status);
            $swooleServer->task([
                'namespace' => 'App\Middleware',
                'class' => 'NotifyOnModify',
                'method' => 'req',
            ]);
        }

        return $response;
    }
}

// Para execução direta do script
$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => 'web:3002/build/astro/website/',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
]);
$responseBuild = curl_exec($curl);
curl_close($curl);
echo json_encode($responseBuild);
echo "\n";
