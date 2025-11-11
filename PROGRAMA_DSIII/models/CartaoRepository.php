<?php
declare(strict_types=1);

// Caminhos corrigidos para usar BASE_PATH (que é definido no index.php)
require_once BASE_PATH . '/core/Database.php';
require_once BASE_PATH . '/models/Cartao.php';

class CartaoRepository
{
    public function buscarPorBaralho(int $metodoEstudoId): array
    {
        $sql = "SELECT * FROM Cards WHERE metodoEstudoId = ? ORDER BY dataProximaRevisao ASC";
        $dados = Database::buscarTodos($sql, [$metodoEstudoId]);
        
        $cartoes = [];
        foreach ($dados as $linha) {
            $cartoes[] = $this->criarCartaoDoArray($linha);
        }
        
        return $cartoes;
    }

    // AQUI ESTÁ A FUNÇÃO QUE O ERRO DIZ ESTAR FALTANDO
    public function buscarProximoParaRevisao(int $metodoEstudoId): ?Cartao
    {
        $sql = "SELECT * FROM Cards 
                WHERE metodoEstudoId = ? 
                AND dataProximaRevisao <= NOW() 
                ORDER BY dataProximaRevisao ASC 
                LIMIT 1";
        
        $dados = Database::buscarUm($sql, [$metodoEstudoId]);
        
        if (!$dados) {
            return null;
        }
        
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
        
        if (!$dados) {
            return null;
        }
        
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
}