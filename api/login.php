<?php

require __DIR__ . '/../vendor/autoload.php';

use Notes\App\Controllers\LoginController;

$method = strtolower($_SERVER['REQUEST_METHOD']);

$controller = new LoginController();

switch($method){
    case 'post':
        $controller->login();
        break;
}