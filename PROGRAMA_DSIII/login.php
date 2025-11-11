<?php

declare(strict_types=1);
define('BASE_PATH', __DIR__);



// =====================================================================
// CONFIGURAÇÕES INICIAIS
// =====================================================================
session_start();

// Oculta erros em produção
ini_set('display_errors', '0');
error_reporting(0);

// =====================================================================
// REDIRECIONAMENTO SE O USUÁRIO JÁ ESTIVER LOGADO
// =====================================================================
if (isset($_SESSION['id_aluno'])) {
    header("Location: index.php");
    exit();
}

// =====================================================================
// MENSAGENS DE FEEDBACK (SUCESSO OU ERRO)
// =====================================================================
$mensagemSucessoCadastro = $_SESSION['sucesso_cadastro'] ?? null;
unset($_SESSION['sucesso_cadastro']);

$mensagemErroLogin = $_SESSION['erro_login'] ?? null;
unset($_SESSION['erro_login']);

// =====================================================================
// VARIÁVEIS DE CABEÇALHO
// =====================================================================
$pageTitle = "Login - Curva Adaptativa";
require_once 'views/v_login.php';
?>

