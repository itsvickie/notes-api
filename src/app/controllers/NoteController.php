<?php

namespace Notes\App\Controllers;

use Notes\Database\Config;
use Sprained\Validator;
use Notes\App\Middlewares\Auth;

class NoteController extends Config
{
    public function register()
    {
        $auth = new Auth();
        $jwt = $auth->jwt();

        $body = json_decode(file_get_contents("php://input"), true);
        $validator = new Validator();

        $title = $validator->required($body['title'], 'O Título da Nota');
        $description = $body['description'];

        $conn = $this->connect();
        $sql = $conn->prepare("INSERT INTO note (title, description, id_user) VALUES (?, ?, ?)");
        $sql->bind_param("ssi", $title, $description, $jwt['id']);

        if($sql->execute()){
            http_response_code(201);
            die();
        }

        http_response_code(500);
        die(json_encode(['message' => 'A nota não pôde ser cadastrada!']));
    }

    public function list()
    {
        $auth = new Auth();
        $jwt = $auth->jwt();    
        $conn = $this->connect();

        if(isset($_GET['id_note'])){
            $sql = $conn->prepare("SELECT title, description FROM note WHERE id = ?");
            $sql->bind_param("i", $_GET['id_note']);

            if($sql->execute()){
                $sql = $sql->get_result();
                $sql = $sql->fetch_assoc();
                http_response_code(200);
                die(json_encode($sql));
            }

            http_response_code(500);
            die(json_encode(['message' => 'Nota não cadastrada!']));
        } else {
            $sql = $conn->prepare("SELECT id, title, description FROM note WHERE id_user = ?");
            $sql->bind_param("i", $jwt['id']);

            if($sql->execute()){
                http_response_code(200);
                $sql = $sql->get_result();
                $sql = $sql->fetch_all(MYSQLI_ASSOC);
                die(json_encode($sql));
            }

            http_response_code(500);
            die(json_encode(['message' => 'Usuário não encontrado!']));
        } 
    }

    public function update()
    {
        $auth = new Auth();
        $jwt = $auth->jwt();

        $body = json_decode(file_get_contents("php://input"), true);
    
        $id = $_GET['id'];
        $arr = [];
        $title = isset($body['title']) ? $body['title'] : false;
        $description = isset($body['description']) ? $body['description'] : false;
        $param = '';

        $conn = $this->connect();

        $sql = $conn->prepare("SELECT id FROM note WHERE id = ?");
        $sql->bind_param("i", $id);
        $sql->execute();
        $sql = $sql->get_result();
        $sql = $sql->fetch_assoc();

        if(isset($sql['id'])){
            $sql = "UPDATE note SET ";
    
            if($title){
                $sql .= "title = ? ";
                $param .= 's';
                array_push($arr, $title);
            }
    
            if($description){
                $sql .= $title ? ",description = ? " : "description = ? ";
                $param .= 's';
                array_push($arr, $description);
            }
    
            $param .= 'i';
            array_push($arr, $id);
            
            $sql = $conn->prepare($sql . "WHERE id = ?");
            $sql->bind_param($param, ...$arr);
            
            if($sql->execute()){
                http_response_code(200);
                die();
            }
        }

        http_response_code(500);
        die(json_encode(['message' => 'Nota não encontrada!']));
    }

    public function delete()
    {
        $auth = new Auth();
        $jwt = $auth->jwt(); 

        $id = $_GET['id'];

        $conn = $this->connect();

        $sql = $conn->prepare("DELETE FROM note WHERE id = ?");
        $sql->bind_param("i", $id);

        if($sql->execute()){
            http_response_code(200);
            die();
        }

        http_response_code(500);
        die(json_encode(['message' => 'A Nota não pôde ser excluída!']));
    }
}