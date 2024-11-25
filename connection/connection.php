<?php
require "C:/xampp/htdocs/GADGETHUB/vendor/autoload.php";

try {
    $client = new MongoDB\Client("mongodb://localhost:27017");
    $db = $client->ecommerce;
    $collection = $db->users;
} catch (MongoDB\Driver\Exception\ConnectionException $e) {
    die("Failed to connect to database ".$e->getMessage());
}

?>


