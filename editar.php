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
