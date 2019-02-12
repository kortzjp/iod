<?php

require_once './core/Template.php';

class CuatrimestreView {

    public function crear($datos = array(), $mensaje = '') {        

        $header = file_get_contents("./public/html/admin/admin_header.html");
        $datosHeader = array('usuario' => $_SESSION['usuario'], 'nombre' => $_SESSION['nombre']);

        $tmpl = new Template($header);
        $header = $tmpl->render($datosHeader);

        $contenido = file_get_contents("./public/html/cuatrimestre/cuatrimestre_crear.html");
        $tmpl = new Template($contenido);
        $contenido = $tmpl->render_regex($datos, "LISTA_CUATRIMESTRES");
        
        $footer = file_get_contents("./public/html/admin/admin_footer.html");

        print $header;
        print $contenido;
        print $footer;
        
    }

}
