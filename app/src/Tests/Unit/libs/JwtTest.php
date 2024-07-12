<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use Lib\jwt\jwt;
use Lib\cryptography\token;
use Firebase\JWT\JWT as JWTTOKEN;

class JwtTest extends TestCase
{
    private array $data;
    private array $user_permissions;

    protected function setUp(): void
    {
        $this->data = [
            'use_id' => 1,
            'use_name' => 'Test User',
            'use_email' => 'test@example.com',
            'use_slug' => 'test-user',
            'use_usg_id' => 1,
            'use_avatar' => 'avatar.png',
            'usg_title' => 'admin'
        ];
        $this->user_permissions = ['read', 'write'];
    }

    #[TestDox('Gera um token JWT com dados válidos e permissões')]
    public function testGenerator(): void
    {
        $testCases = [
            [
                'data' => [
                    'use_id' => 1,
                    'use_name' => 'Test User',
                    'use_email' => 'test@example.com',
                    'use_slug' => 'test-user',
                    'use_usg_id' => 1,
                    'use_avatar' => 'avatar.png',
                    'usg_title' => 'admin'
                ],
                'permissions' => ['read', 'write']
            ],
            [
                'data' => [
                    'use_id' => 2,
                    'use_name' => 'Another User',
                    'use_email' => 'another@example.com',
                    'use_slug' => 'another-user',
                    'use_usg_id' => 2,
                    'use_avatar' => 'avatar2.png',
                    'usg_title' => 'user'
                ],
                'permissions' => []
            ]
        ];

        foreach ($testCases as $case) {
            $token = jwt::generator($case['data'], $case['permissions']);
            $this->assertNotEmpty($token, 'Token should not be empty');
        }
    }

    #[TestDox('Decodifica um token JWT e verifica os dados do usuário')]
    public function testDecodeToken(): void
    {
        $token = jwt::generator($this->data, $this->user_permissions);
        $decoded = jwt::decodetoken($token);
        $this->assertEquals($this->data['use_id'], $decoded->user_id);
        $this->assertEquals($this->data['use_name'], $decoded->user_name);
    }

    #[TestDox('Verifica a validade de um token JWT')]
    public function testVerifyToken(): void
    {
        $token = jwt::generator($this->data, $this->user_permissions);
        $this->assertTrue(jwt::verifyToken($token));
    }

    #[TestDox('Lança uma exceção ao verificar um token JWT inválido')]
    public function testInvalidToken(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Unexpected value');
        jwt::verifyToken('invalid.token');
    }

    #[TestDox('Lança uma exceção ao verificar um token JWT expirado')]
    public function testExpiredToken(): void
    {
        $expiredData = $this->data;
        $expiredData['exp'] = time() - 3600; // 1 hour ago
        $token = JWTTOKEN::encode($expiredData, token::privatekey(), 'RS256');
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Expired token');
        jwt::verifyToken($token);
    }

    #[TestDox('Obtém o navegador a partir do User-Agent')]
    public function testGetBrowser(): void
    {
        $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.3';
        $browser = jwt::getBrowser($userAgent);
        $this->assertEquals('Chrome', $browser);
    }
}
