import { initializeApp } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-app.js";
import { getAuth, onAuthStateChanged } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-auth.js";

// Configuração idêntica à do seu projeto OddMax
const firebaseConfig = {
  apiKey: "AIzaSyACqz98WONOsFwK9mP2udGfYI6a7H8bwO0",
  authDomain: "oddmax-2004b.firebaseapp.com",
  projectId: "oddmax-2004b",
  storageBucket: "oddmax-2004b.firebasestorage.app",
  messagingSenderId: "317240346694",
  appId: "1:317240346694:web:e551215b1cf64d58246247"
};

const app = initializeApp(firebaseConfig);
const auth = getAuth(app);

// MONITOR DE ACESSO
onAuthStateChanged(auth, (user) => {
    if (!user) {
        // Se não houver usuário logado, manda para o login
        console.log("Acesso negado. Redirecionando...");
        window.location.href = "login.html";
    } else {
        console.log("Acesso autorizado: ", user.email);
    }
});