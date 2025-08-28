<?php
session_start();
include("log.php");

if (isset($_SESSION['usuario'])) {
    $id_usuario = $_SESSION['usuario']['id'];
    registrarLog($id_usuario, "Logout", "Usuário saiu do sistema.");
}

session_destroy();
header("Location: login.html");
exit();
?>
