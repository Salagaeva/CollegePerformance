<?php
$host = "localhost";
$user = "root";          
$password = '$iblis81$'; 
$dbname = "test";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

$conn->set_charset("utf8");
?>
