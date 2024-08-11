<?php

require_once '../services/DatabaseService.php';
require_once '../entities/Application.php';

class ApplicationController
{
    // NOTE: My DatabaseService I created for reusability. 
    private $dbService;

    public function __construct()
    {
        $this->dbService = new DatabaseService();
    }

    // GET api/applications
    public function getApplicationsByOrgId($id)
    {
        $applications = $this->dbService->fetchApplicationsByOrganizerId($id);
        header('Content-Type: application/json');

        if (empty($applications)) {
            http_response_code(404);
            echo json_encode(["error" => "Resource not found"]);
        } else {
            echo json_encode($applications);
        }
    }

    // GET api/application/{id}
    public function getApplicationById($id)
    {
        $application = $this->dbService->fetchApplicationById($id);
        header('Content-Type: application/json');

        if ($application === null) {
            http_response_code(404);
            echo json_encode(["error" => "Resource not found"]);
        } else {
            echo json_encode($application);
        }
    }
    // NOTE/TODO: For these two routes below, it's crucial we sanitize the input to address possible vulnerabilities such as SQL injections.
    // POST api/application
    public function postApplication()
    {
        $requestBody = json_decode(file_get_contents("php://input"));

        // TODO: Ideally, there should be server side form validation here

        $title = $requestBody->title;
        $description = $requestBody->description;
        $eventType = $requestBody->type;
        $eventId = $requestBody->event_id;
        $email = $requestBody->email;
        // NOTE/TODO: should use unix timestamp and convert to YYYY/MM/DD format but we'll use this for proof of concept
        $deadlineDate = $requestBody->deadline_date;

        $application = new Application();
        $application->organizer_id = 1;
        $application->event_id = $eventId;
        $application->title = $title;
        $application->description = $description;
        $application->email = $email;
        $application->type = $eventType;
        $application->status = Application::STATUS_PENDING;
        $application->deadline_date = $deadlineDate;
        // TODO: add proper photo upload capabilities but using placeholder images for proof of concept.
        $application->image = "https://picsum.photos/700/250";

        $newApplication = $this->dbService->insertNewApplication($application);

        header('Content-Type: application/json');

        if ($application !== null) {
            http_response_code(201);
            echo json_encode($newApplication);
        } else {
            http_response_code(500);
            // NOTE: Overall, there could be better handling for these situations with better logging in place
            echo json_encode(["error" => "failed to create resource"]);
        }
    }

    // PUT api/application/:id
    public function putApplication()
    {
        $isDirty = false;
        $requestBody = json_decode(file_get_contents("php://input"));
        header('Content-Type: application/json');

        // TODO: The intent here is not to support partial updates via a partial request payload. There should ideally be validations to validate the payload. 

        $application = $this->dbService->fetchApplicationById($requestBody->id);

        if (empty($application)) {
            http_response_code(404);
            echo json_encode(["error" => "Resource not found"]);
            exit();
        }

        // Checking if there are any new updates to Application
        foreach ($requestBody as $key => $value) {
            if (property_exists($application, $key)) {
                if ($application->$key !== $value) {
                    $isDirty = true;
                    break;
                }
            }
        }

        if (!$isDirty) {
            http_response_code(204);
            exit();
        }

        $application->deadline_date = $requestBody->deadline_date;
        $application->organizer_id = $requestBody->organizer_id;
        $application->event_id = $requestBody->event_id;
        $application->title = $requestBody->title;
        $application->description = $requestBody->description;
        $application->email = $requestBody->email;
        $application->type = $requestBody->type;
        $application->status = $requestBody->status;
        $application->deadline_date = $requestBody->deadline_date;
        $application->image = $requestBody->image;

        $isUpdated = $this->dbService->updateApplication($application);

        if ($isUpdated) {
            http_response_code(204);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "failed to update resource"]);
        }
    }
}
