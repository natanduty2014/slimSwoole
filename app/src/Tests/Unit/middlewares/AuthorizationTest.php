<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use Slim\Psr7\Factory\ServerRequestFactory;
use Slim\Psr7\Factory\ResponseFactory;
use Psr\Http\Server\RequestHandlerInterface;
use App\Middlewares\Authorization;
use Lib\jwt\jwt;

class AuthorizationTest extends TestCase
{
    #[TestDox('Autoriza uma requisição com um token JWT válido')]
    public function testAuthorizationWithValidToken()
    {
        // Dados do usuário para o payload do token
        $data = [
            'use_id' => 1,
            'use_name' => 'John Doe',
            'use_email' => 'john.doe@example.com',
            'use_slug' => 'john-doe',
            'use_usg_id' => 2,
            'use_avatar' => 'avatar.png',
            'usg_title' => 'Admin'
        ];

        // Permissões do usuário (opcional)
        $user_permissions = ['read', 'write', 'delete'];

        // Gerar o token válido
        $valid_token = jwt::generator($data, $user_permissions);

        // Mock do request com um token válido
        $request = (new ServerRequestFactory())->createServerRequest('GET', '/');
        $request = $request->withHeader('authorization', 'Bearer ' . $valid_token);

        // Mock do handler
        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->method('handle')->willReturn((new ResponseFactory())->createResponse(200));

        // Chama o método de autorização
        $response = Authorization::Authorization($request, $handler);

        // Verifica se o status da resposta é 200
        $this->assertEquals(200, $response->getStatusCode());
    }

    #[TestDox('Rejeita uma requisição com um token JWT inválido')]
    public function testAuthorizationWithInvalidToken()
    {
        // Mock do request com um token inválido
        $request = (new ServerRequestFactory())->createServerRequest('GET', '/');
        $request = $request->withHeader('authorization', 'Bearer invalid_token');

        // Mock do handler
        $handler = $this->createMock(RequestHandlerInterface::class);

        // Chama o método de autorização
        $response = Authorization::Authorization($request, $handler);

        // Verifica se o status da resposta é 401
        $this->assertEquals(401, $response->getStatusCode());
    }
}