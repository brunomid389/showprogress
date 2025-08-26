<?php
session_start();
include("conexao.php");


$usuario = $_SESSION['usuario'] ?? null;


if (!$usuario) {
    header("Location: login.html");
    exit();
}


// Adicionar filho
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email_filho'])) {
    $email_filho = trim($_POST['email_filho']);


    // Busca o filho pelo email + data de nascimento
    $stmt = $conn->prepare("SELECT id_usuario, nascimento FROM usuario WHERE email = ?");
    $stmt->bind_param("s", $email_filho);
    $stmt->execute();
    $result = $stmt->get_result();


    if ($result && $result->num_rows > 0) {
        $filho = $result->fetch_assoc();
        $id_filho = $filho['id_usuario'];
        $data_nasc = $filho['nascimento'];


        // Calcular idade
        $hoje = new DateTime();
        $nascimento = new DateTime($data_nasc);
        $idade = $hoje->diff($nascimento)->y;


        if ($idade >= 18) {
            $msg = "Esse usuário não pode ser adicionado como filho (tem $idade anos).";
        } else {
            // Verifica se já está cadastrado como filho
            $check = $conn->prepare("SELECT * FROM parente WHERE id_pai = ? AND id_filho = ?");
            $check->bind_param("ii", $usuario['id'], $id_filho);
            $check->execute();
            $check_result = $check->get_result();


            if ($check_result->num_rows === 0) {
                $insert = $conn->prepare("INSERT INTO parente (id_pai, id_filho) VALUES (?, ?)");
                $insert->bind_param("ii", $usuario['id'], $id_filho);
                $insert->execute();
                $msg = "Filho adicionado com sucesso!";
            } else {
                $msg = "Esse usuário já está cadastrado como seu filho.";
            }
        }
    } else {
        $msg = "Usuário com este email não encontrado.";
    }
}


// Buscar filhos cadastrados
$stmt = $conn->prepare("SELECT u.id_usuario, u.nome, u.email FROM usuario u
                        INNER JOIN parente p ON u.id_usuario = p.id_filho
                        WHERE p.id_pai = ?");
$stmt->bind_param("i", $usuario['id']);
$stmt->execute();
$filhos = $stmt->get_result();


// Buscar mensagens do filho selecionado
$mensagens = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_filho'])) {
    $id_filho = intval($_POST['id_filho']);


    $stmt = $conn->prepare("SELECT texto, data_envio FROM mensagem WHERE id_usuario = ? ORDER BY data_envio DESC");
    $stmt->bind_param("i", $id_filho);
    $stmt->execute();
    $mensagens = $stmt->get_result();
}


?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Controle Parental</title>
  <link rel="stylesheet" href="style.css">


  <link rel="stylesheet" href="assets/css/parente.css">
 <link rel="stylesheet" href="assets/css/general.css">
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


  <div class="container">
    <!-- Sidebar -->
    <aside class="sidebar">
      <div class="profile">
 <img src="https://randomuser.me/api/portraits/men/32.jpg" class="profile-pic">
      <div class="profile-name"><?= htmlspecialchars($usuario['nome']); ?></div>
      </div>
    <nav class="menu">
      <a href="perfil.php"><button>Perfil</button></a>
      <a href="index.php"><button>Explorar</button></a>
      <a href="reels.html"><button>Reels</button></a>
      <a href="chat.php"><button>Chat geral</button></a>
      <a href="projetos.html"><button>Projetos</button></a>
      <a href="configurações.html"><button>Configurações</button></a>
      <a href="parente.php"><button>Controle parental</button></a>
    </nav>
    </aside>


    <!-- Conteúdo principal -->
    <main class="content">
      <div class="parental-container">
        <h2>Controle Parental</h2>
        <p>Adicione e visualize as mensagens enviadas por seus filhos.</p>


        <?php if (isset($msg)) echo "<p><strong>$msg</strong></p>"; ?>


        <!-- Formulário para adicionar filho -->
        <form method="POST" action="">
          <label for="email_filho">Adicionar filho pelo email:</label><br>
          <input type="email" name="email_filho" required>
          <button type="submit">Adicionar</button>
        </form>


        <!-- Selecionar filho -->
        <form method="POST" action="" style="margin-top: 20px;">
          <label for="id_filho">Escolha o filho:</label>
          <select name="id_filho" required>
            <option value="">-- Selecione --</option>
            <?php while ($f = $filhos->fetch_assoc()): ?>
              <option value="<?= $f['id_usuario'] ?>"><?= htmlspecialchars($f['nome']) ?> (<?= htmlspecialchars($f['email']) ?>)</option>
            <?php endwhile; ?>
          </select>
          <button type="submit">Ver mensagens</button>
        </form>


        <!-- Mensagens -->
        <div class="mensagens">
          <?php if (!empty($mensagens) && $mensagens->num_rows > 0): ?>
            <h3>Mensagens do filho selecionado:</h3>
            <?php while ($m = $mensagens->fetch_assoc()): ?>
              <div class="mensagem">
                <p><?= htmlspecialchars($m['texto']) ?></p>
                <small><?= htmlspecialchars($m['data_envio']) ?></small>
              </div>
            <?php endwhile; ?>
          <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_filho'])): ?>
            <p>Nenhuma mensagem encontrada para este filho.</p>
          <?php endif; ?>
        </div>
      </div>
    </main>
  </div>
</body>
</html>