<?php

require_once './core/HandlerSession.php';
require_once './core/handlers.php';

require_once 'EvaluacionesView.php';
require_once 'EvaluacionesModel.php';

require_once './modules/curso/CursoModel.php';
require_once './modules/alumno/AlumnoModel.php';
require_once './modules/reportes/ReportesModel.php';
require_once './modules/horario/HorarioModel.php';
require_once './modules/asistencias/AsistenciasModel.php';

class EvaluacionesController {

    private $vista;
    private $modelo;

    function __construct($metodo, $arg) {

        $this->vista = new EvaluacionesView();
        $this->modelo = new EvaluacionesModel();

        if (method_exists($this, $metodo)) {
            call_user_func(array($this, $metodo), $arg);
        } else {
            //print 'Recurso inexistente';
            //$this->home();
            header("Location: /docente/evaluaciones");
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
            header('Location: /docente/evaluaciones');
        } else {
            $idCurso = $arg[0];
            $idDocenet = $_SESSION['id'];

            $cursoModelo = new CursoModel();
            $curso = $cursoModelo->get($idCurso, $idDocenet);

            if (empty($curso)) {
                header('Location: /docente/evaluaciones');
            } else {
                // echo "lista de evaluaciones";
                $clases = $this->modelo->dia($idCurso, $_POST['dia']);
                $d = $_POST['dia'];
                $encontrado = false;
                foreach ($clases as $key => $value) {
                    if ($value['dia'] == $d) {
                        $encontrado = true;
                    }
                }
                if (!$encontrado) {
                    $dia = array('dia' => $d);
                    array_push($clases, $dia);
                }
//                echo "<pre>";
//                print_r($clases);
//                echo "</pre>";
//                exit();
                $totalDias = count($clases);

                $evaluacionesModelo = new EvaluacionesModel();
                $evaluaciones_datos = $evaluacionesModelo->lista_evaluaciones($idCurso, $clases[0]['dia'], $clases[$totalDias - 1]['dia']);

                $alumnoModelo = new AlumnoModel();
                $alumnos = $alumnoModelo->lista($idCurso);

                if (empty($alumnos)) {
                    header("location: /docente/evaluaciones/error_lista");
                } else {
                    // obtener la lista de alumnos

                    $auxAlumno = array();
                    $n = 0;

                    foreach ($alumnos as $row => $alumno) {

                        $auxAlumno[$n]['n'] = ($n + 1);
                        $auxAlumno[$n]['id'] = $alumno['id'];
                        $auxAlumno[$n]['matricula'] = $alumno['matricula'];
                        $auxAlumno[$n]['nombre'] = $alumno['nombre'];
                        $auxAlumno[$n]['estado'] = $alumno['estado'];

                        $suma = 0;
                        $d = 0;

                        foreach ($evaluaciones_datos as $key => $unAlumno) {
                            foreach ($clases as $key => $unDia) {
                                if ($unAlumno['id'] == $alumno['id'] && $unAlumno['dia'] == $unDia['dia']) {
                                    $auxAlumno[$n][$unDia['dia']] = $unAlumno['calificacion'];
                                    $d++;
                                    $suma += $unAlumno['calificacion'];
                                }
                            }
                        }
                        while ($d < $totalDias) {
                            $auxAlumno[$n][$clases[$d]['dia']] = '<label> 0 <input type="radio" name="alumnos[' . $auxAlumno[$n]['id'] . '_' . $clases[$d]['dia'] . ']" value="0" > </label> '
                                    . '<label> 1 <input type="radio" name="alumnos[' . $auxAlumno[$n]['id'] . '_' . $clases[$d]['dia'] . ']" value="1" checked > </label> '
                                    . '<label> 2 <input type="radio" name="alumnos[' . $auxAlumno[$n]['id'] . '_' . $clases[$d]['dia'] . ']" value="2" checked > </label>';
                            ;

                            $d++;
                        }

                        $auxAlumno[$n]['porcentaje'] = number_format($suma * 100 / ($totalDias * 2), 0, '.', '');
                        $n++;
                    }

                    $this->vista->mostrar_lista($auxAlumno, $clases);
                }
//                }
            }
        }
    }

    public function guardar($arg = array()) {

        $save_evaluaciones = array();
        foreach ($_REQUEST['alumnos'] as $alumno => $value) {
            $datos = explode('_', $alumno);
            $save_evaluaciones["id"] = $datos[0];
            $save_evaluaciones["dia"] = $datos[1];
            $save_evaluaciones["calificacion"] = $value;

            $this->modelo->set($save_evaluaciones);
        }

        header("location: /docente/evaluaciones");
    }

    public function resultados($arg = array()) {

        if (empty($arg) || $arg[0] == '') {
            header('Location: /docente/evaluaciones');
        } else {
            $idCurso = $arg[0];
            $alumnos = $this->modelo->lista_evaluaciones($idCurso, '', '');

            if (empty($alumnos)) {
                header("Location: /docente/evaluaciones");
            } else {

                $datos = array('asignatura' => $_POST['asignatura']);
                $obj = (object) $datos;

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

                            $auxAlumno[$n - 1]['porcentaje'] = number_format($suma * 100 / ($totalDiasRegistrados * 2 ), 0, '.', '');
                            $suma = 0;
                        }

                        $auxAlumno[$n]['n'] = ($n + 1);
                        $auxAlumno[$n]['id'] = $alumno['id'];
                        $auxAlumno[$n]['matricula'] = $matricula;
                        $auxAlumno[$n]['nombre'] = $alumno['nombre'];
                        $auxAlumno[$n]['estado'] = $alumno['estado'];

                        $suma += $alumno['calificacion'];
                        $totalDiasRegistrados = 1;
                    } else {
                        //$auxAlumno[$n][$alumno['dia']] = $alumno['asistencia'];
                        $suma += $alumno['calificacion'];
                        $totalDiasRegistrados++;
                    }
                }

                $auxAlumno[$n]['porcentaje'] = number_format($suma * 100 / ($totalDiasRegistrados * 2 ), 0, '.', '');

                $lista = array();
                for ($a = 0; $a < count($auxAlumno); $a++) {
                    //$aa = array( 'dia' => $clases[$d] );
                    $alum = (object) $auxAlumno[$a];
                    $lista[] = $alum;
                }

                $this->vista->resultados($lista);
            }
        }
    }
    
}
