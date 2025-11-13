<?php
declare(strict_types=1);
define('BASE_PATH', __DIR__);

session_start();

function formatarTempo(int $segundos): string {
    if ($segundos < 60) { return "{$segundos}s"; }
    $minutos = floor($segundos / 60);
    if ($minutos < 60) { return "{$minutos}min"; }
    $horas = floor($minutos / 60);
    $minutosRestantes = $minutos % 60;
    if ($minutosRestantes == 0) { return "{$horas}h"; }
    return "{$horas}h {$minutosRestantes}min";
}

if (!isset($_SESSION['id_aluno'])) {
    header("Location: login.php");
    exit();
}

date_default_timezone_set('America/Sao_Paulo');

require_once BASE_PATH . '/core/Database.php';
require_once BASE_PATH .'/models/AlgoritmoSRS.php';
require_once BASE_PATH .'/models/MaterialEstudo.php';
require_once BASE_PATH .'/models/Cartao.php';
require_once BASE_PATH .'/models/CartaoRepository.php';
require_once BASE_PATH .'/models/BaralhoRepository.php';

$cartaoRepo = new CartaoRepository();
$baralhoRepo = new BaralhoRepository();
$algoritmo = new AlgoritmoSRS();
$perfilCodigo = $_SESSION['id_aluno'];

// Processar ações AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'responder':
                // ... (código 'responder' existente) ...
                try {
                    $cartaoId = (int) $_POST['cartao_id'];
                    $acertou = $_POST['acertou'] === 'true';
                    $cartao = $cartaoRepo->buscarPorId($cartaoId);
                    if ($cartao) {
                        $nivelAnterior = $cartao->getNivelAprendizagem();
                        $cartao->responder($acertou, $algoritmo);
                        $cartaoRepo->atualizar($cartaoId, $cartao, $acertou);
                        $cartaoRepo->registrarHistorico($cartaoId, $acertou, $nivelAnterior, $cartao->getNivelAprendizagem());
                        $cartaoRepo->registrarEstudoDiario($perfilCodigo, $acertou);
                        echo json_encode(['success' => true, 'message' => $acertou ? '✅ Ótimo!' : '📚 Vamos revisar!', 'proximaRevisao' => $cartao->getDataProximaRevisao()->format('d/m/Y')]);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Cartão não encontrado']);
                    }
                } catch (Exception $e) {
                    echo json_encode(['success' => false, 'message' => 'Erro: ' . $e->getMessage()]);
                }
                exit;
                
            case 'adicionar_cartao':
                // ... (código 'adicionar_cartao' existente) ...
                try {
                    $metodoEstudoId = (int) $_POST['baralho_id'];
                    $pergunta = trim($_POST['pergunta']);
                    $resposta = trim($_POST['resposta']);
                    if (!empty($pergunta) && !empty($resposta)) {
                        $novoCartao = new Cartao($pergunta, $resposta);
                        $cartaoId = $cartaoRepo->inserir($novoCartao, $metodoEstudoId);
                        echo json_encode(['success' => true, 'message' => '✅ Cartão adicionado!', 'cartaoId' => $cartaoId]);
                    } else {
                        echo json_encode(['success' => false, 'message' => '⚠️ Preencha todos os campos!']);
                    }
                } catch (Exception $e) {
                    echo json_encode(['success' => false, 'message' => 'Erro: ' . $e->getMessage()]);
                }
                exit;
                
            case 'criar_baralho':
                // ... (código 'criar_baralho' existente) ...
                try {
                    $materia = trim($_POST['materia']);
                    $descricao = trim($_POST['descricao'] ?? '');
                    if (!empty($materia)) {
                        $baralhoId = $baralhoRepo->inserir($materia, $descricao, $perfilCodigo);
                        echo json_encode(['success' => true, 'message' => '✅ Baralho criado!', 'baralhoId' => $baralhoId]);
                    } else {
                        echo json_encode(['success' => false, 'message' => '⚠️ Informe o nome!']);
                    }
                } catch (Exception $e) {
                    echo json_encode(['success' => false, 'message' => 'Erro: ' . $e->getMessage()]);
                }
                exit;

            case 'registrar_tempo':
                // ... (código 'registrar_tempo' existente) ...
                try {
                    $segundos = (int)($_POST['segundos'] ?? 0);
                    if ($segundos > 0) {
                        $cartaoRepo->adicionarTempoEstudo($perfilCodigo, $segundos);
                        echo json_encode(['success' => true]);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Segundos inválidos']);
                    }
                } catch (Exception $e) {
                    echo json_encode(['success' => false, 'message' => 'Erro: ' . $e->getMessage()]);
                }
                exit;
            
            case 'editar_baralho':
                // ... (código 'editar_baralho' existente) ...
                try {
                    $baralhoId = (int) $_POST['baralho_id'];
                    $materia = trim($_POST['materia']);
                    $descricao = trim($_POST['descricao'] ?? '');
                    if (!empty($materia) && $baralhoId > 0) {
                        $baralhoRepo->atualizar($baralhoId, $materia, $descricao);
                        echo json_encode(['success' => true, 'message' => '✅ Baralho atualizado!']);
                    } else {
                        echo json_encode(['success' => false, 'message' => '⚠️ Dados inválidos.']);
                    }
                } catch (Exception $e) {
                    echo json_encode(['success' => false, 'message' => 'Erro: ' . $e->getMessage()]);
                }
                exit;
                
            case 'deletar_baralho':
                // ... (código 'deletar_baralho' existente) ...
                try {
                    $baralhoId = (int) $_POST['baralho_id'];
                    if ($baralhoId > 0) {
                        $baralhoRepo->deletar($baralhoId);
                        echo json_encode(['success' => true, 'message' => '🗑️ Baralho excluído.']);
                    } else {
                        echo json_encode(['success' => false, 'message' => '⚠️ ID do baralho inválido.']);
                    }
                } catch (Exception $e) {
                    if ($e->getCode() == '23000') {
                         echo json_encode(['success' => false, 'message' => 'Erro! Exclua os cartões deste baralho primeiro.']);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Erro: ' . $e->getMessage()]);
                    }
                }
                exit;

            // (O 'case get_cards_for_deck' foi removido, pois não é mais necessário)

            case 'editar_cartao':
                // ... (código 'editar_cartao' existente) ...
                try {
                    $cartaoId = (int) $_POST['cartao_id'];
                    $pergunta = trim($_POST['pergunta']);
                    $resposta = trim($_POST['resposta']);
                    if ($cartaoId > 0 && !empty($pergunta) && !empty($resposta)) {
                        $cartaoRepo->atualizarCartao($cartaoId, $pergunta, $resposta);
                        echo json_encode(['success' => true, 'message' => '✅ Cartão atualizado!']);
                    } else {
                        echo json_encode(['success' => false, 'message' => '⚠️ Dados inválidos.']);
                    }
                } catch (Exception $e) {
                    echo json_encode(['success' => false, 'message' => 'Erro: ' . $e->getMessage()]);
                }
                exit;

            case 'deletar_cartao':
                // ... (código 'deletar_cartao' existente) ...
                try {
                    $cartaoId = (int) $_POST['cartao_id'];
                    if ($cartaoId > 0) {
                        $cartaoRepo->deletarCartao($cartaoId);
                        echo json_encode(['success' => true, 'message' => '🗑️ Cartão excluído.']);
                    } else {
                        echo json_encode(['success' => false, 'message' => '⚠️ ID do cartão inválido.']);
                    }
                } catch (Exception $e) {
                    echo json_encode(['success' => false, 'message' => 'Erro: ' . $e->getMessage()]);
                }
                exit;

        } // Fim do switch
    } // Fim do if(isset)
} // Fim do if(POST)

// ... (Resto do carregamento de dados)
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
$totalRevisoes = 0;
$totalAcertos = 0;
foreach ($baralhos as $baralho) {
    $stats = $cartaoRepo->buscarEstatisticas($baralho['id']);
    $totalCartoes += (int) $stats['total'];
    $totalRevisoes += (int) $stats['totalRevisoes'];
    $totalAcertos += (int) $stats['totalAcertos'];
}
if ($totalRevisoes > 0) {
    $taxaAcerto = (int) round(($totalAcertos / $totalRevisoes) * 100);
} else {
    $taxaAcerto = 0;
}
$calendario = $cartaoRepo->getCalendarioRevisoes($perfilCodigo);
$diasConsecutivos = $cartaoRepo->getDiasConsecutivos($perfilCodigo);
$statsHoje = $cartaoRepo->getEstatisticasHoje($perfilCodigo);
$tempoEstudoHoje = $statsHoje['tempoEstudo'] ?? 0;

// ===============================================
// ==== NOVA LINHA (CARREGAR TODOS OS CARTÕES) ====
// ===============================================
$todosOsCartoes = $cartaoRepo->buscarTodosPorPerfil($perfilCodigo);

require_once 'views/v_dashboard.php';
?>