<?php

class enterController{
    public function actionJoin(){
      require_once ROOT . '/app/views/authorization/enter.php';
      return true;
    }
}