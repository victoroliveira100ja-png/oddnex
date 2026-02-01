async function buscarPalpites(camp) {
    const data = document.getElementById('filtro-data').value;
    if (!data) return alert("Selecione uma data primeiro!");

    document.getElementById('welcome-message').style.display = 'none';
    const titulo = document.getElementById('titulo-camp');
    titulo.innerText = camp;
    titulo.style.display = 'block';

    const container = document.getElementById('lista-rodadas');
    container.innerHTML = '<p>Buscando rodadas...</p>';

    try {
        const snapshot = await db.collection('palpites')
            .where('data', '==', data)
            .where('campeonato', '==', camp)
            .get();

        if (snapshot.empty) {
            container.innerHTML = '<p style="color:gray;">Nenhum palpite para esta data.</p>';
            return;
        }

        // Agrupa jogos por rodada
        const rodadas = {};
        snapshot.forEach(doc => {
            const p = doc.data();
            const r = p.rodada || "Rodada Não Definida";
            if (!rodadas[r]) rodadas[r] = [];
            rodadas[r].push(p);
        });

        container.innerHTML = '';
        Object.keys(rodadas).sort().forEach(nome => {
            const div = document.createElement('div');
            div.className = 'rodada-container';
            
            const jogosHTML = rodadas[nome].map(j => `
                <div class="card">
                    <span class="badge">${j.status || 'PENDENTE'}</span>
                    <small>${j.horario || ''}</small>
                    <h3 style="margin:10px 0">${j.times}</h3>
                    <div class="entrada-box">
                        <strong>Entrada:</strong> ${j.entrada}<br>
                        <strong>Odd:</strong> ${j.odd} | <strong>Confiança:</strong> ${j.confianca}
                    </div>
                </div>
            `).join('');

            div.innerHTML = `
                <div class="rodada-header" onclick="this.parentElement.classList.toggle('active')">
                    ${nome} (${rodadas[nome].length} jogos)
                </div>
                <div class="rodada-content">${jogosHTML}</div>
            `;
            container.appendChild(div);
        });
    } catch (e) {
        container.innerHTML = '<p>Erro ao carregar dados.</p>';
    }
}