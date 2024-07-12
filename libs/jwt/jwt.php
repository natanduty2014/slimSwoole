<?php

namespace Lib\jwt;

use Firebase\JWT\JWT as JWTTOKEN;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;
use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;
use DomainException;
use InvalidArgumentException;
use UnexpectedValueException;
use Lib\cryptography\token;
use Lib\cryptography\encode;

class jwt
{
    private static $token;

    // Gerar o token
    private static function generatorprivate(array $data, ?array $user_permissions = null): string
    {
        // 7 dias
        $time = time() + (7 * 24 * 60 * 60);
        date_default_timezone_set('America/Sao_Paulo');

        $HTTP_ORIGIN = $_SERVER['HTTP_ORIGIN'] ?? 'http://localhost';
        $HTTP_X_FORWARDED_FOR = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? 'http://localhost';

        $ip = [
            $HTTP_ORIGIN,
            encode::encode($HTTP_X_FORWARDED_FOR)
        ];

        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';

        $payload = [
            'iss' => "http://localhost",
            'aud' => $ip,
            'iat' => time(),
            'browser' => self::getBrowser($userAgent),
            'platform' => $_SERVER['HTTP_SEC_CH_UA_PLATFORM'] ?? 'unknown',
            "user_id" => $data['use_id'],
            "user_name" => $data['use_name'],
            "user_email" => $data['use_email'],
            "user_slug" => $data['use_slug'],
            "user_usg_id" => $data['use_usg_id'],
            "user_avatar" => $data['use_avatar'],
            "user_role" => $data['usg_title'],
            'exp' => $time,
            'user_permissions' => $user_permissions
        ];

        return JWTTOKEN::encode($payload, token::privatekey(), 'RS256');
    }

    // Retornar o token gerado
    public static function generator(array $data, ?array $user_permissions = null): string
    {
        return self::generatorprivate($data, $user_permissions);
    }

    // Abrir o token
    private static function decodetokenprivate(string $jwt): object|string
    {
        $jwt = str_replace('Bearer ', '', $jwt);

        try {
            return JWTTOKEN::decode($jwt, new Key(token::publickey(), 'RS256'));
        } catch (InvalidArgumentException $e) {
            return 'InvalidArgumentException';
        } catch (DomainException $e) {
            return 'DomainException';
        } catch (SignatureInvalidException $e) {
            return 'SignatureInvalidException';
        } catch (BeforeValidException $e) {
            return 'BeforeValidException';
        } catch (ExpiredException $e) {
            return 'ExpiredException';
        } catch (UnexpectedValueException $e) {
            return 'UnexpectedValueException';
        }
    }

    // Retornar o token aberto
    public static function decodetoken(string $token): object|string
    {
        return self::decodetokenprivate($token);
    }

    public static function verifyToken(string $token): bool
    {
        $token = str_replace('Bearer ', '', $token);

        if (empty($token)) {
            throw new \Exception('Empty token');
        }

        $decodedToken = self::decodetoken($token);

        if ($decodedToken === 'InvalidArgumentException') {
            throw new \Exception('Invalid argument');
        }
        if ($decodedToken === 'SignatureInvalidException') {
            throw new \Exception('Signature invalid');
        }
        if ($decodedToken === 'BeforeValidException') {
            throw new \Exception('Before valid');
        }
        if ($decodedToken === 'ExpiredException') {
            throw new \Exception('Expired token');
        }
        if ($decodedToken === 'DomainException') {
            throw new \Exception('Domain exception');
        }
        if ($decodedToken === 'UnexpectedValueException') {
            throw new \Exception('Unexpected value');
        }

        return true;
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
            'EWW' => '/w3m/i',
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
