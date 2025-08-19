<?php
$host = "localhost";
$db = "sinas";
$user = "root";
$pass = "senha";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}
?>