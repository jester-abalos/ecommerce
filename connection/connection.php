<?php
require '../vendor/autoload.php'; // Composer autoload

$client = new MongoDB\Client("mongodb://localhost:27017");
$db = $client->GADGETHUB->users;// Ensure this is the correct collection
?>