<?php

require_once './core/Template.php';

class CursoView {
    
    public function home( ){
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
    
        public function asistencias( $cursos = array() ){
        $usuario = $_SESSION['usuario'];
        $nombre = $_SESSION['nombre'];
        
        $header = file_get_contents("./public/html/docente/docente_header.html");
        $datosHeader = array('usuario' => $usuario, 'nombre' => $nombre);

        $tmpl = new Template($header);
        $header = $tmpl->render($datosHeader);
        
        $footer = file_get_contents("./public/html/docente/docente_footer.html");
        $contenido = file_get_contents("./public/html/asistencia/asistencia_home.html");
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
