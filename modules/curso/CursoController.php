<?php

require_once './core/HandlerSession.php';
require_once './core/handlers.php';

require_once 'CursoView.php';
require_once 'CursoModel.php';

require_once './modules/horario/HorarioModel.php';

class CursoController {

    private $vista;
    private $modelo;

    function __construct($metodo, $arg) {

        $this->vista = new CursoView();
        $this->modelo = new CursoModel();

        if (method_exists($this, $metodo)) {
            call_user_func(array($this, $metodo), $arg);
        } else {
            print 'Recurso inexistente';
        }
    }

    public function home() {
        HandlerSession()->check_session(USER_DOC);
        //$cursos = $this->modelo->getCursos($_SESSION['id']);
        $this->vista->home();
    }

    public function asistencias() {
        HandlerSession()->check_session(USER_DOC);
        $cursos = $this->modelo->getCursos($_SESSION['id']);
        $this->vista->asistencias($cursos);
    }

    public function crear() {
        HandlerSession()->check_session(USER_TUTOR);

        $cuatrimestre = recoge('cuatrimestre');
        $asignatura = recoge('asignatura');
        $docente = recoge('docente');
        $grupo = strtoupper(recoge('grupo'));

        $lunes = empty($_POST['lunes']) ? null : $_POST['lunes'];
        $martes = empty($_POST['martes']) ? null : $_POST['martes'];
        $miercoles = empty($_POST['miercoles']) ? null : $_POST['miercoles'];
        $jueves = empty($_POST['jueves']) ? null : $_POST['jueves'];
        $viernes = empty($_POST['viernes']) ? null : $_POST['viernes'];
        $sabado = empty($_POST['sabado']) ? null : $_POST['sabado'];

        //var_dump($_REQUEST);

        if (($cuatrimestre == '' || $asignatura == '' || $docente == '' || $grupo == '') || ( $lunes == 'null' && $martes == 'null' && $miercoles == 'null' && $jueves == 'null' && $viernes == 'null' && $sabado == 'null')) {
            header('Location: /tutor/cursos/error');
        } else {

            $datos = array(
                'cuatrimestre' => $cuatrimestre,
                'asignatura' => $asignatura,
                'docente' => $docente,
                'grupo' => $grupo,
                'lunes' => $lunes,
                'martes' => $martes,
                'miercoles' => $miercoles,
                'jueves' => $jueves,
                'viernes' => $viernes,
                'sabado' => $sabado);

            $resultado = $this->modelo->set($datos);

            if ($resultado == 'creado' ) {
                header('Location: /tutor/cursos/success');
            } else {
                header('Location: /tutor/cursos/danger');
            }
        }
    }

    public function guardar() {
        HandlerSession()->check_session(USER_TUTOR);

        $id = recoge('id');
        $cuatrimestre = recoge('cuatrimestre');
        $asignatura = recoge('asignatura');
        $docente = recoge('docente');
        $grupo = recoge('grupo');

        if ($docente == '' || $cuatrimestre == '' || $asignatura == '' || $grupo == '') {
            header('Location: /tutor/docentes/error');
        } else {
            $datos = array(
                'id' => $id,
                'cuatrimestre' => $cuatrimestre,
                'asignatura' => $asignatura,
                'docente' => $docente,
                'activacion' => $grupo);

            $respuesta = $this->modelo->edit($datos);

            header('Location: /tutor/docentes/' . $respuesta);
        }
    }

    public function confirmar($mensaje = '') {

        echo $mensaje;
    }

}
