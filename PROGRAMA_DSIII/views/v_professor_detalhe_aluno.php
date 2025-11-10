<!DOCTYPE html>
<html>
<head><title>Progresso do Aluno</title></head>
<body>
    <?php if ($dados_progresso): ?>

        <h2>Progresso de: <?= htmlspecialchars($dados_progresso['aluno']->getNome()) ?></h2>
        <p>Email: <?= htmlspecialchars($dados_progresso['aluno']->getEmail()) ?></p>

        <hr>

        <h3>Baralhos (Decks) do Aluno</h3>

        <?php if (empty($dados_progresso['baralhos'])): ?>
            <p>Este aluno ainda não criou nenhum baralho.</p>
        <?php else: ?>
            <table border="1">
                <thead>
                    <tr>
                        <th>Baralho (Matéria)</th>
                        <th>Total de Cartões</th>
                        <th>Cartões Para Revisar Hoje</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($dados_progresso['baralhos'] as $baralho): ?>
                    <tr>
                        <td><?= htmlspecialchars($baralho['materia']) ?></td>
                        <td><?= $baralho['totalCartoes'] ?></td>
                        <td><?= $baralho['paraRevisar'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

    <?php else: ?>
        <h1>Aluno não encontrado.</h1>
    <?php endif; ?>

    <br>
    <a href="painel_professor.php">Voltar para a lista de alunos</a>
</body>
</html>