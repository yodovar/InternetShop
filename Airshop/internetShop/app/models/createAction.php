<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
$roleID = $_COOKIE['RoleID'];

// Проверяем, была ли отправлена форма
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получаем данные из формы
    $login = $_POST['login'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Переменная для ошибок
    $errorMessage = '';
    // Проверка на пустоту
    if (empty($login) || empty($password) || empty($confirm_password)) {
        $errorMessage = "Все поля обязательны для заполнения.";
        header("Location: /Airshop/InternetShop/create.php?error=" . urlencode($errorMessage));
        exit;
    } else if ($password !== $confirm_password) {
        $errorMessage = "Пароли не совпадают.";
        header("Location: /Airshop/InternetShop/create.php?confirm_password_error=" . urlencode($errorMessage));
        exit;
    } else if (strlen(trim($login)) <= 5) {
        $errorMessage = "Логин должен содержать более 5 символов!";
        header("Location: /Airshop/InternetShop/create.php?login_error=" . urlencode($errorMessage));
        exit;
    } else if (strlen(trim($password)) <= 5) {
        $errorMessage = "Пароль должен содержать более 5 символов!";
        header("Location: /Airshop/InternetShop/create.php?password_error=" . urlencode($errorMessage));
        exit;
    } else if (!preg_match('/^(?=.*[a-zA-Z])(?=.*\d)[a-zA-Z\d]+$/', $password)) {
        $errorMessage = "Пароль должен содержать хотя бы одну букву и одну цифру (латинские буквы).";
        header("Location: /Airshop/InternetShop/create.php?password_error=" . urlencode($errorMessage));
        exit;
    }

    // Если ошибок нет, продолжаем выполнение
    require_once '/Applications/MAMP/htdocs/Airshop/internetShop/app/models/Database.php';

    // Настройки подключения
    $host = 'localhost';
    $dbname = 'Airshop';
    $username = 'root';
    $passwordDB = 'root';

    try {
        // Создание объекта базы данных
        $database = new Database($host, $dbname, $username, $passwordDB);
        $sellers = new Sellers($host, $dbname, $username, $passwordDB);
        $sellers->createTable();
        $sellers->insertData();
        $products = new TableforProducts($pdo);

        // Проверка на существование пользователя с таким логином
        $checkUserQuery = "SELECT * FROM Users WHERE Login = :login";
        $stmt = $database->getPDO()->prepare($checkUserQuery);
        $stmt->execute([':login' => $login]);
        $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);

        // Если такой пользователь уже существует
        if ($existingUser) {
            $errorMessage = "Пользователь с таким логином уже существует.";
            header("Location: /Airshop/InternetShop/create.php?error=" . urlencode($errorMessage));
            exit;
        }

        // Хэшируем пароль
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Вставляем нового пользователя в базу данных
        $insertUserQuery = "INSERT INTO Users (Login, Password, RoleID) VALUES (:login, :password, :roleID)";
        $stmt = $database->getPDO()->prepare($insertUserQuery);
        $stmt->execute([
            ':login' => $login,
            ':password' => $hashedPassword,
            ':roleID' => $roleID
        ]);

        // Получаем ID нового пользователя
        $userIDQuery = "SELECT ID FROM Users WHERE Login = :login";
        $stmt = $database->getPDO()->prepare($userIDQuery);
        $stmt->execute([':login' => $login]);
        $userID = $stmt->fetchColumn();

        if ($userID) {
            // Устанавливаем куки
            setcookie("user_id", $userID, time() + 3600, "/");   // Кука для ID пользователя
            setcookie("RoleID", $roleID, time() + 3600, "/");    // Кука для роли
            setcookie("login", $login, time() + 3600, "/");      // Кука для логина

            // Перенаправляем на главную страницу
            header("Location: /Airshop/InternetShop/home/index.php");
            exit();
        } else {
            echo "Ошибка: не удалось получить ID нового пользователя.";
        }
    } catch (PDOException $e) {
        echo "Ошибка: " . $e->getMessage();
        exit;
    }
}
?>