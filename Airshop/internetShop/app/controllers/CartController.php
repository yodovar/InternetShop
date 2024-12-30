<?php

class cartController{
    public function actionChoose(){
        require_once ROOT . '/app/views/cart/index.php';
        return true;
        echo "CARTHINA";
    }
}
