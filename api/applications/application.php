<?php
require_once('../controllers/ApplicationController.php');

$controller = new ApplicationController();

// Extract ID from request uri
$request_uri = $_SERVER['REQUEST_URI'];
preg_match('/\/api\/applications\/(\d+)/', $request_uri, $matches);

$id = $matches[1];
$requestMethod = $_SERVER['REQUEST_METHOD'];

// NOTE/TODO: Ideally, we should use some sort of router library or build our own router class to handle these requests but I opted against it for this initial build as a proof of concept. 
switch($requestMethod) {
    case "GET":
        $controller->getApplicationById($id);
        break;
    case "PUT":
        $controller->putApplication();
        break;
}