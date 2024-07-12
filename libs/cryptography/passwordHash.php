<?php

namespace Lib\cryptography;

class passwordHash
{
  static public function passwordHash(string $password): string
  {
    //password_hash PASSWORD_ARGON2I
    return  password_hash($password, PASSWORD_ARGON2I);
  }
}