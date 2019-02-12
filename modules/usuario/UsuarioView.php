<?php

require_once './core/Template.php';

class UsuarioView {

    public function login($error) {
        $contenido = file_get_contents("./public/html/login.html");

        if ( $error == "") {
            $datos = array('display' => 'none', 'error' => '');
        } else {
            $datos = array('display' => 'on', 'error' => 'Datos incorrectos.');
        }
        $tmpl = new Template($contenido);
        $contenido = $tmpl->render($datos);

        print $contenido;
    }

    public function recupera($error) {
        if (isset($_POST['email'])) {
            $contenido = file_get_contents("./public/html/correo.html");
        } else {
            $contenido = file_get_contents("./public/html/recupera.html");
        }
        if (empty($error)) {
            $datos = array('error' => '');
        } else {
            $datos = array('error' => 'El correo electrónico No existe.' );
        }
        $tmpl = new Template($contenido);
        $contenido = $tmpl->render($datos);

        print $contenido;
    }

    public function correo($correo) {

        $contenido = file_get_contents("./public/html/correo.html");

        $datos = array('email' => $correo);

        $tmpl = new Template($contenido);
        $contenido = $tmpl->render($datos);

        print $contenido;
    }

    public function cambio_pass($id, $token, $error) {

        $contenido = file_get_contents("./public/html/cambio_pass.html");

        $datos = array('error' => $error, 'id' => $id, 'token' => $token);

        $tmpl = new Template($contenido);
        $contenido = $tmpl->render($datos);

        print $contenido;
    }

    public function mensaje($tipo, $mensaje) {

        $contenido = file_get_contents("./public/html/mensaje.html");
        if ($tipo == 'Error: ') {
            $datos = array('tipo' => 'danger', 'titulo' => $tipo, 'mensaje' => $mensaje);
        }
        else if ($tipo == 'Correcto: ') {
            $datos = array('tipo' => 'success', 'titulo' => $tipo, 'mensaje' => $mensaje);
        }

        $tmpl = new Template($contenido);
        $contenido = $tmpl->render($datos);

        print $contenido;
    }

}
