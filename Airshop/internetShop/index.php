<?php
define("ROOT", dirname(__FILE__));
include_once ROOT."/core/Router.php";
// echo ROOT;
// include_once ROOT."/functions.php";

$router = new Router();
$router->run();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Internet Shop</title>
    <link rel="stylesheet" href="/Airshop/internetShop/public/css/style.css">
</head>
<body>
    <!-- Подключение header.php с использованием ROOT -->
   
    
    <main class="content">
        <div class="hero-section">
            <h1>Welcome to Our Online Shop!</h1>
            <p class="hero-description">Please choose your role.</p>
            <div class="buttons">
                 <a href="/Airshop/InternetShop/create?role=2" class="btn">Seller</a>
                 <a href="/Airshop/InternetShop/create?role=1" class="btn">Merchant</a>
            </div>
        </div>
    </main>
    
    <?php require ROOT . "/app/views/layouts/footer.php"; ?>
</body>
</html>
