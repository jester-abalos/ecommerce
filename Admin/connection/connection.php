<?php
require '../vendor/autoload.php'; // Composer autoload

$client = new MongoDB\Client("mongodb://localhost:27017/");
$collection = $client->GADGETHUB->admin; 

?>
