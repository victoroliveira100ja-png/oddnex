const resultados = [
  {
    jogo: "Arsenal x Chelsea",
    mercado: "Ambas Marcam",
    odd: 1.85,
    stake: 1.5,
    resultado: "green"
  },
  {
    jogo: "Real Madrid x Sevilla",
    mercado: "Over 2.5 gols",
    odd: 1.72,
    stake: 2,
    resultado: "green"
  },
  {
    jogo: "PSG x Lyon",
    mercado: "PSG vence",
    odd: 1.60,
    stake: 2,
    resultado: "red"
  }
];

const container = document.getElementById("lista-resultados");

let totalStake = 0;
let lucro = 0;
let greens = 0;

resultados.forEach(r => {
  totalStake += r.stake;

  if (r.resultado === "green") {
    lucro += (r.odd - 1) * r.stake;
    greens++;
  } else {
    lucro -= r.stake;
  }

  const card = document.createElement("div");
  card.className = `pick-card ${r.resultado}`;

  card.innerHTML = `
    <h2>${r.jogo}</h2>
    <p><strong>Mercado:</strong> ${r.mercado}</p>
    <p><strong>Odd:</strong> ${r.odd}</p>
    <p><strong>Stake:</strong> ${r.stake}u</p>
    <p class="resultado ${r.resultado}">
      ${r.resultado.toUpperCase()}
    </p>
  `;

  container.appendChild(card);
});

// Cálculos
const roi = ((lucro / totalStake) * 100).toFixed(1);
const assertividade = ((greens / resultados.length) * 100).toFixed(0);

document.getElementById("roi").innerText = `${roi}%`;
document.getElementById("assertividade").innerText = `${assertividade}%`;
document.getElementById("unidades").innerText = `${lucro.toFixed(2)}u`;
