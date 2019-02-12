<?php

require_once './core/HandlerSession.php';
require_once './core/handlers.php';
require_once 'AsignaturaView.php';
require_once 'AsignaturaModel.php';

class AsignaturaController {

    private $vista;
    private $modelo;

    function __construct($metodo, $arg) {

        $this->vista = new AsignaturaView();
        $this->modelo = new AsignaturaModel();

        if (method_exists($this, $metodo)) {
            call_user_func(array($this, $metodo), $arg);
        } else {
            print 'Recurso inexistente';
        }
    }

    public function home() {
        
    }

    public function mostrar() {

    }

    public function guardar() {
        HandlerSession()->check_session(USER_TUTOR);
        
        $id = recoge('id');
        $clave = recoge('clave');
        $nombre = recoge('nombre');
        $carrera = recoge('carrera');
        $cuatrimestre = recoge('cuatrimestre');

        if ($clave == '' || $nombre == '' || $carrera == '' || $cuatrimestre == '') {
            header('Location: /tutor/asignaturas/error');
        } else {
            $datos = array(
                'id' => $id, 
                'clave' => $clave, 
                'nombre' => $nombre, 
                'carrera' => $carrera, 
                'cuatrimestre' => $cuatrimestre );
            
            $respuesta = $this->modelo->edit( $datos );

            header('Location: /tutor/asignaturas/' . $respuesta);
        }
    }

    public function crear() {
        HandlerSession()->check_session(USER_TUTOR);
         
        $clave = recoge('clave');
        $nombre = recoge('nombre');
        $carrera = recoge('carrera');
        $cuatrimestre = recoge('cuatrimestre');

        if ($clave == '' || $nombre == '' || $carrera == '' || $cuatrimestre == '') {
            header('Location: /tutor/asignaturas/error');
        } else {
            $datos = array(
                'clave' => $clave, 
                'nombre' => $nombre, 
                'carrera' => $carrera, 
                'cuatrimestre' => $cuatrimestre );
            
            $respuesta = $this->modelo->set( $datos );

            header('Location: /tutor/asignaturas/' . $respuesta);
        }
    }

    public function editar($id) {

    }

    public function confirmar($mensaje = '') {

        echo $mensaje;
    }

}
