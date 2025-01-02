<?php
if (isset($_GET['role'])) {
    $role = $_GET['role'];

    // Проверяем, что это за роль, и устанавливаем соответствующую куку
    if ($role == 2) {
        // Устанавливаем куки для роли Селлера
        setcookie('RoleID', 2, time() + 36000, '/'); // Кука будет жить 1 час
    } elseif ($role == 1) {
        // Устанавливаем куки для роли Мерчанта
        setcookie('RoleID', 1, time() + 36000, '/');
    }

    // После установки куки перенаправляем на нужную страницу
}

$errorMessage = '';

if (isset($_GET['error'])) {
    $errorMessage = $_GET['error'];
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AirShop - Регистрация</title>
    <link rel="stylesheet" href="/Airshop/internetShop/public/css/styleForAuth.css">
</head>
<body>
    <div class="container">
        <h1>Регистрация - AirShop</h1>

        <!-- Форма регистрации -->
        <form action="/Airshop/InternetShop/app/models/createAction.php" method="POST">
            <div class="form-group">
                <label for="login">Логин</label>
                <input type="text" id="login" name="login" required>
                <!-- Сообщение об ошибке для логина -->
                <?php if (isset($_GET['login_error'])): ?>
                    <p class="error-message"><?= htmlspecialchars($_GET['login_error']) ?></p>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="password">Пароль</label>
                <input type="password" id="password" name="password" required>
                <small style="color: darkgoldenrod;" class="error-message">Используйте латинские буквы и цифры обязательно!</small>
                <!-- Сообщение об ошибке для пароля -->
                <?php if (isset($_GET['password_error'])): ?>
                    <p class="error-message"><?= htmlspecialchars($_GET['password_error']) ?></p>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="confirm_password">Подтверждение пароля</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
                <!-- Сообщение об ошибке для подтверждения пароля -->
                <?php if (isset($_GET['confirm_password_error'])): ?>
                    <p class="error-message"><?= htmlspecialchars($errorMessage) ?></p>
                <?php endif; ?>
            </div>

            <!-- Общие сообщения об ошибке -->
            <?php if ($errorMessage): ?>
                <p class="error-message"><?= htmlspecialchars($errorMessage) ?></p>
            <?php endif; ?>

            <div class="form-group">
                <button type="submit">Зарегистрироваться</button>
            </div>
        </form>

        <div class="footer">
        <?php if ($_COOKIE['RoleID'] == "" || $_COOKIE['RoleID'] == 1): ?>

            <p> если вы продавец<a href="create.php?role=2">Продавец</a></p>

            <?php endif;?>

            <p>Уже есть аккаунт? <a href="enter.php">Войти</a></p>
        </div>
    </div>
</body>
</html>