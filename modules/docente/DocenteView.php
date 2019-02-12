<?php

require_once './core/Template.php';

class DocenteView {

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

    public function alumnos($cursos = array(), $mensaje, $tipo) {
        $usuario = $_SESSION['usuario'];
        $nombre = $_SESSION['nombre'];

        $header = file_get_contents("./public/html/docente/docente_header.html");
        $datosHeader = array('usuario' => $usuario, 'nombre' => $nombre);

        $tmpl = new Template($header);
        $header = $tmpl->render($datosHeader);

        $footer = file_get_contents("./public/html/docente/docente_footer.html");
        $contenido = file_get_contents("./public/html/docente/docente_alumnos.html");

        $datosContenido = array('mensaje' => $mensaje, 'tipo' => $tipo);

        $tmpl = new Template($contenido);
        $contenido = $tmpl->render($datosContenido);

        $tmpl = new Template($contenido);
        $contenido = $tmpl->render_regex($cursos, "LISTA_CURSOS");


        print $header;
        print $contenido;
        print $footer;
    }

    public function lista_alumnos_eliminar($alumnos, $carreras, $datosContenido) {
        $usuario = $_SESSION['usuario'];
        $nombre = $_SESSION['nombre'];

        $header = file_get_contents("./public/html/docente/docente_header.html");
        $datosHeader = array('usuario' => $usuario, 'nombre' => $nombre);

        $tmpl = new Template($header);
        $header = $tmpl->render($datosHeader);

        $footer = file_get_contents("./public/html/docente/docente_footer.html");
        $contenido = file_get_contents("./public/html/docente/docente_lista.html");
        $contenido .= file_get_contents("./public/html/docente/docente_lista_eliminar.html");


        $tmpl = new Template($contenido);
        $contenido = $tmpl->render($datosContenido);

        $tmpl = new Template($contenido);
        $contenido = $tmpl->render_regex($carreras, "LISTA_CARRERAS");

        $tmpl = new Template($contenido);
        $contenido = $tmpl->render_regex($alumnos, "LISTA_ALUMNOS");

        print $header;
        print $contenido;
        print $footer;
    }

    public function lista_alumnos_agregar($alumnos, $carreras, $datosContenido) {
        $usuario = $_SESSION['usuario'];
        $nombre = $_SESSION['nombre'];

        $header = file_get_contents("./public/html/docente/docente_header.html");
        $datosHeader = array('usuario' => $usuario, 'nombre' => $nombre);

        $tmpl = new Template($header);
        $header = $tmpl->render($datosHeader);

        $footer = file_get_contents("./public/html/docente/docente_footer.html");
        $contenido = file_get_contents("./public/html/docente/docente_lista.html");
        $contenido .= file_get_contents("./public/html/docente/docente_lista_agregar.html");


        $tmpl = new Template($contenido);
        $contenido = $tmpl->render($datosContenido);

        $tmpl = new Template($contenido);
        $contenido = $tmpl->render_regex($carreras, "LISTA_CARRERAS");

        $tmpl = new Template($contenido);
        $contenido = $tmpl->render_regex($alumnos, "LISTA_ALUMNOS");

        print $header;
        print $contenido;
        print $footer;
    }

    public function asistencias($cursos = array(), $datosMes, $mensaje, $tipo) {
        $usuario = $_SESSION['usuario'];
        $nombre = $_SESSION['nombre'];

        $header = file_get_contents("./public/html/docente/docente_header.html");
        $datosHeader = array('usuario' => $usuario, 'nombre' => $nombre);

        $tmpl = new Template($header);
        $header = $tmpl->render($datosHeader);

        $footer = file_get_contents("./public/html/docente/docente_footer.html");
        $contenido = file_get_contents("./public/html/docente/docente_asistencias.html");

        $datosContenido = array('mensaje' => $mensaje, 'tipo' => $tipo);

        $tmpl = new Template($contenido);
        $contenido = $tmpl->render($datosContenido);

        $tmpl = new Template($contenido);
        $contenido = $tmpl->render($datosMes);

        $tmpl = new Template($contenido);
        $contenido = $tmpl->render_regex($cursos, "LISTA_CURSOS");


        print $header;
        print $contenido;
        print $footer;
    }
    
    public function evaluaciones($cursos = array(), $mensaje, $tipo) {
        $usuario = $_SESSION['usuario'];
        $nombre = $_SESSION['nombre'];

        $header = file_get_contents("./public/html/docente/docente_header.html");
        $datosHeader = array('usuario' => $usuario, 'nombre' => $nombre);

        $tmpl = new Template($header);
        $header = $tmpl->render($datosHeader);

        $footer = file_get_contents("./public/html/docente/docente_footer.html");
        $contenido = file_get_contents("./public/html/docente/docente_evaluaciones.html");

        $datosContenido = array('mensaje' => $mensaje, 'tipo' => $tipo);

        $tmpl = new Template($contenido);
        $contenido = $tmpl->render($datosContenido);

        $tmpl = new Template($contenido);
        $contenido = $tmpl->render_regex($cursos, "LISTA_CURSOS");


        print $header;
        print $contenido;
        print $footer;
    }
    
    public function parciales($cursos = array(), $mensaje, $tipo) {
        $usuario = $_SESSION['usuario'];
        $nombre = $_SESSION['nombre'];

        $header = file_get_contents("./public/html/docente/docente_header.html");
        $datosHeader = array('usuario' => $usuario, 'nombre' => $nombre);

        $tmpl = new Template($header);
        $header = $tmpl->render($datosHeader);

        $footer = file_get_contents("./public/html/docente/docente_footer.html");
        $contenido = file_get_contents("./public/html/docente/docente_parciales.html");

        $datosContenido = array('mensaje' => $mensaje, 'tipo' => $tipo);

        $tmpl = new Template($contenido);
        $contenido = $tmpl->render($datosContenido);

        $tmpl = new Template($contenido);
        $contenido = $tmpl->render_regex($cursos, "LISTA_CURSOS");


        print $header;
        print $contenido;
        print $footer;
    }

    public function lista_asistencias($alumnos = 0, $carreras = 0, $datosContenido = 0) {
        $usuario = $_SESSION['usuario'];
        $nombre = $_SESSION['nombre'];

        $header = file_get_contents("./public/html/docente/docente_header.html");
        $datosHeader = array('usuario' => $usuario, 'nombre' => $nombre);

        $tmpl = new Template($header);
        $header = $tmpl->render($datosHeader);

        $footer = file_get_contents("./public/html/docente/docente_footer.html");
        $contenido = file_get_contents("./public/html/docente/docente_lista_asistencias.html");
//        $contenido .= file_get_contents("./public/html/docente/docente_lista_eliminar.html");
//        
//        
//        $tmpl = new Template($contenido);
//        $contenido = $tmpl->render($datosContenido);
//        
//        $tmpl = new Template($contenido);
//        $contenido = $tmpl->render_regex($carreras,"LISTA_CARRERAS");
//        
//        $tmpl = new Template($contenido);
//        $contenido = $tmpl->render_regex($alumnos,"LISTA_ALUMNOS");

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
