<?php
$errorMessage = '';  // Инициализируем переменную для сообщений об ошибке

// Проверяем, была ли отправлена форма
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получаем данные из формы
    $login = trim($_POST['login'] ?? '');  // Очищаем от пробелов
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? ''; // Добавим confirm_password, если нужно

    // Проверка на пустоту
    if (empty($login) || empty($password)) {
        $errorMessage = "Все поля обязательны для заполнения.";
    } else {
        // Подключаем файл с классом Database
        require_once '/Applications/MAMP/htdocs/Airshop/internetShop/app/models/Database.php';

        // Настройки подключения
        $host = 'localhost';
        $dbname = 'AIRSHOP'; // Название базы данных
        $username = 'root';
        $dbpassword = 'root';

        // Создаем объект базы данных
        $database = new Database($host, $dbname, $username, $dbpassword);

        // Получаем объект PDO из класса Database
        $pdo = $database->getPDO();

        try {
            // Проверка существования логина
            $query = "SELECT ID, Login, Password, RoleID FROM Users WHERE Login = :login";
            $stmt = $pdo->prepare($query);
            $stmt->execute([':login' => $login]);

            // Проверяем, нашли ли пользователя
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['Password'])) {
                // Если логин и пароль совпали, авторизуем пользователя
                // Записываем данные в cookie
                setcookie("user_id", $user['ID'], time() + 3600, "/");   // Кука для ID пользователя
                setcookie("role_id", $user['RoleID'], time() + 3600, "/");  // Кука для роли пользователя
                setcookie("login", $user['Login'], time() + 3600, "/");  // Кука для логина

                // Перенаправляем на главную страницу (HomeIndex.php)
                header("Location: /Airshop/InternetShop/home/index.php");
                exit; // Прекращаем выполнение скрипта, чтобы предотвратить дальнейшие ошибки
            } else {
                // Если логин и пароль не совпадают, остаемся на текущей странице и выводим ошибку
                $errorMessage = "Неверный логин или пароль.";
            }
        } catch (PDOException $e) {
            $errorMessage = "Ошибка при подключении к базе данных: " . $e->getMessage();
        }
    }
    if (isset($errorMessage)) {
        header("Location: /Airshop/InternetShop/enter");
        exit();
    }
}

// Если есть сообщение об ошибке из формы авторизации
