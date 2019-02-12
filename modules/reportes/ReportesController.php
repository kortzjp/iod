<?php

require_once './core/HandlerSession.php';
require_once './core/handlers.php';

require_once 'ReportesView.php';
require_once 'ReportesModel.php';

require_once './modules/evaluaciones/EvaluacionesModel.php';
require_once './modules/cuadrantes/CuadrantesModel.php';
require_once './modules/cuatrimestre/CuatrimestreModel.php';
require_once './modules/parcial/ParcialModel.php';

class ReportesController {

    private $vista;
    private $modelo;

    function __construct($metodo, $arg) {

        $this->vista = new ReportesView();
        $this->modelo = new ReportesModel();

        if (method_exists($this, $metodo)) {
            call_user_func(array($this, $metodo), $arg);
        } else {
            //print 'Recurso inexistente';
            //$this->home();
            header("Location: /tutor/evaluaciones");
        }
    }

    public function home() {
        HandlerSession()->check_session(USER_DOC);
        //$cursos = $this->modelo->getCursos($_SESSION['id']);
        //$this->vista->home();
    }

    public function mostrar($argumentos = array()) {

        $grupo = $_POST['grupo'];

        $cuatrimestreModelo = new CuatrimestreModel();
        $cuatrimestre = $cuatrimestreModelo->get(1);  // cuatrimestre activo

        $cuadrantesModelo = new CuadrantesModel();
        $asignaturas = $cuadrantesModelo->lista($grupo, $cuatrimestre[0]['id']);

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
        $cuadrantesModelo = new CuadrantesModel();
        $totalAlumnos = $cuadrantesModelo->alumnos_asignatura($grupo, $cuatrimestre[0]['id']);
        $auxAlumnos = array();
        $auxCursos = array();
        foreach ($totalAlumnos as $row => $curso) {
            // para cambiar de nombre en la lista
            $auxAlumnos['alumnos' . $curso['curso']] = $curso['alumnos'];
            $auxCursos[] = $curso['curso'];
        }
        $listaCursos = (object) $auxAlumnos;
// **************************************************//

        $pagina = $this->vista->listaAsignatuas($lista, $listaCursos);

        $datosCalificaciones = array();
        for ($index = 0; $index < count($auxCursos); $index++) {
            $datosCalificaciones["parcial1" . $auxCursos[$index]] = $this->calificacion_grupo($auxCursos[$index], 'primero');
            $datosCalificaciones["parcial2" . $auxCursos[$index]] = $this->calificacion_grupo($auxCursos[$index], 'segundo');
            $datosCalificaciones["parcial3" . $auxCursos[$index]] = $this->calificacion_grupo($auxCursos[$index], 'tercero');

            $sumaParciales = $datosCalificaciones["parcial1" . $auxCursos[$index]] + $datosCalificaciones["parcial2" . $auxCursos[$index]] + $datosCalificaciones["parcial3" . $auxCursos[$index]];
            $datosCalificaciones["promedio" . $auxCursos[$index]] = number_format($sumaParciales / 3, 2, '.', '');
            //$tmpl = new Template($str);
            //$str = $tmpl->render($datosCalificaciones); // datos en el renglon de un curso.
            $pagina = $this->vista->calificaciones($datosCalificaciones, $pagina);
        }


        $this->vista->mostrar($pagina);
    }

    public function calificacion_grupo($curso, $parcial) {

        $parcialModelo = new ParcialModel();
        $alumnos = $parcialModelo->lista_calificaciones($curso, $parcial);

        $suma = 0;
        $total = count($alumnos);
        for ($n = 0; $n < $total; $n++) {
            $suma += $alumnos[$n][$parcial];
        }

        return number_format($suma / $total, 2, '.', '');
    }

}
