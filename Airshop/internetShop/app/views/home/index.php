<?php
// Подключение к базе данных
$host = 'localhost';
$dbname = 'AIRSHOP';
$username = 'root';
$passwordDB = 'root';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $passwordDB);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Ошибка подключения к базе данных: " . $e->getMessage());
}

// Инициализация переменных фильтров
$category = isset($_GET['category']) ? $_GET['category'] : '';
$price_min = isset($_GET['price_min']) ? (int)$_GET['price_min'] : 0;
$price_max = isset($_GET['price_max']) ? (int)$_GET['price_max'] : 0;
$search = isset($_GET['search']) ? $_GET['search'] : '';
$store = isset($_GET['store']) ? $_GET['store'] : '';

// Стартуем SQL-запрос
$query = "SELECT * FROM products WHERE 1";

// Добавляем фильтрацию по категории, если она указана
if (!empty($category)) {
    $query .= " AND category = :category";
}

// Добавляем фильтрацию по цене, если указаны минимальные и максимальные цены
if ($price_min > 0) {
    $query .= " AND price >= :price_min";
}

if ($price_max > 0) {
    $query .= " AND price <= :price_max";
}

// Добавляем поиск по названию или описанию продукта
if (!empty($search)) {
    $query .= " AND (Store LIKE :search OR description LIKE :search)";
}

// Добавляем фильтрацию по магазину, если указано
if (!empty($store)) {
    $query .= " AND Store LIKE :store";
}

// Сортируем по дате регистрации по умолчанию, если фильтры не указаны
if (empty($category) && $price_min == 0 && $price_max == 0 && empty($search) && empty($store)) {
    $query .= " ORDER BY date_reg DESC";
}

// Выполняем запрос
$stmt = $pdo->prepare($query);

// Привязываем параметры к запросу
if (!empty($category)) {
    $stmt->bindParam(':category', $category);
}

if ($price_min > 0) {
    $stmt->bindParam(':price_min', $price_min, PDO::PARAM_INT);
}

if ($price_max > 0) {
    $stmt->bindParam(':price_max', $price_max, PDO::PARAM_INT);
}

if (!empty($search)) {
    $search_term = '%' . $search . '%';
    $stmt->bindParam(':search', $search_term);
}

if (!empty($store)) {
    $store_term = '%' . $store . '%';
    $stmt->bindParam(':store', $store_term);
}

// Выполняем запрос и получаем данные
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Фильтрация товаров</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            color: #333;
            margin: 0;
            padding: 0;
            transition: background-color 0.3s, color 0.3s;
        }

        body.dark-mode {
            background-color: #333;
            color: #f0f0f0;
        }

        .filter-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #444;
            padding: 10px 20px;
        }

        .filter-container input,
        .filter-container select,
        .filter-container button {
            padding: 8px;
            margin-right: 10px;
            border: 1px solid #666;
            border-radius: 4px;
            background-color: #333;
            color: white;
            font-size: 16px;
        }

        .filter-container input[type="text"] {
            width: 250px;
        }

        .filter-container button {
            cursor: pointer;
            background-color: #008cba;
            transition: background-color 0.3s ease;
        }

        .filter-container button:hover {
            background-color: #005f6b;
        }

        .product {
            background-color: #fff;
            border-radius: 8px;
            padding: 20px;
            margin: 20px;
            display: inline-block;
            width: calc(33.33% - 40px);
            box-sizing: border-box;
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            color: #333;
        }

        .product.dark-mode {
            background-color: #444;
            color: #f0f0f0;
        }

        .product:hover {
            transform: scale(1.05);
            box-shadow: 0px 6px 20px rgba(0, 0, 0, 0.2);
        }

        .product img {
            max-width: 100%;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .product h3 {
            font-size: 24px;
            font-weight: bold;
            color: #008cba;
            margin-top: 10px;
        }

        .product p {
            font-size: 16px;
            margin: 5px 0;
        }

        .product .price {
            font-size: 20px;
            color: #d9534f;
            font-weight: bold;
        }

        .product .date {
            font-size: 14px;
            color: #777;
            margin-top: 10px;
        }

        .product .description {
            font-size: 14px;
            color: #555;
            margin-top: 10px;
        }

        @media (max-width: 768px) {
            .product {
                width: calc(50% - 40px);
            }
        }

        @media (max-width: 480px) {
            .product {
                width: 100%;
            }
        }
    </style>
</head>
<body>

<!-- Форма фильтрации -->
<div class="filter-container">
    <form action="" method="GET" style="display: flex; align-items: center;">
        <input type="text" name="search" placeholder="Поиск товара..." value="<?php echo htmlspecialchars($search); ?>">
        <select name="category">
            <option value="">Все категории</option>
            <option value="Электроника">Электроника</option>
            <option value="Одежда и мода">Одежда и мода</option>
            <option value="Мебель">Мебель</option>
            <option value="Бытовая техника">Бытовая техника</option>
            <option value="Игрушки">Игрушки</option>
            <option value="Книги">Книги</option>
            <option value="Спортивные товары">Спортивные товары</option>
            <option value="Автомобили и мотоциклы">Автомобили и мотоциклы</option>
            <option value="Здоровье и красота">Здоровье и красота</option>
            <option value="Еда и напитки">Еда и напитки</option>
            <option value="Инструменты">Инструменты</option>
            <option value="Сад и огород">Сад и огород</option>
            <option value="Товары для животных">Товары для животных</option>
            <option value="Искусство и рукоделие">Искусство и рукоделие</option>
            <option value="Музыка">Музыка</option>
            <option value="Путешествия">Путешествия</option>
            <option value="Игры">Игры</option>
            <option value="Канцтовары">Канцтовары</option>
            <option value="Товары для детей">Товары для детей</option>
            <option value="Роскошь">Роскошь</option>
            <option value="Умный дом">Умный дом</option>
            <option value="Фитнес и здоровье">Фитнес и здоровье</option>
            <option value="Строительство">Строительство</option>
            <option value="Ювелирные изделия">Ювелирные изделия</option>
            <option value="Винтаж">Винтаж</option>
        </select>
        <input type="number" name="price_min" placeholder="Цена от" value="<?php echo $price_min; ?>" min="0">
        <input type="number" name="price_max" placeholder="Цена до" value="<?php echo $price_max; ?>" min="0">
        <button type="submit">Фильтровать</button>
    </form>

    <!-- Кнопка переключения тем -->
    <button onclick="toggleTheme()">Переключить тему</button>
</div>

<!-- Вывод товаров -->
<div class="product-container">
    <?php if ($products): ?>
        <?php foreach ($products as $product): ?>
            <div class="product">
                <img src="<?php echo $product['photo']; ?>" alt="<?php echo htmlspecialchars($product['Store']); ?>">
                <h3><?php echo htmlspecialchars($product['Store']); ?></h3>
                <p>Категория: <?php echo htmlspecialchars($product['category']); ?></p>
                <p class="price"><?php echo $product['price']; ?> руб.</p>
                <p class="date"><?php echo date('d-m-Y', strtotime($product['date_reg'])); ?></p>
                <p class="description"><?php echo htmlspecialchars($product['description']); ?></p>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Товары не найдены.</p>
    <?php endif; ?>
</div>

<script>
    function toggleTheme() {
        document.body.classList.toggle('dark-mode');
        var products = document.querySelectorAll('.product');
        products.forEach(function(product) {
            product.classList.toggle('dark-mode');
        });
    }
</script>

</body>
</html>