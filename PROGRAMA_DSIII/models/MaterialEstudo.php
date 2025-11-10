<?php

declare(strict_types=1);

// Importa a classe AlgoritmoSRS, pois faremos referência a ela.
// Mesmo que o arquivo ainda não exista, já declaramos a dependência.
require_once 'AlgoritmoSRS.php';

/**
 * Classe base abstrata para qualquer item que precise ser revisado
 * periodicamente através do método de repetição espaçada.
 */
abstract class MaterialEstudo
{
    /**
     * @param string $pergunta O conteúdo principal do material de estudo.
     * @param int $nivelAprendizagem Nível de conhecimento do item (0 = novo, 1 = aprendeu, etc.).
     * @param DateTime $dataProximaRevisao Data para a próxima revisão do item.
     */
    public function __construct(
        public string $pergunta,
        protected int $nivelAprendizagem = 0,
        protected DateTime $dataProximaRevisao = new DateTime()
    ) {
    }

    /**
     * Método abstrato para processar a resposta do usuário.
     * A classe filha DEVE implementar este método.
     *
     * @param bool $acertou TRUE se o usuário acertou a resposta, FALSE caso contrário.
     * @param AlgoritmoSRS $algoritmo O objeto que contém a lógica de cálculo.
     * @return void
     */
    abstract public function responder(bool $acertou, AlgoritmoSRS $algoritmo): void;
    
    // Métodos para obter os valores protegidos (Getters)
    public function getNivelAprendizagem(): int
    {
        return $this->nivelAprendizagem;
    }

    public function getDataProximaRevisao(): DateTime
    {
        return $this->dataProximaRevisao;
    }
}