// js/protect-vip.js
(function () {
  const role = localStorage.getItem("oddmax_role");
  const expira = localStorage.getItem("oddmax_expira");

  if (role !== "vip" || !expira) {
    window.location.href = "login.html";
    return;
  }

  const hoje = new Date();
  const dataExpira = new Date(expira);

  if (hoje > dataExpira) {
    alert("⛔ Sua assinatura VIP expirou.");
    localStorage.clear();
    window.location.href = "assinar.html";
  }
})();
