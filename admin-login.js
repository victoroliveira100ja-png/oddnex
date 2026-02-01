// js/admin-login.js
function loginAdmin() {
  const email = document.getElementById("email").value;
  const senha = document.getElementById("senha").value;
  const erro = document.getElementById("erro");

  const ADMIN_EMAIL = "admin@oddmax.com";
  const ADMIN_SENHA = "paulistao2026";

  if (email !== ADMIN_EMAIL || senha !== ADMIN_SENHA) {
    erro.innerText = "Credenciais inválidas.";
    erro.style.color = "#ff4d4d";
    return;
  }

  localStorage.setItem("oddmax_role", "admin");
  localStorage.setItem("oddmax_admin", "true");

  window.location.href = "admin.html";
}
