<?php 

class Router {
    private $controller;

    public function __Construct() {
        require_once 'controller/Controller.php';
        $this->controller = new Controller("pure_mvc", "root", "");
    }

    public function __Destruct() {
        $controller = null;
    }

    public function handleRequest() {

        if (isset($_GET['op']) ) {
            $op = $_GET['op'];

        } else {
            $op = "";
        }

        switch ($op) {
            case 'blanco':
                return $this->controller->calltoaction();
                break;


            default:
                return $this->controller->home();
                break;
        }
    }
}