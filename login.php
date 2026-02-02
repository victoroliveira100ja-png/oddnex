<?php
session_start();
require_once 'conexao.php';

$erro = "";

// Se já estiver logado, manda direto pro Dashboard
if (isset($_SESSION['usuario_id'])) {
    header("Location: dashboard.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $senha = $_POST['password'];

    if (!empty($email) && !empty($senha)) {
        // Busca o usuário no seu MySQL
        $stmt = $pdo->prepare("SELECT id, nome, senha FROM usuarios WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verifica a senha (estou usando password_verify por segurança)
        if ($usuario && password_verify($senha, $usuario['senha'])) {
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];
            
            header("Location: dashboard.php");
            exit;
        } else {
            $erro = "E-mail ou senha inválidos.";
        }
    } else {
        $erro = "Preencha todos os campos.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>OddNex VIP — Acesso Exclusivo</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&family=Poppins:wght@700&display=swap" rel="stylesheet">
  <style>
    :root {
      --primary: #7d33ff;
      --secondary: #00e0ff;
      --pink: #ff4ecd;
      --bg: #05070a;
      --glass: rgba(13,17,23,0.75);
      --border: rgba(255,255,255,0.15);
      --text: #f0f6fc;
    }
    * { box-sizing: border-box; }
    body {
      margin: 0; height: 100vh;
      background: radial-gradient(circle at top left, rgba(125,51,255,0.35), transparent 45%),
                  radial-gradient(circle at bottom right, rgba(0,224,255,0.35), transparent 45%), var(--bg);
      display: flex; align-items: center; justify-content: center;
      font-family: 'Inter', sans-serif; color: var(--text);
    }
    .login-card {
      width: 100%; max-width: 440px; padding: 50px 42px;
      background: var(--glass); border: 1px solid var(--border);
      border-radius: 30px; backdrop-filter: blur(18px);
      box-shadow: 0 30px 60px rgba(0,0,0,0.7), 0 0 90px rgba(125,51,255,0.35);
      text-align: center; position: relative;
    }
    .login-card::before {
      content: ""; position: absolute; top: 0; left: 50%; transform: translateX(-50%);
      width: 180px; height: 3px; border-radius: 50px;
      background: linear-gradient(90deg, transparent, var(--primary), var(--secondary), transparent);
    }
    .logo { font-family: 'Poppins', sans-serif; font-size: 36px; margin-bottom: 10px; letter-spacing: -1px; }
    .logo span {
      background: linear-gradient(135deg, var(--primary), var(--secondary), var(--pink));
      -webkit-background-clip: text; -webkit-text-fill-color: transparent;
    }
    .subtitle { font-size: 14px; color: #8b949e; margin-bottom: 35px; }
    .field { text-align: left; margin-bottom: 22px; }
    .field label { display: block; font-size: 11px; font-weight: 800; letter-spacing: 1px; color: #8b949e; margin-bottom: 8px; text-transform: uppercase; }
    .field input { width: 100%; padding: 16px; background: #010409; border: 1px solid var(--border); border-radius: 14px; color: white; font-size: 15px; outline: none; }
    .field input:focus { border-color: var(--primary); box-shadow: 0 0 0 4px rgba(125,51,255,0.15); }
    .btn-login {
      width: 100%; padding: 18px; border-radius: 16px; border: none;
      font-weight: 800; font-size: 15px; letter-spacing: 1px; cursor: pointer; color: white;
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      box-shadow: 0 10px 25px rgba(125,51,255,0.45); margin-top: 10px;
    }
    .error-msg { background: rgba(255, 71, 87, 0.1); color: #ff4757; padding: 10px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; border: 1px solid rgba(255, 71, 87, 0.2); }
  </style>
</head>
<body>

<div class="login-card">
  <div class="logo">OddNex<span>VIP</span> 💎</div>
  <div class="subtitle">Acesso exclusivo para membros</div>

  <?php if($erro): ?>
    <div class="error-msg"><?php echo $erro; ?></div>
  <?php endif; ?>

  <form method="POST" action="login.php">
    <div class="field">
      <label>E-mail</label>
      <input type="email" name="email" placeholder="seu@email.com" required>
    </div>

    <div class="field">
      <label>Senha</label>
      <input type="password" name="password" placeholder="••••••••" required>
    </div>

    <button type="submit" class="btn-login">ENTRAR NA ÁREA VIP</button>
  </form>
</div>

</body>
</html>
