<?php
define('BASE_PATH', dirname(__DIR__));
session_start();
error_log("POST recebido: " . print_r($_POST, true));


// ATIVAR ERROS NA TELA - Use isso para o ambiente de desenvolvimento
ini_set('display_errors', 1); // Altere para 0 em produção quando quiser suprimir
error_reporting(E_ALL);

// Dependências (mantidas)
require_once BASE_PATH .'/models/Pessoa.php'; 
require_once BASE_PATH .'/models/Aluno.php'; 

// A partir daqui o resto do seu script permanece exatamente como você enviou
// ...


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Coleta e Validação dos dados
    $nome = trim($_POST['nome'] ?? '');
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL); // Sanitização e validação de email
    $cpf = trim($_POST['cpf'] ?? '');
    $nome_Usuario = trim($_POST['nome_Usuario'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $telefone = trim($_POST['telefone'] ?? '');
    $nivelDificuldade = (int)($_POST['nivelDificuldade'] ?? 3);
    
    // Validação de campos essenciais
    if (empty($nome) || !$email || empty($cpf) || empty($nome_Usuario) || strlen($senha) < 6) {
        $_SESSION['erro_cadastro'] = "Preencha todos os campos corretamente (e-mail válido e senha de no mínimo 6 caracteres).";
        header("Location: ../CadastroAluno.php");
        exit();
    }

    try {
        // Instancia o novo Aluno e define as propriedades (chamando o construtor Pessoa via setters)
        $novoAluno = new Aluno();
        $novoAluno->setNome($nome);
        $novoAluno->setEmail($email);
        $novoAluno->setCpf($cpf);
        $novoAluno->setNomeUsuario($nome_Usuario);
        $novoAluno->setSenha($senha); // Hash da senha
        $novoAluno->setTelefone($telefone);
        $novoAluno->setNivelFoco($nivelDificuldade);
        
        // Validação de duplicidade
        if ($novoAluno->existeUsuario()) {
            throw new Exception("Duplicidade: CPF, E-mail ou Nome de Usuário já cadastrado.");
        }

        // Persistência: chama o método cadastrar que lida com Pessoa e Aluno
        if ($novoAluno->cadastrar()) {
            $_SESSION['sucesso_cadastro'] = "Cadastro realizado com sucesso! Você já pode fazer login.";
            header("Location: ../login.php");
            exit();
        } else {
            $_SESSION['erro_cadastro'] = "Falha interna ao realizar o cadastro. Tente novamente.";
        }

    } catch (Exception $e) {

        error_log("ERRO DETALHADO AO SALVAR ALUNO: " . $e->getMessage());



        $mensagemErro = $e->getMessage();
        if (strpos($mensagemErro, 'Duplicidade') !== false) {
             $_SESSION['erro_cadastro'] = $mensagemErro;
        } else {
             $_SESSION['erro_cadastro'] = "Erro inesperado ao salvar. Tente novamente.";
             error_log("Erro no cadastro: " . $e->getMessage());
        }
    }
    
    // Redireciona de volta em caso de falha
    header("Location: ../CadastroAluno.php");
    exit();
} else {
    // Acesso direto ao script sem POST
    header("Location: ../CadastroAluno.php");
    exit();
}