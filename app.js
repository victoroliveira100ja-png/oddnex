// ================= FIREBASE =================
const firebaseConfig = {
  apiKey: "AIzaSyACqz98WONOsFwK9mP2udGfYI6a7H8bwO0",
  authDomain: "oddmax-2004b.firebaseapp.com",
  projectId: "oddmax-2004b",
};

if (!firebase.apps.length) {
  firebase.initializeApp(firebaseConfig);
}

const db = firebase.firestore();

// ================= PALPITES (PÁGINA PUBLICA) =================
const palpitesContainer = document.getElementById("palpites");

if (palpitesContainer) {
  db.collection("palpites")
    .orderBy("criadoEm", "desc")
    .onSnapshot((snapshot) => {
      palpitesContainer.innerHTML = "";

      if (snapshot.empty) {
        palpitesContainer.innerHTML =
          "<p class='center'>Nenhum palpite disponível.</p>";
        return;
      }

      snapshot.forEach((doc) => {
        const p = doc.data();

        palpitesContainer.innerHTML += `
          <div class="pick-card">
            <div class="pick-top">
              <span class="league">${p.campeonato || ""}</span>
              <span class="badge">${p.status}</span>
            </div>

            <h3>${p.jogo}</h3>

            <div class="pick-info">
              <span>🎯 <strong>${p.entrada}</strong></span><br>
              <span>📊 Odd: ${p.odd}</span><br>
              <span>⚠️ Risco: ${p.risco}</span>
            </div>

            ${
              p.analise
                ? `<div class="analysis"><p>${p.analise}</p></div>`
                : ""
            }
          </div>
        `;
      });
    });
}
