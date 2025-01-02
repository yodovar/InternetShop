<?php
// Получаем роль из cookie или сессии
session_start();

// Получение roleId из cookie
$roleId = isset($_COOKIE['RoleID']) ? $_COOKIE['RoleID'] : null;

// Настройки подключения к базе данных
class Database {
    private static $instance = null;
    private $connection;

    private function __construct() {
        $this->connection = new PDO('mysql:host=localhost;dbname=Airshop', 'root', 'root');
        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public static function getConnection() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance->connection;
    }
}

// Класс для работы с товарами
class ProductManager {
    private $roleId;

    public function __construct($roleId) {
        $this->roleId = $roleId;
    }

    // Получение всех товаров из базы данных
    public function getAllProducts() {
        $db = Database::getConnection();  // Получаем подключение к базе данных
        $query = "SELECT * FROM products";  // SQL-запрос для получения всех продуктов
        $stmt = $db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);  // Возвращаем все товары как ассоциативный массив
    }
}

require ROOT . "/app/views/layouts/header.php"; 

// Создаем экземпляр класса ProductManager
$productManager = new ProductManager($roleId);

// Получаем все продукты
$products = $productManager->getAllProducts();
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
    <section class="product-gallery" id="product-gallery">
        <?php
            // Выводим все продукты
            if ($products) {
                foreach ($products as $product) {
                    echo '<div class="product-item">';
                    echo '<h4>№ : ' . $product['id'] . '</h4><br />';
                    echo '<h4>Магазин: ' . $product['Store'] . '</h4><br />';
                    echo '<img src="' . $product['photo'] . '" alt="' . $product['category'] . '">';
                    echo '<h5>' . $product['category'] . '</h5>';
                    echo '<p>' . $product['description'] . '</p>';
                    echo '<p>Price: $' . $product['price'] . '</p>';
                    // Добавляем ссылку с параметром id товара
                    echo '<a href="/Airshop/InternetShop/cart/add?id=' . $product['id'] . '" class="add-to-cart">Add to Cart</a>';
                    echo '</div>';
                }
            } else {
                echo '<p>No products available.</p>';
            }
        ?>
    </section>

    <!-- Кнопка перехода в корзину -->
    <a href="/Airshop/InternetShop/cart/index" class="btn" id="view-cart-btn">View Cart</a>
</main>

<?php
    require ROOT . "/app/views/layouts/footer.php"; 
?>

</body>
</html>