<?php
require 'conexao.php';

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
