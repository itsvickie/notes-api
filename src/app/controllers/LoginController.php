<?php

namespace Notes\App\Controllers;

use Notes\Database\Config;
use Notes\Services\Jwt;
use Sprained\Validator;

class LoginController extends Config
{
    public function login()
    {
        $body = json_decode(file_get_contents("php://input"), true);
        $validator = new Validator();

        $username = $validator->required($body['username'], 'O Nome de Usuário');
        $password = $validator->password($body['password']);

        $conn = $this->connect();
        $sql = $conn->prepare("SELECT id FROM user WHERE username = ? AND password = ?");
        $sql->bind_param("ss", $username, $password);
        $sql->execute();
        $sql = $sql->get_result();
        $sql = $sql->fetch_assoc();

        if(isset($sql)){
            $token = new Jwt();
            $token = $token->create(json_encode($sql['id']));
            http_response_code(200);
            die($token);
        }

        http_response_code(500);
        die(json_encode(['message' => 'Usuário e/ou Senha Incorreto(s)!']));
    }
}