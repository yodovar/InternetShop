<?php
$errorMessage ="";
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AirShop - Авторизация</title>
    <link rel="stylesheet" href="/Airshop/internetShop/public/css/styleForAuth.css">
</head>
<body>

    <div class="container">
        <h1>Авторизация - AirShop</h1>

        <!-- Форма авторизации -->
        <form action="/Airshop/InternetShop/app/models/enterAction.php" method="POST">
            <div class="form-group">
                <label for="login">Логин</label>
                <input type="text" id="login" name="login" required>
            </div>

            <div class="form-group">
                <label for="password">Пароль</label>
                <input type="password" id="password" name="password" required>
            </div>

            <!-- Сообщение об ошибке, если оно есть -->
            <?php
            if ($errorMessage) {
                echo "<p class='error-message'>$errorMessage</p>";
            }
            ?>

            <div class="form-group">
                <button type="submit">Войти</button>
            </div>
        </form>

        <div class="footer">
            <p>Нет аккаунта? <a href="create.php">Зарегистрироваться</a></p>
        </div>
    </div>

</body>
</html>