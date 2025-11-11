<?php
session_start();

// Remove todas as variáveis da sessão
$_SESSION = [];

// Destroi a sessão
session_destroy();

// Redireciona para a página de login
header("Location: login.php");
exit();
?>