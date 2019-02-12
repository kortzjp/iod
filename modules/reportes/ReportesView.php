<?php

require_once './core/Template.php';

class ReportesView {

    public function grupos($grupos = array()) {
        $str = file_get_contents('./public/html/reportes/reportes.html');
        $tml = new Template($str);
        print $tml->render_regex($lista_grupos, "GRUPOS");
    }

    public function listaAsignatuas($lista, $listaCursos) {


        $str = file_get_contents('./public/html/calificaciones/calificaciones_resultado.html');
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

        return $str;
    }

    public function calificaciones($calificaciones, $pagina) {

        $tmpl = new Template($pagina);
        return $tmpl->render($calificaciones); // datos en el renglon de un curso.        
    }
    
    public function mostrar( $contenido ) {
        $usuario = $_SESSION['usuario'];
        $nombre = $_SESSION['nombre'];

        $header = file_get_contents("./public/html/tutor/tutor_header.html");
        $datosHeader = array('usuario' => $usuario, 'nombre' => $nombre);

        $tmpl = new Template($header);
        $header = $tmpl->render($datosHeader);

        $footer = file_get_contents("./public/html/tutor/tutor_footer.html");
        //$contenido = file_get_contents("./public/html/tutor/tutor_home.html");
        print $header;
        print $contenido;
        print $footer;
    }

}
