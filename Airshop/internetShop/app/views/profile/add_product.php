<?php
require_once '/Applications/MAMP/htdocs/Airshop/internetShop/app/models/Database.php';

// Проверяем, был ли отправлен POST-запрос
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получение данных из формы
    $name = $_POST['name'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $image = $_FILES['image'];

    // Проверяем, загрузилось ли изображение
    if ($image['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '/path/to/upload/'; // Укажите директорию для загрузки
        $uploadFile = $uploadDir . basename($image['name']);

        if (move_uploaded_file($image['tmp_name'], $uploadFile)) {
            echo "File is valid and was successfully uploaded.\n";
        } else {
            echo "File upload failed.\n";
        }
    }

    // Сохранение данных в базу данных
    $db = new Database('localhost', 'AIRSHOP', 'root', 'root', $login, $password, $roleID);
    $pdo = $db->getPDO();

    $sql = "INSERT INTO products (Store, photo, category, description, price, pub_id, UserID) 
            VALUES (:store, :photo, :category, :description, :price, :pub_id, :user_id)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':store' => 'Demo Store', // Замените на данные магазина
        ':photo' => $uploadFile,
        ':category' => $category,
        ':description' => $description,
        ':price' => $price,
        ':pub_id' => time(), // Уникальный идентификатор
        ':user_id' => 1 // ID пользователя (пример)
    ]);

    echo "Product added successfully!";
    header('Location: index.php');
    exit;
} else {
    echo "Invalid request.";
}
?>