<?php
session_start();
include("conexao.php");

// Recebe dados do formulário
$email = $_POST['email'];
$senha = $_POST['senha'];

// Evita SQL injection (usa prepared statement)
$sql = "SELECT id_usuario, nome, email FROM usuario WHERE email = ? AND senha = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $email, $senha);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    // Usuário encontrado
    $user = $result->fetch_assoc();

    // Guarda dados na sessão
    $_SESSION['usuario'] = [
        'id'    => $user['id_usuario'],
        'nome'  => $user['nome'],
        'email' => $user['email']
    ];

    // Redireciona para a página principal
    header("Location: index.html");
    exit();
} else {
    // Login inválido
    echo "<script>alert('Email ou senha incorretos.'); window.location.href='login.html';</script>";
}

$stmt->close();
$conn->close();
?>
