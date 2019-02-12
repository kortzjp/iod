<?php

require_once './core/HandlerSession.php';
require_once './core/handlers.php';

require_once 'CuadrantesView.php';
require_once 'CuadrantesModel.php';

require_once './modules/curso/CursoModel.php';
require_once './modules/horario/HorarioModel.php';
require_once './modules/asistencias/AsistenciasModel.php';
require_once './modules/evaluaciones/EvaluacionesModel.php';

class CuadrantesController {

    private $vista;
    private $modelo;

    function __construct($metodo, $arg) {

        $this->vista = new CuadrantesView();
        $this->modelo = new CuadrantesModel();

        if (method_exists($this, $metodo)) {
            call_user_func(array($this, $metodo), $arg);
        } else {
            //print 'Recurso inexistente';
            //$this->home();
            header("Location: /tutor/cuadrantes");
        }
    }

    public function home() {
        HandlerSession()->check_session(USER_DOC);
        //$cursos = $this->modelo->getCursos($_SESSION['id']);
        //$this->vista->home();
    }

    public function mostrar($argumentos = array()) {
        $this->modelo = new CuadrantesModel();
        $asignaturas = $this->modelo->lista($_POST['grupo'], $_POST['cuatrimestre']);

        $this->modelo = new CuadrantesModel();
        $totalAlumnos = $this->modelo->alumnos_asignatura($_POST['grupo'], $_POST['cuatrimestre']);
        $auxAlumnos = array();
        $auxCursos = array();

//        echo "<pre>";
//            print_r($totalAlumnos);
//            echo "<pre>";

        foreach ($totalAlumnos as $row => $curso) {
            // para cambiar de nombre en la lista
            $auxAlumnos['alumnos' . $curso['curso']] = $curso['alumnos'];
            $auxCursos[] = $curso['curso'];
        }
        $listaCursos = (object) $auxAlumnos;

        $this->modelo = new CuadrantesModel();
        $fechas_quincena = $this->modelo->quincenas_cuadrante();
        $quincena = $_POST['quincena'];

        $fecha_inicio = $fechas_quincena[$quincena]['inicio'];
        $fecha_fin = $fechas_quincena[$quincena]['fin'];

        $mensaje = "[ " . date("M j", strtotime($fecha_inicio)) . " a " . date("j", strtotime($fecha_fin)) . " ]";
        $datos = array('grupo' => $_POST['grupo'], 'quincena' => $mensaje);
        $objGrupo = (object) $datos;

        $pagina = $this->vista->cuadrantes($asignaturas, $listaCursos, $objGrupo);

        for ($i = 0; $i < count($auxCursos); $i++) {
        
        $datosCuadrante = $this->cuadrantes($auxCursos[$i], $fecha_inicio, $fecha_fin);
        $pagina = $this->vista->datosRengloCurso($pagina, $datosCuadrante);
        
        }
        

        $this->vista->mostrarCuadrantes($pagina);
    }

    function mostrar_lista_asistencias($clases, $asistencias_datos, $existenDatos) {


        $dias = array();
        $asistencias = array();

        $totalDias = count($clases);

        for ($d = 0; $d < $totalDias; $d++) {

            $asis = array('dia' => $clases[$d]);
            $fech = (object) $asis;
            $asistencias[] = $fech;

            $uno = new DateTime($clases[$d]);
            $dia = array('dia' => $uno->format('d'));
            $fecha = (object) $dia;
            $dias[] = $fecha;
        }

        // obtener la lista de alumnos
        $matricula = '';
        $auxAlumno = array();
        $n = -1;
        $cambio = true;
        $suma = 0;
        $totalDiasRegistrados = 0;

        foreach ($asistencias_datos as $row => $alumno) {
            // para cambiar de nombre en la lista
            if (strcmp($alumno['matricula'], $matricula) !== 0) {
                $matricula = $alumno['matricula'];
                $n++;
                if ($n >= 1) {
                    $auxAlumno[$n - 1]['asistencia'] = number_format($suma * 100 / $totalDias, 0, '.', '');
                    $suma = 0;
                }

                $auxAlumno[$n]['id'] = $alumno['id'];

                if ($existenDatos) {
                    //$auxAlumno[$n][$alumno['dia']] = $alumno['asistencia'];
                    $suma += $alumno['asistencia'];
                }
                $totalDiasRegistrados = 1;
            } else {

                $suma += $alumno['asistencia'];
                $totalDiasRegistrados++;
            }
        }


        $auxAlumno[$n]['asistencia'] = number_format($suma * 100 / $totalDias, 0, '.', '');

        return $auxAlumno;
    }

    function mostra_lista_evaluaciones($curso, $fechaInicio, $fechaFin) {
        $evaluacionesModelo = new EvaluacionesModel();
        $alumnos = $evaluacionesModelo->lista_evaluaciones($curso, $fechaInicio, $fechaFin);

        if (empty($alumnos)) {
            return 0;
        }

        // obtener la lista de alumnos
        $matricula = '';
        $auxAlumno = array();
        $n = -1;
        $cambio = true;
        $suma = 0;
        $totalDiasRegistrados = 0;

        foreach ($alumnos as $row => $alumno) {
            // para cambiar de nombre en la lista
            if (strcmp($alumno['matricula'], $matricula) !== 0) {
                $matricula = $alumno['matricula'];
                $n++;
                if ($n >= 1) {

                    $auxAlumno[$n - 1]['evaluacion'] = number_format($suma * 100 / ($totalDiasRegistrados * 2 ), 0, '.', '');
                    $suma = 0;
                }

                $auxAlumno[$n]['id'] = $alumno['id'];

                $suma += $alumno['calificacion'];
                $totalDiasRegistrados = 1;
            } else {
                $suma += $alumno['calificacion'];
                $totalDiasRegistrados++;
            }
        }

        $auxAlumno[$n]['evaluacion'] = number_format($suma * 100 / ($totalDiasRegistrados * 2 ), 0, '.', '');

        $lista = array();
        for ($a = 0; $a < count($auxAlumno); $a++) {
            $alum = (object) $auxAlumno[$a];
            $lista[] = $alum;
        }

        return $auxAlumno;
    }

    function cuadrantes($curso, $fecha_inicio, $fecha_fin) {

        $asistenciasModelo = new AsistenciasModel();
        $alumnos = $asistenciasModelo->lista_alumnos($curso);

        $asistenciasModelo = new AsistenciasModel();
        $diasDeClase = $asistenciasModelo->horario_quincena($curso, $fecha_inicio, $fecha_fin);


        $diaFinal = count($diasDeClase) - 1;
        $asistenciasModelo = new AsistenciasModel();
        $asistencias = $asistenciasModelo->lista_asistencias($curso, $diasDeClase[0], $fecha_fin);

        if (empty($asistencias)) {
            $cuadrantes = array();
            $cuadrantes['c1v' . $curso] = 0;
            $cuadrantes['c1p' . $curso] = 0;
            $cuadrantes['c2v' . $curso] = 0;
            $cuadrantes['c2p' . $curso] = 0;
            $cuadrantes['c3v' . $curso] = 0;
            $cuadrantes['c3p' . $curso] = 0;
            $cuadrantes['c4v' . $curso] = 0;
            $cuadrantes['c4p' . $curso] = 0;
            return $cuadrantes;
        } else {

            $pAsistencias = $this->mostrar_lista_asistencias($diasDeClase, $asistencias, true);
            $evaluaciones = $this->mostra_lista_evaluaciones($curso, $fecha_inicio, $fecha_fin);

            $cuadrante1 = 0;
            $cuadrante2 = 0;
            $cuadrante3 = 0;
            $cuadrante4 = 0;
            $total = count($pAsistencias);

            for ($a = 0; $a < $total; $a++) {
                if ($pAsistencias[$a]['asistencia'] >= 70 && $evaluaciones[$a]['evaluacion'] >= 70) {
                    $cuadrante1++;
                } else if ($pAsistencias[$a]['asistencia'] < 70 && $evaluaciones[$a]['evaluacion'] >= 70) {
                    $cuadrante2++;
                } else if ($pAsistencias[$a]['asistencia'] >= 70 && $evaluaciones[$a]['evaluacion'] < 70) {
                    $cuadrante3++;
                } else {
                    $cuadrante4++;
                }
            }

            $cuadrantes = array();
            $cuadrantes['c1v' . $curso] = $cuadrante1;
            $cuadrantes['c1p' . $curso] = number_format($cuadrante1 * 100 / $total, 2, '.', '');
            $cuadrantes['c2v' . $curso] = $cuadrante2;
            $cuadrantes['c2p' . $curso] = number_format($cuadrante2 * 100 / $total, 2, '.', '');
            $cuadrantes['c3v' . $curso] = $cuadrante3;
            $cuadrantes['c3p' . $curso] = number_format($cuadrante3 * 100 / $total, 2, '.', '');
            $cuadrantes['c4v' . $curso] = $cuadrante4;
            $cuadrantes['c4p' . $curso] = number_format($cuadrante4 * 100 / $total, 2, '.', '');

            return $cuadrantes;
        }
    }

}
