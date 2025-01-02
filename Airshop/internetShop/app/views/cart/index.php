<?php
session_start();

// Проверяем, существует ли корзина в сессии, если нет, то создаем её
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Функция для добавления товара в корзину
if (isset($_GET['id'])) {
    $productId = (int) $_GET['id'];

    // Проверяем, есть ли товар в корзине, если нет - добавляем
    if (!in_array($productId, $_SESSION['cart'])) {
        $_SESSION['cart'][] = $productId;  // Добавляем товар в корзину
    }
}

// Подключение к базе данных
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

// Получаем информацию о товарах, которые в корзине
class CartManager {
    public function getCartProducts($cart) {
        $db = Database::getConnection();
        $placeholders = implode(',', array_fill(0, count($cart), '?'));
        $stmt = $db->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
        $stmt->execute($cart);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

$cartManager = new CartManager();
$cartProducts = $cartManager->getCartProducts($_SESSION['cart']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - AirShop</title>
    <link rel="stylesheet" href="/Airshop/internetShop/public/css/cartStyle.css">
</head>
<body>

<div id="form-overlay" style="display: none;"></div>

<main>
    <!-- Страница корзины -->
    <section class="cart-section" id="cart-section">
        <h1>Your Shopping Cart</h1>
        
        <?php if (count($cartProducts) > 0): ?>
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Store</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Delete</th>
                        <th>Buy</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cartProducts as $product): ?>
                        <tr>
                            <td>
                                <img src="<?= $product['photo'] ?>" alt="<?= $product['category'] ?>" width="100">
                                <p><?= $product['Store'] ?></p>
                            </td>
                            <td><?= $product['Store'] ?></td>
                            <td><?= $product['category'] ?></td>
                            <td>$<?= $product['price'] ?></td>
                            <td>
                                <a href="/Airshop/InternetShop/cart/remove?id=<?= $product['id'] ?>" class="remove-item">Remove</a>
                             </td>
                             <td>
                                <a href="/Airshop/InternetShop/cart/remove?id=<?= $product['id'] ?>" class="buy-item">Buy</a>
                             </td>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <a href="/Airshop/InternetShop/checkout" class="btn">Proceed to Checkout</a>
        <?php else: ?>
            <p>Your cart is empty.</p>
        <?php endif; ?>
    </section>
</main>

<?php require ROOT . "/app/views/layouts/footer.php"; ?>

</body>
</html>