<?php
require_once('../entities/Application.php');

class DatabaseService
{
    private $serverName;
    private $userName;
    private $password;
    private $dbName;
    private $connection;

    public function __construct()
    {
        // TODO: REALLY SHOULD PARAMETERIZE THESE CREDENTIALS
        $this->serverName = "mysqldb";
        $this->userName = "root";
        $this->password = "rootpassword";
        $this->dbName = "foodventeny_db";

        $this->connect();
    }

    private function connect()
    {
        try {
            $this->connection = new PDO("mysql:host=$this->serverName;dbname=$this->dbName", $this->userName, $this->password);

            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Failed to connect to db " . $e->getMessage();
            exit;
        }
    }

    // TODO: Ideally, these methods should be even further organized/moved into possibly different services per entity i.e. Applications, Organizers, etc. 

    // TODO/NOTE: For scalability, it may be worth implementing an ORM but did not feel it was necessary for this proof of concept. 

    public function fetchApplicationsByOrganizerId(int $organizerId): ?array
    {
        try {
            $stmt = $this->connection->prepare("SELECT * FROM applications WHERE organizer_id = :orgId");
            $stmt->bindParam(':orgId', $organizerId, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Failed to fetch " . $e->getMessage();
            return [];
        }
    }

    public function fetchApplicationById(int $id): ?Application
    {
        try {
            $stmt = $this->connection->prepare("SELECT * FROM applications WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $stmt->setFetchMode(PDO::FETCH_CLASS, 'Application');

            $result = $stmt->fetch();

            if ($result !== false) {
                return $result;
            }

            return null;
        } catch (PDOException $e) {
            echo "Failed to fetch " . $e->getMessage();
            return null;
        }
    }

    public function insertNewApplication(Application $application): ?Application
    {
        try {
            $stmt = $this->connection->prepare(
                "INSERT INTO applications (organizer_id, event_id, title, description, email, type, status, deadline_date, image)
                VALUES (:organizer_id, :event_id, :title, :description, :email, :type, :status, :deadline_date, :image)"
            );
            $stmt->bindParam(':organizer_id', $application->organizer_id);
            $stmt->bindParam(':event_id', $application->event_id);
            $stmt->bindParam(':title', $application->title);
            $stmt->bindParam(':description', $application->description);
            $stmt->bindParam(':email', $application->email);
            $stmt->bindParam(':type', $application->type);
            $stmt->bindParam(':status', $application->status);
            $stmt->bindParam(':deadline_date', $application->deadline_date);
            $stmt->bindParam(':image', $application->image);

            $stmt->execute();

            $lastInsertId = $this->connection->lastInsertId();

            return $this->fetchApplicationById($lastInsertId);
        } catch (PDOException $e) {
            echo "Failed to insert " . $e->getMessage();
            // NOTE: Not necessary to rollback here for a failed single record insertion. For multiple records or larger transactions, we should. 
            return null;
        }
    }

    public function updateApplication(Application $application): bool
    {
        try {
            $stmt = $this->connection->prepare(
                "UPDATE applications 
                SET organizer_id = :organizer_id,  event_id = :event_id, title = :title, description = :description, email = :email, type = :type, status = :status, deadline_date = :deadline_date, image = :image
                WHERE id = :id"
            );
            $stmt->bindParam(':id', $application->id);
            $stmt->bindParam(':organizer_id', $application->organizer_id);
            $stmt->bindParam(':event_id', $application->event_id);
            $stmt->bindParam(':title', $application->title);
            $stmt->bindParam(':description', $application->description);
            $stmt->bindParam(':email', $application->email);
            $stmt->bindParam(':type', $application->type);
            $stmt->bindParam(':status', $application->status);
            $stmt->bindParam(':deadline_date', $application->deadline_date);
            $stmt->bindParam(':image', $application->image);

            $stmt->execute();

            $numImpactedRows = $stmt->rowCount();

            if ($numImpactedRows === 0) {
                return false;
            }

            return true;
        } catch (PDOException $e) {
            echo "Failed to update " . $e->getMessage();
            // Not necessary to rollback here for a failed single record update. See note above. 
            return false;
        }
    }
}
