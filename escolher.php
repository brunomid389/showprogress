<?php
session_start();
include("conexao.php");
include("log.php"); // para registrar no log

// Verifica se dados do cadastro existem
if (!isset($_SESSION['cadastro'])) {
    echo "<script>alert('Dados de cadastro não encontrados.'); location.href='cadastro.html';</script>";
    exit();
}

// Dados da comunidade enviados no form
$uf     = $_POST['uf'];
$cidade = $_POST['cidade'];
$bairro = $_POST['bairro'];

// Procurar comunidade no banco
$sql = "SELECT id_comunidade 
        FROM comunidade 
        WHERE uf = ? AND cidade = ? AND bairro = ?
        LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $uf, $cidade, $bairro);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $id_comunidade = $row['id_comunidade'];

    // Dados do usuário guardados na sessão
    $nome     = $_SESSION['cadastro']['nome'];
    $email    = $_SESSION['cadastro']['email'];
    $telefone = $_SESSION['cadastro']['telefone'];
    $nascimento = $_SESSION['cadastro']['nascimento'];
    $senha    = $_SESSION['cadastro']['senha'];

    // Inserir usuário
    $sqlU = "INSERT INTO usuario (nome, telefone, email, nascimento, senha, id_comunidade)
             VALUES (?, ?, ?, ?, ?, ?)";
    $stmtU = $conn->prepare($sqlU);
    $stmtU->bind_param("sssssi", $nome, $telefone, $email, $nascimento, $senha, $id_comunidade);

if ($stmtU->execute()) {
    // Pega o ID do usuário recém-criado
    $novoIdUsuario = $stmtU->insert_id;

    // Registrar log corretamente
    $acao = "Cadastro de usuário";
    $detalhes = "Usuário: $nome ($email), Comunidade: $bairro/$cidade - $uf";
    registrarLog($novoIdUsuario, $acao, $detalhes);

    // Limpar sessão
    unset($_SESSION['cadastro']);

    echo "<script>alert('Cadastro concluído com sucesso!'); location.href='login.html';</script>";
} else {
        echo "Erro ao inserir usuário: " . $stmtU->error;
    }

    $stmtU->close();

} else {
    echo "<script>alert('Comunidade não encontrada. Por favor, insira novamente.'); location.href='escolher.html';</script>";
}

$stmt->close();
$conn->close();
?>
