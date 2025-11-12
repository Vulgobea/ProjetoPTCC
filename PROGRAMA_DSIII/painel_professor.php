<?php
// Define o caminho absoluto (Igual ao index.php)
declare(strict_types=1);
define('BASE_PATH', __DIR__); 

session_start();

// Corrige os caminhos de 'require'
require_once BASE_PATH . '/core/Database.php';
require_once BASE_PATH . '/models/ProfessorModel.php';
require_once BASE_PATH . '/models/Aluno.php';
require_once BASE_PATH . '/models/BaralhoRepository.php';

// --- Segurança ---
// (Esta verificação já existia e está correta)
if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'professor') {
    header("Location: login.php");
    exit();
}

$professorModel = new ProfessorModel();

// --- Roteamento (Decide o que mostrar) ---
if (isset($_GET['ver_aluno_id'])) {

    $id_aluno_para_ver = (int)$_GET['ver_aluno_id'];
    $dados_progresso = $professorModel->getProgressoAluno($id_aluno_para_ver);

    // Chama a VIEW de "Detalhe" (Corrigido o caminho)
    require BASE_PATH . '/views/v_professor_detalhe_aluno.php';

} else {

    $lista_de_alunos = $professorModel->listarAlunos();

    // Chama a VIEW de "Listagem" (Corrigido o caminho)
    require BASE_PATH . '/views/v_professor_lista_alunos.php';
}
?>