<?php

namespace Notes\App\Controllers;

use Notes\Database\Config;
use Sprained\Validator;

class UserController extends Config 
{
    public function register()
    {
        $body = json_decode(file_get_contents("php://input"), true);
        $validator = new Validator();

        $username = $validator->required($body['username'], 'O Nome de Usuário');
        $password = $validator->password($body['password']);

        $conn = $this->connect();
        $sql = $conn->prepare("SELECT id FROM user WHERE username = ?");
        $sql->bind_param("s", $username);
        $sql->execute();
        $sql = $sql->get_result();
        $sql = $sql->fetch_assoc();

        if(isset($sql)){
            http_response_code(500);
            die(json_encode(['message' => 'Nome de Usuário já cadastrado!']));
        }

        $sql = $conn->prepare("INSERT INTO user (username, password) VALUES (?, ?)");
        $sql->bind_param("ss", $username, $password);
        
        if($sql->execute()){
            http_response_code(201);
            die();
        }

        http_response_code(500);
        die(json_encode(['message' => 'Não foi possível cadastrar o usuário!']));
    }
}