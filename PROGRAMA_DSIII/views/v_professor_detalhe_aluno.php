<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Progresso do Aluno</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
            position: relative;
        }
        body::before {
            content: ''; position: absolute; width: 150%; height: 150%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 1px, transparent 1px);
            background-size: 50px 50px; animation: moveBackground 20s linear infinite; z-index: 0;
        }
        @keyframes moveBackground { 0% { transform: translate(0, 0); } 100% { transform: translate(50px, 50px); } }

        .container { position: relative; z-index: 1; width: 100%; max-width: 900px; }
        .logo-container { text-align: center; margin-bottom: 30px; }
        .logo {
            display: inline-flex; align-items: center; gap: 12px; background: rgba(255, 255, 255, 0.95);
            padding: 15px 30px; border-radius: 20px; box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        }
        .logo i {
            font-size: 32px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
        }
        .logo h1 {
            font-size: 28px; font-weight: 800; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
        }
        .main-card {
            background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px);
            border-radius: 24px; padding: 40px; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }
        .card-header { text-align: center; margin-bottom: 30px; }
        .card-header h2 { font-size: 28px; font-weight: 700; color: #1f2937; margin-bottom: 8px; }
        .card-header p { color: #6b7280; font-size: 15px; }

        /* Estilos da Tabela */
        .styled-table {
            width: 100%; border-collapse: collapse; margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05); border-radius: 12px; overflow: hidden;
        }
        .styled-table thead tr {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff; text-align: left; font-weight: 600;
        }
        .styled-table th, .styled-table td { padding: 15px 20px; }
        .styled-table tbody tr { border-bottom: 1px solid #f3f4f6; }
        .styled-table tbody tr:nth-of-type(even) { background-color: #f9fafb; }
        .styled-table tbody tr:last-of-type { border-bottom: none; }
        
        .back-link {
            display: block; text-align: center; margin-top: 24px;
            font-size: 14px; color: #6b7280;
        }
        .back-link a { color: #667eea; font-weight: 600; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo-container">
            <div class="logo">
                <i class="fas fa-brain"></i>
                <h1>StudyCards</h1>
            </div>
        </div>

        <div class="main-card">
            <?php if ($dados_progresso): ?>
                <div class="card-header">
                    <h2>
                        <i class="fas fa-user-graduate" style="color:#667eea;"></i>
                        Progresso de: <?= htmlspecialchars($dados_progresso['aluno']->getNome()) ?>
                    </h2>
                    <p><?= htmlspecialchars($dados_progresso['aluno']->getEmail()) ?></p>
                </div>

                <h3 style="font-weight: 600; color: #1f2937; margin-bottom: 15px;">
                    Baralhos (Decks) do Aluno
                </h3>

                <table class="styled-table">
                    <thead>
                        <tr>
                            <th>Baralho (Matéria)</th>
                            <th>Total de Cartões</th>
                            <th>Cartões Para Revisar Hoje</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($dados_progresso['baralhos']) || $dados_progresso['baralhos'][0]['totalCartoes'] == 0): ?>
                             <tr>
                                <td colspan="3" style="text-align: center; color: #6b7280; padding: 20px;">
                                    Este aluno ainda não criou nenhum baralho ou cartão.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($dados_progresso['baralhos'] as $baralho): ?>
                            <tr>
                                <td><?= htmlspecialchars($baralho['materia']) ?></td>
                                <td><?= $baralho['totalCartoes'] ?></td>
                                <td><?= $baralho['paraRevisar'] ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            <?php else: ?>
                 <div class="card-header">
                    <h2><i class="fas fa-exclamation-triangle" style="color:#ef4444;"></i> Aluno não encontrado.</h2>
                </div>
            <?php endif; ?>
            
            <div class="back-link">
                <a href="painel_professor.php">
                    <i class="fas fa-arrow-left"></i> Voltar para a lista de alunos
                </a>
            </div>
        </div>
    </div>
</body>
</html>