<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

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
    } else if(strlen(trim($login)) <= 5){
        $errorMessage = "Логин должен содержать более 5 символов!";
        header("Location: /Airshop/InternetShop/create.php?login_error=" . urlencode($errorMessage));
        exit;
    } else if(strlen(trim($password)) <= 5){
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

        // Проверка на существование пользователя с таким логином
        $checkUserQuery = "SELECT * FROM Users WHERE Login = :login";
        $stmt = $database->getPDO()->prepare($checkUserQuery);
        $stmt->execute([':login' => $login]);
        $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);

        // Если такой пользователь уже существует
        if ($existingUser) {
            $errorMessage = "Пользователь с таким логином уже существует.";
            header("Location: /Airshop/InternetShop/create.php");
            exit;
        }

        // Получаем роль пользователя из cookie
        $role = $_COOKIE['RoleID']; 

        // Вставка пользователя в таблицу
        $database->insertUser($login, $password, $role);

        // Создаем объект Sellers и выполняем действия
        $sellers = new Sellers($host, $dbname, $username, $passwordDB);
        $sellers->createTable();
        $sellers->insertData();
        $sellers->closeConnection();

    } catch (PDOException $e) {
        echo "Ошибка: " . $e->getMessage();
        exit;
    }

    // После успешной регистрации и обработки данных, выполняем редирект на главную страницу
    header("Location: /Airshop/InternetShop/home/index");
    exit;
}
?>