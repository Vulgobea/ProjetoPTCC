<!DOCTYPE html>
<html>
<head><title>Painel do Professor - Alunos</title></head>
<body>
    <h1>Meus Alunos</h1>
    <table border="1">
        <thead>
            <tr>
                <th>ID Aluno</th>
                <th>Nome</th>
                <th>Email</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($lista_de_alunos as $aluno): ?>
            <tr>
                <td><?= $aluno['id_aluno'] ?></td>
                <td><?= htmlspecialchars($aluno['nome']) ?></td>
                <td><?= htmlspecialchars($aluno['email']) ?></td>
                <td>
                    <a href="painel_professor.php?ver_aluno_id=<?= $aluno['id_aluno'] ?>">
                        Ver Progresso
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <a href="controller/logout.php">Sair</a>
</body>
</html>