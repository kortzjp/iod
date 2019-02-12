<?php

require_once './core/Template.php';

class CuadrantesView {

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

    public function cuadrantes($asignaturas, $listaCursos, $objGrupo) {

        $str = file_get_contents('./public/html/cuadrantes/cuadrantes_resultado.html');


        $tmpl = new Template($str);
        $str = $tmpl->render_regex($asignaturas, 'ASIGNATURAS');   // celdas de asignaturas ALUMNOS

        $tmpl = new Template($str);
        $str = $tmpl->render_regex($asignaturas, 'GRAFICA_C1');

        $tmpl = new Template($str);
        $str = $tmpl->render($listaCursos); // numero de alumnos por curso.

        $tpml = new Template($str);
        $str = $tpml->render($objGrupo);
        $tpml = new Template($str);
        $str = $tpml->render($objGrupo);

        return $str;
    }

    public function datosRengloCurso($pagina, $datos) {
        $tmpl = new Template($pagina);
        $str = $tmpl->render($datos); // datos en el renglon de un curso.

        return $str;
    }

    public function mostrarCuadrantes($pagina) {
        $usuario = $_SESSION['usuario'];
        $nombre = $_SESSION['nombre'];

        $header = file_get_contents("./public/html/tutor/tutor_header.html");
        $datosHeader = array('usuario' => $usuario, 'nombre' => $nombre);

        $tmpl = new Template($header);
        $header = $tmpl->render($datosHeader);

        $footer = file_get_contents("./public/html/tutor/tutor_footer.html");

        print $header;
        print $pagina;
        print $footer;
    }

}
