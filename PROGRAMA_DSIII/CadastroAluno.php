<?php
// CadastroAluno.php - Interface de Cadastro MELHORADA

session_start();
ini_set('display_errors', 0);
error_reporting(0);

// Redireciona se já estiver logado
if (isset($_SESSION['id_aluno'])) {
    header("Location: index.php");
    exit();
}

$mensagemErro = $_SESSION['erro_cadastro'] ?? null;
unset($_SESSION['erro_cadastro']);

$pageTitle = "Cadastro - Curva Adaptativa";
require_once 'views/v_cadastro.php';
?>
