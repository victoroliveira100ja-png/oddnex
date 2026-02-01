// js/login.js
function login() {
  const email = document.getElementById("email").value;
  const senha = document.getElementById("senha").value;
  const erro = document.getElementById("erro");

  if (!email || !senha) {
    erro.innerText = "Preencha todos os campos.";
    erro.style.color = "#ff4d4d";
    return;
  }

  const expira = localStorage.getItem("oddmax_expira");

  if (!expira) {
    erro.innerText = "Assinatura VIP não encontrada ou expirada.";
    erro.style.color = "#ff4d4d";
    return;
  }

  localStorage.setItem("oddmax_role", "vip");
  window.location.href = "vip.html";
}
