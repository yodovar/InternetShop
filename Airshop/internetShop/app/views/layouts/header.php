<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Airshop</title>
    <link rel="stylesheet" href="/Airshop/internetShop/public/css/style.css">
</head>
<body>
<header class="navbar">
    <div class="logo">
        <h1>Airshop</h1>
    </div>
    <nav class="nav-links">
        <ul>
            <li><a href="home/index">Home</a></li>
            <li><a href="product/index">Product</a></li>
            <li><a href="cart/index">Cart</a></li>
            <li><a href="about/index">About</a></li>
            <?php if (isset($_COOKIE['RoleID']) && $_COOKIE['RoleID'] == 2): ?>

            <li><a href="/Airshop/internetShop/profile/index">Profile</a></li>
            
            <?php endif; ?>
        </ul>
    </nav>
    <div class="menu-icon" id="menu-icon">
        <i class="fas fa-bars"></i>
    </div>
</header>






