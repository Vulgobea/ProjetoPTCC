<?php
require_once BASE_PATH .'/models/Aluno.php';
require_once BASE_PATH .'/core/Database.php';
require_once BASE_PATH .'/models/BaralhoRepository.php'; // Vamos usar isso!

class ProfessorModel {

    /**
     * Lista todos os alunos (como o seu Administrador->listaCadastrados)
     */
    public function listarAlunos() {
        $sql = "SELECT id_aluno, nome, email FROM Aluno WHERE tipo_usuario = 'aluno'";
        return Database::buscarTodos($sql);
    }

    /**
     * Busca o progresso de UM aluno específico
     * (Esta é a nova "Visualizar Cadastro")
     */
    public function getProgressoAluno(int $id_aluno) {
        // 1. Busca os dados do aluno
        $aluno = Aluno::carregarPorId($id_aluno); // Reutiliza seu Aluno.php
        if (!$aluno) {
            return null;
        }

        // 2. Busca os baralhos e estatísticas desse aluno
        //    (Aqui usamos o seu BaralhoRepository!)
        $baralhoRepo = new BaralhoRepository();
        $baralhos = $baralhoRepo->buscarComEstatisticas($id_aluno);

        return [
            'aluno' => $aluno,
            'baralhos' => $baralhos
        ];
    }
}
?>