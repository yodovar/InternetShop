<?php
// Подключение к базе данных
$host = 'localhost';
$dbname = 'AIRSHOP';
$user = 'root';
$pass = 'root';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Ошибка подключения к базе данных: " . $e->getMessage());
}

// Получение UserID из куки
$currentUserID = isset($_COOKIE['user_id']) ? (int)$_COOKIE['user_id'] : null;

if (!$currentUserID) {
    die("Ошибка: Не найден идентификатор пользователя.");
}

// Получение текущего логина пользователя
$stmt = $pdo->prepare("SELECT login FROM Users WHERE ID = :id");
$stmt->execute([':id' => $currentUserID]);
$currentLogin = $stmt->fetchColumn();

if (!$currentLogin) {
    die("Ошибка: Пользователь не найден.");
}

// Получение текущего имени магазина
$stmt = $pdo->prepare("SELECT Store FROM products WHERE UserID = :id");
$stmt->execute([':id' => $currentUserID]);
$currentStore = $stmt->fetchColumn();

// Если форма обновления профиля отправлена
if (isset($_POST['update_profile'])) {
    $newLogin = $_POST['login'];
    $newStore = $_POST['store'];
    $newPassword = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;

    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare("UPDATE Users SET login = :login WHERE ID = :id");
        $stmt->execute([':login' => $newLogin, ':id' => $currentUserID]);

        $stmt = $pdo->prepare("UPDATE Sellers SET login = :login WHERE ID = :id");
        $stmt->execute([':login' => $newLogin, ':id' => $currentUserID]);

        $stmt = $pdo->prepare("UPDATE Products SET Store = :store WHERE UserID = :id");
        $stmt->execute([':store' => $newStore, ':id' => $currentUserID]);

        if ($newPassword) {
            $stmt = $pdo->prepare("UPDATE Users SET password = :password WHERE ID = :id");
            $stmt->execute([':password' => $newPassword, ':id' => $currentUserID]);
        }

        $pdo->commit();
        $currentLogin = $newLogin;
        $currentStore = $newStore;
    } catch (PDOException $e) {
        $pdo->rollBack();
        die("Ошибка обновления данных: " . $e->getMessage());
    }
}

// Если форма добавления продукта отправлена
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $category = $_POST['category'];
    $description = $_POST['description'];
    $price = (int)$_POST['price'];
    $pub_id = random_int(100000000, 999999999);

    $photo = $_FILES['photo'];
    $photoName = '';

    if ($photo['error'] === 0) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array(mime_content_type($photo['tmp_name']), $allowedTypes)) {
            $uploadDir = '/Airshop/internetShop/uploads/';
            $photoName = $uploadDir . uniqid() . '_' . basename($photo['name']);
            if (!move_uploaded_file($photo['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . $photoName)) {
                die("Ошибка загрузки изображения.");
            }
        } else {
            die("Неверный формат изображения. Допустимы только JPEG, PNG и GIF.");
        }
    }

    $stmt = $pdo->prepare("INSERT INTO Products (Store, photo, category, description, price, pub_id, UserID) 
                           VALUES (:store, :photo, :category, :description, :price, :pub_id, :UserID)");
    $stmt->execute([
        ':store' => $currentStore,
        ':photo' => $photoName,
        ':category' => $category,
        ':description' => $description,
        ':price' => $price,
        ':pub_id' => $pub_id,
        ':UserID' => $currentUserID,
    ]);
}

// Получение всех записей из базы
$stmt = $pdo->prepare("SELECT * FROM Products WHERE UserID = :userID ORDER BY date_reg DESC");
$stmt->execute([':userID' => $currentUserID]);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    require ROOT . "/app/views/layouts/header.php"; 
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="/Airshop/internetShop/public/css/profileStyle.css">
</head>

<body>
    <center>
<div class="profile-section" id="profile-section">
    <button id="profile-toggle-btn">Изменить профиль</button>
    <form action="" method="POST" class="profile-form" id="profile-form" style="display: none;">
        <label for="login" id="label-login">Логин:</label>
        <input type="text" name="login" id="login" value="<?= htmlspecialchars($currentLogin) ?>" required><br><br>

        <label for="store" id="label-store">Имя магазина:</label>
        <input type="text" name="store" id="store" value="<?= htmlspecialchars($currentStore) ?>" required><br><br>

        <label for="password" id="label-password">Новый пароль (если нужно):</label>
        <input type="password" name="password" id="password"><br><br>

        <button type="submit" name="update_profile" id="submit-profile-btn">Изменить профиль</button>
    </form>
</div>

    <div class="add-product-section">
        <h1>Добавление продукта</h1>
        <form action="" method="POST" enctype="multipart/form-data" class="add-product-form">
    <label for="category">Категория:</label>
    <select name="category" id="category" required>
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
    </select><br><br>

    <label for="description">Описание:</label>
    <textarea name="description" id="description" required></textarea><br><br>

    <label for="price">Цена:</label>
    <input type="number" name="price" id="price" required><br><br>

    <label for="photo">Фото:</label>
    <input type="file" name="photo" id="photo" required><br><br>

    <button type="submit" name="add_product">Добавить продукт</button>
</form>
    </div>
    </center>

    <div class="products-section">
        <h2>Публикации:</h2>
        <div class="products-list">
            <?php foreach ($products as $product): ?>
                <div class="product-card">
                    <h3 class="product-store">Магазин: <?= htmlspecialchars($product['Store']) ?></h3>
                    <img src="<?= htmlspecialchars($product['photo']) ?>" alt="Фото продукта" class="product-photo">
                    <p class="product-price">Цена: <?= htmlspecialchars($product['price']) ?> сомони</p>
                    <p class="product-category">Категория: <?= htmlspecialchars($product['category']) ?></p>
                    <p class="product-description">Описание: <?= nl2br(htmlspecialchars($product['description'])) ?></p>
                    <p class="product-date">Дата публикации: <?= htmlspecialchars($product['date_reg']) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <script>
    document.addEventListener("DOMContentLoaded", () => {
    const profileToggleBtn = document.getElementById("profile-toggle-btn");
    const profileForm = document.getElementById("profile-form");

    profileToggleBtn.addEventListener("click", () => {
        // Переключаем видимость формы
        if (profileForm.style.display === "none" || profileForm.style.display === "") {
            profileForm.style.display = "block";
            profileToggleBtn.textContent = "Скрыть профиль";
        } else {
            profileForm.style.display = "none";
            profileToggleBtn.textContent = "Изменить профиль";
        }
    });
});
</script>
</body>
</html>