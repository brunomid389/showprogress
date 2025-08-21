<?php
session_start();
include("conexao.php");
include("log.php"); // Função de logs

if (!isset($_SESSION['usuario'])) {
    header("Location: login.html");
    exit();
}

$usuarioId = $_SESSION['usuario']['id'];

// Atualizar dados se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $telefone = $_POST['telefone'] ?? '';
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';
    $nascimento = $_POST['nascimento'] ?? '';

    // Buscar dados antigos para log
    $stmtOld = $conn->prepare("SELECT nome, telefone, email, senha, nascimento FROM usuario WHERE id_usuario=?");
    $stmtOld->bind_param("i", $usuarioId);
    $stmtOld->execute();
    $resultOld = $stmtOld->get_result();
    $usuarioAntigo = $resultOld->fetch_assoc();
    $stmtOld->close();

    $sql = "UPDATE usuario SET nome=?, telefone=?, email=?, senha=?, nascimento=? WHERE id_usuario=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $nome, $telefone, $email, $senha, $nascimento, $usuarioId);
    $stmt->execute();
    $stmt->close();

    // Atualiza sessão
    $_SESSION['usuario']['nome'] = $nome;
    $_SESSION['usuario']['telefone'] = $telefone;
    $_SESSION['usuario']['email'] = $email;
    $_SESSION['usuario']['nascimento'] = $nascimento;

    // Registrar log
    $detalhes = "Antes: Nome={$usuarioAntigo['nome']}, Telefone={$usuarioAntigo['telefone']}, Email={$usuarioAntigo['email']}, Nascimento={$usuarioAntigo['nascimento']}";
    $detalhes .= " | Depois: Nome={$nome}, Telefone={$telefone}, Email={$email}, Nascimento={$nascimento}";
    registrarLog($usuarioId, "Alteração de perfil", $detalhes);

    header("Location: perfil.php");
    exit();
}

// Buscar dados atuais do usuário
$sql = "SELECT u.nome, u.telefone, u.email, u.senha, u.nascimento, c.uf, c.cidade, c.bairro 
        FROM usuario u 
        JOIN comunidade c ON u.id_comunidade = c.id_comunidade
        WHERE u.id_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuarioId);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Perfil</title>
  <link href="assets/css/profile.css" rel="stylesheet">
</head>
<body>

  <div class="header">
    <a href="index.php" class="back-arrow">←</a>
    Perfil
  </div>

  <div class="cover"></div>

  <div class="profile-container">
    <div class="profile-pic-container">
      <img src="https://randomuser.me/api/portraits/men/42.jpg" alt="Foto de perfil" class="profile-pic">
      <div class="edit-icon" onclick="editProfilePic()">✎</div>
    </div>

    <form action="perfil.php" method="post" id="profileForm">
      
      <div class="info-section">
        <div name="nome" class="info-label">Nome</div>
        <div class="info-value" contenteditable="false"><?= htmlspecialchars($usuario['nome']); ?></div>
        <input type="hidden" name="nome">
      </div>

      <div class="info-section">
        <div name="telefone" class="info-label">Telefone</div>
        <div class="info-value" contenteditable="false"><?= htmlspecialchars($usuario['telefone']); ?></div>
        <input type="hidden" name="telefone">
      </div>

      <div class="info-section">
        <div name="email" class="info-label">Email</div>
        <div class="info-value" contenteditable="false"><?= htmlspecialchars($usuario['email']); ?></div>
        <input type="hidden" name="email">
      </div>

      <div class="info-section">
        <div name="senha" class="info-label">Senha</div>
        <div class="info-value" contenteditable="false"><?= htmlspecialchars($usuario['senha']); ?></div>
        <input type="hidden" name="senha">
      </div>

      <div class="info-section">
        <div name="nascimento" class="info-label">Data de Nascimento</div>
        <div class="info-value" contenteditable="false"><?= htmlspecialchars($usuario['nascimento']); ?></div>
        <input type="hidden" name="nascimento">
      </div>

      <div class="info-section">
        <div name="comunidade" class="info-label">Comunidade</div>
        <div class="info-value" contenteditable="false"><?= htmlspecialchars($usuario['bairro'] . " - " . $usuario['cidade'] . "/" . $usuario['uf']); ?></div>
      </div>

      <hr style="margin:20px 0;">

      <div class="info-section">
        <div class="info-label">Notificações</div>
        <label>
          <input type="checkbox" name="notificacoes" checked disabled> Ativar notificações
        </label>
      </div>

      <div class="info-section">
        <div class="info-label">Tema</div>
        <select name="tema" class="info-value" disabled>
          <option value="claro" selected>Claro</option>
          <option value="escuro">Escuro</option>
        </select>
      </div>

      <div class="info-section">
        <div class="info-label">Idioma</div>
        <select name="idioma" class="info-value" disabled>
          <option value="pt-br" selected>Português</option>
          <option value="en">Inglês</option>
          <option value="es">Espanhol</option>
        </select>
      </div>
      
      <button class="edit-btn" type="button" onclick="toggleEdit()">Editar</button>
    </form>
  </div>

  <script src="assets/js/profile.js"></script>
</body>
</html>
