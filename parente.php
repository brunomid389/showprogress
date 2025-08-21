<?php
session_start();
include("conexao.php");

if (!isset($_SESSION['usuario'])) {
    header("Location: login.html");
    exit();
}

$usuario = $_SESSION['usuario'];
$id_pai = $usuario['id'];
$mensagemErro = "";
$mensagemSucesso = "";

// Adicionar novo filho
if (isset($_POST['email_filho'])) {
    $email_filho = trim($_POST['email_filho']);

    // Verifica se o usuário existe
    $sql = "SELECT id_usuario, nome FROM usuario WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email_filho);
    $stmt->execute();
    $res = $stmt->get_result();
    $filho = $res->fetch_assoc();

    if ($filho) {
        $id_filho = $filho['id_usuario'];

        // Verifica se já existe vínculo
        $sqlCheck = "SELECT * FROM parente WHERE id_pai = ? AND id_filho = ?";
        $stmtCheck = $conn->prepare($sqlCheck);
        $stmtCheck->bind_param("ii", $id_pai, $id_filho);
        $stmtCheck->execute();
        $resCheck = $stmtCheck->get_result();

        if ($resCheck->num_rows == 0) {
            // Insere na tabela parente
            $sqlInsert = "INSERT INTO parente (id_pai, id_filho) VALUES (?, ?)";
            $stmtInsert = $conn->prepare($sqlInsert);
            $stmtInsert->bind_param("ii", $id_pai, $id_filho);
            if ($stmtInsert->execute()) {
                $mensagemSucesso = "Filho adicionado com sucesso!";
            } else {
                $mensagemErro = "Erro ao adicionar filho.";
            }
        } else {
            $mensagemErro = "Este filho já está cadastrado.";
        }
    } else {
        $mensagemErro = "Não existe nenhum usuário com esse email.";
    }
}

// Buscar filhos já cadastrados
$sqlFilhos = "SELECT u.id_usuario, u.nome, u.email 
              FROM usuario u
              INNER JOIN parente p ON u.id_usuario = p.id_filho
              WHERE p.id_pai = ?";
$stmtFilhos = $conn->prepare($sqlFilhos);
$stmtFilhos->bind_param("i", $id_pai);
$stmtFilhos->execute();
$resultFilhos = $stmtFilhos->get_result();

$filhos = [];
while ($row = $resultFilhos->fetch_assoc()) {
    $filhos[] = $row;
}

$mensagens = [];
$filhoSelecionado = null;

// Se um filho for selecionado para ver mensagens
if (isset($_POST['id_filho'])) {
    $id_filho = intval($_POST['id_filho']);
    foreach ($filhos as $f) {
        if ($f['id_usuario'] == $id_filho) {
            $filhoSelecionado = $f;
            break;
        }
    }

    if ($filhoSelecionado) {
        $sqlMsg = "SELECT texto, data_envio FROM mensagem WHERE id_usuario = ? ORDER BY data_envio DESC";
        $stmtMsg = $conn->prepare($sqlMsg);
        $stmtMsg->bind_param("i", $id_filho);
        $stmtMsg->execute();
        $resMsg = $stmtMsg->get_result();

        while ($row = $resMsg->fetch_assoc()) {
            $mensagens[] = $row;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Controle Parental</title>
</head>
<body>
<h2>Controle Parental</h2>
<p>Bem-vindo, <?php echo htmlspecialchars($usuario['nome']); ?>!</p>

<?php if ($mensagemErro) echo "<p style='color:red;'>$mensagemErro</p>"; ?>
<?php if ($mensagemSucesso) echo "<p style='color:green;'>$mensagemSucesso</p>"; ?>

<h3>Adicionar filho</h3>
<form method="post" action="parente.php">
    <input type="email" name="email_filho" placeholder="Email do filho" required>
    <button type="submit">Adicionar</button>
</form>

<h3>Filhos cadastrados</h3>
<?php if (count($filhos) > 0): ?>
<form method="post" action="parente.php">
    <select name="id_filho" required>
        <option value="">-- Selecione um filho --</option>
        <?php foreach ($filhos as $f): ?>
            <option value="<?php echo $f['id_usuario']; ?>" 
              <?php echo ($filhoSelecionado && $filhoSelecionado['id_usuario'] == $f['id_usuario']) ? "selected" : ""; ?>>
              <?php echo htmlspecialchars($f['nome'] . " (" . $f['email'] . ")"); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <button type="submit">Ver mensagens</button>
</form>
<?php else: ?>
<p>Nenhum filho cadastrado ainda.</p>
<?php endif; ?>

<?php if ($filhoSelecionado): ?>
<h3>Mensagens de <?php echo htmlspecialchars($filhoSelecionado['nome']); ?></h3>
<?php if (count($mensagens) > 0): ?>
<ul>
    <?php foreach ($mensagens as $msg): ?>
        <li><strong><?php echo date("d/m/Y H:i", strtotime($msg['data_envio'])); ?>:</strong>
            <?php echo htmlspecialchars($msg['texto']); ?></li>
    <?php endforeach; ?>
</ul>
<?php else: ?>
<p>Este filho ainda não enviou mensagens.</p>
<?php endif; ?>
<?php endif; ?>
</body>
</html>
