<?php
$errormessage = "";

class Database {
    private $pdo;

    public function __construct($host, $dbname, $username, $password) {
        try {
            $this->pdo = new PDO("mysql:host=$host;charset=utf8", $username, $password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->createDatabase();
            $this->pdo->exec("USE $dbname");
            $this->createUsersTable();
        } catch (PDOException $e) {
            global $errormessage;
            $errormessage = "Ошибка подключения к базе данных: " . $e->getMessage();
        }
    }

    public function getPDO() {
        return $this->pdo;
    }

    public function createDatabase() {
        $createBD = "CREATE DATABASE IF NOT EXISTS AIRSHOP CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci";
        $useDB = "USE AIRSHOP";
        try {
            $this->pdo->exec($createBD);
            $this->pdo->exec($useDB);
        } catch (PDOException $e) {
            global $errormessage;
            $errormessage = "Ошибка создания базы данных: " . $e->getMessage();
        }
    }

    private function createUsersTable() {
        $sql = "
        CREATE TABLE IF NOT EXISTS Users (
            ID INT AUTO_INCREMENT PRIMARY KEY,
            Login VARCHAR(50) NOT NULL UNIQUE,
            Password VARCHAR(255) NOT NULL,
            RoleID INT NOT NULL,
            DateReg TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            CHECK (RoleID IN (1, 2))
        ) ENGINE=InnoDB;
        ";
        try {
            $this->pdo->exec($sql);
        } catch (PDOException $e) {
            global $errormessage;
            $errormessage = "Ошибка создания таблицы 'Users': " . $e->getMessage();
        }
    }

    public function insertUser($login, $password, $roleID) {
        global $errormessage;
        $sql = "INSERT INTO Users (Login, Password, RoleID) VALUES (:login, :password, :roleID)";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':login' => $login,
                ':password' => password_hash($password, PASSWORD_DEFAULT), // Хешируем пароль для безопасности
                ':roleID' => $roleID
            ]);
        } catch (PDOException $e) {
            $errormessage = "Ошибка добавления пользователя: " . $e->getMessage();
        }
    }
}

class Sellers {
    private $pdo;

    public function __construct($host, $dbname, $username, $password) {
        try {
            $this->pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Ошибка подключения к базе данных: " . $e->getMessage());
        }
    }

    public function createTable() {
        $createTableQuery = "
            CREATE TABLE IF NOT EXISTS Sellers (
                ID INT AUTO_INCREMENT PRIMARY KEY,
                Login VARCHAR(50) NOT NULL UNIQUE,
                RoleID INT NOT NULL,
                DateReg TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB;
        ";
        try {
            $this->pdo->exec($createTableQuery);
        } catch (PDOException $e) {
            echo "Ошибка при создании таблицы 'Sellers': " . $e->getMessage();
        }
    }

    public function insertData() {
        $insertDataQuery = "
            INSERT INTO Sellers (Login, RoleID)
            SELECT Login, RoleID
            FROM Users
            WHERE RoleID = 2;
        ";
        try {
            $this->pdo->exec($insertDataQuery);
        } catch (PDOException $e) {
            echo "Ошибка при вставке данных в таблицу 'Sellers': " . $e->getMessage();
        }
    }

    public function closeConnection() {
        $this->pdo = null;
    }
}
?>