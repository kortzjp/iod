<?php

require_once './core/HandlerSession.php';
require_once './core/handlers.php';

require_once 'CalificacionesView.php';
require_once 'CalificacionesModel.php';

require_once './modules/curso/CursoModel.php';
require_once './modules/alumno/AlumnoModel.php';

class CalificacionesController {

    //put your code here
    private $vista;
    private $modelo;

    function __construct($metodo, $arg) {

        $this->vista = new ParcialView();
        $this->modelo = new ParcialModel();

        if (method_exists($this, $metodo)) {
            call_user_func(array($this, $metodo), $arg);
        } else {
            //print 'Recurso inexistente';
            $this->home();
        }
    }

    public function calificaciones($arg = array()) {
        HandlerSession()->check_session(USER_DOC);

        if (empty($arg) || $arg[0] == '') {
            header('Location: /tutor/calificaciones');
        } else {
            $idCurso = $arg[0];
            $idDocenet = $_SESSION['id'];

            $cursoModelo = new CursoModel();
            $curso = $cursoModelo->getCursoAsignatura($idCurso, $idDocenet);

            if (empty($curso)) {
                header('Location: /tutor/calificaiones');
            } else {

                $cuadrantesModel = new CuadrantesModel();
                $asignaturas = $cuadrantesModelo->lista($_POST['grupo'], $_POST['cuatrimestre']);

                $auxAsignatura = array();
                $n = 0;

                foreach ($asignaturas as $row => $asignatura) {
                    // para cambiar de nombre en la lista
                    $auxAsignatura[$n]['id'] = $asignatura['id'];
                    $auxAsignatura[$n]['asignatura'] = $asignatura['asignatura'];
                    $auxAsignatura[$n]['docente'] = strtoupper($asignatura['docente']);
                    //$auxAsignatura[$n]['grupo'] = $asignatura['grupo'];
                    $n++;
                }

                $lista = array();

                for ($a = 0; $a < count($auxAsignatura); $a++) {
                    $asig = (object) $auxAsignatura[$a];
                    $lista[] = $asig;
                }

//++++++++++++++++++++++++++++++++++++++++++++++++++
// Numero de alumnnos por asignatura
                $cuadrantesModelo = new CuadrantesController();
                $totalAlumnos = $cuadrantesModelo->alumnos_asignatura($_POST['grupo'], $_POST['cuatrimestre']);
                $auxAlumnos = array();
                $auxCursos = array();
                foreach ($totalAlumnos as $row => $curso) {
                    // para cambiar de nombre en la lista
                    $auxAlumnos['alumnos' . $curso['curso']] = $curso['alumnos'];
                    $auxCursos[] = $curso['curso'];
                }
                $listaCursos = (object) $auxAlumnos;


                $datosCalificaciones = array();
                for ($index = 0; $index < count($auxCursos); $index++) {
                    $datosCalificaciones["parcial1" . $auxCursos[$index]] = calificacion_grupo($auxCursos[$index], 'primero');
                    $datosCalificaciones["parcial2" . $auxCursos[$index]] = calificacion_grupo($auxCursos[$index], 'segundo');
                    $datosCalificaciones["parcial3" . $auxCursos[$index]] = calificacion_grupo($auxCursos[$index], 'tercero');

                    $sumaParciales = $datosCalificaciones["parcial1" . $auxCursos[$index]] + $datosCalificaciones["parcial2" . $auxCursos[$index]] + $datosCalificaciones["parcial3" . $auxCursos[$index]];
                    $datosCalificaciones["promedio" . $auxCursos[$index]] = number_format($sumaParciales / 3, 2, '.', '');
                    $tmpl = new Template($str);
                    $str = $tmpl->render($datosCalificaciones); // datos en el renglon de un curso.
                }


            }
        }
    }

    public function calificacion_grupo($curso, $parcial) {

        $alumnos_controller = new ParcialesController();
        $alumnos = $alumnos_controller->lista_calificaciones($curso, $parcial);


        $suma = 0;
        $total = count($alumnos);
        for ($n = 0; $n < $total; $n++) {
            $suma += $alumnos[$n][$parcial];
        }

        return number_format($suma / $total, 2, '.', '');
    }

}
