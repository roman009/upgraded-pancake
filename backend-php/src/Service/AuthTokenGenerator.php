<?php

namespace App\Service;

class AuthTokenGenerator
{
    public function __invoke()
    {
        return base64_encode(bin2hex(random_bytes(64)));
    }
}
