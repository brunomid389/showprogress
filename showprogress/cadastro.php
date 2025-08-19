<?php
session_start(); // Vai salvar os dados

include("conexao.php");

$nome = $_POST['nome'];
$email = $_POST['email'];
$telefone = $_POST['telefone'];
$senha = $_POST['senha'];
$confirmSenha = $_POST['confirmSenha'];

if ($confirmSenha !== $senha) {
    echo "<script>location.href='cadastro.html';</script>";
    exit();
}

// Vai salvar os dados aqui
$_SESSION['cadastro'] = [
    'nome' => $nome,
    'email' => $email,
    'telefone' => $telefone,
    'senha' => $senha
];

header("Location: escolher.html");
exit();
?>