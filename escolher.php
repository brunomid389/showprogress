<?php
session_start();
include("conexao.php");

// Ensure we have the data from step 1
if (!isset($_SESSION['cadastro'])) {
    echo "<script>alert('Dados de cadastro não encontrados.'); location.href='cadastro.html';</script>";
    exit();
}

// Get community data from form
$uf     = $_POST['uf'];
$cidade = $_POST['cidade'];
$bairro = $_POST['bairro'];

// Look up the community ID
$sql = "SELECT id_comunidade 
        FROM comunidade 
        WHERE uf = '$uf' AND cidade = '$cidade' AND bairro = '$bairro'
        LIMIT 1";

$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    // Community exists
    $row = mysqli_fetch_assoc($result);
    $id_comunidade = $row['id_comunidade'];

    // Get user data from session
    $nome     = $_SESSION['cadastro']['nome'];
    $email    = $_SESSION['cadastro']['email'];
    $telefone = $_SESSION['cadastro']['telefone'];
    $senha    = $_SESSION['cadastro']['senha'];

    // Insert user with matching community ID
    $sqlU = "INSERT INTO usuario (nome, telefone, email, senha, id_comunidade)
             VALUES ('$nome', '$telefone', '$email', '$senha', '$id_comunidade')";

    if (mysqli_query($conn, $sqlU)) {
        unset($_SESSION['cadastro']); // Clear saved session data
        echo "<script>alert('Cadastro concluído com sucesso!'); location.href='login.php';</script>";
    } else {
        echo "Erro ao inserir usuário: " . mysqli_error($conn);
    }

} else {
    // Community not found
    echo "<script>alert('Comunidade não encontrada. Por favor, insira novamente.'); location.href='escolher.html';</script>";
}

mysqli_close($conn);
?>