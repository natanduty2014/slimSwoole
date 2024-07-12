<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use App\Models\swoole;
use Lib\database\eloquent\config;

class swooleTest extends TestCase
{
    private static $id;

    protected function setUp(): void {
        parent::setUp();
        // Inicialize o estado necessário para os testes
    }

    #[TestDox('Testa a criação de um novo swoole')]
    public function testCreateSwoole()
    {
        $data = [
            'nome' => 'Test Swoole'
        ];

        $result = swoole::create($data);
        self::$id = $result['id'];
        $this->assertEquals(201, $result['status']);
    }

    #[TestDox('Testa a busca de um swoole pelo ID')]
    public function testSearchSwooleByID()
    {
        $id = self::$id;
        $swoole = swoole::searchID($id);

        $this->assertNotEquals(404, $swoole['status']);
        $this->assertEquals($id, $swoole['data']->id);
    }

    #[TestDox('Testa a listagem de todos os swoole')]
    public function testListAllSwoole()
    {
        $swooleList = swoole::listall();
        
        $this->assertIsIterable($swooleList['data']);
        foreach ($swooleList['data'] as $swoole) {
            $this->assertNotNull($swoole->nome);
        }
    }
}