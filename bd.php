<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "e_commerce";

// Création de la connexion bdd
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion bdd
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}
?>
