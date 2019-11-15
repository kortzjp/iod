<?php

require_once './core/Template.php';

class AlumnoView {

    public function home() {
        $usuario = $_SESSION['usuario'];
        $nombre = $_SESSION['nombre'];

        $header = file_get_contents("./public/html/alumno/alumno_header.html");
        $datosHeader = array('usuario' => $usuario, 'nombre' => $nombre);

        $tmpl = new Template($header);
        $header = $tmpl->render($datosHeader);

        $footer = file_get_contents("./public/html/alumno/alumno_footer.html");
        $contenido = file_get_contents("./public/html/alumno/alumno_home.html");
        print $header;
        print $contenido;
        print $footer;
    }

    /**
     * Muestra las asignaturas a las que se esta inscrito y el nombre del
     *  docente que la imparte 
     */
    public function evaluacion($mensaje = '', $cursos = array()) {
        $usuario = $_SESSION['usuario'];
        $nombre = $_SESSION['nombre'];

        $header = file_get_contents("./public/html/alumno/alumno_header.html");
        $datosHeader = array('usuario' => $usuario, 'nombre' => $nombre);

        $tmpl = new Template($header);
        $header = $tmpl->render($datosHeader);

        $footer = file_get_contents("./public/html/alumno/alumno_footer.html");

        if (empty($mensaje)) {
            $contenido = file_get_contents("./public/html/alumno/alumno_evaluacion.html");
            $datosContenido = array('mensaje' => '', 'tipo' => '');

            $tmpl = new Template($contenido);
            $contenido = $tmpl->render($datosContenido);

            $tmpl = new Template($contenido);
            $contenido = $tmpl->render_regex($cursos, "LISTA_CURSOS");
        } else {
            $contenido = file_get_contents("./public/html/alumno/evaluacion_contestada.html");
            $datosContenido = array('mensaje' => $mensaje, 'tipo' => 'callout-info');

            $tmpl = new Template($contenido);
            $contenido = $tmpl->render($datosContenido);
        }
        print $header;
        print $contenido;
        print $footer;
    }

    public function comenzarevaluacion($cursos, $preguntas) {
        $usuario = $_SESSION['usuario'];
        $nombre = $_SESSION['nombre'];

        $header = file_get_contents("./public/html/alumno/alumno_header.html");
        $datosHeader = array('usuario' => $usuario, 'nombre' => $nombre);

        $tmpl = new Template($header);
        $header = $tmpl->render($datosHeader);

        $footer = file_get_contents("./public/html/alumno/alumno_footer.html");

        $contenido = file_get_contents("./public/html/alumno/alumno_comenzarevaluacion.html");
        $datosContenido = array('mensaje' => '', 'tipo' => '');

        $tmpl = new Template($contenido);
        $contenido = $tmpl->render($datosContenido);

        $tmpl = new Template($contenido);
        $contenido = $tmpl->render_regex($cursos, "LISTA_CURSOS");

        $tmpl = new Template($contenido);
        $contenido = $tmpl->render_regex($preguntas, "LISTA_PREGUNTAS");

        print $header;
        print $contenido;
        print $footer;
    }

    public function crear($mensaje = '') {

        $contenido = file_get_contents("./public/html/carrera/carrera_crear.html");

        print $contenido;
    }

    public function agregar($totalAlumnos) {

        //echo "Mesnaje. se han agregado $totalAlumnos alumnos al curso.";
//        echo "<pre>";
//        print_r($_REQUEST);
//        echo "</pre>";
        $curso = recoge('curso');
        header("Location: /docente/lista_alumnos/$curso/agregado");
    }

    public function mostrar($carreras = array()) {

        if (empty($carreras)) {
            echo "No existen carreras";
        } else {
            $contenido = file_get_contents("./public/html/carrera/carrera_mostrar.html");
            $tmp = new Template($contenido);
            $contenido = $tmp->render_regex($carreras, "LISTA_CARRERAS");
            print $contenido;
        }
    }

    public function parcialesAlumnos($alumnos, $listaCursos,$modelo) {

        $usuario = $_SESSION['usuario'];
        $nombre = $_SESSION['nombre'];

        $header = file_get_contents("./public/html/".$modelo."/".$modelo."_header.html");
        $datosHeader = array('usuario' => $usuario, 'nombre' => $nombre);

        $tmpl = new Template($header);
        $header = $tmpl->render($datosHeader);

        $footer = file_get_contents("./public/html/".$modelo."/".$modelo."_footer.html");
        $contenido = file_get_contents("./public/html/".$modelo."/".$modelo."_resultado.html");

        $grupo = '';
        if (isset($_POST['grupo']) && !empty($_POST['grupo']))
            $grupo = ' DEL ' . $_POST['grupo'];
        $tipo = '';
        if (isset($_POST['estado']) && !empty($_POST['estado']))
            $tipo = ' EN ' . $_POST['estado'];

        $datos = array('grupo' => $grupo, 'tipo' => $tipo, 'nombre' => $alumnos[0]['nombre']);
        $objGrupo = (object) $datos;
        
        $tml = new Template($contenido);
        $contenido = $tml->render($objGrupo);

        $tmpl = new Template($contenido);
        $contenido = $tmpl->render_regex($listaCursos, "ASIGNATURAS");

        $tmpl = new Template($contenido);
        $contenido = $tmpl->render_regex($alumnos, "ALUMNOS");

        print $header;
        print $contenido;
        print $footer;
    }

    public function parciales($alumnos, $listaCursos,$modelo) {

        $usuario = $_SESSION['usuario'];
        $nombre = $_SESSION['nombre'];

        $header = file_get_contents("./public/html/".$modelo."/".$modelo."_header.html");
        $datosHeader = array('usuario' => $usuario, 'nombre' => $nombre);

        $tmpl = new Template($header);
        $header = $tmpl->render($datosHeader);

        $footer = file_get_contents("./public/html/".$modelo."/".$modelo."_footer.html");
        $contenido = file_get_contents("./public/html/".$modelo."/".$modelo."_resultado.html");

        $grupo = '';
        if (isset($_POST['grupo']) && !empty($_POST['grupo']))
            $grupo = ' DEL ' . $_POST['grupo'];
        $tipo = '';
        if (isset($_POST['estado']) && !empty($_POST['estado']))
            $tipo = ' EN ' . $_POST['estado'];

        $datos = array('grupo' => $grupo,
            'tipo' => $tipo);
        $objGrupo = (object) $datos;
        
        $tml = new Template($contenido);
        $contenido = $tml->render($objGrupo);

        $tmpl = new Template($contenido);
        $contenido = $tmpl->render_regex($listaCursos, "ASIGNATURAS");

        $tmpl = new Template($contenido);
        $contenido = $tmpl->render_regex($alumnos, "ALUMNOS");

        print $header;
        print $contenido;
        print $footer;
    }
    public function editar($carrera = array()) {

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
