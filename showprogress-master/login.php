<?php
session_start();
include("conexao.php");
include("log.php"); // inclui a função de logs

$email = $_POST['email'] ?? '';
$senha = $_POST['senha'] ?? '';

// Evita SQL injection usando prepared statements
$sql = "SELECT id_usuario, nome, email, senha FROM usuario WHERE email = ? AND senha = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $email, $senha);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    // Login bem-sucedido
    $user = $result->fetch_assoc();

    // Salva dados na sessão
    $_SESSION['usuario'] = [
        'id'    => $user['id_usuario'],
        'nome'  => $user['nome'],
        'email' => $user['email']
    ];

    // Registra log com ID do usuário
    registrarLog($user['id_usuario'], "Login bem-sucedido", "Email: $email");

    header("Location: index.php");
    exit();
} else {
    // Login falhou (usuário não existe ou senha incorreta)
    registrarLog(null, "Tentativa de login falhou", "Email: $email");

    echo "<script>alert('Email ou senha incorretos.'); window.location.href='login.html';</script>";
}

$stmt->close();
$conn->close();
?>
