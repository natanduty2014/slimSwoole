<?php

namespace Lib\cryptography;

class encode
{
    static public function encode($data)
    {
        //random_bytes
        // $iv = \random_bytes(16);
        // $key = \random_bytes(16);
        // var_dump(bin2hex($iv));
        // var_dump(bin2hex($key));
        //openssl_encrypt
        $data = $data;
        $method = getenv('CRYPTOGRAPHY_METHOD_ENCODE');
        $key = \hex2bin(getenv('CRYPTOGRAPHY_KEY_ENCODE'));
        $iv = \hex2bin(getenv('CRYPTOGRAPHY_IV_ENCODE'));
        $encrypted = openssl_encrypt($data, $method, $key, 0, $iv);
        $encoded = base64_encode($encrypted);
        return $encoded;
    }

    static public function libsodium($data){
        $key = \hex2bin(getenv('CRYPTOGRAPHY_KEY_LIBSODIUM'));
        $nonce = \hex2bin(getenv('CRYPTOGRAPHY_NONCE_LIBSODIUM'));
        $encrypted = \sodium_crypto_secretbox($data, $nonce, $key);
        $encoded = base64_encode($encrypted);
        return $encoded;
    }
}
