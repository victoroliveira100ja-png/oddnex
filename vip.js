// CONFIGURAÇÃO FIREBASE
const firebaseConfig = {
  apiKey: "AIzaSyACqz98WONOsFwK9mP2udGfYI6a7H8bwO0",
  authDomain: "oddmax-2004b.firebaseapp.com",
  projectId: "oddmax-2004b",
  storageBucket: "oddmax-2004b.firebasestorage.app",
  messagingSenderId: "317240346694",
  appId: "1:317240346694:web:e551215b1cf64d58246247"
};

// INICIALIZAÇÃO
if (!firebase.apps.length) {
  firebase.initializeApp(firebaseConfig);
}

const db = firebase.firestore();
const lista = document.getElementById("lista-palpites");

// LISTAR PALPITES (MOSTRA TODOS)
db.collection("palpites")
  .orderBy("criadoEm", "desc")
  .onSnapshot((snapshot) => {
    lista.innerHTML = "";

    if (snapshot.empty) {
      lista.innerHTML = `
        <div style="text-align:center;color:#8b949e;padding:30px">
          Nenhum palpite disponível no momento.
        </div>`;
      return;
    }

    snapshot.forEach((doc) => {
      const p = doc.data();

      lista.innerHTML += `
        <div class="pick-card">

          <div class="card-header">
            <strong>${p.jogo}</strong>
            <span class="badge-status">${p.status}</span>
          </div>

          <div class="card-body">
            <small>${p.campeonato || ""} • ${p.horario || ""}</small>

            <div class="stats-grid">
              <div class="stat-box">
                <span class="stat-label">Entrada</span>
                <span class="stat-value">${p.entrada}</span>
              </div>
              <div class="stat-box">
                <span class="stat-label">Odd</span>
                <span class="stat-value">${p.odd || "-"}</span>
              </div>
              <div class="stat-box">
                <span class="stat-label">Confiança</span>
                <span class="stat-value">${p.chance || "-"}%</span>
              </div>
            </div>

            <div class="analysis-box">
              ${p.analise || "Boa leitura de jogo para esta partida."}
            </div>
          </div>

        </div>
      `;
    });
  }, (error) => {
    console.error("Erro ao carregar palpites:", error);
    lista.innerHTML = `
      <div style="color:red;text-align:center">
        Erro ao carregar palpites.
      </div>`;
  });

// LOGOUT (se usar auth)
window.fazerLogout = function () {
  firebase.auth().signOut().then(() => {
    window.location.href = "login.html";
  });
};
