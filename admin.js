const firebaseConfig = {
    apiKey: "AIzaSyACqz98WONOsFwK9mP2udGfYI6a7H8bwO0",
    authDomain: "oddmax-2004b.firebaseapp.com",
    projectId: "oddmax-2004b",
    storageBucket: "oddmax-2004b.firebasestorage.app",
    messagingSenderId: "317240346694",
    appId: "1:317240346694:web:e551215b1cf64d58246247"
};

if (!firebase.apps.length) firebase.initializeApp(firebaseConfig);
const db = firebase.firestore();
let todosPalpites = [];

// Sincroniza os dados do Firebase
db.collection("palpites").orderBy("criadoEm", "desc").onSnapshot(snapshot => {
    todosPalpites = snapshot.docs.map(doc => ({ id: doc.id, ...doc.data() }));
    renderizarPainel();
});

function publicarPalpite() {
    const palpite = {
        jogo: document.getElementById('jogo').value,
        campeonato: document.getElementById('campeonato').value,
        data_jogo: document.getElementById('data_jogo').value,
        horario: document.getElementById('horario').value,
        entrada: document.getElementById('entrada').value,
        odd: document.getElementById('odd').value,
        chance: document.getElementById('chance').value,
        analise: document.getElementById('analise').value,
        status: "⌛ PENDENTE",
        criadoEm: firebase.firestore.FieldValue.serverTimestamp()
    };

    db.collection("palpites").add(palpite).then(() => {
        alert("Palpite enviado!");
    });
}

function renderizarPainel() {
    const container = document.getElementById("lista-gerenciamento");
    const dataFiltro = document.getElementById("filtro-data-admin").value;
    container.innerHTML = "";

    if (!dataFiltro) {
        container.innerHTML = "<p style='text-align:center; color:gray;'>Selecione uma data acima para gerenciar os jogos.</p>";
        return;
    }

    const filtrados = todosPalpites.filter(p => p.data_jogo === dataFiltro);
    const campeonatos = [...new Set(filtrados.map(p => p.campeonato))];

    if (filtrados.length === 0) {
        container.innerHTML = "<p style='text-align:center; color:gray;'>Nenhum jogo para esta data.</p>";
        return;
    }

    campeonatos.forEach(camp => {
        const divGrupo = document.createElement('div');
        divGrupo.className = 'camp-grupo active'; // Inicia aberto para facilitar
        
        const divHeader = document.createElement('div');
        divHeader.className = 'camp-header';
        divHeader.innerHTML = `🏆 ${camp}`;
        divHeader.onclick = () => divGrupo.classList.toggle('active');

        const divContent = document.createElement('div');
        divContent.className = 'camp-content';

        const jogosDoCamp = filtrados.filter(p => p.campeonato === camp);
        jogosDoCamp.forEach(p => {
            divContent.innerHTML += `
                <div class="item-jogo">
                    <strong>${p.jogo}</strong> <small style="color:#8b949e;">[${p.horario}] - ${p.status}</small>
                    <div class="btn-group">
                        <button class="btn-st" style="background:#ff9800" onclick="atualizarStatus('${p.id}', '🔥 AO VIVO')">LIVE</button>
                        <button class="btn-st" style="background:#2e7d32" onclick="atualizarStatus('${p.id}', '✅ GREEN')">GREEN</button>
                        <button class="btn-st" style="background:#c62828" onclick="atualizarStatus('${p.id}', '❌ RED')">RED</button>
                        <button class="btn-st" style="background:#424242" onclick="excluirPalpite('${p.id}')">EXCLUIR</button>
                    </div>
                </div>`;
        });

        divGrupo.appendChild(divHeader);
        divGrupo.appendChild(divContent);
        container.appendChild(divGrupo);
    });
}

window.atualizarStatus = (id, status) => db.collection("palpites").doc(id).update({ status });
window.excluirPalpite = (id) => confirm("Deseja apagar?") && db.collection("palpites").doc(id).delete();