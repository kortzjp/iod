<?php
date_default_timezone_set('America/Mexico_City');

require_once './settings.php';

class FrontController {

    public function start() {

        $uri = $_SERVER['REQUEST_URI'];
        $datos = explode('/', $uri);  // separa un cadena, en un arreglo
        array_shift($datos);  // quita el primer elemento del arreglo

        $modulo = isset($datos[0]) && !empty($datos[0]) ? $datos[0] : 'usuario';
        $recurso = isset($datos[1]) ? $datos[1] : 'login';

        $arg = array();
        for ($i = 2; $i < count($datos); $i++) {
            $arg[] = $datos[$i];
        }

        $className = ucwords($modulo) . "Controller";

        $ruta = "modules/$modulo/" . $className . ".php";

        if (file_exists($ruta)) {
            require_once $ruta;
            $controller = new $className($recurso, $arg);
        } else {
            //echo "Recurso inexistente";
            header("Location: /");
        }
    }

}
