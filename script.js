// Fonctions JavaScript côté client

console.log('SUNU Jersey - App chargée');

// Vérifier le panier au chargement
window.addEventListener('load', function() {
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    if (cart.length > 0) {
        console.log('Panier:', cart);
    }
});