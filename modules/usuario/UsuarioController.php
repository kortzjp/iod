<?php

require_once './core/HandlerSession.php';
require_once 'UsuarioView.php';
require_once 'UsuarioModel.php';

class UsuarioController {

    private $vista;
    private $modelo;

    function __construct($metodo, $arg) {
        $this->vista = new UsuarioView();
        $this->modelo = new UsuarioModel();
        if (method_exists($this, $metodo)) {
            // llamar al metodo dentro de esta clase
            call_user_func(array($this, $metodo), $arg);
        } else {
            print 'Metodo inexistente ' . $metodo . " - " . $arg;
        }
    }

    public function login($arg = array()) {
        //echo "mostrar el formulario de ingreso";
        if (!empty($arg) && $arg[0] == 'error')
            $this->vista->login($arg);
        else
            $this->vista->login("");
    }

    public function recupera($arg = array()) {
        //echo "mostrar el formulario de ingreso";
        if (isset($_POST['email'])) {

            $email = $this->recoge('email');
            //$usuarioModel = new UsuarioModel();
            $resultado = $this->modelo->get($email);

            if (!empty($resultado)) {
                $id = $resultado[0]['id'];
                $nombre = $resultado[0]['nombre'];
                $usuario = $resultado[0]['usuario'];
                $token = $this->modelo->generaTokenPass($id);

                $url = 'http://' . $_SERVER["SERVER_NAME"] . '/usuario/verifica_token/' . $id . '/' . $token;
                $asunto = 'Recuperar password';
                $cuerpo = "Hola $nombre: <br /> <br />"
                        . "Usuario:<strong>$usuario</strong><br />"
                        . "Se ha solicitado un reinicio de contrase&ntilde;a."
                        . "<br /><br />Para restaurar la contrase&ntilde;a, "
                        . "da clic o ingresa en tu navegador la siguiente direcci&oacute;n: "
                        . "<a href='$url'>$url</a> ";

                if ($this->enviarEmail($id, $email, $resultado[0]['nombre'], $asunto, $cuerpo)) {
                    $this->vista->correo($email);
                } else {
                    //echo "Error al enviar el correo para recuperar el password";
                    header("Location: /usuario/recupera/error_envio");
                }
            } else {
                //echo " Error el correo electronico no existe.";
                header("Location: /usuario/recupera/error_correo");
            }
        } else {
            $datos = empty($arg) ? '' : $arg[0];
            $this->vista->recupera($datos);
        }
    }

    public function verifica_token($arg = array()) {
        $id = $arg[0];
        $token = $arg[1];
        $error = '';
        if (isset($arg[2]) && $arg[2] == 'error1') {
            $error = 'Las contraseñas no son iguales.';
        }
        if (isset($arg[2]) && $arg[2] == 'error2') {
            $error = 'La contraseña debe tener 6 letras.';
        }

        $resultado = $this->modelo->verificaTokenPass($id, $token);
        if (!$resultado) {
            //echo "error con los datos(id, token) para recuperar el password";
            $tipo = "Error: ";
            $mensaje = "Algo anda mal intenta otra vez.";
            $this->vista->mensaje($tipo, $mensaje);
        } else {
            //echo " fomulario para cambiar el password";
            $this->vista->cambio_pass($id, $token, $error);
        }
    }

    public function cambio_pass($arg = array()) {
        $password = $this->recoge('password');
        $pass_con = $this->recoge('pass_con');
        $id = $this->recoge('id');
        $token = $this->recoge('token');
        if (strlen($password) > 5 && strlen($pass_con) > 5) {

            $resultado = $this->validaPassword($password, $pass_con);
            if (!$resultado) {
                //echo "Error: el password no es igual";
                header('Location: ../usuario/verifica_token/' . $id . '/' . $token . '/error1');
            } else {
                //echo "Cambio de pasword correcto";
                $password = $this->hashPassword($password);
                $datos = array('id' => $id, 'password' => $password, 'token' => $token);
                if ($this->modelo->edit($datos) > 0) {
                    $this->vista->mensaje('Correcto: ', 'Tu contraseña se ha cambiado.');
                } else {
                    $this->vista->mensaje('Error: ', 'Algo anda mal, inteta otra vez.');
                }
            }
        } else {
            // error el password no tiene seis letras.
            header('Location: ../usuario/verifica_token/' . $id . '/' . $token . '/error2');
        }
    }

    public function ingresar() {
        // veririficar los datos ingresados
        HandlerSession()->check_user_data();
        // HandlerSession::check_user_data();        
    }

    public function salir() {
        // veririficar los datos ingresados
        HandlerSession()->destroy_session();
    }

    public static function recoge($var) {
        $tmp = (isset($_REQUEST[$var])) ? trim(htmlspecialchars($_REQUEST[$var], ENT_QUOTES, "UTF-8")) : "";
        return $tmp;
    }

    public function hashPassword($password) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        return $hash;
    }

    public function validaPassword($var1, $var2) {
        if (strcmp($var1, $var2) !== 0) {
            return false;
        } else {
            return true;
        }
    }

    public function enviarEmail($id, $email, $nombre, $asunto, $cuerpo) {

        require_once './PHPMailer/PHPMailerAutoload.php';

        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = 'tls';
        $mail->Host = 'a2plcpnl0608.prod.iad2.secureserver.net'; //Modificar
        $mail->Port = '587'; //Modificar
        $mail->Username = 'upa.academia.sistemas@somosinformatica.com'; //Modificar
        $mail->Password = 'Academia?18'; //Modificar

        $mail->setFrom('upa.academia.sistemas@somosinformatica.com', 'Academia Sistemas'); //Modificar
        $mail->addAddress($email, $nombre);

        $mail->Subject = $asunto;
        $mail->Body = $cuerpo;
        $mail->IsHTML(true);

        if ($mail->send())
            return true;
        else
            return false;
    }

}
