<?php
define('BASE_PATH', __DIR__);
session_start();
require_once 'core/Database.php';
require_once 'models/ProfessorModel.php';
require_once 'models/Aluno.php';
require_once 'models/BaralhoRepository.php';

// --- Segurança ---
// Se não for professor, expulsa
if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'professor') {
    header("Location: login.php");
    exit();
}

$professorModel = new ProfessorModel();

// --- Roteamento (Decide o que mostrar) ---
// Se o professor clicou em "Ver Progresso" de um aluno
if (isset($_GET['ver_aluno_id'])) {
    
    // LÓGICA DE VISUALIZAR (Igual ao seu ADMVisualizarCadastro.php)
    $id_aluno_para_ver = (int)$_GET['ver_aluno_id'];
    $dados_progresso = $professorModel->getProgressoAluno($id_aluno_para_ver);
    
    // Chama a VIEW de "Detalhe"
    require 'views/v_professor_detalhe_aluno.php';

} else {
    
    // LÓGICA DE LISTAR (Igual ao seu ADMListarCadastrados.php)
    $lista_de_alunos = $professorModel->listarAlunos();
    
    // Chama a VIEW de "Listagem"
    require 'views/v_professor_lista_alunos.php';
}
?>