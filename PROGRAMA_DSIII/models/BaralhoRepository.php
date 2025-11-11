<?php
declare(strict_types=1);

require_once BASE_PATH .'/core/Database.php';
require_once BASE_PATH .'/models/Cartao.php';

class BaralhoRepository
{
    public function buscarPorPerfil(int $perfilCodigo): array
    {
        $sql = "SELECT * FROM MetodosEstudo WHERE perfilCodigo = ? ORDER BY dataCriacao DESC";
        return Database::buscarTodos($sql, [$perfilCodigo]);
    }

    public function buscarPorId(int $id): ?array
    {
        $sql = "SELECT * FROM MetodosEstudo WHERE id = ?";
        return Database::buscarUm($sql, [$id]);
    }

    public function inserir(string $materia, string $descricao, int $perfilCodigo): int
    {
        $sql = "INSERT INTO MetodosEstudo (materia, descricao, perfilCodigo) VALUES (?, ?, ?)";
        Database::executar($sql, [$materia, $descricao, $perfilCodigo]);
        return (int) Database::ultimoId();
    }

    public function atualizar(int $id, string $materia, string $descricao): void
    {
        $sql = "UPDATE MetodosEstudo SET materia = ?, descricao = ? WHERE id = ?";
        Database::executar($sql, [$materia, $descricao, $id]);
    }

    public function deletar(int $id): void
    {
        $sql = "DELETE FROM MetodosEstudo WHERE id = ?";
        Database::executar($sql, [$id]);
    }

    public function buscarComEstatisticas(int $perfilCodigo): array
    {
        $sql = "SELECT 
                m.id,
                m.materia,
                m.descricao,
                m.dataCriacao,
                COUNT(c.id) as totalCartoes,
                SUM(CASE WHEN c.dataProximaRevisao <= NOW() THEN 1 ELSE 0 END) as paraRevisar,
                ROUND(AVG(c.nivelAprendizagem), 1) as nivelMedio
                FROM MetodosEstudo m
                LEFT JOIN Cards c ON m.id = c.metodoEstudoId
                WHERE m.perfilCodigo = ?
                GROUP BY m.id
                ORDER BY m.dataCriacao DESC";
        
        return Database::buscarTodos($sql, [$perfilCodigo]);
    }
}