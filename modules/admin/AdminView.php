<?php

require_once './core/Template.php';

class AdminView {

    public function home($usuario, $nombre) {
        $header = file_get_contents("./public/html/admin/admin_header.html");
        $datosHeader = array('usuario' => $usuario, 'nombre' => $nombre);

        $tmpl = new Template($header);
        $header = $tmpl->render($datosHeader);

        $contenido = file_get_contents("./public/html/admin/admin_home.html");
        $footer = file_get_contents("./public/html/admin/admin_footer.html");

        print $header;
        print $contenido;
        print $footer;
    }

}
