<?php

require_once './core/Template.php';

class EvaluacionesView {

    public function home() {
        $usuario = $_SESSION['usuario'];
        $nombre = $_SESSION['nombre'];

        $header = file_get_contents("./public/html/docente/docente_header.html");
        $datosHeader = array('usuario' => $usuario, 'nombre' => $nombre);

        $tmpl = new Template($header);
        $header = $tmpl->render($datosHeader);

        $footer = file_get_contents("./public/html/docente/docente_footer.html");
        $contenido = file_get_contents("./public/html/docente/docente_home.html");
        print $header;
        print $contenido;
        print $footer;
    }

    public function mostrar_lista($lista, $dias) {
        $str = file_get_contents('./public/html/evaluaciones/evaluaciones_lista.html');


        $datos = array(
            'asignatura' => $_POST['asignatura']);
        $obj = (object) $datos;

        $tml = new Template($str);
        $str = $tml->render($obj);

        $tmpl = new Template($str);
        $str = $tmpl->render_regex($dias, 'DIA');   // celdas de encabezado DIA

        $tmpl = new Template($str);
        $str = $tmpl->render_regex($dias, 'DIAS');   // celdas de fecha de evaluaciones DIAS

        $tmpl = new Template($str);
        $contenido = $tmpl->render_regex($lista, 'ALUMNOS');   // celdas de alumnos ALUMNOS

        $this->evaluaciones($contenido);
    }

    public function evaluaciones($contenido) {
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

    public function resultados($lista) {
        $str = file_get_contents('./public/html/evaluaciones/evaluaciones_final.html');
        $datos = array(
            'asignatura' => $_POST['asignatura']);

        $tmpl = new Template($str);
        $str = $tmpl->render($datos); // titulos de la página

        $tmpl = new Template($str);
        $contenido = $tmpl->render_regex($lista, 'ALUMNOS');   // celdas de alumnos ALUMNOS

        $this->evaluaciones($contenido);
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
