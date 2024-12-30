<?php

class createController{
    public function actionNewaccount(){
      require_once ROOT . '/app/views/authorization/create.php';
      return true;
    }
}