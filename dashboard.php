<?php
session_start();
require_once 'conexao.php';

// Proteção: Se não houver sessão, volta para o login
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

// Busca todos os palpites do banco de dados
try {
    $stmt = $pdo->query("SELECT * FROM palpites ORDER BY rodada DESC, id DESC");
    $palpites = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro ao carregar palpites: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>OddNex VIP - Dashboard de Elite</title>
  
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&family=Poppins:wght@700&display=swap" rel="stylesheet">

  <style>
    :root {
      --primary: #7d33ff;
      --primary-bright: #a166ff;
      --bg: #05070a;
      --card-bg: #111418;
      --border: #1f242c;
      --green: #2ecc71;
      --red: #ff4757;
      --live: #ff4757; 
      --whatsapp: #25d366;
      --gradient: linear-gradient(135deg, #7d33ff 0%, #3a11ff 100%);
    }

    /* --- AJUSTES PARA CELULAR --- */
    @media (max-width: 768px) {
      .sidebar { position: fixed; z-index: 1001; height: 100%; }
      .top-bar { flex-wrap: wrap; gap: 10px !important; }
      .calc-topbar { order: 3; width: 100%; justify-content: space-between; overflow-x: auto; font-size: 9px; }
      .search-container input { width: 120px !important; }
      .welcome-card h1 { font-size: 2rem !important; }
      .content-scroll { padding: 10px !important; }
      .card { padding: 15px !important; }
    }

    .rodada-container { width: 100%; max-width: 650px; margin-bottom: 15px; }
    
    .rodada-header {
      width: 100%; background: var(--card-bg); border: 1px solid var(--primary); 
      color: white; padding: 18px; border-radius: 15px; cursor: pointer; 
      display: flex; justify-content: space-between; align-items: center;
      font-weight: 900; text-transform: uppercase; letter-spacing: 1px;
      transition: 0.3s;
    }
    .rodada-header:hover { background: rgba(125, 51, 255, 0.1); }
    
    .rodada-content { display: flex; flex-direction: column; align-items: center; padding-top: 20px; width: 100%; }

    .analise-expansivel {
      max-height: 0; overflow: hidden;
      transition: max-height 0.4s ease-in-out, opacity 0.3s; opacity: 0;
    }
    .analise-expansivel.show {
      max-height: 1000px; opacity: 1; margin-top: 15px; padding-top: 15px;
      border-top: 1px solid rgba(125, 51, 255, 0.2);
    }
    .btn-toggle-analise {
      width: 100%; background: rgba(125, 51, 255, 0.05); border: 1px solid var(--border);
      color: var(--primary-bright); padding: 10px; border-radius: 12px;
      cursor: pointer; font-size: 11px; font-weight: 800;
      text-transform: uppercase; margin-top: 10px; transition: 0.3s;
      display: flex; justify-content: center; align-items: center; gap: 8px;
    }

    body {
      background-color: var(--bg); background-image: radial-gradient(circle at 50% -20%, #1e104a, transparent);
      color: #f0f6fc; margin: 0; font-family: 'Inter', sans-serif; display: flex; height: 100vh; overflow: hidden;
    }

    .sidebar { width: 280px; background: #090b0f; border-right: 1px solid var(--border); display: flex; flex-direction: column; flex-shrink: 0; transition: all 0.3s ease; overflow: hidden; z-index: 1000; }
    .sidebar.closed { width: 0; border-right: none; }
    
    .sidebar-header { padding: 30px 20px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border); white-space: nowrap; }
    .sidebar-header h2 { margin: 0; font-size: 1.4rem; color: #fff; font-weight: 900; letter-spacing: 2px; }
    
    .btn-close-sidebar { background: none; border: none; color: white; font-size: 24px; cursor: pointer; display: flex; align-items: center; }

    .sidebar-menu { flex: 1; overflow-y: auto; padding: 15px; min-width: 280px; }
    .menu-label { font-size: 11px; color: var(--primary-bright); font-weight: 800; margin: 25px 0 10px 10px; text-transform: uppercase; letter-spacing: 1px; }

    .camp-btn {
      width: 100%; background: transparent; color: #8b949e; border: none; padding: 10px 15px; border-radius: 8px; margin-bottom: 3px;
      text-align: left; cursor: pointer; font-weight: 600; transition: 0.2s; display: flex; align-items: center; gap: 10px;
    }
    .camp-btn:hover { background: rgba(255,255,255,0.05); color: white; }
    .camp-btn.active { color: white; background: var(--gradient); box-shadow: 0 4px 12px rgba(125, 51, 255, 0.3); }

    .main-content { flex: 1; display: flex; flex-direction: column; width: 100%; min-width: 0; position: relative; }
    .top-bar { padding: 10px 20px; background: rgba(17, 20, 24, 0.9); backdrop-filter: blur(10px); border-bottom: 1px solid var(--border); display: flex; align-items: center; gap: 20px; min-height: 65px; }
    
    .btn-reopen { 
      position: fixed; top: 15px; left: 15px; z-index: 999; background: var(--card-bg); border: 1px solid var(--primary); 
      color: white; padding: 8px 12px; border-radius: 8px; cursor: pointer; display: none; font-weight: bold;
    }
    .sidebar.closed ~ .main-content .btn-reopen { display: block; }

    .search-container { display: flex; align-items: center; gap: 10px; }
    .search-container input { width: 180px; background: #0d1117; border: 1px solid var(--border); padding: 10px 15px; border-radius: 10px; color: white; outline: none; }

    .filter-bar { display: flex; gap: 10px; padding: 15px 30px; background: rgba(0,0,0,0.2); width: 100%; box-sizing: border-box; overflow-x: auto; }
    .filter-tag { padding: 6px 15px; border-radius: 12px; font-size: 10px; font-weight: 800; cursor: pointer; border: 1px solid var(--border); color: #8b949e; transition: 0.2s; text-transform: uppercase; white-space: nowrap; }
    .filter-tag.active { background: var(--primary); color: white; border-color: var(--primary); }

    .calc-topbar { display: flex; align-items: center; gap: 10px; background: #161b22; padding: 5px 15px; border-radius: 12px; border: 1px solid var(--border); }
    .calc-topbar input { width: 60px; background: #05070a; border: 1px solid var(--primary); padding: 5px; border-radius: 5px; color: white; text-align: center; }
    .calc-result { font-size: 10px; font-weight: bold; line-height: 1.2; text-align: center; }
    .calc-result span { color: var(--green); display: block; }

    .content-scroll { flex: 1; padding: 10px 30px 30px 30px; overflow-y: auto; display: flex; flex-direction: column; align-items: center; }
    
    .card { width: 100%; max-width: 650px; background: var(--card-bg); border: 1px solid rgba(125, 51, 255, 0.3); border-radius: 24px; padding: 25px; margin-bottom: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.5); box-sizing: border-box; }
    .entrada-box { background: #0d1117; border-left: 6px solid var(--primary); padding: 20px; border-radius: 15px; margin: 20px 0; }
    .radar-bar-bg { width: 100%; height: 8px; background: rgba(255,255,255,0.05); border-radius: 10px; overflow: hidden; margin-top: 8px; }
    .radar-bar-fill { height: 100%; background: linear-gradient(90deg, var(--primary), var(--green)); border-radius: 10px; transition: width 1s ease; }

    .btn-suporte { position: fixed; bottom: 30px; right: 30px; width: 60px; height: 60px; background-color: var(--whatsapp); border-radius: 50%; display: flex; justify-content: center; align-items: center; z-index: 1000; cursor: pointer; }
  </style>
</head>
<body>

  <aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
      <h2>OddNex VIP</h2>
      <button class="btn-close-sidebar" onclick="toggleSidebar()">☰</button>
    </div>
    <div class="sidebar-menu">
        <div class="menu-label">🇧🇷 Brasil Nacional</div>
        <button class="camp-btn" onclick="filtrarCamp('Brasileirão Série A')">⚽ Brasileirão Série A</button>
        <button class="camp-btn" onclick="filtrarCamp('Brasileirão Série B')">⚽ Brasileirão Série B</button>
        
        <div class="menu-label">🇪🇺 Europa</div>
        <button class="camp-btn" onclick="filtrarCamp('Premier League')">🏴󠁧󠁢󠁥󠁮󠁧󠁿 Premier League</button>
        <button class="camp-btn" onclick="filtrarCamp('Champions League')">🏆 Champions League</button>

        <button class="camp-btn" onclick="limparFiltros()" style="margin-top:25px; color: var(--primary); justify-content: center;">🔄 MOSTRAR TODOS</button>
        <a href="logout.php" style="text-decoration:none;"><button class="camp-btn" style="color:var(--red);">🚪 SAIR DA CONTA</button></a>
    </div>
  </aside>

  <main class="main-content">
    <button class="btn-reopen" onclick="toggleSidebar()">☰ Menu</button>

    <div class="top-bar">
      <div class="search-container">
        <input type="text" id="inputBusca" onkeyup="filtrarGeral()" placeholder="🔍 Buscar jogo...">
      </div>
      <div class="calc-topbar">
        <span style="font-size: 11px; font-weight: bold; color: var(--primary-bright);">BANCA R$</span>
        <input type="number" id="bancaValor" placeholder="Ex: 100" oninput="calcularStake()">
        <div class="calc-result">10% <span id="stake10">R$ 0,00</span></div>
        <div class="calc-result">20% <span id="stake20">R$ 0,00</span></div>
        <div class="calc-result">50% <span id="stake50">R$ 0,00</span></div>
      </div>
    </div>

    <div class="filter-bar">
        <div class="filter-tag active" onclick="filtrarStatus('TODOS', this)">📊 Todos</div>
        <div class="filter-tag" onclick="filtrarStatus('AO VIVO', this)">📡 Ao Vivo</div>
        <div class="filter-tag" onclick="filtrarStatus('GREEN', this)">✅ Greens</div>
        <div class="filter-tag" onclick="filtrarStatus('RED', this)">❌ Reds</div>
    </div>

    <div class="content-scroll" id="lista-palpites">
      <?php
      $rodada_atual = null;
      foreach ($palpites as $p): 
        if ($rodada_atual !== $p['rodada']): 
          if ($rodada_atual !== null) echo '</div></div>'; // Fecha anterior
          $rodada_atual = $p['rodada'];
      ?>
        <div class="rodada-container" data-rodada="<?php echo $p['rodada']; ?>">
            <div class="rodada-header">
                <span>RODADA <?php echo $p['rodada']; ?></span>
                <span>▼</span>
            </div>
            <div class="rodada-content">
      <?php endif; ?>
                
                <div class="card palpite-item" 
                     data-jogo="<?php echo strtolower($p['jogo']); ?>" 
                     data-camp="<?php echo $p['campeonato']; ?>"
                     data-status="<?php echo $p['status']; ?>">
                    
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;">
                        <span style="color:#8b949e; font-size:12px; font-weight:bold;"><?php echo $p['horario']; ?></span>
                        <span style="background:var(--primary); padding: 5px 12px; border-radius: 8px; font-size: 11px; font-weight: 900;"><?php echo $p['status']; ?></span>
                    </div>
                    <h2 style="margin:10px 0; font-size:1.8rem; font-weight:900;"><?php echo $p['jogo']; ?></h2>
                    
                    <div class="entrada-box">
                        <div style="font-size:1.6rem; font-weight:900; color:white;"><?php echo $p['entrada']; ?></div>
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-top:5px;">
                            <b style="color:var(--green); font-size:1.3rem;">ODD <?php echo $p['odd']; ?></b>
                            <span style="font-size:12px; font-weight:900; color:var(--primary-bright);"><?php echo $p['chance']; ?>% CHANCE</span>
                        </div>
                        <div class="radar-bar-bg"><div class="radar-bar-fill" style="width:<?php echo $p['chance']; ?>%"></div></div>
                    </div>
                    
                    <button class="btn-toggle-analise" onclick="toggleAnalise(this)">🔍 VER ANÁLISE</button>
                    <div class="analise-expansivel">
                        <p style="color:#8b949e; font-size:14px; line-height:1.6;"><?php echo $p['analise']; ?></p>
                    </div>
                </div>

      <?php endforeach; if ($rodada_atual !== null) echo '</div></div>'; ?>
    </div>
  </main>

  <script>
    function toggleSidebar() { document.getElementById('sidebar').classList.toggle('closed'); }

    function toggleAnalise(btn) {
        const div = btn.nextElementSibling;
        div.classList.toggle('show');
        btn.innerText = div.classList.contains('show') ? '🔼 FECHAR ANÁLISE' : '🔍 VER ANÁLISE';
    }

    function calcularStake() {
      const banca = document.getElementById('bancaValor').value;
      const stakes = [10, 20, 50];
      stakes.forEach(s => {
        document.getElementById('stake'+s).innerText = banca > 0 ? 'R$ ' + (banca * (s/100)).toFixed(2) : 'R$ 0,00';
      });
    }

    // Filtros em tempo real (JavaScript puro)
    function filtrarGeral() {
        const busca = document.getElementById('inputBusca').value.toLowerCase();
        const itens = document.querySelectorAll('.palpite-item');
        
        itens.forEach(item => {
            const jogo = item.getAttribute('data-jogo');
            const show = jogo.includes(busca);
            item.style.display = show ? 'block' : 'none';
        });
    }

    function filtrarCamp(nome) {
        const itens = document.querySelectorAll('.palpite-item');
        itens.forEach(item => {
            item.style.display = (item.getAttribute('data-camp') === nome) ? 'block' : 'none';
        });
    }

    function filtrarStatus(status, btn) {
        document.querySelectorAll('.filter-tag').forEach(t => t.classList.remove('active'));
        btn.classList.add('active');
        const itens = document.querySelectorAll('.palpite-item');
        itens.forEach(item => {
            if(status === 'TODOS') item.style.display = 'block';
            else item.style.display = (item.getAttribute('data-status') === status) ? 'block' : 'none';
        });
    }

    function limparFiltros() {
        document.querySelectorAll('.palpite-item').forEach(i => i.style.display = 'block');
    }
  </script>
</body>
</html>
