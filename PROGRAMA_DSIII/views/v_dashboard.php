<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudyCards - Repetição Espaçada</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        /* ... (Todo o seu CSS existente) ... */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container { max-width: 1200px; margin: 0 auto; }
        .header {
            background: #1a1a2e; padding: 20px 30px; border-radius: 12px; margin-bottom: 20px;
            display: flex; justify-content: space-between; align-items: center;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        }
        .header h1 {
            color: #667eea; font-size: 1.5em; display: flex; align-items: center; gap: 10px;
        }
        .header-stats { display: flex; gap: 40px; }
        .header-stat { text-align: center; }
        .header-stat-value { font-size: 1.8em; font-weight: bold; color: #667eea; }
        .header-stat-label { font-size: 0.85em; color: #999; }
        .logout-btn {
            color: #ff6b6b; font-weight: bold; text-decoration: none;
            padding: 8px 16px; border-radius: 8px; transition: all 0.3s;
        }
        .logout-btn:hover { background: rgba(255, 107, 107, 0.1); }
        .nav-tabs { display: flex; gap: 10px; margin-bottom: 20px; flex-wrap: wrap; }
        .nav-tab {
            background: #2d2d44; color: #999; padding: 12px 24px; border-radius: 8px;
            cursor: pointer; transition: all 0.3s; border: none; font-size: 0.95em;
            font-weight: 500; display: flex; align-items: center; gap: 8px;
        }
        .nav-tab:hover { background: #3d3d54; transform: translateY(-2px); }
        .nav-tab.active { background: #667eea; color: white; }
        .content-section { display: none; }
        .content-section.active { display: block; }
        .study-area {
            background: #1a1a2e; padding: 50px 40px; border-radius: 15px;
            text-align: center; box-shadow: 0 8px 30px rgba(0,0,0,0.3);
        }
        .study-title { color: #667eea; font-size: 1.5em; margin-bottom: 30px; }
        .flip-button {
            background: #667eea; color: white; padding: 12px 30px; border: none;
            border-radius: 8px; font-size: 1em; font-weight: 600; cursor: pointer;
            margin-bottom: 20px; transition: all 0.3s;
        }
        .flip-button:hover { background: #5568d3; transform: translateY(-2px); }
        .flashcard {
            background: linear-gradient(135deg, #2d2d44 0%, #1f1f30 100%);
            padding: 80px 40px; border-radius: 12px; margin: 20px auto; max-width: 650px;
            min-height: 280px; display: flex; align-items: center; justify-content: center;
            cursor: pointer; transition: all 0.3s; position: relative;
            box-shadow: 0 8px 25px rgba(0,0,0,0.3);
        }
        .flashcard:hover { transform: scale(1.02); }
        .card-label {
            position: absolute; top: 20px; left: 25px; font-size: 0.75em;
            color: #667eea; font-weight: 700; letter-spacing: 1px;
        }
        .card-content {
            font-size: 1.4em; color: #ffffff; font-weight: 400; line-height: 1.6;
        }
        .action-buttons { display: flex; gap: 15px; justify-content: center; margin-top: 30px; }
        .btn {
            padding: 14px 50px; border: none; border-radius: 8px; font-size: 1em;
            font-weight: 600; cursor: pointer; transition: all 0.3s;
            display: flex; align-items: center; gap: 8px;
        }
        .btn:hover { transform: translateY(-2px); }
        .btn-wrong { background: #ff6b6b; color: white; }
        .btn-wrong:hover { background: #ee5a5a; }
        .btn-correct { background: #51cf66; color: white; }
        .btn-correct:hover { background: #40b555; }
        .card-info { margin-top: 25px; color: #999; font-size: 0.9em; }
        .deck-grid {
            display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 20px; margin-bottom: 20px;
        }
        .deck-card {
            background: #1a1a2e; padding: 25px; border-radius: 12px;
            transition: all 0.3s; box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            display: flex; flex-direction: column; justify-content: space-between;
        }
        .deck-card:hover {
            transform: translateY(-5px); box-shadow: 0 8px 25px rgba(0,0,0,0.3);
        }
        .deck-card-title {
            color: #667eea; margin-bottom: 15px; font-size: 1.2em;
            display: flex; align-items: center; gap: 10px;
        }
        .deck-info {
            display: flex; justify-content: space-between; color: #999;
            font-size: 0.9em; margin-bottom: 15px;
        }
        .deck-progress {
            width: 100%; height: 6px; background: #2d2d44;
            border-radius: 10px; overflow: hidden;
        }
        .deck-progress-bar {
            height: 100%; background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
            border-radius: 10px; transition: width 0.3s;
        }
        .create-deck-button {
            background: #2d2d44; color: #999; padding: 20px; border-radius: 12px;
            border: 2px dashed #3d3d54; cursor: pointer; transition: all 0.3s;
            text-align: center; font-weight: 600; display: flex; align-items: center;
            justify-content: center; gap: 10px; min-height: 150px;
        }
        .create-deck-button:hover { border-color: #667eea; color: #667eea; }
        .form-container {
            background: #1a1a2e; padding: 40px; border-radius: 15px;
            max-width: 700px; margin: 0 auto; box-shadow: 0 8px 30px rgba(0,0,0,0.3);
        }
        .form-title {
            color: #667eea; font-size: 1.5em; margin-bottom: 30px; text-align: center;
        }
        .form-group { margin-bottom: 25px; }
        .form-group label {
            display: block; margin-bottom: 10px; font-weight: 600; color: #ccc;
        }
        .form-group select, .form-group input, .form-group textarea {
            width: 100%; padding: 15px; border: 2px solid #2d2d44;
            border-radius: 8px; font-size: 1em; transition: border-color 0.3s;
            background: #2d2d44; color: #fff;
        }
        .form-group select:focus, .form-group input:focus, .form-group textarea:focus {
            outline: none; border-color: #667eea;
        }
        .form-group textarea { min-height: 120px; resize: vertical; font-family: inherit; }
        .btn-submit {
            background: #667eea; color: white; width: 100%; padding: 15px;
            display: flex; align-items: center; justify-content: center; gap: 10px;
        }
        .btn-submit:hover { background: #5568d3; }
        .stats-grid {
            display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px; margin-bottom: 30px;
        }
        .stat-card {
            background: #1a1a2e; padding: 30px; border-radius: 12px;
            text-align: center; box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        .stat-card-icon { font-size: 3em; margin-bottom: 15px; }
        .stat-card-value {
            font-size: 2.5em; font-weight: bold; color: #667eea; margin-bottom: 10px;
        }
        .stat-card-label { color: #999; font-size: 0.95em; }
        .calendar-card {
            background: #1a1a2e; padding: 30px; border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        .calendar-title {
            color: #667eea; font-size: 1.2em; margin-bottom: 20px;
            display: flex; align-items: center; gap: 10px;
        }
        .calendar-item {
            color: #999; padding: 8px 0; border-bottom: 1px solid #2d2d44;
        }
        .calendar-item:last-child { border-bottom: none; }
        .success-message {
            background: #1a1a2e; padding: 40px; border-radius: 15px;
            text-align: center; box-shadow: 0 8px 30px rgba(0,0,0,0.3);
        }
        .success-icon { font-size: 4em; margin-bottom: 20px; }
        .success-text {
            color: #51cf66; font-size: 1.5em; font-weight: 600; margin-bottom: 15px;
        }
        .success-subtext { color: #999; }
        @media (max-width: 768px) {
            .header { flex-direction: column; gap: 20px; text-align: center; }
            .header-stats { flex-direction: column; gap: 15px; }
            .nav-tabs { flex-wrap: wrap; }
            .action-buttons { flex-direction: column; }
            .deck-grid { grid-template-columns: 1fr; }
        }
        
        /* CSS para Botões de Ação do Baralho e Modal */
        .deck-actions { margin-top: 20px; display: flex; gap: 10px; }
        .btn-action {
            padding: 8px 12px; border: none; border-radius: 6px; cursor: pointer;
            transition: all 0.3s; font-size: 0.85em; font-weight: 600;
            display: flex; align-items: center; gap: 6px;
        }
        .btn-edit { background: #2d2d44; color: #999; }
        .btn-edit:hover { background: #3d3d54; color: #fff; }
        .btn-delete { background: rgba(255, 107, 107, 0.1); color: #ff6b6b; }
        .btn-delete:hover { background: #ff6b6b; color: white; }
        .modal-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.7); backdrop-filter: blur(5px);
            display: none; align-items: center; justify-content: center; z-index: 1000;
        }
        .modal-content {
            background: #1a1a2e; padding: 30px 40px; border-radius: 15px;
            max-width: 600px; width: 90%; box-shadow: 0 10px 40px rgba(0,0,0,0.5);
            position: relative;
        }
        .modal-close {
            position: absolute; top: 15px; right: 20px; font-size: 1.8em;
            color: #999; cursor: pointer; transition: all 0.3s;
        }
        .modal-close:hover { color: #fff; transform: rotate(90deg); }

        /* ======================================================== */
        /* ==== CSS para a lista de cartões (O que foi removido) ==== */
        /* ======================================================== */
        /* .deck-card-content, .deck-view-header, .btn-back, .card-list-table ... */
        /* Todo o CSS da "Parte 1" foi removido pois não vamos mais usar a "tela" interna */
        
        /* CSS ATUALIZADO para a nova tabela "Meus Cartões" */
        .card-list-table {
            width: 100%;
            border-collapse: collapse;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            border-radius: 12px;
            overflow: hidden;
            background: #1a1a2e;
            color: #ccc;
        }
        .card-list-table thead tr {
            background: #2d2d44;
            color: #fff;
            text-align: left;
            font-weight: 600;
        }
        .card-list-table th, .card-list-table td {
            padding: 15px 20px;
        }
        .card-list-table tbody tr {
            border-bottom: 1px solid #2d2d44;
        }
        .card-list-table tbody tr:last-of-type {
            border-bottom: none;
        }
        .card-list-table tbody tr:hover {
            background-color: #25253a;
        }
        .card-list-table td:first-child { /* Pergunta */
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .card-list-table td:nth-child(2) { /* Resposta */
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
         .card-list-table td:nth-child(3) { /* Baralho */
            font-style: italic;
            color: #999;
        }
        .card-list-table .btn-action {
            padding: 6px 10px;
            font-size: 0.8em;
        }
        .card-list-table .deck-actions {
            margin-top: 0;
        }

    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-brain"></i> StudyCards</h1>
            <div class="header-stats">
                <div class="header-stat">
                    <div class="header-stat-value"><?= $totalParaRevisar ?></div>
                    <div class="header-stat-label">Para Revisar</div>
                </div>
                <div class="header-stat">
                    <div class="header-stat-value"><?= $taxaAcerto ?>%</div>
                    <div class="header-stat-label">Taxa de Acerto</div>
                </div>
            </div>
            <a href="Controller/logout.php" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i> Sair
            </a>
        </div>

        <div class="nav-tabs">
            <button class="nav-tab active" data-section="study">
                <i class="fas fa-graduation-cap"></i> Estudar
            </button>
            <button class="nav-tab" data-section="decks">
                <i class="fas fa-layer-group"></i> Meus Baralhos
            </button>
            <button class="nav-tab" data-section="my-cards">
                <i class="fas fa-id-card"></i> Meus Cartões
            </button>
            <button class="nav-tab" data-section="new">
                <i class="fas fa-plus-circle"></i> Novo Cartão
            </button>
            <button class="nav-tab" data-section="stats">
                <i class="fas fa-chart-line"></i> Estatísticas
            </button>
        </div>

        <div id="study" class="content-section active">
             <div class="study-area">
                <h2 class="study-title">Sessão de Estudos</h2>
                <?php if ($cartaoAtual): ?>
                    <button class="flip-button" onclick="flipCard()">🔄 Virar Cartão</button>
                    <div class="flashcard" id="flashcard" onclick="flipCard()">
                        <div class="card-label">PERGUNTA</div>
                        <div class="card-content"><?= htmlspecialchars($cartaoAtual->pergunta) ?></div>
                    </div>
                    <div class="action-buttons">
                        <button class="btn btn-wrong" onclick="answerCard(false)">❌ Errei</button>
                        <button class="btn btn-correct" onclick="answerCard(true)">✅ Acertei</button>
                    </div>
                    <p class="card-info">Cartão 1 de <?= $baralhoAtual['totalCartoes'] ?> • Baralho: <?= htmlspecialchars($baralhoAtual['materia']) ?></p>
                <?php else: ?>
                    <div class="success-message">
                        <div class="success-icon">🎉</div>
                        <div class="success-text">Parabéns! Você revisou todos os cartões de hoje!</div>
                        <div class="success-subtext">Volte amanhã para continuar seus estudos.</div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div id="decks" class="content-section">
            <div class="deck-grid">
                <?php foreach ($baralhos as $baralho): 
                    $progresso = $baralho['totalCartoes'] > 0 
                        ? (($baralho['totalCartoes'] - $baralho['paraRevisar']) / $baralho['totalCartoes']) * 100 
                        : 0;
                ?>
                    <div class="deck-card">
                        <div>
                            <h3 class="deck-card-title">📘 <?= htmlspecialchars($baralho['materia']) ?></h3>
                            <div class="deck-info">
                                <span><?= $baralho['totalCartoes'] ?> cartões</span>
                                <span><?= $baralho['paraRevisar'] ?> para revisar</span>
                            </div>
                            <div class="deck-progress">
                                <div class="deck-progress-bar" style="width: <?= round($progresso) ?>%;"></div>
                            </div>
                        </div>
                        <div class="deck-actions">
                            <button class="btn-action btn-edit" onclick="abrirModalEditar(
                                <?= $baralho['id'] ?>,
                                '<?= htmlspecialchars($baralho['materia'], ENT_QUOTES) ?>',
                                '<?= htmlspecialchars($baralho['descricao'], ENT_QUOTES) ?>'
                            )">
                                <i class="fas fa-pencil-alt"></i> Editar
                            </button>
                            <button class="btn-action btn-delete" onclick="deletarBaralho(
                                <?= $baralho['id'] ?>,
                                '<?= htmlspecialchars($baralho['materia'], ENT_QUOTES) ?>'
                            )">
                                <i class="fas fa-trash"></i> Excluir
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>

                <div class="create-deck-button">
                    <i class="fas fa-plus"></i> Criar Novo Baralho
                </div>
            </div>
        </div>

        <div id="my-cards" class="content-section">
            <div class="form-container" style="max-width: 1000px;">
                <h2 class="form-title">Gerenciar Meus Cartões</h2>
                
                <table class="card-list-table">
                    <thead>
                        <tr>
                            <th>Pergunta</th>
                            <th>Resposta</th>
                            <th>Baralho</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody id="my-card-list-body">
                        <?php if (empty($todosOsCartoes)): ?>
                            <tr>
                                <td colspan="4" style="text-align: center; padding: 20px; color: #999;">
                                    Você ainda não criou nenhum cartão.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($todosOsCartoes as $cartao): 
                                $safePergunta = htmlspecialchars($cartao['pergunta'], ENT_QUOTES);
                                $safeResposta = htmlspecialchars($cartao['resposta'], ENT_QUOTES);
                            ?>
                            <tr>
                                <td><?= $safePergunta ?></td>
                                <td><?= $safeResposta ?></td>
                                <td><?= htmlspecialchars($cartao['nome_baralho']) ?></td>
                                <td>
                                    <div class="deck-actions">
                                        <button class="btn-action btn-edit" onclick="abrirModalEditarCartao(
                                            <?= $cartao['id'] ?>,
                                            '<?= $safePergunta ?>',
                                            '<?= $safeResposta ?>'
                                        )">
                                            <i class="fas fa-pencil-alt"></i> Editar
                                        </button>
                                        <button class="btn-action btn-delete" onclick="deletarCartao(
                                            <?= $cartao['id'] ?>,
                                            '<?= $safePergunta ?>'
                                        )">
                                            <i class="fas fa-trash"></i> Excluir
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div id="new" class="content-section">
             <div class="form-container">
                <h2 class="form-title">Adicionar Novo Cartão</h2>
                <form id="formNovoCartao" onsubmit="adicionarCartao(event)">
                    <div class="form-group">
                        <label>Selecione o Baralho</label>
                        <select name="baralho_id" required>
                            <option value="">-- Escolha um baralho --</option>
                            <?php foreach ($baralhos as $baralho): ?>
                                <option value="<?= $baralho['id'] ?>"><?= htmlspecialchars($baralho['materia']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Pergunta (Frente do Cartão)</label>
                        <textarea name="pergunta" placeholder="Ex: O que é encapsulamento?" required></textarea>
                    </div>
                    <div class="form-group">
                        <label>Resposta (Verso do Cartão)</label>
                        <textarea name="resposta" placeholder="Ex: É o princípio de esconder detalhes internos..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-submit">💾 Salvar Cartão</button>
                </form>
            </div>
        </div>
        
        <div id="new-deck" class="content-section">
             <div class="form-container">
                <h2 class="form-title">Criar Novo Baralho</h2>
                <form id="formNovoBaralho" onsubmit="adicionarBaralho(event)">
                    <div class="form-group">
                        <label>Nome do Baralho (Matéria)</label>
                        <input type="text" name="materia" placeholder="Ex: PHP Orientado a Objetos" required>
                    </div>
                    <div class="form-group">
                        <label>Descrição (Opcional)</label>
                        <textarea name="descricao" placeholder="Ex: Conceitos fundamentais de POO em PHP"></textarea>
                    </div>
                    <button type="submit" class="btn btn-submit">💾 Salvar Baralho</button>
                </form>
            </div>
        </div>

        <div id="stats" class="content-section">
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-card-icon">🎯</div>
                    <div class="stat-card-value"><?= $totalCartoes ?></div>
                    <div class="stat-card-label">Cartões Estudados</div>
                </div>
                 <div class="stat-card">
                    <div class="stat-card-icon">🔥</div>
                    <div class="stat-card-value"><?= $diasConsecutivos ?></div>
                    <div class="stat-card-label">Dias Consecutivos</div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-icon">⏱️</div>
                    <div class="stat-card-value"><?= formatarTempo($tempoEstudoHoje) ?></div>
                    <div class="stat-card-label">Tempo de Estudo Hoje</div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-icon">✅</div>
                    <div class="stat-card-value"><?= $taxaAcerto ?>%</div>
                    <div class="stat-card-label">Taxa de Acerto</div>
                </div>
            </div>
            <div class="calendar-card">
                <h3 class="calendar-title">📅 Calendário de Revisões</h3>
                <div class="calendar-item">Hoje: <?= $totalParaRevisar ?> cartões</div>
                <?php if (empty($calendario)): ?>
                    <div class="calendar-item">Nenhuma revisão futura agendada.</div>
                <?php else: ?>
                    <?php 
                    foreach ($calendario as $item): 
                        $data = new DateTime($item['data_revisao']);
                        $hoje = new DateTime(); $amanha = new DateTime('tomorrow');
                        $labelData = '';
                        if ($data->format('Y-m-d') == $amanha->format('Y-m-d')) {
                            $labelData = 'Amanhã';
                        } else {
                            $dias = $hoje->diff($data)->days;
                            $labelData = "Em $dias dias";
                        }
                    ?>
                        <div class="calendar-item"><?= $labelData ?>: <?= $item['total'] ?> cartões</div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        </div> <div id="editDeckModal" class="modal-overlay">
        <div class="modal-content">
            <span class="modal-close" onclick="fecharModalEditar()">&times;</span>
            <div class="form-container" style="box-shadow: none; padding: 10px 0 0 0;">
                <h2 class="form-title">Editar Baralho</h2>
                <form id="formEditarBaralho" onsubmit="salvarEdicaoBaralho(event)">
                    <input type="hidden" id="edit-baralho-id" name="baralho_id">
                    <div class="form-group">
                        <label>Nome do Baralho (Matéria)</label>
                        <input type="text" id="edit-materia" name="materia" required>
                    </div>
                    <div class="form-group">
                        <label>Descrição (Opcional)</label>
                        <textarea id="edit-descricao" name="descricao"></textarea>
                    </div>
                    <button type="submit" class="btn btn-submit">💾 Salvar Alterações</button>
                </form>
            </div>
        </div>
    </div>
    
    <div id="editCardModal" class="modal-overlay">
        <div class="modal-content">
            <span class="modal-close" onclick="fecharModalEditarCartao()">&times;</span>
            <div class="form-container" style="box-shadow: none; padding: 10px 0 0 0;">
                <h2 class="form-title">Editar Cartão</h2>
                
                <form id="formEditarCartao" onsubmit="salvarEdicaoCartao(event)">
                    <input type="hidden" id="edit-cartao-id" name="cartao_id">
                    
                    <div class="form-group">
                        <label>Pergunta</label>
                        <textarea id="edit-pergunta" name="pergunta" required></textarea>
                    </div>

                    <div class="form-group">
                        <label>Resposta</label>
                        <textarea id="edit-resposta" name="resposta" required></textarea>
                    </div>

                    <button type="submit" class="btn btn-submit">
                        💾 Salvar Alterações
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        let isFlipped = false;
        const cardContent = {
            front: <?= json_encode($cartaoAtual ? $cartaoAtual->pergunta : '') ?>,
            back: <?= json_encode($cartaoAtual ? $cartaoAtual->resposta : '') ?>
        };
        const cartaoId = <?= $cartaoAtual ? $cartaoAtual->id : 'null' ?>;

        // --- FUNÇÃO showSection (Simplificada) ---
        function showSection(sectionId) {
            document.querySelectorAll('.content-section').forEach(section => {
                section.classList.remove('active');
            });
            document.querySelectorAll('.nav-tab').forEach(tab => {
                tab.classList.remove('active');
            });
            
            const targetSection = document.getElementById(sectionId);
            if (targetSection) {
                targetSection.classList.add('active');
            }
            
            let targetTab;
            if (sectionId === 'new-deck') {
                targetTab = document.querySelector('.nav-tab[data-section="decks"]');
            } else {
                // A nova aba "my-cards" é encontrada aqui
                targetTab = document.querySelector(`.nav-tab[data-section="${sectionId}"]`);
            }
            
            if (targetTab) {
                targetTab.classList.add('active');
            }
        }

        // --- O "ADESIVO" DAS ABAS ---
        document.addEventListener('DOMContentLoaded', function() {
            const tabs = document.querySelectorAll('.nav-tab');
            tabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    const sectionId = this.getAttribute('data-section');
                    showSection(sectionId);
                });
            });
            const createDeckBtn = document.querySelector('.create-deck-button');
            createDeckBtn.addEventListener('click', function() {
                showSection('new-deck');
            });
        });

        // --- Funções de Estudo (flipCard, answerCard) ---
        function flipCard() {
            const card = document.getElementById('flashcard');
            const content = card.querySelector('.card-content');
            const label = card.querySelector('.card-label');
            isFlipped = !isFlipped;
            content.style.opacity = '0';
            setTimeout(() => {
                if (isFlipped) {
                    content.textContent = cardContent.back;
                    label.textContent = "RESPOSTA";
                } else {
                    content.textContent = cardContent.front;
                    label.textContent = "PERGUNTA";
                }
                content.style.opacity = '1';
            }, 200);
        }
        function answerCard(acertou) {
            if (!cartaoId) { alert('Nenhum cartão disponível!'); return; }
            const formData = new FormData();
            formData.append('action', 'responder');
            formData.append('cartao_id', cartaoId);
            formData.append('acertou', acertou);
            fetch('index.php', { method: 'POST', body: formData })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message + '\nPróxima revisão: ' + data.proximaRevisao);
                    location.reload();
                } else {
                    alert('Erro: ' + data.message);
                }
            })
            .catch(error => { console.error('Erro:', error); alert('Erro ao processar resposta'); });
        }

        // --- Funções de Adicionar (adicionarCartao, adicionarBaralho) ---
        function adicionarCartao(event) {
            event.preventDefault();
            const formData = new FormData(event.target);
            formData.append('action', 'adicionar_cartao');
            fetch('index.php', { method: 'POST', body: formData })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                if (data.success) {
                    event.target.reset();
                    setTimeout(() => location.reload(), 1000);
                }
            })
            .catch(error => { console.error('Erro:', error); alert('Erro ao adicionar cartão'); });
        }
        function adicionarBaralho(event) {
            event.preventDefault(); 
            const form = document.getElementById('formNovoBaralho');
            const formData = new FormData(form);
            formData.append('action', 'criar_baralho'); 
            fetch('index.php', { method: 'POST', body: formData })
            .then(response => response.json())
            .then(data => {
                alert(data.message); 
                if (data.success) {
                    form.reset();
                    setTimeout(() => location.reload(), 500); 
                }
            })
            .catch(error => { console.error('Erro:', error); alert('Erro ao criar baralho'); });
        }

        // --- Funções de Editar/Deletar BARALHO (Sem alteração) ---
        const deckModal = document.getElementById('editDeckModal');
        function abrirModalEditar(baralhoId, materia, descricao) {
            document.getElementById('edit-baralho-id').value = baralhoId;
            document.getElementById('edit-materia').value = materia;
            document.getElementById('edit-descricao').value = descricao;
            deckModal.style.display = 'flex';
        }
        function fecharModalEditar() {
            deckModal.style.display = 'none';
        }
        function salvarEdicaoBaralho(event) {
            event.preventDefault();
            const form = document.getElementById('formEditarBaralho');
            const formData = new FormData(form);
            formData.append('action', 'editar_baralho');
            fetch('index.php', { method: 'POST', body: formData })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                if (data.success) {
                    fecharModalEditar();
                    location.reload();
                }
            })
            .catch(error => { console.error('Erro:', error); alert('Erro ao salvar.'); });
        }
        function deletarBaralho(baralhoId, materia) {
            const confirmado = confirm(`Tem certeza que deseja excluir o baralho "${materia}"?\n\nAVISO: Você só pode excluir baralhos que NÃO tenham cartões.`);
            if (confirmado) {
                const formData = new FormData();
                formData.append('action', 'deletar_baralho');
                formData.append('baralho_id', baralhoId);
                fetch('index.php', { method: 'POST', body: formData })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    if (data.success) {
                        location.reload();
                    }
                })
                .catch(error => { console.error('Erro:', error); alert('Erro ao deletar.'); });
            }
        }
        // Fechar modal de BARALHO ao clicar fora
        window.addEventListener('click', function(event) {
            if (event.target == deckModal) {
                fecharModalEditar();
            }
        });

        // --- Timer de Tempo de Estudo ---
        setInterval(function() {
            const formData = new FormData();
            formData.append('action', 'registrar_tempo');
            formData.append('segundos', 30); 
            fetch('index.php', { method: 'POST', body: formData })
            .then(response => response.json())
            .then(data => {
                if (data.success) { console.log('Tempo de estudo salvo.'); } 
                else { console.error('Falha ao salvar tempo de estudo.'); }
            })
            .catch(error => { console.error('Erro no ping de tempo:', error); });
        }, 30000);

        // ========================================================
        // ==== 5. NOVAS FUNÇÕES JAVASCRIPT (Gerenciar Cartões) ====
        // ========================================================

        const cardModal = document.getElementById('editCardModal');

        // (A função viewDeck foi removida, não é mais necessária)
        // (A função renderCardTable foi removida, o PHP faz isso agora)

        function abrirModalEditarCartao(cartaoId, pergunta, resposta) {
            document.getElementById('edit-cartao-id').value = cartaoId;
            document.getElementById('edit-pergunta').value = pergunta;
            document.getElementById('edit-resposta').value = resposta;
            cardModal.style.display = 'flex';
        }

        function fecharModalEditarCartao() {
            cardModal.style.display = 'none';
        }

        function salvarEdicaoCartao(event) {
            event.preventDefault();
            const form = document.getElementById('formEditarCartao');
            const formData = new FormData(form);
            formData.append('action', 'editar_cartao'); // Ação que o index.php espera

            fetch('index.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                if (data.success) {
                    fecharModalEditarCartao();
                    location.reload(); // Recarrega a página inteira
                }
            })
            .catch(error => { console.error('Erro:', error); alert('Erro ao salvar.'); });
        }

        function deletarCartao(cartaoId, pergunta) {
            // Sanitiza a pergunta para o 'confirm'
            const safePergunta = document.createTextNode(pergunta).textContent;
            const confirmado = confirm(`Tem certeza que deseja excluir o cartão:\n\n"${safePergunta}"`);
            
            if (confirmado) {
                const formData = new FormData();
                formData.append('action', 'deletar_cartao'); // Ação que o index.php espera
                formData.append('cartao_id', cartaoId);

                fetch('index.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    if (data.success) {
                        location.reload(); // Recarrega a página inteira
                    }
                })
                .catch(error => { console.error('Erro:', error); alert('Erro ao deletar.'); });
            }
        }

        // Fecha o modal de CARTÃO se clicar fora
        window.addEventListener('click', function(event) {
            if (event.target == cardModal) {
                fecharModalEditarCartao();
            }
        });

    </script>
</body>
</html>