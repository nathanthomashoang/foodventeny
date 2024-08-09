<?php
require_once "../controllers/ApplicationController.php";

// NOTE: Hardcoding organization id here for this proof of concept but a TODO is to retrieve all applications based on the org id of the user currently logged in. 
$orgId = 1;

// NOTE: Moved logic into controller for cleanliness/organization. 
$controller = new ApplicationController();

$requestMethod = $_SERVER['REQUEST_METHOD'];

// NOTE/TODO: Ideally, we should use some sort of router library or build our own router class to handle these requests but I opted against it for this initial build as a proof of concept. 
switch ($requestMethod) {
    case "GET":
        $controller->getApplicationsByOrgId($orgId);
        break;
    case "POST":
        $controller->postApplication();
        break;
}
