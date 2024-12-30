<?php
    require ROOT . "/app/views/layouts/header.php"; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - AirShop</title>
    <link rel="stylesheet" href="/Airshop/internetShop/public/css/homeStyle.css">
</head>
<body>

<div id="form-overlay" style="display: none;"></div>

<main>
    <!-- Галерея товаров -->
    <section class="product-gallery" id="product-gallery"></section>

    <!-- Кнопка добавления нового товара -->
    <button id="add-product-btn" class="add-product-button">+</button>

    <!-- Форма добавления нового товара -->
    <div id="add-product-form" style="display: none;">
        <h4>Add a New Product</h4>
        <label for="product-name">Product Name:</label>
        <input type="text" id="product-name" placeholder="Enter product name">
        <label for="product-category">Category:</label>
        <input type="text" id="product-category" placeholder="Enter product category">
        <label for="product-price">Price:</label>
        <input type="number" id="product-price" placeholder="Enter product price" step="0.01">
        <label for="product-image">Product Image:</label>
        <input type="file" id="product-image" accept="image/*">
        <button id="submit-product-btn" class="submit-product-button">Add Product</button>
    </div>

    <!-- Кнопка перехода в корзину -->
    <a href="/Airshop/InternetShop/cart/index" class="btn" id="view-cart-btn">View Cart</a>
</main>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Загружаем сохраненные продукты из LocalStorage
        loadProductsFromLocalStorage();

        const addProductBtn = document.getElementById('add-product-btn');
        const addProductForm = document.getElementById('add-product-form');
        const formOverlay = document.getElementById('form-overlay');

        // Открытие формы
        addProductBtn.addEventListener('click', function () {
            addProductForm.style.display = 'block';
            formOverlay.style.display = 'block';
        });

        // Закрытие формы при клике на overlay
        formOverlay.addEventListener('click', function () {
            addProductForm.style.display = 'none';
            formOverlay.style.display = 'none';
        });

        // Добавление нового товара
        document.getElementById('submit-product-btn').addEventListener('click', function () {
            const name = document.getElementById('product-name').value;
            const category = document.getElementById('product-category').value;
            const price = document.getElementById('product-price').value;
            const imageInput = document.getElementById('product-image');
            const image = imageInput.files[0];

            if (name && category && price && image) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const newProduct = {
                        id: Date.now(), // Уникальный ID для каждого продукта
                        name: name,
                        category: category,
                        price: price,
                        image: e.target.result
                    };

                    saveProductToLocalStorage(newProduct);
                    addProductToGallery(newProduct);

                    // Очистка формы
                    document.getElementById('product-name').value = '';
                    document.getElementById('product-category').value = '';
                    document.getElementById('product-price').value = '';
                    imageInput.value = '';
                    addProductForm.style.display = 'none';
                    formOverlay.style.display = 'none';
                };
                reader.readAsDataURL(image);
            } else {
                alert('Please fill in all fields.');
            }
        });

        // Используем делегирование событий для кнопок "Add to Cart"
        document.getElementById('product-gallery').addEventListener('click', function (e) {
            if (e.target && e.target.classList.contains('add-to-cart-btn')) {
                const productId = e.target.getAttribute('data-id');
                const product = getProductFromLocalStorage(productId);
                if (product) {
                    addToCart(product);
                }
            }
        });
    });

    // Сохранение продукта в LocalStorage
    function saveProductToLocalStorage(product) {
        let products = JSON.parse(localStorage.getItem('products')) || [];
        products.push(product);
        localStorage.setItem('products', JSON.stringify(products));
    }

    // Загрузка продуктов из LocalStorage
    function loadProductsFromLocalStorage() {
        let products = JSON.parse(localStorage.getItem('products')) || [];
        products.forEach(product => addProductToGallery(product));
    }

    // Добавление продукта в галерею
    function addProductToGallery(product) {
        const productGallery = document.getElementById('product-gallery');
        const newProductItem = document.createElement('div');
        newProductItem.classList.add('product-item');
        newProductItem.innerHTML = `
            <img src="${product.image}" alt="${product.name}">
            <h3>${product.name}</h3>
            <p>${product.category}</p>
            <p class="product-price">$${parseFloat(product.price).toFixed(2)}</p>
            <button class="add-to-cart-btn" data-id="${product.id}">Add to Cart</button>
        `;
        productGallery.appendChild(newProductItem);
    }

    // Получение продукта из LocalStorage по ID
    function getProductFromLocalStorage(id) {
        let products = JSON.parse(localStorage.getItem('products')) || [];
        return products.find(product => product.id == id);
    }

    // Добавление товара в корзину
    function addToCart(product) {
        let cart = JSON.parse(localStorage.getItem('cart')) || [];
        cart.push(product);
        localStorage.setItem('cart', JSON.stringify(cart));
        alert(`${product.name} has been added to the cart!`);
    }
</script>

<?php
    require ROOT . "/app/views/layouts/footer.php"; 
?>
</body>
</html>