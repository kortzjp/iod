<?php

require_once './core/Template.php';

class TutorView {

    public function home() {
        $usuario = $_SESSION['usuario'];
        $nombre = $_SESSION['nombre'];

        $header = file_get_contents("./public/html/tutor/tutor_header.html");
        $datosHeader = array('usuario' => $usuario, 'nombre' => $nombre);

        $tmpl = new Template($header);
        $header = $tmpl->render($datosHeader);

        $footer = file_get_contents("./public/html/tutor/tutor_footer.html");
        $contenido = file_get_contents("./public/html/tutor/tutor_home.html");
        print $header;
        print $contenido;
        print $footer;
    }

    public function listaCursos($cursos, $cuatrimestre, $asignaturas, $docentes, $mensaje, $tipo) {
        $usuario = $_SESSION['usuario'];
        $nombre = $_SESSION['nombre'];

        $header = file_get_contents("./public/html/tutor/tutor_header.html");
        $datosHeader = array('usuario' => $usuario, 'nombre' => $nombre);

        $tmpl = new Template($header);
        $header = $tmpl->render($datosHeader);

        $footer = file_get_contents("./public/html/tutor/tutor_footer.html");

        $datosContenido = array('mensaje' => $mensaje, 'tipo' => $tipo);
        $contenido = file_get_contents("./public/html/curso/tutor_cursos.html");

        $tmpl = new Template($contenido);
        $contenido = $tmpl->render($datosContenido);

        $tmpl = new Template($contenido);
        $contenido = $tmpl->render_regex($cursos, "LISTA_CURSOS");

        $tmpl = new Template($contenido);
        $contenido = $tmpl->render_regex($cuatrimestre, "LISTA_CUATRIMESTRE");

        $tmpl = new Template($contenido);
        $contenido = $tmpl->render_regex($asignaturas, "LISTA_ASIGNATURAS");

        $tmpl = new Template($contenido);
        $contenido = $tmpl->render_regex($docentes, "LISTA_DOCENTES");

        print $header;
        print $contenido;
        print $footer;
    }

    public function listaDocentes($asignaturas = array(), $mensaje, $tipo) {
        $usuario = $_SESSION['usuario'];
        $nombre = $_SESSION['nombre'];

        $header = file_get_contents("./public/html/tutor/tutor_header.html");
        $datosHeader = array('usuario' => $usuario, 'nombre' => $nombre);

        $tmpl = new Template($header);
        $header = $tmpl->render($datosHeader);

        $footer = file_get_contents("./public/html/tutor/tutor_footer.html");

        $datosContenido = array('mensaje' => $mensaje, 'tipo' => $tipo);
        $contenido = file_get_contents("./public/html/docente/docentes.html");

        $tmpl = new Template($contenido);
        $contenido = $tmpl->render($datosContenido);

        $tmpl = new Template($contenido);
        $contenido = $tmpl->render_regex($asignaturas, "LISTA_DOCENTES");

        $tmpl = new Template($contenido);
        $contenido = $tmpl->render_regex($asignaturas, "DOCENTES_EDITAR");

        print $header;
        print $contenido;
        print $footer;
    }
    
    public function listaGrupos($grupos = array(), $mensaje, $tipo) {
        $usuario = $_SESSION['usuario'];
        $nombre = $_SESSION['nombre'];

        $header = file_get_contents("./public/html/tutor/tutor_header.html");
        $datosHeader = array('usuario' => $usuario, 'nombre' => $nombre);

        $tmpl = new Template($header);
        $header = $tmpl->render($datosHeader);

        $footer = file_get_contents("./public/html/tutor/tutor_footer.html");

        $datosContenido = array('mensaje' => $mensaje, 'tipo' => $tipo);
        $contenido = file_get_contents("./public/html/reportes/reportes.html");

        $tmpl = new Template($contenido);
        $contenido = $tmpl->render($datosContenido);

        $tmpl = new Template($contenido);
        $contenido = $tmpl->render_regex($grupos, "GRUPOS");

        print $header;
        print $contenido;
        print $footer;
    }
    
    public function evaluaciones($grupos = array(), $mensaje, $tipo) {
        $usuario = $_SESSION['usuario'];
        $nombre = $_SESSION['nombre'];

        $header = file_get_contents("./public/html/tutor/tutor_header.html");
        $datosHeader = array('usuario' => $usuario, 'nombre' => $nombre);

        $tmpl = new Template($header);
        $header = $tmpl->render($datosHeader);

        $footer = file_get_contents("./public/html/tutor/tutor_footer.html");

        $datosContenido = array('mensaje' => $mensaje, 'tipo' => $tipo);
        $contenido = file_get_contents("./public/html/evaluaciones/evaluaciones.html");

        $tmpl = new Template($contenido);
        $contenido = $tmpl->render($datosContenido);

        $tmpl = new Template($contenido);
        $contenido = $tmpl->render_regex($grupos, "GRUPOS");

        print $header;
        print $contenido;
        print $footer;
    }

    public function listaAsignaturas($asignaturas = array(), $carrera, $mensaje, $tipo) {
        $usuario = $_SESSION['usuario'];
        $nombre = $_SESSION['nombre'];

        $header = file_get_contents("./public/html/tutor/tutor_header.html");
        $datosHeader = array('usuario' => $usuario, 'nombre' => $nombre);

        $tmpl = new Template($header);
        $header = $tmpl->render($datosHeader);

        $footer = file_get_contents("./public/html/tutor/tutor_footer.html");

        $datosContenido = array('carrera' => $carrera, 'mensaje' => $mensaje, 'tipo' => $tipo);
        $contenido = file_get_contents("./public/html/asignaturas/asignaturas.html");

        $tmpl = new Template($contenido);
        $contenido = $tmpl->render($datosContenido);

        $tmpl = new Template($contenido);
        $contenido = $tmpl->render_regex($asignaturas, "LISTA_ASIGNATURAS");

        $tmpl = new Template($contenido);
        $contenido = $tmpl->render_regex($asignaturas, "ASIGNATURAS_EDITAR");

        print $header;
        print $contenido;
        print $footer;
    }

    public function cuadrantes($grupos,$quincenas, $carrera,$cuatrimestre, $mensaje, $tipo){
        $usuario = $_SESSION['usuario'];
        $nombre = $_SESSION['nombre'];

        $header = file_get_contents("./public/html/tutor/tutor_header.html");
        $datosHeader = array('usuario' => $usuario, 'nombre' => $nombre);

        $tmpl = new Template($header);
        $header = $tmpl->render($datosHeader);

        $footer = file_get_contents("./public/html/tutor/tutor_footer.html");

        $datosContenido = array('carrera' => $carrera,'cuatrimestre' => $cuatrimestre, 'mensaje' => $mensaje, 'tipo' => $tipo);
        $contenido = file_get_contents("./public/html/cuadrantes/cuadrantes.html");

        $tmpl = new Template($contenido);
        $contenido = $tmpl->render($datosContenido);

        $tmpl = new Template($contenido);
        $contenido = $tmpl->render_regex($grupos, "GRUPOS");

        $tmpl = new Template($contenido);
        $contenido = $tmpl->render_regex($quincenas, "QUINCENAS");

        print $header;
        print $contenido;
        print $footer;
    }

    public function crear($carreras, $usuario, $nombre) {
        $header = file_get_contents("./public/html/admin/admin_header.html");
        $datosHeader = array('usuario' => $usuario, 'nombre' => $nombre);

        $tmpl = new Template($header);
        $header = $tmpl->render($datosHeader);

        $footer = file_get_contents("./public/html/admin/admin_footer.html");

        $contenido = file_get_contents("./public/html/tutor/tutor_crear.html");

        $tmpl = new Template($contenido);
        $contenido = $tmpl->render_regex($carreras, 'LISTA_CARRERAS');

        print $header;
        print $contenido;
        print $footer;
    }

    public function mensaje($tipo, $titulo, $mensaje) {
        $contenido = file_get_contents("./public/html/mensaje.html");
        $datos = array('tipo' => $tipo, 'titulo' => $titulo, 'mensaje' => $mensaje);

        $tmpl = new Template($contenido);
        $contenido = $tmpl->render($datos);

        print $header;
        print $contenido;
        print $footer;
    }

}
