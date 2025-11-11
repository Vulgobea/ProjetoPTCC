<?php
// Pessoa.php - Classe Base para Pessoas/Usuários

require_once BASE_PATH .'/core/Database.php';

// Observação: remover 'use PDO' e 'use PDOException' (não necessários)
class Pessoa {

    protected ?int $id_pessoa = null;
    protected string $nome;
    protected string $email;
    protected string $telefone;

    public function __construct(string $nome = '', string $email = '', string $telefone = '', ?int $id_pessoa = null) {
        $this->nome = $nome;
        $this->email = $email;
        $this->telefone = $telefone;
        $this->id_pessoa = $id_pessoa;
    }

 public function setIdPessoa(?int $id) { $this->id_pessoa = $id; }
public function getIdPessoa(): ?int { return $this->id_pessoa; }

public function getNome(): string { return $this->nome; }
 public function getEmail(): string { return $this->email; }
public function getTelefone(): string { return $this->telefone; }

public function setNome(string $nome): void { $this->nome = $nome; }
 public function setEmail(string $email): void { $this->email = $email; }
 public function setTelefone(string $telefone): void { $this->telefone = $telefone; }

/**
     * Carrega dados de Pessoa por ID da tabela pessoa.
     * @param int $id_pessoa ID da pessoa na tabela 'pessoa'.
     * @return Pessoa|null
     */
public static function carregarPorIdPessoa(int $id_pessoa): ?Pessoa {

$sql = "SELECT id_pessoa, nome, email, telefone FROM pessoa WHERE id_pessoa = :id";


$pessoaData = Database::buscarUm($sql, [':id' => $id_pessoa]);

if ($pessoaData) {
 
 return new Pessoa(
 $pessoaData['nome'],
 $pessoaData['email'],
 $pessoaData['telefone'],
(int)$pessoaData['id_pessoa']
 );
}

 return null;

}
}