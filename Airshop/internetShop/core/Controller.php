<?php
// core/Controller.php

// core/Controller.php

class Controller
{
    // Метод для рендеринга представлений
    public function render($viewName)
    {
        // Путь к файлу представления
        $viewPath = 'app/views/' . $viewName . '.php';

        // Проверяем, существует ли файл представления
        if (file_exists($viewPath)) {
            require_once $viewPath;
        } else {
            die("View not found: " . $viewPath);
        }
    }
}