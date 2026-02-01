// js/assinar.js
function assinar() {
  const email = document.getElementById("email").value;
  const senha = document.getElementById("senha").value;
  const erro = document.getElementById("erro");

  if (!email || !senha) {
    erro.innerText = "Preencha todos os campos.";
    erro.style.color = "#ff4d4d";
    return;
  }

  const hoje = new Date();
  const expira = new Date();
  expira.setDate(hoje.getDate() + 30);

  localStorage.setItem("oddmax_email", email);
  localStorage.setItem("oddmax_role", "vip");
  localStorage.setItem("oddmax_expira", expira.toISOString());

  alert("💎 VIP ativado com sucesso!");
  window.location.href = "vip.html";
}
