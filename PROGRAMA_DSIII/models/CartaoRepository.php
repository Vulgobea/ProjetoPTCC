<?php
declare(strict_types=1);

require_once BASE_PATH . '/core/Database.php';
require_once BASE_PATH . '/models/Cartao.php';

class CartaoRepository
{
    // ... (todas as funções existentes, como buscarPorBaralho, buscarProximoParaRevisao, etc.) ...
    
    public function buscarPorBaralho(int $metodoEstudoId): array
    {
        $sql = "SELECT * FROM Cards WHERE metodoEstudoId = ? ORDER BY dataProximaRevisao ASC";
        return Database::buscarTodos($sql, [$metodoEstudoId]);
    }
    
    public function buscarProximoParaRevisao(int $metodoEstudoId): ?Cartao
    {
        $sql = "SELECT * FROM Cards 
                WHERE metodoEstudoId = ? 
                AND dataProximaRevisao <= NOW() 
                ORDER BY dataProximaRevisao ASC 
                LIMIT 1";
        $dados = Database::buscarUm($sql, [$metodoEstudoId]);
        if (!$dados) { return null; }
        return $this->criarCartaoDoArray($dados);
    }

    public function contarParaRevisar(int $metodoEstudoId): int
    {
        $sql = "SELECT COUNT(*) as total FROM Cards 
                WHERE metodoEstudoId = ? AND dataProximaRevisao <= NOW()";
        $resultado = Database::buscarUm($sql, [$metodoEstudoId]);
        return (int) $resultado['total'];
    }

    public function inserir(Cartao $cartao, int $metodoEstudoId): int
    {
        $sql = "INSERT INTO Cards (pergunta, resposta, metodoEstudoId, nivelAprendizagem, dataProximaRevisao) 
                VALUES (?, ?, ?, ?, ?)";
        Database::executar($sql, [
            $cartao->pergunta,
            $cartao->resposta,
            $metodoEstudoId,
            $cartao->getNivelAprendizagem(),
            $cartao->getDataProximaRevisao()->format('Y-m-d H:i:s')
        ]);
        return (int) Database::ultimoId();
    }

    public function atualizar(int $id, Cartao $cartao, bool $acertou): void
    {
        $sql = "UPDATE Cards SET 
                nivelAprendizagem = ?,
                dataProximaRevisao = ?,
                totalRevisoes = totalRevisoes + 1,
                acertos = acertos + ?,
                erros = erros + ?,
                dataUltimaRevisao = NOW()
                WHERE id = ?";
        Database::executar($sql, [
            $cartao->getNivelAprendizagem(),
            $cartao->getDataProximaRevisao()->format('Y-m-d H:i:s'),
            $acertou ? 1 : 0,
            $acertou ? 0 : 1,
            $id
        ]);
    }

    public function registrarHistorico(int $cardId, bool $acertou, int $nivelAnterior, int $nivelNovo): void
    {
        $sql = "INSERT INTO HistoricoRevisoes (cardId, acertou, nivelAnterior, nivelNovo) 
                VALUES (?, ?, ?, ?)";
        Database::executar($sql, [$cardId, $acertou, $nivelAnterior, $nivelNovo]);
    }

    public function buscarPorId(int $id): ?Cartao
    {
        $sql = "SELECT * FROM Cards WHERE id = ?";
        $dados = Database::buscarUm($sql, [$id]);
        if (!$dados) { return null; }
        return $this->criarCartaoDoArray($dados);
    }

    private function criarCartaoDoArray(array $dados): Cartao
    {
        $cartao = new Cartao($dados['pergunta'], $dados['resposta']);
        $reflection = new ReflectionClass($cartao);
        $nivelProp = $reflection->getProperty('nivelAprendizagem');
        $nivelProp->setAccessible(true);
        $nivelProp->setValue($cartao, (int) $dados['nivelAprendizagem']);
        $dataProp = $reflection->getProperty('dataProximaRevisao');
        $dataProp->setAccessible(true);
        $dataProp->setValue($cartao, new DateTime($dados['dataProximaRevisao']));
        $cartao->id = (int) $dados['id'];
        return $cartao;
    }

    public function buscarEstatisticas(int $metodoEstudoId): array
    {
        $sql = "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN dataProximaRevisao <= NOW() THEN 1 ELSE 0 END) as paraRevisar,
                SUM(totalRevisoes) as totalRevisoes,
                SUM(acertos) as totalAcertos,
                SUM(erros) as totalErros,
                ROUND(AVG(nivelAprendizagem), 2) as nivelMedio
                FROM Cards 
                WHERE metodoEstudoId = ?";
        return Database::buscarUm($sql, [$metodoEstudoId]) ?? [];
    }
    
    // ... (Funções de Estatísticas: registrarEstudoDiario, getDiasConsecutivos, etc.) ...
    public function registrarEstudoDiario(int $perfilCodigo, bool $acertou): void {
        $acertoValor = $acertou ? 1 : 0;
        $erroValor = $acertou ? 0 : 1;
        $sql = "INSERT INTO EstatisticasDiarias (perfilCodigo, data, cartoesRevisados, acertos, erros)
                VALUES (?, CURDATE(), 1, ?, ?)
                ON DUPLICATE KEY UPDATE
                cartoesRevisados = cartoesRevisados + 1,
                acertos = acertos + VALUES(acertos),
                erros = erros + VALUES(erros)";
        Database::executar($sql, [$perfilCodigo, $acertoValor, $erroValor]);
    }
    public function getCalendarioRevisoes(int $perfilCodigo, int $limite = 5): array {
        $sql = "SELECT DATE(c.dataProximaRevisao) as data_revisao, COUNT(c.id) as total
                FROM Cards c
                JOIN MetodosEstudo m ON c.metodoEstudoId = m.id
                WHERE m.perfilCodigo = ? AND c.dataProximaRevisao > CURDATE()
                GROUP BY DATE(c.dataProximaRevisao) ORDER BY data_revisao ASC LIMIT $limite";
        return Database::buscarTodos($sql, [$perfilCodigo]);
    }
    public function getDiasConsecutivos(int $perfilCodigo): int {
        $sql = "SELECT DISTINCT data FROM EstatisticasDiarias
                WHERE perfilCodigo = ? ORDER BY data DESC";
        $resultados = Database::buscarTodos($sql, [$perfilCodigo]);
        if (empty($resultados)) { return 0; }
        $diasConsecutivos = 0;
        $hoje = new DateTime('today');
        $ontem = new DateTime('yesterday');
        $dataMaisRecente = new DateTime($resultados[0]['data']);
        if ($dataMaisRecente == $hoje || $dataMaisRecente == $ontem) {
            $diasConsecutivos = 1; 
            $dataAnterior = $dataMaisRecente;
            for ($i = 1; $i < count($resultados); $i++) {
                $dataAtual = new DateTime($resultados[$i]['data']);
                $dataEsperada = (clone $dataAnterior)->modify('-1 day');
                if ($dataAtual == $dataEsperada) {
                    $diasConsecutivos++; 
                    $dataAnterior = $dataAtual;
                } else {
                    break;
                }
            }
        }
        return $diasConsecutivos;
    }
    public function adicionarTempoEstudo(int $perfilCodigo, int $segundos): void {
        $sql = "INSERT INTO EstatisticasDiarias (perfilCodigo, data, tempoEstudo)
                VALUES (?, CURDATE(), ?)
                ON DUPLICATE KEY UPDATE
                tempoEstudo = tempoEstudo + VALUES(tempoEstudo)";
        Database::executar($sql, [$perfilCodigo, $segundos]);
    }
    public function getEstatisticasHoje(int $perfilCodigo): ?array {
        $sql = "SELECT * FROM EstatisticasDiarias
                WHERE perfilCodigo = ? AND data = CURDATE()";
        return Database::buscarUm($sql, [$perfilCodigo]);
    }

    // Funções de Editar/Deletar Cartão (já existem)
    public function atualizarCartao(int $cartaoId, string $pergunta, string $resposta): void
    {
        $sql = "UPDATE Cards SET pergunta = ?, resposta = ? WHERE id = ?";
        Database::executar($sql, [$pergunta, $resposta, $cartaoId]);
    }

    public function deletarCartao(int $cartaoId): void
    {
        $sql = "DELETE FROM Cards WHERE id = ?";
        Database::executar($sql, [$cartaoId]);
    }
    
    // ===============================================
    // ==== NOVA FUNÇÃO (BUSCAR TODOS OS CARTÕES) ====
    // ===============================================

    /**
     * Busca TODOS os cartões de um usuário, juntando com o nome do baralho.
     */
    public function buscarTodosPorPerfil(int $perfilCodigo): array
    {
        $sql = "SELECT 
                    c.id, 
                    c.pergunta, 
                    c.resposta, 
                    m.materia as nome_baralho
                FROM Cards c
                JOIN MetodosEstudo m ON c.metodoEstudoId = m.id
                WHERE m.perfilCodigo = ?
                ORDER BY m.materia, c.dataCriacao DESC";
        
        return Database::buscarTodos($sql, [$perfilCodigo]);
    }

} // <- Fim da classe
?>