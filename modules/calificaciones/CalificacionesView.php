<?php

require_once './core/Template.php';

class CalificacionesView {

    public function home() {
 
                $str = file_get_contents('./public/html/calificaciones_resultado.html');
                $datos = array('grupo' => $_POST['grupo']);
                $objGrupo = (object) $datos;

                $tpml = new Template($str);
                $str = $tpml->render($objGrupo);

                $tmpl = new Template($str);
                $str = $tmpl->render_regex($lista, 'ASIGNATURAS');   // celdas de asignaturas ALUMNOS

                $tmpl = new Template($str);
                $str = $tmpl->render_regex($lista, 'GRAFICA_C1');

                $tmpl = new Template($str);
                $str = $tmpl->render($listaCursos); // numero de alumnos por curso.


    }

    public function mostrar_lista($lista, $datos) {
        $str = file_get_contents('./public/html/parcial/parcial_lista.html');

        $obj = (object) $datos;

        $tml = new Template($str);
        $str = $tml->render($obj);

        $tmpl = new Template($str);
        $contenido = $tmpl->render_regex($lista, 'ALUMNOS');   // celdas de alumnos ALUMNOS

        $this->parcial($contenido);
    }

    public function parcial($contenido) {
        $usuario = $_SESSION['usuario'];
        $nombre = $_SESSION['nombre'];

        $header = file_get_contents("./public/html/docente/docente_header.html");
        $datosHeader = array('usuario' => $usuario, 'nombre' => $nombre);

        $tmpl = new Template($header);
        $header = $tmpl->render($datosHeader);

        $footer = file_get_contents("./public/html/docente/docente_footer.html");

        print $header;
        print $contenido;
        print $footer;
    }

    public function resultados($lista, $datos ) {
        $str = file_get_contents('./public/html/parcial/parcial_final.html');
       
        $tmpl = new Template($str);
        $str = $tmpl->render($datos); // titulos de la página

        $tmpl = new Template($str);
        $contenido = $tmpl->render_regex($lista, 'ALUMNOS');   // celdas de alumnos ALUMNOS

        $this->parcial($contenido);
    }

    public function mensaje($tipo, $titulo, $mensaje) {

        $usuario = $_SESSION['usuario'];
        $nombre = $_SESSION['nombre'];

        $header = file_get_contents("./public/html/docente/docente_header.html");
        $datosHeader = array('usuario' => $usuario, 'nombre' => $nombre);

        $tmpl = new Template($header);
        $header = $tmpl->render($datosHeader);

        $footer = file_get_contents("./public/html/docente/docente_footer.html");
        $contenido = file_get_contents("./public/html/docente/docente_mensaje.html");

        $datos = array('tipo' => $tipo, 'titulo' => $titulo, 'mensaje' => $mensaje);

        $tmpl = new Template($contenido);
        $contenido = $tmpl->render($datos);

        print $header;
        print $contenido;
        print $footer;
    }

}
