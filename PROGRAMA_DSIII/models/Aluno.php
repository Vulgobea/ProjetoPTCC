<?php
require_once '../core/Database.php';

class Aluno {
    private $id_aluno;
    private $nome;
    private $nomeUsuario;
    private $email;
    private $telefone;
    private $cpf;
    private $senha; 
    private $nivelFoco;

    public function __construct() {}

    // =================== GETTERS E SETTERS ===================
    public function setIdAluno($id) { $this->id_aluno = $id; }
    public function getIdAluno() { return $this->id_aluno; }

    public function setNome($nome) { $this->nome = $nome; }
    public function getNome() { return $this->nome; }

    public function setNomeUsuario($nomeUsuario) { $this->nomeUsuario = $nomeUsuario; }
    public function getNomeUsuario() { return $this->nomeUsuario; }

    public function setEmail($email) { $this->email = $email; }
    public function getEmail() { return $this->email; }

    public function setTelefone($telefone) { $this->telefone = $telefone; }
    public function getTelefone() { return $this->telefone; }

    public function setCpf($cpf) { $this->cpf = $cpf; }
    public function getCpf() { return $this->cpf; }

    public function setSenha($senha) { $this->senha = $senha; }
    public function getSenha() { return $this->senha; }

    public function setNivelFoco($nivelFoco) { $this->nivelFoco = $nivelFoco; }
    public function getNivelFoco() { return $this->nivelFoco; }

    // =================== VERIFICA DUPLICIDADE ===================
    public function existeUsuario(): bool {
        $db = Database::getConexao();
        $sql = "SELECT COUNT(*) 
                FROM aluno 
                WHERE email = :email OR cpf = :cpf OR nome_usuario = :nome_usuario";
        try {
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':email', $this->email, PDO::PARAM_STR);
            $stmt->bindParam(':cpf', $this->cpf, PDO::PARAM_STR);
            $stmt->bindParam(':nome_usuario', $this->nomeUsuario, PDO::PARAM_STR);
            $stmt->execute();
            $existe = $stmt->fetchColumn();
            return $existe > 0;
        } catch (PDOException $e) {
            error_log("Erro ao checar duplicidade: " . $e->getMessage());
            return false;
        }
    }

    // =================== CADASTRAR ===================
    public function cadastrar(): bool {
        $db = Database::getConexao();
        $db->beginTransaction();
        try {
            $sqlPessoa = "INSERT INTO pessoa (nome, email, telefone) VALUES (:nome, :email, :telefone)";
            $stmtPessoa = $db->prepare($sqlPessoa);
            $stmtPessoa->execute([
                ':nome' => $this->nome,
                ':email' => $this->email,
                ':telefone' => $this->telefone
            ]);
            $idPessoa = $db->lastInsertId();

            $sqlAluno = "INSERT INTO aluno (nome_usuario, email, telefone, cpf, senha_hash, fk_pessoa, nivel_foco, data_cadastro)
                         VALUES (:nome_usuario, :email, :telefone, :cpf, :senha_hash, :fk_pessoa, :nivel_foco, NOW())";
            $stmtAluno = $db->prepare($sqlAluno);
            $stmtAluno->execute([
                ':nome_usuario' => $this->nomeUsuario,
                ':email' => $this->email,
                ':telefone' => $this->telefone,
                ':cpf' => $this->cpf,
                ':senha_hash' => password_hash($this->senha, PASSWORD_DEFAULT),
                ':fk_pessoa' => $idPessoa,
                ':nivel_foco' => $this->nivelFoco
            ]);

            $db->commit();
            $this->id_aluno = $db->lastInsertId();
            return true;
        } catch (PDOException $e) {
            $db->rollBack();
            error_log("Erro no cadastro: " . $e->getMessage());
            return false;
        }
    }

    // =================== AUTENTICAR ===================
    public static function autenticar(string $login, string $senha): ?Aluno {
        $db = Database::getConexao();
        $sql = "SELECT a.id_aluno, a.nome_usuario, a.email, a.senha_hash, a.nivel_foco, p.nome, a.telefone, a.cpf
                FROM aluno a
                INNER JOIN pessoa p ON a.fk_pessoa = p.id_pessoa
                WHERE a.email = :email OR a.nome_usuario = :usuario
                LIMIT 1";

        try {
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':email', $login, PDO::PARAM_STR);
            $stmt->bindParam(':usuario', $login, PDO::PARAM_STR);
            $stmt->execute();
            $dados = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($dados && password_verify($senha, $dados['senha_hash'])) {
                $aluno = new self();
                $aluno->setIdAluno($dados['id_aluno']);
                $aluno->setNomeUsuario($dados['nome_usuario']);
                $aluno->setNome($dados['nome']);
                $aluno->setEmail($dados['email']);
                $aluno->setTelefone($dados['telefone']);
                $aluno->setCpf($dados['cpf']);
                $aluno->setNivelFoco($dados['nivel_foco']);
                $aluno->setSenha($dados['senha_hash']);
                return $aluno;
            }
            return null;
        } catch (PDOException $e) {
            error_log("Erro na autenticação: " . $e->getMessage());
            return null;
        }
    }

    // =================== CARREGAR POR ID ===================
    public static function carregarPorId($alunoId): ?Aluno {
        $db = Database::getConexao();
        $sql = "SELECT a.id_aluno, a.nome_usuario, a.email, a.telefone, a.cpf, a.nivel_foco, p.nome
                FROM aluno a
                INNER JOIN pessoa p ON a.fk_pessoa = p.id_pessoa
                WHERE a.id_aluno = :id";

        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id', $alunoId, PDO::PARAM_INT);
        $stmt->execute();
        $dados = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($dados) {
            $aluno = new self();
            $aluno->setIdAluno($dados['id_aluno']);
            $aluno->setNomeUsuario($dados['nome_usuario']);
            $aluno->setNome($dados['nome']);
            $aluno->setEmail($dados['email']);
            $aluno->setTelefone($dados['telefone']);
            $aluno->setCpf($dados['cpf']);
            $aluno->setNivelFoco($dados['nivel_foco']);
            return $aluno;
        }
        return null;
    }

    // =================== ATUALIZAR PERFIL ===================
    public function atualizarPerfil(): bool {
        $db = Database::getConexao();
        $db->beginTransaction();
        try {
            $sqlFk = "SELECT fk_pessoa FROM aluno WHERE id_aluno = :id";
            $stmtFk = $db->prepare($sqlFk);
            $stmtFk->bindParam(':id', $this->id_aluno);
            $stmtFk->execute();
            $fkPessoa = $stmtFk->fetchColumn();

            $sqlPessoa = "UPDATE pessoa SET nome = :nome, email = :email, telefone = :telefone WHERE id_pessoa = :fk";
            $stmtPessoa = $db->prepare($sqlPessoa);
            $stmtPessoa->execute([
                ':nome' => $this->nome,
                ':email' => $this->email,
                ':telefone' => $this->telefone,
                ':fk' => $fkPessoa
            ]);

            $sqlAluno = "UPDATE aluno SET nome_usuario = :nome_usuario, email = :email, telefone = :telefone, nivel_foco = :nivel_foco
                         WHERE id_aluno = :id_aluno";
            $stmtAluno = $db->prepare($sqlAluno);
            $stmtAluno->execute([
                ':nome_usuario' => $this->nomeUsuario,
                ':email' => $this->email,
                ':telefone' => $this->telefone,
                ':nivel_foco' => $this->nivelFoco,
                ':id_aluno' => $this->id_aluno
            ]);

            $db->commit();
            return true;
        } catch (PDOException $e) {
            $db->rollBack();
            error_log("Erro ao atualizar perfil: " . $e->getMessage());
            return false;
        }
    }
}
?>