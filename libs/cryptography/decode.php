<?php

namespace Lib\cryptography;

class decode{
    static public function decode($data)
    {
        //openssl_dncrypt
        $data = $data;
        $method = getenv('CRYPTOGRAPHY_METHOD');
        $key = \hex2bin(getenv('CRYPTOGRAPHY_KEY'));
        $iv = \hex2bin(getenv('CRYPTOGRAPHY_IV'));
        $decoded = base64_decode($data);
        $decrypted = openssl_decrypt($decoded, $method, $key, 0, $iv);
        return $decrypted;
    }

    static public function libsodium($data){
        $key = \hex2bin(getenv('CRYPTOGRAPHY_KEY_LIBSODIUM'));
        $nonce = \hex2bin(getenv('CRYPTOGRAPHY_NONCE_LIBSODIUM'));
        $decoded = base64_decode($data);
        $decrypted = \sodium_crypto_secretbox_open($decoded, $nonce, $key);
        return $decrypted;
    }
}