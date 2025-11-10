<?php
declare(strict_types=1);

// Inicia a sessão primeiro, antes de qualquer uso de variáveis de sessão
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['id_aluno'])) {
    header("Location: login.php");
    exit();
}

// Configuração
date_default_timezone_set('America/Sao_Paulo');

// Incluir classes
require_once 'core/Database.php';
require_once 'AlgoritmoSRS.php';
require_once 'MaterialEstudo.php';
require_once 'Cartao.php';
require_once 'CartaoRepository.php';
require_once 'BaralhoRepository.php';

// Inicializar repositórios
$cartaoRepo = new CartaoRepository();
$baralhoRepo = new BaralhoRepository();
$algoritmo = new AlgoritmoSRS();

// Pegar o código do aluno logado da sessão
$perfilCodigo = $_SESSION['id_aluno'];



// Processar ações AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'responder':
                try {
                    $cartaoId = (int) $_POST['cartao_id'];
                    $acertou = $_POST['acertou'] === 'true';
                    
                    $cartao = $cartaoRepo->buscarPorId($cartaoId);
                    
                    if ($cartao) {
                        $nivelAnterior = $cartao->getNivelAprendizagem();
                        $cartao->responder($acertou, $algoritmo);
                        $cartaoRepo->atualizar($cartaoId, $cartao, $acertou);
                        $cartaoRepo->registrarHistorico($cartaoId, $acertou, $nivelAnterior, $cartao->getNivelAprendizagem());
                        
                        echo json_encode([
                            'success' => true,
                            'message' => $acertou ? '✅ Ótimo! Continue assim!' : '📚 Vamos revisar em breve!',
                            'proximaRevisao' => $cartao->getDataProximaRevisao()->format('d/m/Y'),
                            'novoNivel' => $cartao->getNivelAprendizagem()
                        ]);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Cartão não encontrado']);
                    }
                } catch (Exception $e) {
                    echo json_encode(['success' => false, 'message' => 'Erro: ' . $e->getMessage()]);
                }
                exit;
                
            case 'adicionar_cartao':
                try {
                    $metodoEstudoId = (int) $_POST['baralho_id'];
                    $pergunta = trim($_POST['pergunta']);
                    $resposta = trim($_POST['resposta']);
                    
                    if (!empty($pergunta) && !empty($resposta)) {
                        $novoCartao = new Cartao($pergunta, $resposta);
                        $cartaoId = $cartaoRepo->inserir($novoCartao, $metodoEstudoId);
                        
                        echo json_encode([
                            'success' => true,
                            'message' => '✅ Cartão adicionado com sucesso!',
                            'cartaoId' => $cartaoId
                        ]);
                    } else {
                        echo json_encode(['success' => false, 'message' => '⚠️ Preencha todos os campos!']);
                    }
                } catch (Exception $e) {
                    echo json_encode(['success' => false, 'message' => 'Erro: ' . $e->getMessage()]);
                }
                exit;
                
            case 'criar_baralho':
                try {
                    $materia = trim($_POST['materia']);
                    $descricao = trim($_POST['descricao'] ?? '');
                    
                    if (!empty($materia)) {
                        $baralhoId = $baralhoRepo->inserir($materia, $descricao, $perfilCodigo);
                        
                        echo json_encode([
                            'success' => true,
                            'message' => '✅ Baralho criado com sucesso!',
                            'baralhoId' => $baralhoId
                        ]);
                    } else {
                        echo json_encode(['success' => false, 'message' => '⚠️ Informe o nome do baralho!']);
                    }
                } catch (Exception $e) {
                    echo json_encode(['success' => false, 'message' => 'Erro: ' . $e->getMessage()]);
                }
                exit;
        }
    }
}

// Buscar dados
$baralhos = $baralhoRepo->buscarComEstatisticas($perfilCodigo);
$totalParaRevisar = 0;
foreach ($baralhos as $baralho) {
    $totalParaRevisar += (int) $baralho['paraRevisar'];
}

$cartaoAtual = null;
$baralhoAtual = null;
foreach ($baralhos as $baralho) {
    $cartao = $cartaoRepo->buscarProximoParaRevisao($baralho['id']);
    if ($cartao !== null) {
        $cartaoAtual = $cartao;
        $baralhoAtual = $baralho;
        break;
    }
}

$totalCartoes = 0;
$taxaAcerto = 0;
$totalRevisoes = 0;
$totalAcertos = 0;

foreach ($baralhos as $baralho) {
    $stats = $cartaoRepo->buscarEstatisticas($baralho['id']);
    $totalCartoes += (int) $stats['total'];
    $totalRevisoes += (int) $stats['totalRevisoes'];
    $totalAcertos += (int) $stats['totalAcertos'];
}

if ($totalRevisoes > 0) {
    $taxaAcerto = round(($totalAcertos / $totalRevisoes) * 100);
}
require_once 'views/v_dashboard.php';
?>
