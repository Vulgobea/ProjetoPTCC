<?php

declare(strict_types=1);

require_once 'Cartao.php';

/**
 * Representa uma coleção de Cartões.
 * Atua como um gerenciador para um tópico ou matéria de estudo.
 */
class Baralho
{
    /**
     * @var Cartao[] Um array para armazenar os objetos Cartao.
     */
    private array $cartoes = [];

    /**
     * @param string $nome O nome do baralho.
     */
    public function __construct(public readonly string $nome)
    {
    }

    /**
     * Adiciona um objeto Cartao ao baralho.
     *
     * @param Cartao $cartao O cartão a ser adicionado.
     * @return void
     */
    public function adicionarCartao(Cartao $cartao): void
    {
        $this->cartoes[] = $cartao;
    }

    /**
     * Procura e retorna o próximo cartão que está pronto para ser revisado.
     * Um cartão está pronto se sua data de próxima revisão for hoje ou já passou.
     *
     * @return Cartao|null Retorna o objeto Cartao se encontrar um, ou null se não houver nenhum para revisar.
     */
    public function obterProximoParaRevisao(): ?Cartao
    {
        $agora = new DateTime();

        foreach ($this->cartoes as $cartao) {
            // Compara as datas. Se a data de revisão do cartão for menor ou igual a agora...
            if ($cartao->getDataProximaRevisao() <= $agora) {
                return $cartao; // ...encontramos um cartão para revisar!
            }
        }

        return null; // Nenhum cartão precisa ser revisado no momento.
    }
}