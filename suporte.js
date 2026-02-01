// js/suporte.js
function suporte() {
    // Seu número: 55 11 986652226
    const telefone = "5511986652226"; 
    const mensagem = encodeURIComponent("Olá! Sou membro do OddMax VIP e preciso de ajuda.");
    const url = `https://wa.me/${telefone}?text=${mensagem}`;
    
    window.open(url, '_blank');
}