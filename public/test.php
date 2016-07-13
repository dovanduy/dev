<?php
$servername = "localhost";
$username = "vuong761_balo";
$password = "balo@2016";

try {
    $conn = new PDO("mysql:host=$servername;dbname=vuong761_ken", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected successfully"; 
    }
catch(PDOException $e)
    {
    echo "Connection failed: " . $e->getMessage();
    }
?>