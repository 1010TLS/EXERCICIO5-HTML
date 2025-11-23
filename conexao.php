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
?>
