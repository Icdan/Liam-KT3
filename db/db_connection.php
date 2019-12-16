<?php

// Database server
$server = "localhost";
// Database gebruiker
$user = "root";
// Wachtwoord voor gebruiker
$password = "";
// Database selectie
$db = "excellent_taste";

// Defineer de variabel die we later gebruiken voor queries om contact te leggen met de database
$conn = mysqli_connect($server, $user, $password, $db);

// Zodat we weten als de connectie niet goed is
if (!$conn) {
    die("Er ging iets mis met de connectie met de database");
}