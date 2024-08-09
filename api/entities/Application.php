<?php

class Application
{
    public const STATUS_APPROVED = "Approved";
    public const STATUS_PENDING = "Pending";
    public const STATUS_REJECTED = "Rejected";

    public int $id;
    public int $organizer_id;
    public int $event_id;
    public string $title;
    public string $description;
    public string $email;
    public string $type;
    public string $status;
    public string $deadline_date;
    public string $image;

    // NOTE: We do not require a constructor when using PDO::FETCH_CLASS

    // NOTE: In other frameworks, I generally like creating private properties and using setter/getter methods on entities but I opted against it here to reduce boilerplate code
}
