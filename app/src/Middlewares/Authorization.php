<?php

namespace App\Middlewares;

use Slim\Psr7\Response as SlimResponse;
use Psr\Http\Message\ResponseInterface as ResponseSlim;
use Psr\Http\Message\ServerRequestInterface as RequestSlim;
use Psr\Http\Server\RequestHandlerInterface as RequestHandlerSlim;
use Lib\jwt\jwt;
use Lib\cryptography\decode;

class Authorization
{
    /**
     * @OA\Info(
     *   version="0.4.0",
     *   title="My API",
     *   description="This is a sample server Petstore server.  You can find out more about Swagger at [http://swagger.io](http://swagger.io) or on [irc.freenode.net, #swagger](http://swagger.io/irc/). For this sample, you can use the api key `special-key` to test the authorization filters.",
     *    @OA\contact(
     *      name="API Support",
     *      url="http://www.example.com/support",
     *      email="support@example.com"
     *    ),
     *   @OA\Attachable()
     * )
     * @OA\SecurityScheme(
     *     securityScheme="bearerAuth",
     *     type="http",
     *     scheme="bearer",
     *     bearerFormat="JWT"
     * ),
     * @OA\Response(
     *    response=200,
     *    description="Success"
     * ),
     * @OA\Response(
     *    response=400,
     *    description="Bad request"
     * ),
     * @OA\Response(
     *    response=401,
     *    description="Unauthorized"
     * ),
     * @OA\Response(
     *    response=500,
     *    description="Internal Server Error"
     * )
     */
    public static function Authorization(RequestSlim $request, RequestHandlerSlim $handler): ResponseSlim
    {
        try {
            $authorizationHeader = $request->getHeader('authorization')[0] ?? null;
            if (!$authorizationHeader) {
                throw new \Exception('Token inválido');
            }

            $token = jwt::verifyToken($authorizationHeader);
            if (!$token) {
                throw new \Exception('Token inválido');
            }

            $jwt = jwt::decodetoken($authorizationHeader);
            $getIPExternal = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? 'http://localhost';
            $HTTP_ORIGIN = $_SERVER['HTTP_ORIGIN'] ?? 'http://localhost';
            $HTTP_SEC_CH_UA_PLATFORM = $_SERVER['HTTP_SEC_CH_UA_PLATFORM'] ?? '';

            // Add additional validation checks if necessary
            // if (self::getBrowser($_SERVER['HTTP_USER_AGENT'] ?? '') !== $jwt->browser) {
            //     throw new \Exception('Token_not_allowed');
            // }
            // if ($getIPExternal !== decode::decode($jwt->aud[1])) {
            //     throw new \Exception('Token_not_allowed');
            // }
            // if ($HTTP_ORIGIN !== $jwt->aud[0]) {
            //     throw new \Exception('Token_not_allowed');
            // }
            // if ($HTTP_SEC_CH_UA_PLATFORM !== $jwt->platform) {
            //     throw new \Exception('Token_not_allowed');
            // }

            $response = $handler->handle($request);
            $status = $response->getStatusCode();
            $existingContent = (string)$response->getBody();

            $newResponse = new SlimResponse();
            $newResponse->getBody()->write($existingContent);

            return $newResponse
                ->withHeader('Content-Type', 'application/json')
                ->withHeader('Access-Control-Allow-Origin', '*')
                ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization')
                ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
                ->withStatus($status);
        } catch (\Throwable $e) {
            $response = new SlimResponse();
            $response->getBody()->write(
                json_encode(
                    [
                        'status' => 401,
                        'message' => 'Unauthorized ' . $e->getMessage()
                    ]
                )
            );

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withHeader('Access-Control-Allow-Origin', '*')
                ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization')
                ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
                ->withStatus(401);
        }
    }

    public static function getBrowser(string $userAgent): string
    {
        $patterns = [
            'Firefox' => '/Firefox/i',
            'Chrome' => '/Chrome|CriOS/i',
            'Safari' => '/Safari/i',
            'Edge' => '/Edg/i',
            'Opera' => '/Opera|OPR/i',
            'IE' => '/MSIE/i',
            'Brave' => '/Brave/i',
            'Vivaldi' => '/Vivaldi/i',
            'Yandex' => '/YaBrowser/i',
            'UC Browser' => '/UCBrowser/i',
            'Samsung Internet' => '/SamsungBrowser/i',
            'Nokia Browser' => '/NokiaBrowser/i',
            'Maxthon' => '/Maxthon/i',
            'Konqueror' => '/Konqueror/i',
            'Pale Moon' => '/PaleMoon/i',
            'SeaMonkey' => '/SeaMonkey/i',
            'Avant Browser' => '/Avant Browser/i',
            'Epic Privacy Browser' => '/Epic/i',
            'Waterfox' => '/Waterfox/i',
            'DuckDuckGo Browser' => '/DuckDuckGo/i',
            'Midori' => '/Midori/i',
            'qutebrowser' => '/qutebrowser/i',
            'Sleipnir' => '/Sleipnir/i',
            'GNU IceCat' => '/IceCat/i',
            'GNU IceWeasel' => '/Iceweasel/i',
            'QupZilla' => '/QupZilla/i',
            'Falkon' => '/Falkon/i',
            'Min Browser' => '/Min/i',
            'Dooble' => '/Dooble/i',
            'Elinks' => '/ELinks/i',
            'Links' => '/Links/i',
            'Lynx' => '/Lynx/i',
            'w3m' => '/w3m/i',
            'NetSurf' => '/NetSurf/i',
            'Surf' => '/Surf/i',
            'Dillo' => '/Dillo/i',
            'Amaya' => '/Amaya/i',
            'EWW' => '/w3m/i', // Emacs Web Wowser
            'Emacs w3' => '/w3m/i',
            'MicroEmacs' => '/w3m/i',
            'w3' => '/w3m/i',
            'ELinks' => '/ELinks/i'
        ];

        foreach ($patterns as $browser => $pattern) {
            if (preg_match($pattern, $userAgent)) {
                return $browser;
            }
        }

        return 'Desconhecido';
    }
}
