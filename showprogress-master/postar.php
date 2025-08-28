<?php
session_start();
include("conexao.php");

if (!isset($_SESSION['usuario'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Usuário não logado']);
    exit();
}

$usuarioId = $_SESSION['usuario']['id'];
$titulo = $_POST['titulo'] ?? '';
$texto = $_POST['texto'] ?? '';
$tagNome = $_POST['tag'] ?? 'Nacional';

// Busca ou cria tag
$stmtTag = $conn->prepare("SELECT id_tag FROM tag WHERE nome = ?");
$stmtTag->bind_param("s", $tagNome);
$stmtTag->execute();
$resultTag = $stmtTag->get_result();

if ($resultTag->num_rows > 0) {
    $id_tag = $resultTag->fetch_assoc()['id_tag'];
} else {
    $stmtInsertTag = $conn->prepare("INSERT INTO tag(nome) VALUES (?)");
    $stmtInsertTag->bind_param("s", $tagNome);
    $stmtInsertTag->execute();
    $id_tag = $stmtInsertTag->insert_id;
    $stmtInsertTag->close();
}
$stmtTag->close();

// Pegando a comunidade do usuário
$stmtCom = $conn->prepare("SELECT id_comunidade FROM usuario WHERE id_usuario = ?");
$stmtCom->bind_param("i", $usuarioId);
$stmtCom->execute();
$id_comunidade = $stmtCom->get_result()->fetch_assoc()['id_comunidade'];
$stmtCom->close();

// Inserir postagem
$stmt = $conn->prepare("INSERT INTO postagem(titulo, texto, id_tag, id_usuario, id_comunidade) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("ssiii", $titulo, $texto, $id_tag, $usuarioId, $id_comunidade);
$stmt->execute();
$stmt->close();

echo json_encode(['success' => true]);
?>
