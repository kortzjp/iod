<?php

require_once './core/HandlerSession.php';
require_once './core/handlers.php';

require_once 'AsistenciasView.php';
require_once 'AsistenciasModel.php';

require_once './modules/curso/CursoModel.php';
require_once './modules/horario/HorarioModel.php';

class AsistenciasController {

    private $vista;
    private $modelo;

    function __construct($metodo, $arg) {

        $this->vista = new AsistenciasView();
        $this->modelo = new AsistenciasModel();

        if (method_exists($this, $metodo)) {
            call_user_func(array($this, $metodo), $arg);
        } else {
            //print 'Recurso inexistente';
            //$this->home();
            header("Location: /docente/asistencias");
        }
    }

    public function home() {
        HandlerSession()->check_session(USER_DOC);
        //$cursos = $this->modelo->getCursos($_SESSION['id']);
        //$this->vista->home();
    }

    public function registrar($arg = array()) {
        HandlerSession()->check_session(USER_DOC);

        if (empty($arg) || $arg[0] == '') {
            header('Location: /docente/asistencias');
        } else {
            $idCurso = $arg[0];
            $idDocenet = $_SESSION['id'];

            $cursoModelo = new CursoModel();
            $curso = $cursoModelo->get($idCurso, $idDocenet);

            if (empty($curso)) {
                header('Location: /docente/asistencias');
            } else {

                $alumnos = $this->modelo->lista_alumnos($idCurso);

                $horarioModelo = new HorarioModel();
                $diasDeClase = $horarioModelo->get($idCurso, $_POST['mes']);

                $diaFinal = count($diasDeClase) - 1;
                $lista_asistencias = new AsistenciasModel();
                if (empty($diasDeClase)) {
                    header('Location: /docente/asistencias');
                } else {
                    $asistencias = $lista_asistencias->lista_asistencias_datos($idCurso, $diasDeClase[0], $diasDeClase[$diaFinal]);

                    if (!empty($asistencias)) {
                        //echo "mostra lista de alumnos con asistencias";
                        $this->vista->mostrar_lista_asistencias($idCurso, $diasDeClase, $alumnos, $asistencias, true);
                    } else {
                        // echo "mostrar lista alumnos con campos";
                        $this->vista->mostrar_lista($idCurso, $diasDeClase, $alumnos, false);
                    }
                }
            }
        }
    }

    public function guardar($arg = array()) {
//        echo "<pre>";
//        print_r($arg);
//        print_r($_REQUEST);
//        echo "</pre>";
        $save_asistencias = array();
        foreach ($_REQUEST['alumno'] as $alumno => $value) {
            $datos = explode('_', $alumno);
            $save_asistencias["id"] = $datos[0];
            $save_asistencias["dia"] = $datos[1];
            $save_asistencias["asistencia"] = $value;

            $this->modelo->set($save_asistencias);
        }

        header("location: /docente/asistencias");
    }

}
