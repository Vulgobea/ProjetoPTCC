<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
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
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: absolute;
            width: 150%;
            height: 150%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 1px, transparent 1px);
            background-size: 50px 50px;
            animation: moveBackground 20s linear infinite;
            z-index: 0;
        }

        @keyframes moveBackground {
            0% { transform: translate(0, 0); }
            100% { transform: translate(50px, 50px); }
        }

        .container { position: relative; z-index: 1; width: 100%; max-width: 900px; }

        .logo-container { text-align: center; margin-bottom: 30px; animation: fadeInDown 0.8s ease-out; }

        .logo {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            background: rgba(255, 255, 255, 0.95);
            padding: 15px 30px;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        }

        .logo i {
            font-size: 32px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .logo h1 {
            font-size: 28px;
            font-weight: 800;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .cadastro-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: fadeInUp 0.8s ease-out;
        }

        @keyframes fadeInDown { from {opacity:0;transform:translateY(-30px);} to {opacity:1;transform:translateY(0);} }
        @keyframes fadeInUp { from {opacity:0;transform:translateY(30px);} to {opacity:1;transform:translateY(0);} }

        .card-header { text-align: center; margin-bottom: 30px; }
        .card-header h2 { font-size: 28px; font-weight: 700; color: #1f2937; margin-bottom: 8px; }
        .card-header p { color: #6b7280; font-size: 15px; }

        .progress-bar {
            display: flex; justify-content: space-between; margin-bottom: 30px; position: relative;
        }
        .progress-bar::before {
            content: ''; position: absolute; top: 20px; left: 0; right: 0; height: 2px;
            background: #e5e7eb; z-index: 0;
        }
        .progress-step { display: flex; flex-direction: column; align-items: center; gap: 8px; position: relative; z-index: 1; }
        .step-circle {
            width: 40px; height: 40px; border-radius: 50%; background: white;
            border: 3px solid #e5e7eb; display: flex; align-items: center; justify-content: center;
            font-weight: 600; color: #9ca3af; transition: all 0.3s;
        }
        .progress-step.active .step-circle {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-color: #667eea; color: white;
        }
        .step-label { font-size: 12px; color: #9ca3af; font-weight: 500; }
        .progress-step.active .step-label { color: #667eea; font-weight: 600; }

        .alert {
            padding: 14px 18px; border-radius: 12px; margin-bottom: 24px; display: flex; align-items: center;
            gap: 12px; font-size: 14px; background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5;
        }
        .alert i { font-size: 18px; }

        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
        .form-group { margin-bottom: 20px; }
        .form-group.full-width { grid-column: 1 / -1; }
        .form-label { display: block; font-weight: 600; color: #374151; margin-bottom: 8px; font-size: 14px; }
        .form-label .required { color: #ef4444; }
        .input-wrapper { position: relative; }
        .input-icon {
            position: absolute; left: 16px; top: 50%; transform: translateY(-50%);
            color: #9ca3af; font-size: 16px; pointer-events: none;
        }
        .form-control {
            width: 100%; padding: 13px 16px 13px 44px; border: 2px solid #e5e7eb;
            border-radius: 12px; font-size: 14px; background: white;
        }
        .form-control:focus {
            outline: none; border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        .form-help { font-size: 12px; color: #6b7280; margin-top: 6px; display: flex; align-items: center; gap: 6px; }
        .form-help i { font-size: 14px; }

        .nivel-selector { display: flex; gap: 12px; margin-top: 8px; }
        .nivel-option {
            flex: 1; padding: 12px; border: 2px solid #e5e7eb; border-radius: 10px;
            text-align: center; cursor: pointer; transition: all 0.3s; background: white;
        }
        .nivel-option.selected {
            border-color: #667eea;
            background: linear-gradient(135deg, rgba(102,126,234,0.1), rgba(118,75,162,0.1));
        }

        .nivel-number { font-size: 24px; font-weight: 700; color: #667eea; margin-bottom: 4px; }
        .nivel-label { font-size: 12px; color: #6b7280; font-weight: 500; }

        .btn-cadastrar {
            width: 100%; padding: 16px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white; border: none; border-radius: 12px;
            font-size: 16px; font-weight: 600; cursor: pointer;
            display: flex; align-items: center; justify-content: center; gap: 8px; margin-top: 24px;
        }

        .login-link { text-align: center; margin-top: 24px; font-size: 14px; color: #6b7280; }
        .login-link a { color: #667eea; font-weight: 600; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo-container">
            <div class="logo">
                <i class="fas fa-brain"></i>
                <h1>Curva Adaptativa</h1>
            </div>
        </div>

        <div class="cadastro-card">
            <div class="card-header">
                <h2>Crie sua conta gratuita</h2>
                <p>Comece sua jornada de aprendizado inteligente hoje</p>
            </div>

            <?php if ($mensagemErro): ?>
                <div class="alert">
                    <i class="fas fa-exclamation-circle"></i>
                    <span><?php echo htmlspecialchars($mensagemErro); ?></span>
                </div>
            <?php endif; ?>

            <form action="processa_aluno.php" method="POST">
                <!-- Dados Pessoais -->
                <div class="form-group">
                    <label class="form-label" for="nome">Nome Completo <span class="required">*</span></label>
                    <div class="input-wrapper">
                        <input type="text" class="form-control" id="nome" name="nome" placeholder="Digite seu nome completo" required autofocus>
                        <i class="fas fa-user input-icon"></i>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="email">E-mail <span class="required">*</span></label>
                        <div class="input-wrapper">
                            <input type="email" class="form-control" id="email" name="email" placeholder="seu@email.com" required>
                            <i class="fas fa-envelope input-icon"></i>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="telefone">Telefone</label>
                        <div class="input-wrapper">
                            <input type="text" class="form-control" id="telefone" name="telefone" placeholder="(00) 00000-0000">
                            <i class="fas fa-phone input-icon"></i>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="cpf">CPF <span class="required">*</span></label>
                    <div class="input-wrapper">
                        <input type="text" class="form-control" id="cpf" name="cpf" placeholder="00000000000" required pattern="\d{11}" maxlength="11">
                        <i class="fas fa-id-card input-icon"></i>
                    </div>
                </div>

                <!-- Dados de Acesso -->
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="nome_Usuario">Nome de Usuário <span class="required">*</span></label>
                        <div class="input-wrapper">
                            <input type="text" class="form-control" id="nome_Usuario" name="nome_Usuario" placeholder="@seuusuario" required>
                            <i class="fas fa-at input-icon"></i>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="senha">Senha <span class="required">*</span></label>
                        <div class="input-wrapper">
                            <input type="password" class="form-control" id="senha" name="senha" placeholder="Mínimo 6 caracteres" required minlength="6">
                            <i class="fas fa-lock input-icon"></i>
                        </div>
                    </div>
                </div>

                <!-- Nível de Foco -->
                <div class="form-group">
                    <label class="form-label">Nível de Foco <span class="required">*</span></label>
                    <div class="nivel-selector">
                        <div class="nivel-option" onclick="selectNivel(1)">
                            <input type="radio" name="nivelDificuldade" value="1" id="nivel1">
                            <div class="nivel-number">1</div>
                            <div class="nivel-label">Muito Baixo</div>
                        </div>
                        <div class="nivel-option" onclick="selectNivel(2)">
                            <input type="radio" name="nivelDificuldade" value="2" id="nivel2">
                            <div class="nivel-number">2</div>
                            <div class="nivel-label">Baixo</div>
                        </div>
                        <div class="nivel-option selected" onclick="selectNivel(3)">
                            <input type="radio" name="nivelDificuldade" value="3" id="nivel3" checked>
                            <div class="nivel-number">3</div>
                            <div class="nivel-label">Médio</div>
                        </div>
                        <div class="nivel-option" onclick="selectNivel(4)">
                            <input type="radio" name="nivelDificuldade" value="4" id="nivel4">
                            <div class="nivel-number">4</div>
                            <div class="nivel-label">Alto</div>
                        </div>
                        <div class="nivel-option" onclick="selectNivel(5)">
                            <input type="radio" name="nivelDificuldade" value="5" id="nivel5">
                            <div class="nivel-number">5</div>
                            <div class="nivel-label">Muito Alto</div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-cadastrar">
                    <i class="fas fa-rocket"></i>
                    <span>Criar Minha Conta</span>
                </button>
            </form>

            <div class="login-link">
                Já tem uma conta? <a href="login.php">Faça login aqui</a>
            </div>
        </div>
    </div>

    <script>
        function selectNivel(nivel) {
            document.querySelectorAll('.nivel-option').forEach(opt => opt.classList.remove('selected'));
            event.currentTarget.classList.add('selected');
            document.getElementById('nivel' + nivel).checked = true;
        }

        document.getElementById('telefone').addEventListener('input', function(e) {
            let v = e.target.value.replace(/\D/g, '');
            if (v.length > 11) v = v.slice(0, 11);
            if (v.length > 10) v = v.replace(/^(\d{2})(\d{5})(\d{4}).*/, '($1) $2-$3');
            else if (v.length > 6) v = v.replace(/^(\d{2})(\d{4})(\d{0,4}).*/, '($1) $2-$3');
            else if (v.length > 2) v = v.replace(/^(\d{2})(\d{0,5})/, '($1) $2');
            else if (v.length > 0) v = v.replace(/^(\d*)/, '($1');
            e.target.value = v;
        });

        document.getElementById('cpf').addEventListener('input', e => e.target.value = e.target.value.replace(/\D/g, '').slice(0, 11));
    </script>
</body>
</html>