<?php

require_once "DataHandler.php";
require_once "HtmlElements.php";

class Logic {
    private $DataHandler;
    private $HtmlElements;

    public function __construct($dbName, $username, $pass, $serverAdress, $dbType) {
        $this->DataHandler = new DbHandler($dbName, $username, $pass, $serverAdress, $dbType);
        $this->HtmlElements = new HtmlElements();
        echo 'datahandler en htmlElements ';
        
    }

    public function __destruct() {
        $this->DataHandler = null;
    }
}