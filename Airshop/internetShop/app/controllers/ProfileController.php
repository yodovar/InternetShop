<?php

class profileController{
    public function actionMyProfile(){
      require_once ROOT . '/app/views/profile/index.php';
      return true;
    }
}