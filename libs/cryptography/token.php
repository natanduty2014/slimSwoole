<?php

namespace Lib\cryptography;

class token
{
    private static $publicKey;
    private static $privateKey;
    private static function public()
    {
      return static::$publicKey = getenv('CRYPTOGRAPHY_PUBLIC_KEY');
    }
    public static function publickey(){
        return self::public();
    }
    //
    private static function private()
    {
      return static::$privateKey = getenv('CRYPTOGRAPHY_PRIVATE_KEY');
    }

    public static function privatekey(){
        return self::private();
    }
}
