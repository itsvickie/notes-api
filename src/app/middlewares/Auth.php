<?php

namespace Notes\App\Middlewares;
use Notes\Services\Jwt;

class Auth
{
    public function jwt() 
    {
        $headers = getallheaders();

        if(!isset($headers['Authorization'])){
            http_response_code(401);
            die(json_encode(['message' => 'Token não informado!']));
        }

        $jwt = new Jwt();

        return $jwt->verify($headers['Authorization']);
    }
}