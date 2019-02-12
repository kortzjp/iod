<?php

require_once 'handlers.php';
session_start();

class HandlerSession {

    public function check_user_data() {

        $usuario = recoge("usuario");
        $password = recoge("password");
 
        if ($usuario == "" || $password == "") {
            $this->destroy_session();
        } else {

            $conexion = new mysqli("localhost", "root", "", "sistemastutorias");
            $conexion->set_charset("utf8");

            if ($conexion->connect_errno) {
                die('Error en la conexion: ' . $conexion->connect_errno);
            }

            $stmt = $conexion->prepare("SELECT id, usuario, nombre, password, id_tipo FROM usuarios WHERE usuario = ? AND activacion = 1 LIMIT 1");
            $stmt->bind_param("s", $usuario);
            $stmt->execute();
            $stmt->store_result();
            $num = $stmt->num_rows;
             
            if ($num > 0) {
                $stmt->bind_result($id, $usuario, $nombre, $passwd, $tipo);
                $stmt->fetch();
                if (password_verify($password, $passwd)) {
                    $this->start_session($id, $usuario, $nombre, $tipo);
                } else {
                    $this->destroy_session('error');
                }
            } else {

                $this->destroy_session('error');
            }
            $stmt->close();
        }
    }

    function start_session($id, $usuario, $nombre, $tipo) {
        $_SESSION['id'] = $id;
        $_SESSION['usuario'] = $usuario;
        $_SESSION['nombre'] = $nombre;
        $_SESSION['logueado'] = $tipo;
        if ($tipo == USER_ADMIN )
            header('Location: /admin/home/');
        else if ($tipo == USER_TUTOR )
            header('Location: /tutor/home/');
        else if ($tipo == USER_DOC )
            header('Location: /docente/home/');
        else
            header('Location: /usuario/login/');
    }

    function destroy_session($error = '') {
        session_destroy();
        header('Location: /usuario/login/' . $error);
    }

    function check_session($tipo) {
        if (!isset($_SESSION['logueado'])) {
            $this->destroy_session();
        } else if ($_SESSION['logueado'] != $tipo) {
            //header('Location: /tutor/home/');
            echo"Acceso restringido";
            exit();
        }
    }

}

function HandlerSession() {
    return new HandlerSession();
}
