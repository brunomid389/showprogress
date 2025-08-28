<?php
include 'conexao.php'; // Arquivo de conexão com o banco
$usuario = true; // Simulando login, altere conforme seu sistema
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Página de Reels</title>
  <link rel="stylesheet" href="assets/css/general.css">
  <link rel="stylesheet" href="assets/css/reels.css">
</head>
<body>

<!-- Top Bar -->
<header>
  <div class="top-bar">
    <div class="logo">Logo</div>
    <div class="auth-buttons">
      <?php if ($usuario): ?>
        <form method="post" action="logout.php" style="display:inline;">
          <button type="submit">Sair</button>
        </form>
      <?php else: ?>
        <a href="login.html"><button>Login</button></a>
        <a href="cadastro.html"><button>Cadastro</button></a>
      <?php endif; ?>
    </div>
  </div>
</header>

<div class="container">

  <!-- Sidebar Esquerda -->
  <aside class="sidebar">
    <div class="profile-section">
      <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="Foto de perfil" class="profile-pic">
      <div class="profile-name">João Silva</div>
    </div>

    <nav class="menu">
      <a href="perfil.php"><button>Perfil</button></a>
      <a href="index.php"><button>Explorar</button></a>
      <a href="reels.php"><button>Reels</button></a>
      <a href="chat.php"><button>Chat geral</button></a>
      <a href="parente.php"><button>Controle parental</button></a>
    </nav>
  </aside>

  <!-- Área Central -->
  <main class="feed">

    <!-- Formulário para upload -->
    <form action="upload_video.php" method="POST" enctype="multipart/form-data" class="upload-form">
      <label for="video">Escolher vídeo:</label>
      <input type="file" name="video" accept="video/*" required>
      <button type="submit">Enviar Vídeo</button>
    </form>

    <!-- Lista de vídeos -->
    <div class="reels-list">
      <?php
      $query = "SELECT * FROM videos ORDER BY id DESC";
      $result = mysqli_query($conn, $query);

      while ($row = mysqli_fetch_assoc($result)) {
          echo '<div class="reel">';
          echo '<video src="' . htmlspecialchars($row['caminho']) . '" controls></video>';
          echo '</div>';
      }
      ?>
    </div>

  </main>

  <!-- Barra de Comentários -->
  <aside class="comments-bar">
    <h3>Comentários</h3>
    <div id="comments-list"></div>
    <form id="comment-form">
      <input type="text" id="comment-input" placeholder="Escreva um comentário...">
      <button type="submit">Enviar</button>
    </form>
  </aside>

</div>

<script src="assets/js/reels.js"></script>
</body>
</html>
