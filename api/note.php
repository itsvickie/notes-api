<?php

require __DIR__ . '/../vendor/autoload.php';

use Notes\App\Controllers\NoteController;

$method = strtolower($_SERVER['REQUEST_METHOD']);

$controller = new NoteController();

switch($method){
    case 'post':
        $controller->register();
        break;
    case 'put':
        $controller->update();
        break;
    case 'delete':
        $controller->delete();
        break;
    case 'get':
        $controller->list();
        break;
}