<?php
session_start();
$usuario = $_SESSION['usuario'] ?? null;
if (!$usuario) header("Location: login.html");

include("conexao.php");

// Buscar posts com imagem binária
$sql = "SELECT p.id_postagem, p.titulo, p.texto, p.img_url, t.nome AS tag, u.nome AS usuario, c.uf, c.cidade, c.bairro
        FROM postagem p
        INNER JOIN tag t ON p.id_tag = t.id_tag
        INNER JOIN usuario u ON p.id_usuario = u.id_usuario
        INNER JOIN comunidade c ON p.id_comunidade = c.id_comunidade
        ORDER BY p.id_postagem DESC";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Feed Base</title>
<link rel="stylesheet" href="assets/css/general.css">
<link rel="stylesheet" href="assets/css/index.css" />
</head>
<body>
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

<main>
  <aside class="sidebar">
    <div class="profile-section">
      <img src="https://randomuser.me/api/portraits/men/32.jpg" class="profile-pic">
      <div class="profile-name"><?= htmlspecialchars($usuario['nome']); ?></div>
    </div>
    <nav class="menu">
      <a href="perfil.php"><button>Perfil</button></a>
      <a href="index.php"><button>Explorar</button></a>
        <a href="reels.php"><button>Reels</button></a>
      <a href="chat.php"><button>Chat geral</button></a>
      <a href="parente.php"><button>Controle parental</button></a>
    </nav>
  </aside>

  <section class="feed-area">
    <div class="search-container">
      <input type="text" class="search" placeholder="Pesquisar..." />
    </div>
    <div class="filter-buttons">
      <button>Todos</button>
      <button>Texto</button>
      <button>Imagem</button>
      <button>Vídeo</button>
    </div>

    <div class="posts-grid" id="posts">
      <?php
      if ($result->num_rows > 0) {
          while ($post = $result->fetch_assoc()) {
              $tipo = $post['img_url'] ? 'imagem' : 'texto';
              echo '<div class="post" data-type="'.$tipo.'">';
              echo '<h3>'.htmlspecialchars($post['titulo']).'</h3>';
              echo '<p>'.htmlspecialchars($post['texto']).'</p>';
              if ($post['img_url']) {
                  $imgData = base64_encode($post['img_url']);
                  echo '<img src="data:image/jpeg;base64,'.$imgData.'" style="max-width:100%; border-radius:10px;">';
              }
              echo '<small>Por '.htmlspecialchars($post['usuario']).' | Tag: '.htmlspecialchars($post['tag']).'</small>';
              echo '</div>';
          }
      }
      $conn->close();
      ?>
    </div>

    <div class="container">
      <button id="postBtn" class="float-btn">+</button>

      <div id="postBox" class="post-box hidden">
        <textarea name="titulo" placeholder="escreva seu titulo" id="ti"></textarea>
        <textarea name="texto" placeholder="escreva seu texto aqui" id="te"></textarea>
        <textarea name="tag" placeholder="escreva suas tags aqui" id="ta"></textarea>
        <input type="file" id="imageInput" accept="image/*" style="display:none;" />
        <div class="icons">
          <i class="icon">📎</i>
          <i class="icon" id="imgIcon">🖼️</i>
          <i class="icon">🔤</i>
          <i class="icon">⚠️</i>
        </div>
        <button id="submitPost" class="submit-btn">Postar</button>
      </div>
    </div>
  </section>
</main>

<div id="overlay" style="display:none;"></div>
<script src="assets/js/script.js"></script>
</body>
</html>
