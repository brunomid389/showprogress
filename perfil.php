<?php
session_start();
include("conexao.php");
include("log.php"); // Inclui a função de logs

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

    // Buscar dados antigos para log
    $stmtOld = $conn->prepare("SELECT nome, telefone, email, senha FROM usuario WHERE id_usuario=?");
    $stmtOld->bind_param("i", $usuarioId);
    $stmtOld->execute();
    $resultOld = $stmtOld->get_result();
    $usuarioAntigo = $resultOld->fetch_assoc();
    $stmtOld->close();

    $sql = "UPDATE usuario SET nome=?, telefone=?, email=?, senha=? WHERE id_usuario=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $nome, $telefone, $email, $senha, $usuarioId);
    $stmt->execute();
    $stmt->close();

    // Atualiza a sessão
    $_SESSION['usuario']['nome'] = $nome;
    $_SESSION['usuario']['telefone'] = $telefone;
    $_SESSION['usuario']['email'] = $email;

    // Registrar log com alterações
    $detalhes = "Antes: Nome={$usuarioAntigo['nome']}, Telefone={$usuarioAntigo['telefone']}, Email={$usuarioAntigo['email']}";
    $detalhes .= " | Depois: Nome={$nome}, Telefone={$telefone}, Email={$email}";
    registrarLog($usuarioId, "Alteração de perfil", $detalhes);

    header("Location: perfil.php");
    exit();
}

// Buscar dados atuais do usuário
$sql = "SELECT u.nome, u.telefone, u.email, u.senha, c.uf, c.cidade, c.bairro 
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
        <div class="info-value" contenteditable="false"><?php echo htmlspecialchars($usuario['nome']); ?></div>
        <input type="hidden" name="nome">
      </div>

      <div class="info-section">
        <div name="telefone" class="info-label">Telefone</div>
        <div class="info-value" contenteditable="false"><?php echo htmlspecialchars($usuario['telefone']); ?></div>
        <input type="hidden" name="telefone">
      </div>

      <div class="info-section">
        <div name="email" class="info-label">Email</div>
        <div class="info-value" contenteditable="false"><?php echo htmlspecialchars($usuario['email']); ?></div>
        <input type="hidden" name="email">
      </div>

      <div class="info-section">
        <div name="senha" class="info-label">Senha</div>
        <div class="info-value" contenteditable="false"><?php echo htmlspecialchars($usuario['senha']); ?></div>
        <input type="hidden" name="senha">
      </div>

      <div class="info-section">
        <div name="comunidade" class="info-label">Comunidade</div>
        <div class="info-value" contenteditable="false"><?php echo htmlspecialchars($usuario['bairro'] . " - " . $usuario['cidade'] . "/" . $usuario['uf']); ?></div>
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

  <script>
    function toggleEdit() {
      const fields = document.querySelectorAll('.info-value');
      const btn = document.querySelector('.edit-btn');

      if (btn.textContent === 'Editar') {
        fields.forEach(f => f.setAttribute('contenteditable', 'true'));
        btn.textContent = 'Salvar';
      } else {
        fields.forEach(f => f.setAttribute('contenteditable', 'false'));
        btn.textContent = 'Editar';

        // Atualiza inputs escondidos
        const form = document.getElementById('profileForm');
        form.querySelector('input[name="nome"]').value = fields[0].textContent.trim();
        form.querySelector('input[name="telefone"]').value = fields[1].textContent.trim();
        form.querySelector('input[name="email"]').value = fields[2].textContent.trim();
        form.querySelector('input[name="senha"]').value = fields[3].textContent.trim();

        form.submit(); // envia para atualizar no banco
      }
    }

    function editProfilePic() {
      const newUrl = prompt('Insira a URL da nova foto de perfil:');
      if (newUrl) {
        document.querySelector('.profile-pic').src = newUrl;
      }
    }
  </script>
</body>
</html>
