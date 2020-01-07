<?php
require 'model/logic.php';
class Controller {
    private $Logic;

    public function __Construct($dbName, $username, $pass, $serverAdress = "localhost", $dbType = "mysql" ) {
        $this->Logic = new Logic($dbName, $username, $pass, $serverAdress, $dbType);
    }

    public function __Destruct() {
        $this->Logic = NULL;
    }

   public function home() {
    require_once 'view/home.php';
   }

}