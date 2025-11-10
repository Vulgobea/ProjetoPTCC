<?php
declare(strict_types=1);

class Database
{
    private static ?PDO $conexao = null;
    
    // AJUSTE ESTAS CONFIGURAÇÕES CONFORME SEU AMBIENTE
    private const HOST = 'localhost';
    private const DB_NAME = 'studycards_db';
    private const USER = 'teste';     // ← Seu usuário MySQL
    private const PASS = '1234';         // ← Sua senha MySQL
    private const CHARSET = 'utf8mb4';

    public static function getConexao(): PDO
    {
        if (self::$conexao === null) {
            try {
                $dsn = "mysql:host=" . self::HOST . ";dbname=" . self::DB_NAME . ";charset=" . self::CHARSET;
                
                $opcoes = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ];
                
                self::$conexao = new PDO($dsn, self::USER, self::PASS, $opcoes);
                
            } catch (PDOException $e) {
                die("Erro na conexão com o banco de dados: " . $e->getMessage());
            }
        }
        
        return self::$conexao;
    }

    public static function executar(string $sql, array $params = []): PDOStatement
    {
        $stmt = self::getConexao()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public static function buscarUm(string $sql, array $params = []): ?array
    {
        $stmt = self::executar($sql, $params);
        $resultado = $stmt->fetch();
        return $resultado ?: null;
    }

    public static function buscarTodos(string $sql, array $params = []): array
    {
        $stmt = self::executar($sql, $params);
        return $stmt->fetchAll();
    }

    public static function ultimoId(): string
    {
        return self::getConexao()->lastInsertId();
    }
}