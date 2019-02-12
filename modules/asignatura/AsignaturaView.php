<?php

require_once './core/Template.php';

class AsignaturaView {

    public function crear($mensaje = '') {

        $contenido = file_get_contents("./public/html/carrera/carrera_crear.html");

        print $contenido;
    }

    public function mostrar($carreras = array() ) {

        if (empty($carreras)) {
            echo "No existen carreras";
        } else {
            $contenido = file_get_contents("./public/html/carrera/carrera_mostrar.html");
            $tmp = new Template($contenido);
            $contenido = $tmp->render_regex($carreras, "LISTA_CARRERAS");
            print $contenido;
        }
    }
    
     public function editar( $carrera = array() ) {

        if (empty($carrera)) {
            echo "No existen carrera";
        } else {
            $contenido = file_get_contents("./public/html/carrera/carrera_editar.html");
            $tmp = new Template($contenido);
            $contenido = $tmp->render($carrera);
            print $contenido;
            
        }
    }

}
