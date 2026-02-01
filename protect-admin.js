// js/protect-admin.js
(function () {
  if (
    localStorage.getItem("oddmax_role") !== "admin" ||
    localStorage.getItem("oddmax_admin") !== "true"
  ) {
    window.location.href = "admin-login.html";
  }
})();
