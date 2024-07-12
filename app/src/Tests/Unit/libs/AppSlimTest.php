<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use Slim\Factory\AppFactory;
use Slim\Psr7\Factory\ServerRequestFactory;
use Slim\Psr7\Factory\ResponseFactory;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use App\Middlewares\Authorization;
use Lib\jwt\jwt;

class AppSlimTest extends TestCase
{
    private $app;

    protected function setUp(): void
    {
        $this->app = AppFactory::create();
        $this->app->addRoutingMiddleware();

        // Adiciona o middleware de autorização
        $this->app->add(function (ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
            $authorizationHeader = $request->getHeaderLine('authorization');
            if ($request->getMethod() === 'OPTIONS') {
                $response = new \Slim\Psr7\Response();
                return $response
                    ->withHeader('Access-Control-Allow-Origin', '*')
                    ->withHeader('Access-Control-Allow-Headers', 'Content-Type, authorization')
                    ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
            }

            $response = $handler->handle($request);
            if (!empty($authorizationHeader)) {
                $response = $response->withHeader('authorization', $authorizationHeader);
            }
            return $response;
        });

        // Adiciona uma rota de exemplo
        $this->app->get('/', function (ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
            $response->getBody()->write('Hello, world!');
            return $response;
        });
    }

    #[TestDox('Testa requisição OPTIONS')]
    public function testOptionsRequest()
    {
        $request = (new ServerRequestFactory())->createServerRequest('OPTIONS', '/');
        $response = $this->app->handle($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('*', $response->getHeaderLine('Access-Control-Allow-Origin'));
        $this->assertEquals('Content-Type, authorization', $response->getHeaderLine('Access-Control-Allow-Headers'));
        $this->assertEquals('GET, POST, PUT, DELETE, OPTIONS', $response->getHeaderLine('Access-Control-Allow-Methods'));
    }

    #[TestDox('Testa cabeçalho de autorização')]
    public function testAuthorizationHeader()
    {
        $request = (new ServerRequestFactory())->createServerRequest('GET', '/');
        $request = $request->withHeader('authorization', 'Bearer valid_token');

        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->method('handle')->willReturn((new ResponseFactory())->createResponse(200));

        $response = $this->app->handle($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Bearer valid_token', $response->getHeaderLine('authorization'));
    }

    #[TestDox('Testa autorização com token JWT válido')]
    public function testAuthorizationWithValidToken()
    {
        $data = [
            'use_id' => 1,
            'use_name' => 'John Doe',
            'use_email' => 'john.doe@example.com',
            'use_slug' => 'john-doe',
            'use_usg_id' => 2,
            'use_avatar' => 'avatar.png',
            'usg_title' => 'Admin'
        ];

        $user_permissions = ['read', 'write', 'delete'];
        $valid_token = jwt::generator($data, $user_permissions);

        $request = (new ServerRequestFactory())->createServerRequest('GET', '/');
        $request = $request->withHeader('authorization', 'Bearer ' . $valid_token);

        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->method('handle')->willReturn((new ResponseFactory())->createResponse(200));

        $response = authorization::authorization($request, $handler);

        $this->assertEquals(200, $response->getStatusCode());
    }

    #[TestDox('Testa autorização com token JWT inválido')]
    public function testAuthorizationWithInvalidToken()
    {
        $request = (new ServerRequestFactory())->createServerRequest('GET', '/');
        $request = $request->withHeader('authorization', 'Bearer invalid_token');

        $handler = $this->createMock(RequestHandlerInterface::class);

        $response = Authorization::Authorization($request, $handler);

        $this->assertEquals(401, $response->getStatusCode());
    }
}