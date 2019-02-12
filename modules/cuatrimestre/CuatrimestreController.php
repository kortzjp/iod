<?php

require_once './core/HandlerSession.php';
require_once 'CuatrimestreView.php';
require_once 'CuatrimestreModel.php';

class CuatrimestreController {

    private $vista;
    private $modelo;

    function __construct($metodo, $arg) {

        $this->vista = new CuatrimestreView();

        if (method_exists($this, $metodo)) {
            call_user_func(array($this, $metodo), $arg);
        } else {
            print 'Recurso inexistente';
        }
    }

    public function crear() {
        HandlerSession()->check_session(USER_ADMIN);

        $cuatrimestres = new CuatrimestreModel();
        $datos = $cuatrimestres->get();
        if (empty($datos)) {
            $this->vista->crear($datos, 'No hay cutrimestres registrados');
        } else {
            $listaCuatrimestres = array();
            for ($n = 0; $n < count($datos); $n++) {
                $registro = array('id' => $datos[$n]['id'],
                    'nombre' => $datos[$n]['nombre'],
                    'inicio' => $datos[$n]['inicio'],
                    'fin' => $datos[$n]['fin'],
                    'estado' => ($datos[$n]['estado'] == 0 ? 'Inactivo' : 'Activo'),
                    'estilo' => ($datos[$n]['estado'] == 0 ? 'danger' : 'success') );
                $obj = (object) $registro;
                $listaCuatrimestres[] = $obj;
            }
            $this->vista->crear($listaCuatrimestres, '');
        }
        //echo "Formulario para crear una Cuatrimestre";
    }

    public function guardar() {
        HandlerSession()->check_session(USER_ADMIN);

        $this->modelo = new CuatrimestreModel();
        $respuesta = $this->modelo->guardar();

        header('Location: /Cuatrimestre/confirmar/' . $respuesta);
    }

    public function confirmar($mensaje = '') {

        echo $mensaje;
    }

}
