<?php
// Подключение к базе данных
require_once '/path/to/your/Database.php';

$data = json_decode(file_get_contents('php://input'), true);

// Проверяем, если все данные присутствуют
if (isset($data['name'], $data['category'], $data['description'], $data['price'], $data['image'], $data['userId'], $data['pubId'])) {
    $name = $data['name'];
    $category = $data['category'];
    $description = $data['description'];
    $price = $data['price'];
    $imageData = $data['image']; // Base64 строка изображения
    $userId = $data['userId'];
    $pubId = $data['pubId'];

    // Декодируем изображение и сохраняем его на сервере
    $imageName = uniqid() . '.png'; // Генерация уникального имени для изображения
    $imagePath = '/path/to/your/uploads/' . $imageName;
    file_put_contents($imagePath, base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $imageData)));

    // Создаем запрос на добавление товара в базу данных
    $query = "INSERT INTO products (UserID, Store, photo, category, description, price, pub_id) 
              VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        $userId,
        $name,
        $imageName, // Имя файла изображения
        $category,
        $description,
        $price,
        $pubId
    ]);

    // Возвращаем успешный ответ
    echo json_encode(['status' => 'success']);
} else {
    // Ошибка в данных
    echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
}
?>