<?php
include("conexao.php");

/**
 * Registra uma ação no log
 * 
 * @param int|null $id_usuario ID do usuário (ou null se não existir)
 * @param string $acao Ação realizada
 * @param string|null $detalhes Detalhes adicionais
 */
function registrarLog($id_usuario, $acao, $detalhes = null): void {
    global $conn;

    $sql = "INSERT INTO log (id_usuario, acao, detalhes, data_hora) VALUES (?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);

    // Permite nulo
    if ($id_usuario === null) {
        $stmt->bind_param("iss", $id_usuario, $acao, $detalhes);
    } else {
        $stmt->bind_param("iss", $id_usuario, $acao, $detalhes);
    }

    $stmt->execute();
    $stmt->close();
}
?>
