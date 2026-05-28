<?php
require_once 'config.php';
require_once 'functions.php';

$theme = getActiveTheme($pdo);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panier - SUNU</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        :root {
            --primary: <?php echo $theme['color_primary']; ?>;
            --secondary: <?php echo $theme['color_secondary']; ?>;
        }
        .cart-container { max-width: 900px; margin: 50px auto; padding: 20px; }
        .cart-table { width: 100%; border-collapse: collapse; background: white; border-radius: 10px; overflow: hidden; }
        .cart-table th, .cart-table td { padding: 15px; text-align: left; border-bottom: 1px solid #ddd; }
        .cart-table th { background: #f5f5f5; font-weight: bold; color: var(--primary); }
        .cart-summary { background: #f5f5f5; padding: 20px; border-radius: 10px; text-align: right; margin-top: 20px; }
        .btn-checkout { background: var(--primary); color: white; padding: 15px 40px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; font-weight: bold; }
        .btn-checkout:hover { background: var(--secondary); }
        .btn-remove { background: #e74c3c; color: white; padding: 8px 12px; border: none; border-radius: 3px; cursor: pointer; }
    </style>
</head>
<body>
    <header class="header">
        <div class="container">
            <a href="index.php" style="color: white; text-decoration: none; font-size: 24px;">← SUNU</a>
        </div>
    </header>

    <div class="cart-container">
        <h1 style="color: var(--primary);">🛒 Mon Panier</h1>
        <table class="cart-table">
            <tr>
                <th>Produit</th>
                <th>Prix</th>
                <th>Quantité</th>
                <th>Sous-total</th>
                <th>Action</th>
            </tr>
            <tbody id="cart-items"></tbody>
        </table>
        <div class="cart-summary">
            <h3>Total: <span id="total">0€</span></h3>
            <p id="total-qty" style="margin: 10px 0;"></p>
            <div id="warning" style="color: #e74c3c; margin: 10px 0; display: none;">⚠️ Attention: Plus de 5 maillots - Redirection WhatsApp</div>
            <button class="btn-checkout" onclick="checkout()" style="margin-top: 15px;">Valider la commande</button>
            <a href="index.php" style="display: inline-block; padding: 15px 30px; background: #999; color: white; text-decoration: none; border-radius: 5px; margin-left: 10px; margin-top: 15px;">Continuer le shopping</a>
        </div>
    </div>

    <script>
        function displayCart() {
            let cart = JSON.parse(localStorage.getItem('cart')) || [];
            let html = '';
            let total = 0;
            let totalQty = 0;

            if (cart.length === 0) {
                document.getElementById('cart-items').innerHTML = '<tr><td colspan="5" style="text-align: center; padding: 40px;">Votre panier est vide</td></tr>';
                document.getElementById('total').textContent = '0€';
                return;
            }

            cart.forEach((item, index) => {
                let subtotal = item.price * item.quantity;
                total += subtotal;
                totalQty += item.quantity;
                html += `<tr>
                    <td>${item.name}</td>
                    <td>${item.price.toFixed(2)}€</td>
                    <td><input type="number" value="${item.quantity}" min="1" max="5" onchange="updateQty(${index}, this.value)" style="width: 60px; padding: 8px;"></td>
                    <td>${subtotal.toFixed(2)}€</td>
                    <td><button onclick="removeItem(${index})" class="btn-remove">Supprimer</button></td>
                </tr>`;
            });

            document.getElementById('cart-items').innerHTML = html;
            document.getElementById('total').textContent = total.toFixed(2) + '€';
            document.getElementById('total-qty').textContent = 'Nombre d\'articles: ' + totalQty;
            
            if (totalQty > 5) {
                document.getElementById('warning').style.display = 'block';
            } else {
                document.getElementById('warning').style.display = 'none';
            }
        }

        function updateQty(index, qty) {
            let cart = JSON.parse(localStorage.getItem('cart'));
            qty = parseInt(qty);
            if (qty > 5) {
                alert('❌ Maximum 5 par type!');
                return;
            }
            if (qty > 0) {
                cart[index].quantity = qty;
                localStorage.setItem('cart', JSON.stringify(cart));
                displayCart();
            }
        }

        function removeItem(index) {
            let cart = JSON.parse(localStorage.getItem('cart'));
            cart.splice(index, 1);
            localStorage.setItem('cart', JSON.stringify(cart));
            displayCart();
        }

        function checkout() {
            let cart = JSON.parse(localStorage.getItem('cart'));
            let totalQty = cart.reduce((s, i) => s + i.quantity, 0);
            let totalPrice = cart.reduce((s, i) => s + (i.price * i.quantity), 0);

            if (cart.length === 0) {
                alert('❌ Votre panier est vide');
                return;
            }

            let phone = prompt('📱 Entrez votre numéro WhatsApp (ex: 212xxxxxxxxx):');
            if (!phone) return;

            let message = '📦 COMMANDE SUNU\\n\\n';
            cart.forEach(item => {
                message += `${item.name} x${item.quantity} = ${(item.price * item.quantity).toFixed(2)}€\\n`;
            });
            message += `\\nTOTAL: ${totalPrice.toFixed(2)}€\\nQuantité: ${totalQty}`;

            if (totalQty > 5) {
                message += '\\n\\n⚠️ COMMANDE IMPORTANTE (+ de 5 maillots)';
                window.open(`https://wa.me/212788617023?text=${encodeURIComponent(message)}`, '_blank');
                
                setTimeout(() => {
                    let notifMessage = `📢 Un client souhaite commander ${totalQty} maillots.\\nMessage envoyé au vendeur principal.`;
                    window.open(`https://wa.me/212789263556?text=${encodeURIComponent(notifMessage)}`, '_blank');
                }, 500);
            } else {
                window.open(`https://wa.me/212788617023?text=${encodeURIComponent(message)}`, '_blank');
            }

            localStorage.removeItem('cart');
            setTimeout(() => { 
                alert('✅ Merci pour votre commande! Vous allez être redirigé vers WhatsApp.');
                window.location.href = 'index.php'; 
            }, 1000);
        }

        displayCart();
    </script>
</body>
</html>