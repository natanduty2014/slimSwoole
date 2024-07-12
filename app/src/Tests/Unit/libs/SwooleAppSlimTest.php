<?php

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use Swoole\Http\Server;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Slim\Factory\AppFactory;

class SwooleAppSlimTest extends TestCase
{
    private $server;
    private $app;

    protected function setUp(): void
    {
        // Configurar o servidor Swoole
        $this->server = new Server('0.0.0.0', 3000);

        // Configurar a aplicação Slim
        $this->app = AppFactory::create();
        $this->app->addRoutingMiddleware();
        $this->app->addErrorMiddleware(true, true, true);

        // Adicionar rotas de teste
        $this->app->get('/test', function ($request, $response, $args) {
            $response->getBody()->write('Hello, world!');
            return $response;
        });

        $this->app->post('/test', function ($request, $response, $args) {
            $response->getBody()->write('Posted!');
            return $response;
        });

        $this->app->put('/test', function ($request, $response, $args) {
            $response->getBody()->write('Updated!');
            return $response;
        });

        $this->app->delete('/test', function ($request, $response, $args) {
            $response->getBody()->write('Deleted!');
            return $response;
        });

        $this->app->post('/upload', function ($request, $response, $args) {
            $uploadedFiles = $request->getUploadedFiles();
            $uploadedFile = $uploadedFiles['file'] ?? null;

            if ($uploadedFile && $uploadedFile->getError() === UPLOAD_ERR_OK) {
                $response->getBody()->write('File uploaded successfully!');
            } else {
                $response->getBody()->write('Failed to upload file.');
            }

            return $response;
        });
    }

    #[TestDox('Testa a resposta da aplicação Slim')]
    public function testSlimAppResponse()
    {
        // Testar GET
        $psrRequest = new \Slim\Psr7\Request(
            'GET',
            new \Slim\Psr7\Uri('http', 'localhost', 3000, '/test'),
            new \Slim\Psr7\Headers(),
            [],
            [],
            new \Slim\Psr7\Stream(fopen('php://temp', 'r+'))
        );
        $psrResponse = $this->app->handle($psrRequest);
        $this->assertEquals(200, $psrResponse->getStatusCode());
        $this->assertEquals('Hello, world!', (string) $psrResponse->getBody());

        // Testar POST
        $psrRequest = new \Slim\Psr7\Request(
            'POST',
            new \Slim\Psr7\Uri('http', 'localhost', 3000, '/test'),
            new \Slim\Psr7\Headers(),
            [],
            [],
            new \Slim\Psr7\Stream(fopen('php://temp', 'r+'))
        );
        $psrResponse = $this->app->handle($psrRequest);
        $this->assertEquals(200, $psrResponse->getStatusCode());
        $this->assertEquals('Posted!', (string) $psrResponse->getBody());

        // Testar PUT
        
        $psrRequest = new \Slim\Psr7\Request(
            'PUT',
            new \Slim\Psr7\Uri('http', 'localhost', 3000, '/test'),
            new \Slim\Psr7\Headers(),
            [],
            [],
            new \Slim\Psr7\Stream(fopen('php://temp', 'r+'))
        );
        $psrResponse = $this->app->handle($psrRequest);
        $this->assertEquals(200, $psrResponse->getStatusCode());
        $this->assertEquals('Updated!', (string) $psrResponse->getBody());

        // Testar DELETE
        $psrRequest = new \Slim\Psr7\Request(
            'DELETE',
            new \Slim\Psr7\Uri('http', 'localhost', 3000, '/test'),
            new \Slim\Psr7\Headers(),
            [],
            [],
            new \Slim\Psr7\Stream(fopen('php://temp', 'r+'))
        );
        $psrResponse = $this->app->handle($psrRequest);
        $this->assertEquals(200, $psrResponse->getStatusCode());
        $this->assertEquals('Deleted!', (string) $psrResponse->getBody());
    }

    // #[TestDox('Testa o upload de arquivos')]
    // public function testFileUpload()
    // {
    //     // Criar um arquivo temporário para upload
    //     $tempFile = tmpfile();
    //     fwrite($tempFile, 'file content');
    //     $tempFilePath = stream_get_meta_data($tempFile)['uri'];

    //     // Criar um stream para o arquivo
    //     $stream = new \Slim\Psr7\Stream(fopen($tempFilePath, 'r'));

    //     // Criar um arquivo carregado
    //     $uploadedFile = new \Slim\Psr7\UploadedFile(
    //         $stream,
    //         'test.txt',
    //         'text/plain',
    //         filesize($tempFilePath),
    //         UPLOAD_ERR_OK
    //     );

    //     // Criar a requisição com o arquivo carregado
    //     $psrRequest = new \Slim\Psr7\Request(
    //         'POST',
    //         new \Slim\Psr7\Uri('http', 'localhost', 3000, '/upload'),
    //         new \Slim\Psr7\Headers(),
    //         [],
    //         ['file' => $uploadedFile],
    //         new \Slim\Psr7\Stream(fopen('php://temp', 'r+'))
    //     );

    //     // Processar a requisição com a aplicação Slim
    //     $psrResponse = $this->app->handle($psrRequest);

    //     // Verificar a resposta
    //     $this->assertEquals(200, $psrResponse->getStatusCode());
    //     $this->assertEquals('File uploaded successfully!', (string) $psrResponse->getBody());

    //     // Fechar o arquivo temporário
    //     fclose($tempFile);
    // }
}