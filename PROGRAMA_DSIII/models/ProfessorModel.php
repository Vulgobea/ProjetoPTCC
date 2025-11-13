<?php

// Caminhos corrigidos para usar a constante BASE_PATH
require_once BASE_PATH . '/core/Database.php';
require_once BASE_PATH . '/models/BaralhoRepository.php';
require_once BASE_PATH . '/models/Aluno.php';

class ProfessorModel {

    /**
     * Lista todos os alunos (SQL CORRIGIDA)
     */
    public function listarAlunos() {
        // SQL CORRIGIDA: Agora faz o JOIN com a tabela 'pessoa' para pegar o 'p.nome'
        $sql = "SELECT a.id_aluno, p.nome, a.email 
                FROM Aluno a
                JOIN pessoa p ON a.fk_pessoa = p.id_pessoa
                WHERE a.tipo_usuario = 'aluno'";
        
        return Database::buscarTodos($sql);
    }

    /**
     * Busca o progresso de UM aluno específico
     * (Esta função já estava correta)
     */
    public function getProgressoAluno(int $id_aluno) {
        // 1. Busca os dados do aluno
        $aluno = Aluno::carregarPorId($id_aluno); // Reutiliza seu Aluno.php
        if (!$aluno) {
            return null;
        }

        // 2. Busca os baralhos e estatísticas desse aluno
        $baralhoRepo = new BaralhoRepository();
        $baralhos = $baralhoRepo->buscarComEstatisticas($id_aluno);

        return [
            'aluno' => $aluno,
            'baralhos' => $baralhos
        ];
    }
}
?>