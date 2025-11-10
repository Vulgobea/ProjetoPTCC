<?php

declare(strict_types=1);

/**
 * Responsável pela lógica de cálculo do Sistema de Repetição Espaçada (SRS).
 * Esta classe é independente e não armazena estado, apenas realiza cálculos.
 */
class AlgoritmoSRS
{
    /**
     * Calcula o próximo nível de aprendizagem e a próxima data de revisão.
     *
     * Lógica:
     * - Se o usuário ACERTA: o nível aumenta e o intervalo de dias para a próxima
     * revisão é calculado como 2 elevado ao novo nível (2, 4, 8, 16... dias).
     * - Se o usuário ERRA: o nível de aprendizagem é zerado e a revisão
     * é agendada para o dia seguinte.
     *
     * @param int $nivelAtual O nível de aprendizagem atual do item.
     * @param bool $acertou TRUE se o usuário acertou, FALSE caso contrário.
     * @return array Um array associativo contendo ['novoNivel' => int, 'novaData' => DateTime].
     */
    public function calcular(int $nivelAtual, bool $acertou): array
    {
        $novoNivel = 0;
        $novaData = new DateTime(); // Pega a data e hora de agora

        if ($acertou) {
            $novoNivel = $nivelAtual + 1;
            // pow(2, $novoNivel) calcula 2 elevado à potência do novo nível
            $intervaloEmDias = pow(2, $novoNivel);
            $novaData->modify("+{$intervaloEmDias} days");
        } else {
            $novoNivel = 0; // Reseta o progresso
            $novaData->modify('+1 day');
        }

        return [
            'novoNivel' => $novoNivel,
            'novaData' => $novaData
        ];
    }
}