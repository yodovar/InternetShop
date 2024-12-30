<?php

class homeController{
    public function actionInformation(){
      require_once ROOT . '/app/views/home/index.php';
      return true;
    }
}