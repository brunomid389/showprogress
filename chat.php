<?php
session_start();
include("conexao.php"); // conexão com banco

// Recupera o id do usuário logado da sessão
$id_usuario = $_SESSION['usuario']['id'] ?? null;
$nome_usuario = $_SESSION['usuario']['nome'] ?? "Usuário";

// Se não tiver logado, bloqueia
if (!$id_usuario) {
    die("Usuário não logado. <a href='login.html'>Faça login</a>");
}

// Se o usuário enviou uma nova mensagem
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['mensagem'])) {
    $texto = trim($_POST['mensagem']);
    
    if (!empty($texto)) {
        $stmt = $conn->prepare("INSERT INTO mensagem (texto, id_usuario, id_chat) VALUES (?, ?, 1)");
        $stmt->bind_param("si", $texto, $id_usuario);
        $stmt->execute();
        $stmt->close();
    }
}

// Agora busca TODAS as mensagens do chat geral (não só do usuário logado)
$sql = "SELECT m.texto, u.nome 
        FROM mensagem m
        INNER JOIN usuario u ON m.id_usuario = u.id_usuario
        WHERE m.id_chat = 1
        ORDER BY m.id_comentario ASC";

$result = $conn->query($sql);

$mensagens = [];
while ($row = $result->fetch_assoc()) {
    $mensagens[] = $row;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Chat Geral</title>
  <link rel="stylesheet" href="assets/css/chat.css">
  <link rel="stylesheet" href="assets/css/general.css">
</head>
<body>
  <header>
    <div class="top-bar">
      <div class="logo">Logo</div>
    </div>
  </header>
  <div class="app-container">
    <!-- Sidebar -->
    <aside class="sidebar">
      <div class="profile-section">
        <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="Foto de perfil" class="profile-pic">
        <div class="profile-name"><?php echo htmlspecialchars($nome_usuario); ?></div>
      </div>

      <nav class="menu">
        <a href="perfil.php"><button>Perfil</button></a>
        <a href="index.php"><button>Explorar</button></a>
        <a href="reels.html"><button>Reels</button></a>
        <a href="chat.php"><button>Chat geral</button></a>
        <a href="projetos.html"><button>Projetos</button></a>
        <a href="rascunhos.html"><button>Seus rascunhos</button></a>
        <a href="configurações.html"> <button>Configurações</button></a>
        <a href="logout.php"><button>Sair</button></a>
      </nav>
    </aside>

    <!-- Chat Area -->
    <main class="chat-area">
      <div class="chat-messages" id="chatMessages">
        <?php if (count($mensagens) > 0): ?>
          <?php foreach ($mensagens as $msg): ?>
            <div class="chat-message">
              <strong><?php echo htmlspecialchars($msg['nome']); ?>:</strong>
              <?php echo htmlspecialchars($msg['texto']); ?>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p>Nenhuma mensagem ainda.</p>
        <?php endif; ?>
      </div>

      <div class="chat-input">
        <form method="POST" action="">
          <input type="text" name="mensagem" placeholder="Digite sua mensagem..." required>
          <button type="submit">Enviar</button>
        </form>
      </div>
    </main>
  </div>
</body>
</html>
