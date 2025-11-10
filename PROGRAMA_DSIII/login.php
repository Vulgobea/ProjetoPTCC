<?php

declare(strict_types=1);

// =====================================================================
// CONFIGURAÇÕES INICIAIS
// =====================================================================
session_start();

// Oculta erros em produção
ini_set('display_errors', '0');
error_reporting(0);

// =====================================================================
// REDIRECIONAMENTO SE O USUÁRIO JÁ ESTIVER LOGADO
// =====================================================================
if (isset($_SESSION['id_aluno'])) {
    header("Location: index.php");
    exit();
}

// =====================================================================
// MENSAGENS DE FEEDBACK (SUCESSO OU ERRO)
// =====================================================================
$mensagemSucessoCadastro = $_SESSION['sucesso_cadastro'] ?? null;
unset($_SESSION['sucesso_cadastro']);

$mensagemErroLogin = $_SESSION['erro_login'] ?? null;
unset($_SESSION['erro_login']);

// =====================================================================
// VARIÁVEIS DE CABEÇALHO
// =====================================================================
$pageTitle = "Login - Curva Adaptativa";
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }

        /* Animated background */
        body::before {
            content: '';
            position: absolute;
            width: 150%;
            height: 150%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 1px, transparent 1px);
            background-size: 50px 50px;
            animation: moveBackground 20s linear infinite;
        }

        @keyframes moveBackground {
            0% { transform: translate(0, 0); }
            100% { transform: translate(50px, 50px); }
        }

        .container {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 450px;
        }

        .logo-container {
            text-align: center;
            margin-bottom: 30px;
            animation: fadeInDown 0.8s ease-out;
        }

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

        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: fadeInUp 0.8s ease-out;
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .card-header h2 {
            font-size: 24px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 8px;
        }

        .card-header p {
            color: #6b7280;
            font-size: 14px;
        }

        .alert {
            padding: 14px 18px;
            border-radius: 12px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 14px;
            animation: slideIn 0.4s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #6ee7b7;
        }

        .alert-danger {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        .alert i {
            font-size: 18px;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-label {
            display: block;
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 18px;
            pointer-events: none;
            transition: color 0.3s;
        }

        .form-control {
            width: 100%;
            padding: 14px 16px 14px 48px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 15px;
            transition: all 0.3s;
            background: white;
        }

        .form-control:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        .form-control:focus + .input-icon {
            color: #667eea;
        }

        .btn-login {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .divider {
            text-align: center;
            margin: 30px 0;
            position: relative;
        }

        .divider::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            width: 100%;
            height: 1px;
            background: #e5e7eb;
        }

        .divider span {
            position: relative;
            background: rgba(255, 255, 255, 0.95);
            padding: 0 16px;
            color: #9ca3af;
            font-size: 14px;
            font-weight: 500;
        }

        .signup-link {
            text-align: center;
            margin-top: 24px;
            font-size: 14px;
            color: #6b7280;
        }

        .signup-link a {
            color: #667eea;
            font-weight: 600;
            text-decoration: none;
            transition: color 0.3s;
        }

        .signup-link a:hover {
            color: #764ba2;
            text-decoration: underline;
        }

        .features {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            margin-top: 24px;
        }

        .feature-item {
            text-align: center;
            padding: 16px;
            background: #f9fafb;
            border-radius: 12px;
            transition: all 0.3s;
        }

        .feature-item:hover {
            background: #f3f4f6;
            transform: translateY(-2px);
        }

        .feature-item i {
            font-size: 24px;
            color: #667eea;
            margin-bottom: 8px;
        }

        .feature-item span {
            display: block;
            font-size: 12px;
            color: #6b7280;
            font-weight: 500;
        }

        @media (max-width: 640px) {
            .login-card {
                padding: 30px 24px;
            }

            .logo h1 {
                font-size: 24px;
            }

            .features {
                grid-template-columns: 1fr;
                gap: 12px;
            }
        }
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

        <div class="login-card">
            <div class="card-header">
                <h2>Bem-vindo de volta!</h2>
                <p>Faça login para continuar seus estudos</p>
            </div>

            <?php if ($mensagemSucessoCadastro): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <span><?php echo htmlspecialchars($mensagemSucessoCadastro); ?></span>
                </div>
            <?php endif; ?>

            <?php if ($mensagemErroLogin): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <span><?php echo htmlspecialchars($mensagemErroLogin); ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" action="processa_login.php">
                <div class="form-group">
                    <label class="form-label" for="nome_usuario">E-mail ou Usuário</label>
                    <div class="input-wrapper">
                        <input 
                            type="text" 
                            class="form-control" 
                            id="nome_usuario" 
                            name="nome_usuario" 
                            placeholder="Digite seu e-mail ou usuário"
                            required
                            autofocus
                        >
                        <i class="fas fa-user input-icon"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="senha">Senha</label>
                    <div class="input-wrapper">
                        <input 
                            type="password" 
                            class="form-control" 
                            id="senha" 
                            name="senha" 
                            placeholder="Digite sua senha"
                            required
                        >
                        <i class="fas fa-lock input-icon"></i>
                    </div>
                </div>

                <button type="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt"></i>
                    <span>Entrar</span>
                </button>
            </form>

            <div class="divider">
                <span>Recursos da plataforma</span>
            </div>

            <div class="features">
                <div class="feature-item">
                    <i class="fas fa-brain"></i>
                    <span>Repetição Espaçada</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-chart-line"></i>
                    <span>Progresso Visual</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-calendar-check"></i>
                    <span>Agendamento Smart</span>
                </div>
            </div>

            <div class="signup-link">
                Ainda não tem conta? <a href="CadastroAluno.php">Cadastre-se gratuitamente</a>
            </div>
        </div>
    </div>
</body>
</html>