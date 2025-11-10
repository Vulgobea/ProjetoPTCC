<?php
declare(strict_types=1);

require_once 'MaterialEstudo.php';

/**
 * Representa um "flashcard" com frente (pergunta) e verso (resposta).
 * É uma implementação concreta da classe abstrata MaterialEstudo.
 */
class Cartao extends MaterialEstudo
{
    public int $id = 0; // ID do banco de dados
    
    /**
     * O construtor do Cartao.
     *
     * @param string $pergunta O conteúdo da frente do cartão.
     * @param string $resposta O conteúdo do verso do cartão.
     * @param int $nivelAprendizagem Nível inicial de aprendizagem
     * @param DateTime|null $dataProximaRevisao Data da próxima revisão
     */
    public function __construct(
        string $pergunta,
        public readonly string $resposta,
        int $nivelAprendizagem = 0,
        ?DateTime $dataProximaRevisao = null
    ) {
        // Chama o construtor da classe PAI (MaterialEstudo) para
        // inicializar as propriedades herdadas
        parent::__construct(
            $pergunta, 
            $nivelAprendizagem, 
            $dataProximaRevisao ?? new DateTime()
        );
    }
    
    /**
     * Processa a resposta do usuário para este cartão.
     * Este método implementa o método abstrato da classe pai.
     *
     * @param bool $acertou TRUE se o usuário acertou, FALSE caso contrário.
     * @param AlgoritmoSRS $algoritmo O objeto que fará o cálculo.
     * @return void
     */
    public function responder(bool $acertou, AlgoritmoSRS $algoritmo): void
    {
        // 1. Delega o cálculo pesado para o algoritmo.
        $resultado = $algoritmo->calcular($this->nivelAprendizagem, $acertou);
        
        // 2. Atualiza suas próprias propriedades com o resultado do cálculo.
        $this->nivelAprendizagem = $resultado['novoNivel'];
        $this->dataProximaRevisao = $resultado['novaData'];
    }
    
    /**
     * Define o nível de aprendizagem (usado ao carregar do banco)
     */
    public function setNivelAprendizagem(int $nivel): void
    {
        $this->nivelAprendizagem = $nivel;
    }
    
    /**
     * Define a data da próxima revisão (usado ao carregar do banco)
     */
    public function setDataProximaRevisao(DateTime $data): void
    {
        $this->dataProximaRevisao = $data;
    }
}