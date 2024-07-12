<?php

namespace App\Tests\Unit\Constants;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;

class ConstantsTest extends TestCase
{
   

    #[TestDox('Verifica se as configurações de hora e data estão definidas')]
    public function testDateTimeSettings()
    {
        $this->assertNotFalse(date_default_timezone_get());
        $this->assertNotFalse(setlocale(LC_ALL, 0));
    }

    #[TestDox('Verifica se as configurações de exibição de erros estão definidas')]
    public function testErrorDisplaySettings()
    {
        $this->assertTrue(defined('DISPLAY_ERROR'));
        $this->assertTrue(defined('DISPLAY_ERROR_JSON'));
    }

    #[TestDox('Verifica se as configurações do banco de dados estão definidas')]
    public function testDatabaseSettings()
    {
        $this->assertTrue(defined('TYPE'));
        $this->assertTrue(defined('HOST'));
        $this->assertTrue(defined('DB'));
        $this->assertTrue(defined('USER'));
        $this->assertTrue(defined('PASS'));
        $this->assertTrue(defined('PORT'));
        $this->assertTrue(defined('DRIVER'));
    }

    #[TestDox('Verifica se as configurações do Redis estão definidas')]
    public function testRedisSettings()
    {
        $this->assertTrue(defined('REDIS_HOST'));
        $this->assertTrue(defined('REDIS_PASS'));
        $this->assertTrue(defined('REDIS_PORT'));
    }

    #[TestDox('Verifica se as configurações de email estão definidas')]
    public function testEmailSettings()
    {
        $this->assertTrue(defined('EMAIL_HOST'));
        $this->assertTrue(defined('EMAIL_PORT'));
        $this->assertTrue(defined('EMAIL_USER'));
        $this->assertTrue(defined('EMAIL_PASS'));
        $this->assertTrue(defined('EMAIL_DEBUG'));
        $this->assertTrue(defined('EMAIL_FROM'));
    }

    #[TestDox('Verifica se as configurações de domínio e URL base estão definidas')]
    public function testDomainAndBaseUrlSettings()
    {
        $this->assertTrue(defined('DOMAIN'));
        $this->assertTrue(defined('URL_BASE'));
    }
}