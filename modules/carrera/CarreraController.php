<?php

require_once './core/HandlerSession.php';
require_once 'CarreraView.php';
require_once 'CarreraModel.php';

class CarreraController {

    private $vista;
    private $modelo;

    function __construct($metodo, $arg) {

        $this->vista = new CarreraView();

        if (method_exists($this, $metodo)) {
            call_user_func(array($this, $metodo), $arg);
        } else {
            print 'Recurso inexistente';
        }
    }

    public function mostrar() {
        HandlerSession()->check_session(USER_ADMIN);

        $this->modelo = new CarreraModel();
        $respuesta = $this->modelo->darCarrera();

        $this->vista->mostrar($respuesta);
    }

    public function crear() {
        HandlerSession()->check_session(USER_ADMIN);
        $this->vista->crear();
        //echo "Formulario para crear una carrera";
    }

    public function guardar() {
        HandlerSession()->check_session( USER_ADMIN );

        $this->modelo = new CarreraModel();
        $respuesta = $this->modelo->guardar();

        header('Location: /carrera/confirmar/' . $respuesta);
    }

    public function editar($id) {
        HandlerSession()->check_session( USER_ADMIN );

        $this->modelo = new CarreraModel();
        $respuesta = $this->modelo->darCarrera($id);
        $this->vista->editar( $respuesta[0]);
    }

    public function confirmar($mensaje = '') {

        echo $mensaje;
    }

}
