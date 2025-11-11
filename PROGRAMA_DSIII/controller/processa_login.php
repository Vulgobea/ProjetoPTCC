<?php
define('BASE_PATH', dirname(__DIR__));
session_start();
// processa_login.php - Processa a tentativa de login e inicia a sessão

ini_set('display_errors', 0);
error_reporting(0);

require_once BASE_PATH .'/models/Aluno.php';

// ADIÇÃO TEMPORÁRIA DE DEBUG: Verifica o que está sendo recebido
// Estas mensagens serão gravadas no log de erro do seu servidor (error.log ou similar).
error_log("DEBUG: Método da requisição: " . ($_SERVER['REQUEST_METHOD'] ?? 'NÃO DEFINIDO'));
error_log("DEBUG: Dados do POST: " . print_r($_POST, true)); 
// REMOVA ESTAS DUAS LINHAS APÓS RESOLVER O ERRO

// Redireciona se o acesso não foi via POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../login.php");
    exit();
}

// 1. Coleta de dados. Usamos 'email' pois corrigimos o formulário para este nome.
$login = trim($_POST['nome_usuario'] ?? '');

$senha = $_POST['senha'] ?? '';

// 2. Validação de campos obrigatórios
if (empty($login) || empty($senha)) {
    $_SESSION['erro_login'] = "E-mail/Nome de usuário e senha são obrigatórios.";
    header("Location: ../login.php");
    exit();
}

// 3. Tenta autenticar o aluno
try {
    $alunoAutenticado = Aluno::autenticar($login, $senha);

    if ($alunoAutenticado) {
        // Sucesso na autenticação: inicia a sessão
        $_SESSION['id_aluno'] = $alunoAutenticado->getIdAluno();
        $_SESSION['nome_aluno'] = $alunoAutenticado->getNome();
        
        // Redireciona para a página principal
        header("Location: ../index.php");
        exit();
        
    } else {
        // Falha na autenticação (usuário não encontrado ou senha incorreta)
        $_SESSION['erro_login'] = "E-mail/Nome de usuário ou senha incorretos.";
        header("Location: ../login.php");
        exit();
    }

} catch (Exception $e) {
    // Trata qualquer erro de sistema (como falha de conexão com o DB)
    error_log("Erro durante a tentativa de login: " . $e->getMessage());
    $_SESSION['erro_login'] = "Erro de sistema. Tente novamente mais tarde.";
    header("Location: ../login.php");
    exit();
}
?>