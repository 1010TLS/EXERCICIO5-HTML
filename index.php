<?php
// ⋆⁺₊⋆ ━━━━⊱༒︎ CONFIG/CONEXÃO ༒︎⊰━━━━ ⋆⁺₊⋆ 
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'exemplo_crud';


$con = new mysqli($host, $user, $pass, $db);
if ($con->connect_error) {
    die('Falha na conexão: ' . $con->connect_error);
}
$con->set_charset('utf8mb4');

$erro = '';

// ⋆⁺₊⋆ ━━━━⊱༒︎ Funções utilitárias ༒︎⊰━━━━ ⋆⁺₊⋆ 

function h($v) { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); }
function redirect($path = '') {
    header('Location: ' . ($_SERVER['PHP_SELF'] . $path));
    exit;
}

// ⋆⁺₊⋆ ━━━━⊱༒︎ ROTAS SIMPLES VIA ?action= ༒︎⊰━━━━ ⋆⁺₊⋆ 
$action = $_GET['action'] ?? 'list';


// ⋆⁺₊⋆ ━━━━⊱༒︎ CREATE ༒︎⊰━━━━ ⋆⁺₊⋆ 
if ($action === 'store' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');

    if ($nome === '' || $email === '' || $telefone === '') {
        $erro = 'Preencha todos os campos.';
    } else {
        $stmt = $con->prepare("INSERT INTO contatos (nome, email, telefone) VALUES (?, ?, ?)");
        $stmt->bind_param('sss', $nome, $email, $telefone);
        if (!$stmt->execute()) {
            $erro = 'Erro ao inserir: ' . $con->error;
        } else {
            redirect();
        }
    }
    $action = 'create';
}

// ⋆⁺₊⋆ ━━━━⊱༒︎ UPDATE (salvar) ༒︎⊰━━━━ ⋆⁺₊⋆ 
if ($action === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');

    if ($id <= 0 || $nome === '' || $email === '' || $telefone === '') {
        $erro = 'Dados inválidos.';
        $action = 'edit';
        $_GET['id'] = $id;
    } else {
        $stmt = $con->prepare("UPDATE contatos SET nome = ?, email = ?, telefone = ? WHERE id = ?");
        $stmt->bind_param('sssi', $nome, $email, $telefone, $id);
        if (!$stmt->execute()) {
            $erro = 'Erro ao atualizar: ' . $con->error;
            $action = 'edit';
            $_GET['id'] = $id;
        } else {
            redirect();
        }
    }
}

// ⋆⁺₊⋆ ━━━━⊱༒︎ EXCLUIR ༒︎⊰━━━━ ⋆⁺₊⋆ 
if ($action === 'delete') {
    $id = (int)($_GET['id'] ?? 0);
    if ($id > 0) {
        $stmt = $con->prepare("DELETE FROM contatos WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
    }
    redirect();
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Agenda de Contatos</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">

<?php if ($erro): ?>
    <div class="erro"><?= h($erro) ?></div>
<?php endif; ?>


<?php
// ⋆⁺₊⋆ ━━━━⊱༒︎ LISTA ༒︎⊰━━━━ ⋆⁺₊⋆ 
if ($action === 'list'):
    $result = $con->query("SELECT * FROM contatos ORDER BY nome ASC");
?>
    <h1 class="titulo">Meus Contatos</h1>

    <a class="btn btn-azul" href="?action=create">+ Novo Contato</a>

    <table>
        <tr>
            <th>Nome</th><th>Email</th><th>Telefone</th><th>Ações</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= h($row['nome']) ?></td>
            <td><?= h($row['email']) ?></td>
            <td><?= h($row['telefone']) ?></td>
            <td>
                <a class="btn btn-edit" href="?action=edit&id=<?= h($row['id']) ?>">Editar</a>
                <a class="btn btn-del confirmar-exclusao" href="?action=delete&id=<?= h($row['id']) ?>">
                    Excluir
                </a>

            </td>
        </tr>
        <?php endwhile; ?>
    </table>


<?php
// ⋆⁺₊⋆ ━━━━⊱༒︎ CREATE ༒︎⊰━━━━ ⋆⁺₊⋆ 
elseif ($action === 'create'):
?>
    <h1 class="titulo">Novo Contato</h1>

    <form method="POST" action="?action=store" class="form">
        <label>Nome
            <input type="text" name="nome" required>
        </label>

        <label>Email
            <input type="email" name="email" required>
        </label>

        <label>Telefone
            <input type="text" name="telefone" required>
        </label>

        <button class="btn btn-azul">Salvar</button>
        <a class="btn btn-cinza" href="?">Cancelar</a>
    </form>


<?php
// ⋆⁺₊⋆ ━━━━⊱༒︎ EDIT (form) ༒︎⊰━━━━ ⋆⁺₊⋆ 
elseif ($action === 'edit'):
    $id = (int)($_GET['id'] ?? 0);
    $stmt = $con->prepare("SELECT * FROM contatos WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $dados = $stmt->get_result()->fetch_assoc();

    if (!$dados):
?>
    <p>Contato não encontrado.</p>

<?php else: ?>

    <h1 class="titulo">Editar Contato</h1>

    <form method="POST" action="?action=update" class="form">
        <input type="hidden" name="id" value="<?= h($dados['id']) ?>">

        <label>Nome
            <input type="text" name="nome" required value="<?= h($dados['nome']) ?>">
        </label>

        <label>Email
            <input type="email" name="email" required value="<?= h($dados['email']) ?>">
        </label>

        <label>Telefone
            <input type="text" name="telefone" required value="<?= h($dados['telefone']) ?>">
        </label>

        <button class="btn btn-azul">Atualizar</button>
        <a class="btn btn-cinza" href="?">Cancelar</a>
    </form>

<?php endif; endif; ?>

</div>

<script src="script.js"></script>

</body>
</html>

