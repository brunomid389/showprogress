<?php
// upload_video.php

include 'conexao.php'; // conexão com o banco (mysqli ou PDO)

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['video'])) {
    $video = $_FILES['video'];

    if ($video['error'] === UPLOAD_ERR_OK) {
        $nomeTemp = $video['tmp_name'];
        $nomeFinal = uniqid() . "_" . basename($video['name']);
        $caminho = 'uploads/' . $nomeFinal;

        if (!is_dir('uploads')) {
            mkdir('uploads', 0777, true); // cria a pasta se não existir
        }

        if (move_uploaded_file($nomeTemp, $caminho)) {
            // Salvar no banco
            $stmt = $conn->prepare("INSERT INTO videos (caminho) VALUES (?)");
            $stmt->bind_param("s", $caminho);
            $stmt->execute();

            header("Location: reels.php");
            exit();
        } else {
            echo "Erro ao mover o arquivo.";
        }
    } else {
        echo "Erro no upload do vídeo.";
    }
}
?>
