<?php

require_once './core/HandlerSession.php';
require_once 'AdminView.php';

class AdminController {

    private $vista;

    function __construct($metodo, $arg) {

        $this->vista = new AdminView();

        if (method_exists($this, $metodo)) {
            call_user_func(array($this, $metodo), $arg);
        } else {
            print 'Recurso inexistente';
        }
    }

    public function home() {
        //echo "Home del administrador";
        HandlerSession()->check_session(4);
        $usuario = $_SESSION['usuario'];
        $nombre = $_SESSION['nombre'];
        $this->vista->home($usuario, $nombre);
    }

}
