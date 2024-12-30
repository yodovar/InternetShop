<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart - AirShop</title>
    <link rel="stylesheet" href="/Airshop/internetShop/public/css/homeStyle.css"> <!-- Подключаем стили с главной -->
<!-- Дополнительные стили для корзины -->
</head>
<body>
<main>
    <h1>Your Basket</h1>
    <!-- Секция с товарами -->
    <section id="cart-items" class="product-gallery"></section>

    <!-- Кнопки управления -->
    <div class="cart-actions">
        <button id="checkout-btn" class="btn">Buy All</button>
    </div>
</main>
<style>
    #checkout-btn{
    display: inline-block;
    background-color: #007bff;
    color: white;
    padding: 10px 15px;
    border: none;
    border-radius: 5px;
    text-decoration: none;
    cursor: pointer;
    position: center;
}
</style>
<script>
    // Загрузка товаров в корзине
    document.addEventListener("DOMContentLoaded", function () {
        loadCart();

        // Покупка всех товаров
        document.getElementById('checkout-btn').addEventListener('click', function () {
            if (confirm("Are you sure you want to buy all items?")) {
                localStorage.removeItem('cart');
                alert('Purchase successful!');
                loadCart();
            }
        });
    });

    function loadCart() {
        const cartItems = document.getElementById('cart-items');
        cartItems.innerHTML = ''; // Очищаем текущий список
        let cart = JSON.parse(localStorage.getItem('cart')) || [];

        if (cart.length === 0) {
            cartItems.innerHTML = '<p>Your cart is empty.</p>';
        } else {
            cart.forEach(item => {
                const cartItem = document.createElement('div');
                cartItem.classList.add('product-item'); // Используем тот же класс, что и на главной
                cartItem.innerHTML = `
                    <img src="${item.image}" alt="${item.name}">
                    <h3>${item.name}</h3>
                    <p>${item.category}</p>
                    <p class="product-price">$${parseFloat(item.price).toFixed(2)}</p>
                    <button class="btn remove-item-btn" data-id="${item.id}">Remove</button>
                     <button class="btn remove-item-btn" data-id="${item.id}">Buy</button>
                `;
                cartItems.appendChild(cartItem);
            });

            // Удаление товара из корзины
            document.querySelectorAll('.remove-item-btn').forEach(button => {
                button.addEventListener('click', function () {
                    removeFromCart(this.getAttribute('data-id'));
                    loadCart(); // Перезагрузка корзины
                });
            });
        }
    }

    // Удаление товара из LocalStorage
    function removeFromCart(id) {
        let cart = JSON.parse(localStorage.getItem('cart')) || [];
        cart = cart.filter(item => item.id != id); // Удаляем товар по ID
        localStorage.setItem('cart', JSON.stringify(cart));
    }
</script>
</body>
</html>