<?php

require_once './core/Template.php';

class ParcialView {

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

    public function resultados($lista, $datos) {
        $str = file_get_contents('./public/html/parcial/parcial_final.html');

        $tmpl = new Template($str);
        $str = $tmpl->render($datos); // titulos de la página

        $tmpl = new Template($str);
        $contenido = $tmpl->render_regex($lista, 'ALUMNOS');   // celdas de alumnos ALUMNOS

        $this->parcial($contenido);
    }

    public function parcialesAlumnos($alumnos, $listaCursos) {

        $usuario = $_SESSION['usuario'];
        $nombre = $_SESSION['nombre'];

        $header = file_get_contents("./public/html/tutor/tutor_header.html");
        $datosHeader = array('usuario' => $usuario, 'nombre' => $nombre);

        $tmpl = new Template($header);
        $header = $tmpl->render($datosHeader);

        $footer = file_get_contents("./public/html/tutor/tutor_footer.html");
        $contenido = file_get_contents("./public/html/parcial/parciales_resultado.html");

        $datos = array('grupo' => $_POST['grupo'],
            'tipo' => (!empty($_POST['estado']) ? ' EN ' . $_POST['estado'] : '' ));
        $objGrupo = (object) $datos;

        $tml = new Template($contenido);
        $contenido = $tml->render($objGrupo);

        $tmpl = new Template($contenido);
        $contenido = $tmpl->render_regex($listaCursos, "ASIGNATURAS");

        $tmpl = new Template($contenido);
        $contenido = $tmpl->render_regex($listaCursos, 'CURSOS_ID');

        $tmpl = new Template($contenido);
        $contenido = $tmpl->render_regex($alumnos, "ALUMNOS");

        print $header;
        print $contenido;
        print $footer;
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
