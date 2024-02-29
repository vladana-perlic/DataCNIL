<?php
$servername = "localhost";
$username = "lebarbet";
$password = "dinetom";
$database = "TL_DataCNIL_Test";

// Create connection
$connexion = new mysqli($servername, $username, $password, $database);

// Check connection
if ($connexion->connect_error) {
  die("Connection failed: " . $conn->connect_error);
} 
# echo "Connected successfully";



?>