<?php 

require __DIR__ . '/../vendor/autoload.php';

use Notes\App\Controllers\UserController;

$method = strtolower($_SERVER['REQUEST_METHOD']);

$controller = new UserController();

switch($method){
    case 'post':
        $controller->register();
        break;
}