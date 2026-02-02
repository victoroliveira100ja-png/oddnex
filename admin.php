<?php
require_once 'conexao.php';

// --- LÓGICA DE BACKEND (PHP) ---

// 1. Salvar ou Atualizar Palpite
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_salvar'])) {
    $id = $_POST['edit-id'];
    $dados = [
        $_POST['jogo'], $_POST['campeonato'], $_POST['rodada'], $_POST['entrada'],
        $_POST['odd'], $_POST['chance'], $_POST['horario'], $_POST['data_jogo'],
        $_POST['risco'], $_POST['analise']
    ];

    if (!empty($id)) {
        // Modo Edição
        $sql = "UPDATE palpites SET jogo=?, campeonato=?, rodada=?, entrada=?, odd=?, chance=?, horario=?, data_jogo=?, risco=?, analise=? WHERE id=?";
        $dados[] = $id;
        $stmt = $pdo->prepare($sql);
        $stmt->execute($dados);
    } else {
        // Modo Novo Palpite
        $sql = "INSERT INTO palpites (jogo, campeonato, rodada, entrada, odd, chance, horario, data_jogo, risco, analise, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'ABERTO')";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($dados);
    }
    header("Location: admin.php");
    exit;
}

// 2. Deletar Palpite
if (isset($_GET['deletar'])) {
    $stmt = $pdo->prepare("DELETE FROM palpites WHERE id = ?");
    $stmt->execute([$_GET['deletar']]);
    header("Location: admin.php");
    exit;
}

// 3. Atualizar Status (Via AJAX ou Link rápido)
if (isset($_GET['update_status']) && isset($_GET['id'])) {
    $stmt = $pdo->prepare("UPDATE palpites SET status = ? WHERE id = ?");
    $stmt->execute([$_GET['update_status'], $_GET['id']]);
    header("Location: admin.php");
    exit;
}

// 4. Buscar todos os palpites para a lista
$busca = isset($_GET['q']) ? $_GET['q'] : '';
$query = "SELECT * FROM palpites WHERE jogo LIKE ? OR campeonato LIKE ? ORDER BY id DESC";
$stmt = $pdo->prepare($query);
$stmt->execute(["%$busca%", "%$busca%"]);
$todosPalpites = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Master Admin - OddNex VIP</title>
    <style>
        :root { --primary: #7d33ff; --bg: #05070a; --card: #111418; --border: #1f242c; --green: #2ecc71; --red: #ff4757; --live: #f1c40f; }
        body { background: var(--bg); color: white; font-family: 'Inter', sans-serif; margin: 0; display: flex; height: 100vh; }
        .sidebar-form { width: 400px; background: var(--card); border-right: 1px solid var(--border); padding: 30px; overflow-y: auto; box-sizing: border-box; }
        .main-list { flex: 1; padding: 30px; overflow-y: auto; }
        h2 { color: var(--primary); text-align: center; text-transform: uppercase; font-size: 18px; margin-bottom: 20px; letter-spacing: 1px; }
        .form-group { margin-bottom: 15px; }
        label { font-size: 11px; color: #8b949e; font-weight: bold; margin-bottom: 5px; display: block; text-transform: uppercase; }
        input, select, textarea { width: 100%; padding: 12px; background: #05070a; border: 1px solid var(--border); border-radius: 8px; color: white; outline: none; box-sizing: border-box; font-size: 14px; }
        input:focus, select:focus, textarea:focus { border-color: var(--primary); }
        .btn-postar { width: 100%; padding: 16px; background: var(--primary); color: white; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; margin-top: 10px; text-transform: uppercase; }
        .card-manage { background: var(--card); border: 1px solid var(--border); padding: 15px; border-radius: 12px; margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center; }
        .btn-delete { background: var(--red); color: white; border: none; padding: 8px 12px; border-radius: 6px; cursor: pointer; font-size: 10px; font-weight: bold; margin-left: 5px; text-decoration: none; }
        .btn-edit { background: #3498db; color: white; border: none; padding: 8px 12px; border-radius: 6px; cursor: pointer; font-size: 10px; font-weight: bold; }
        .search-admin { margin-bottom: 20px; position: sticky; top: 0; z-index: 10; background: var(--bg); padding-bottom: 10px; }
    </style>
</head>
<body>
    <div class="sidebar-form">
        <h2 id="titulo-form">🚀 NOVO PALPITE (ODDNEX)</h2>
        
        <form method="POST" id="form-palpite">
            <input type="hidden" name="edit-id" id="edit-id">

            <div class="form-group">
                <label>Jogo:</label>
                <input type="text" name="jogo" id="jogo" placeholder="Ex: Flamengo x Vasco" required>
            </div>
            
            <div class="form-group">
                <label>Campeonato:</label>
                <select name="campeonato" id="campeonato">
                    <optgroup label="BRASIL">
                        <option value="Brasileirão Série A">Brasileirão Série A</option>
                        <option value="Brasileirão Série B">Brasileirão Série B</option>
                        <option value="Copa do Brasil">Copa do Brasil</option>
                    </optgroup>
                    <optgroup label="EUROPA">
                        <option value="Premier League">Premier League</option>
                        <option value="Champions League">Champions League</option>
                    </optgroup>
                    </select>
            </div>

            <div class="form-group">
                <label>Rodada / Fase:</label>
                <select name="rodada" id="rodada">
                    <option value="">Selecione...</option>
                    <?php for($i=1; $i<=38; $i++) echo "<option value='Rodada $i'>Rodada $i</option>"; ?>
                    <option value="Final">Grande Final</option>
                </select>
            </div>

            <div class="form-group">
                <label>Entrada Sugerida:</label>
                <input type="text" name="entrada" id="entrada" placeholder="Ex: Over 2.5 Gols">
            </div>
            
            <div style="display:flex; gap:10px;">
                <div style="flex:1;"><label>Odd:</label><input type="text" name="odd" id="odd" placeholder="1.80"></div>
                <div style="flex:1;"><label>Confiança %:</label><input type="number" name="chance" id="chance" placeholder="90"></div>
            </div>

            <div style="display:flex; gap:10px; margin-top:10px;">
                <div style="flex:1;"><label>Horário:</label><input type="text" name="horario" id="horario" placeholder="16:00"></div>
                <div style="flex:1;"><label>Data:</label><input type="date" name="data_jogo" id="data_jogo"></div>
            </div>

            <div class="form-group" style="margin-top:10px;">
                <label>Nível de Risco:</label>
                <select name="risco" id="risco">
                    <option value="Baixo">Baixo</option>
                    <option value="Médio" selected>Médio</option>
                    <option value="Alto">Alto</option>
                </select>
            </div>

            <div class="form-group">
                <label>Análise Técnica:</label>
                <textarea name="analise" id="analise" rows="3"></textarea>
            </div>

            <button type="submit" name="btn_salvar" class="btn-postar" id="btn-acao">PUBLICAR AGORA</button>
            <button type="button" id="btn-cancelar" style="display:none; background:gray; margin-top:5px;" class="btn-postar" onclick="resetarFormulario()">CANCELAR EDIÇÃO</button>
        </form>
    </div>

    <div class="main-list">
        <h2>📊 GESTÃO ATIVA - ODDNEX</h2>
        
        <form method="GET" class="search-admin">
            <input type="text" name="q" value="<?= htmlspecialchars($busca) ?>" placeholder="🔍 Pesquisar por time ou campeonato...">
        </form>

        <div id="lista-admin">
            <?php foreach ($todosPalpites as $p): ?>
                <div class="card-manage">
                    <div>
                        <b><?= $p['jogo'] ?></b> <small style="color:#8b949e">(<?= $p['rodada'] ?: 'S/R' ?>)</small><br>
                        <small style="color:var(--primary)"><?= $p['campeonato'] ?></small><br>
                        
                        <select onchange="window.location.href='admin.php?id=<?= $p['id'] ?>&update_status='+this.value" style="width:110px; padding:3px; font-size:11px; margin-top:5px;">
                            <option value="ABERTO" <?= $p['status'] == 'ABERTO' ? 'selected' : '' ?>>⏳ Aberto</option>
                            <option value="AO VIVO" <?= $p['status'] == 'AO VIVO' ? 'selected' : '' ?>>🔥 Ao Vivo</option>
                            <option value="GREEN" <?= $p['status'] == 'GREEN' ? 'selected' : '' ?>>✅ Green</option>
                            <option value="RED" <?= $p['status'] == 'RED' ? 'selected' : '' ?>>❌ Red</option>
                        </select>
                    </div>
                    <div>
                        <button class="btn-edit" onclick='prepararEdicao(<?= json_encode($p) ?>)'>EDITAR</button>
                        <a href="admin.php?deletar=<?= $p['id'] ?>" class="btn-delete" onclick="return confirm('Apagar permanentemente?')">EXCLUIR</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
        function prepararEdicao(p) {
            document.getElementById('edit-id').value = p.id;
            document.getElementById('jogo').value = p.jogo;
            document.getElementById('campeonato').value = p.campeonato;
            document.getElementById('rodada').value = p.rodada;
            document.getElementById('entrada').value = p.entrada;
            document.getElementById('odd').value = p.odd;
            document.getElementById('chance').value = p.chance;
            document.getElementById('horario').value = p.horario;
            document.getElementById('data_jogo').value = p.data_jogo;
            document.getElementById('risco').value = p.risco;
            document.getElementById('analise').value = p.analise;
            
            document.getElementById('titulo-form').innerText = "✏️ EDITANDO JOGO";
            document.getElementById('btn-acao').innerText = "SALVAR ALTERAÇÕES";
            document.getElementById('btn-cancelar').style.display = "block";
            window.scrollTo(0,0);
        }

        function resetarFormulario() {
            document.getElementById('form-palpite').reset();
            document.getElementById('edit-id').value = "";
            document.getElementById('titulo-form').innerText = "🚀 NOVO PALPITE";
            document.getElementById('btn-acao').innerText = "PUBLICAR AGORA";
            document.getElementById('btn-cancelar').style.display = "none";
        }
    </script>
</body>
</html>
