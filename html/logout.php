<?php
session_start();
require '../connection/connection.php'; // MongoDB connection setup

use MongoDB\Client;

$client = new Client("mongodb://localhost:27017");
$collection = $client->GADGETHUB->users;

if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];

    // Update status to offline
    $collection->updateOne(
        ['_id' => new MongoDB\BSON\ObjectId($userId)],
        ['$set' => ['status' => 'offline']]
    );
}

// Destroy session and redirect to login
session_unset();
session_destroy();
header("Location: Login.php");
exit;
?>