<?php

require_once './core/HandlerSession.php';
require_once './core/handlers.php';

require_once 'AlumnoModel.php';
require_once 'AlumnoView.php';

require_once './modules/cuatrimestre/CuatrimestreModel.php';
require_once './modules/parcial/ParcialModel.php';

class AlumnoController {

    private $vista;
    private $modelo;

    function __construct($metodo, $arg) {

        $this->vista = new AlumnoView();
        $this->modelo = new AlumnoModel();

        if (method_exists($this, $metodo)) {
            call_user_func(array($this, $metodo), $arg);
        } else {
            print 'Recurso inexistente';
        }
    }

    public function home() {
        HandlerSession()->check_session(USER_ALUM);
        $this->vista->home();
    }

    /**
     * Evaluación docente, llama a la vista para dar la bienvenida a la evaluación
     * docente y mostrar las asignaturas y el nombre del docente que la imparte. 
     * @param type $arg
     */
    public function evaluacion($arg = array()) {
        HandlerSession()->check_session(USER_ALUM);
        $matricula = $_SESSION['usuario'];
        // si ya contesto la evaluacion docente

        if ($this->modelo->contestoevaluacion($matricula)) {
            $mensaje = "Gracias por contestar la evaluación.";
            $this->vista->evaluacion($mensaje);
        } else {
            $cursos = $this->modelo->asignaturas_alumno($matricula);
            $this->vista->evaluacion("", $cursos);
        }
    }

    public function comenzarevaluacion($arg = array()) {
        HandlerSession()->check_session(USER_ALUM);
        $matricula = $_SESSION['usuario'];

        $this->modelo = new AlumnoModel();
        $cursos = $this->modelo->asignaturas_alumno($matricula);

        print_r($arg);
        $dimension = empty($arg) ? 1 : 2;
        $this->modelo = new AlumnoModel();
        $preguntas = $this->modelo->getPreguntas($dimension);

        $this->vista->comenzarevaluacion($cursos, $preguntas);
    }

    public function guardarevaluacion($arg = array()) {
        $data = array();
        foreach ($_POST['respuestas'] as $cursoPregunta => $valor) {
            $datos = explode("_", $cursoPregunta);
            $data["cursan"] = $datos[0];
            $data["pregunta"] = $datos[1];
            $data["respuesta"] = $valor;
            //echo $data["cursan"] . " " . $data["pregunta"] . " " . $data["respuesta"]. "<br>";
            $this->modelo = new AlumnoModel();
            $this->modelo->setRespuestas($data);
        }

        header("location: /alumno/evaluacion");
    }

    public function reporte($arg = array()) {
        if (empty($arg))
            HandlerSession()->check_session(USER_TUTOR);
        else
            HandlerSession()->check_session(USER_ALUM);

        $matricula = "";
        $modulo = "error";
        // si el alumno consulta sus calificaciones, existen el argumento usuario
        if (isset($arg['usuario']) && !empty($arg['usuario'])) {
            $matricula = $arg['usuario'];
            $modulo = "alumno";
        }  // si el tutor consulta la calificacion de un alumno se recibe la matricula de dicho alumno
        else if (isset($_POST['matricula']) && !empty($_POST['matricula'])) {
            $modulo = "tutor";
            $matricula = recoge('matricula');
        }
        if (!empty($matricula)) {
            $alumnoModelo = new AlumnoModel();
            $alumnos = $alumnoModelo->alumno($matricula);

            $alumnoModelo = new AlumnoModel();
            $asignaturas = $alumnoModelo->asignaturas_alumno($matricula);

            $cursos = array();
            $auxAlumno = array();
            $n = 0;
            foreach ($alumnos as $row => $alumno) {
                // para cambiar de nombre en la lista
                $auxAlumno[$n]['n'] = ($n + 1);
                $auxAlumno[$n]['id'] = $alumno['id'];
                $auxAlumno[$n]['matricula'] = $alumno['matricula'];
                $auxAlumno[$n]['nombre'] = $alumno['nombre'];
                // contador de materias reprobadas por paricial
                $reprobadasP1 = 0;
                $reprobadasP2 = 0;
                $reprobadasP3 = 0;
                $reprobadasp = 0;
                $promedio = 0;
                $promedioG = 0;
                $materias = 0;

                foreach ($asignaturas as $key => $curso) {

                    $parciales = new ParcialModel();
                    $listaparciales = $parciales->lista_calificaciones($curso['id'], 'final');

//                    $asistencias = new AsistenciasController();
//                    $totalAsistencias = $asistencias->asistencias($curso['id'], $alumno['id'], '2018/09/03', '2018/12/07');
//                    $faltas = new AsistenciasController();
//                    $totalFaltas = $faltas->faltas($curso['id'], $alumno['id'], '2018/09/03', '2018/12/07');

                    $auxAlumno[$n]['primero' . $curso['id']] = 'NC';
                    $auxAlumno[$n]['segundo' . $curso['id']] = 'NC';
                    $auxAlumno[$n]['tercero' . $curso['id']] = 'NC';
                    $auxAlumno[$n]['promedio' . $curso['id']] = 'NC';
                    $auxAlumno[$n]['reprobada1' . $curso['id']] = '';
                    $auxAlumno[$n]['reprobada2' . $curso['id']] = '';
                    $auxAlumno[$n]['reprobada3' . $curso['id']] = '';
                    $auxAlumno[$n]['reprobadap' . $curso['id']] = '';
                    $auxAlumno[$n]['asistencia' . $curso['id']] = '';
//                    if ($totalAsistencias != 0 || $totalFaltas != 0) {
//                        $porcentaje = (100 * $totalAsistencias ) / ($totalAsistencias + $totalFaltas);
//                        $porcentaje = number_format($porcentaje, 0, '.', '');
//                        $auxAlumno[$n]['asistencia' . $curso['id']] = $porcentaje . ' %';
//                    }

                    foreach ($listaparciales as $key => $rowCalif) {
                        if ($rowCalif['matricula'] == $alumno['matricula']) {

                            $auxAlumno[$n]['primero' . $curso['id']] = $rowCalif['primero'];
                            $auxAlumno[$n]['segundo' . $curso['id']] = $rowCalif['segundo'];
                            $auxAlumno[$n]['tercero' . $curso['id']] = $rowCalif['tercero'];

                            $promedio = ($rowCalif['primero'] + $rowCalif['segundo'] + $rowCalif['tercero']) / 3;
                            $auxAlumno[$n]['promedio' . $curso['id']] = number_format($promedio, 2, '.', '');


                            if ($rowCalif['primero'] < 7) {
                                $reprobadasP1++;
                                $auxAlumno[$n]['reprobadaP1' . $curso['id']] = 'reprobado';
                            }
                            if ($rowCalif['segundo'] < 7) {
                                $reprobadasP2++;
                                $auxAlumno[$n]['reprobadaP2' . $curso['id']] = 'reprobado';
                            }
                            if ($rowCalif['tercero'] < 7) {
                                $reprobadasP3++;
                                $auxAlumno[$n]['reprobadaP3' . $curso['id']] = 'reprobado';
                            }

                            if ($promedio < 7.0) {
                                $auxAlumno[$n]['final' . $curso['id']] = 6;
                                $reprobadasp++;
                                $promedioG += 6;
                                $materias++;
                                $auxAlumno[$n]['reprobadaP' . $curso['id']] = 'reprobado';
                            } else {
                                $final = round($promedio, 0);
                                $promedioG += $final;
                                $materias++;
                                $auxAlumno[$n]['final' . $curso['id']] = $final;
                            }

                            break;
                        }
                    }
                }
                $promedioG = $promedioG / $materias;
                $auxAlumno[$n]['promedio'] = number_format($promedioG, 2, '.', '');

                $clase1 = '';
                $clase2 = '';
                $clase3 = '';
                $clasep = '';
                if ($reprobadasP1 > 2) {
                    $clase1 = 'reprobado';
                }
                if ($reprobadasP2 > 2) {
                    $clase2 = 'reprobado';
                }
                if ($reprobadasP3 > 2) {
                    $clase3 = 'reprobado';
                }
                if ($reprobadasp > 2) {
                    $clasep = 'reprobado';
                }
                $auxAlumno[$n]['reprobadasP1'] = $reprobadasP1;
                $auxAlumno[$n]['reprobadasP2'] = $reprobadasP2;
                $auxAlumno[$n]['reprobadasP3'] = $reprobadasP3;
                $auxAlumno[$n]['reprobadasp'] = $reprobadasp;
                $auxAlumno[$n]['clase1'] = $clase1;
                $auxAlumno[$n]['clase2'] = $clase2;
                $auxAlumno[$n]['clase3'] = $clase3;
                $auxAlumno[$n]['clasep'] = $clasep;
                $n++;
            }

            $this->vista->parcialesAlumnos($auxAlumno, $asignaturas, $modulo);
        } else {
            header('Location: /' . $modulo . '/home');
        }
    }

    public function agregar() {
        // agrega a un alumno a un curso
        HandlerSession()->check_session(USER_DOC);

        $cuatrimestreModelo = new CuatrimestreModel();
        $cuatrimestre = $cuatrimestreModelo->get(1);

        $n = 0;
        foreach ($_POST['alumno'] as $key) {
            $data = array(
                'cuatrimestre' => $cuatrimestre[0]['id'],
                'curso' => $_POST['curso'],
                'alumno' => $key,
                'estado' => $_POST['estado'][$key] == 'normal' ? '' : $_POST['estado'][$key]);
            if ($this->modelo->set($data) == 'creado')
                $n++;
            //print_r($data);
        }
        $this->vista->agregar($n);
    }

    public function eliminar_del_curso() {
        $resultado = $this->modelo->delete($_POST['alumno']);
        $curso = recoge('curso');

        if ($resultado == 'eliminado') {
            header("Location: /docente/lista_alumnos/$curso/borrado");
        } else {
            header("Location: /docente/lista_alumnos/$curso/no_borrado");
        }
    }

    public function guardar() {
        
    }

    public function crear() {
        
    }

    public function editar() {
        // Editar datos del alumno
        HandlerSession()->check_session(USER_ALUM);
        $data = array(
            'id' => $_SESSION['id'],
            'paterno' => $_POST['paterno'],
            'materno' => $_POST['materno'],
            'nombre' => $_POST['nombre'],
            'genero' => $_POST['genero']);

        $this->modelo->edit($data);
        $this->vista->home();
    }

    public function calificaciones() {

        HandlerSession()->check_session(USER_ALUM);

        $matricula = "";
        $modulo = "alumno";
        $matricula = $_SESSION['usuario'];
        
        if (!empty($matricula)) {
            $alumnoModelo = new AlumnoModel();
            $alumnos = $alumnoModelo->alumno($matricula);

            $alumnoModelo = new AlumnoModel();
            $asignaturas = $alumnoModelo->asignaturas_alumno($matricula);

            $cursos = array();
            $auxAlumno = array();
            $n = 0;
            foreach ($alumnos as $row => $alumno) {
                // para cambiar de nombre en la lista
                $auxAlumno[$n]['n'] = ($n + 1);
                $auxAlumno[$n]['id'] = $alumno['id'];
                $auxAlumno[$n]['matricula'] = $alumno['matricula'];
                $auxAlumno[$n]['nombre'] = $alumno['nombre'];
                // contador de materias reprobadas por paricial
                $reprobadasP1 = 0;
                $reprobadasP2 = 0;
                $reprobadasP3 = 0;
                $reprobadasp = 0;
                $promedio = 0;
                $promedioG = 0;
                $materias = 0;

                foreach ($asignaturas as $key => $curso) {

                    $parciales = new ParcialModel();
                    $listaparciales = $parciales->lista_calificaciones($curso['id'], 'final');

//                    $asistencias = new AsistenciasController();
//                    $totalAsistencias = $asistencias->asistencias($curso['id'], $alumno['id'], '2018/09/03', '2018/12/07');
//                    $faltas = new AsistenciasController();
//                    $totalFaltas = $faltas->faltas($curso['id'], $alumno['id'], '2018/09/03', '2018/12/07');

                    $auxAlumno[$n]['primero' . $curso['id']] = 'NC';
                    $auxAlumno[$n]['segundo' . $curso['id']] = 'NC';
                    $auxAlumno[$n]['tercero' . $curso['id']] = 'NC';
                    $auxAlumno[$n]['promedio' . $curso['id']] = 'NC';
                    $auxAlumno[$n]['reprobada1' . $curso['id']] = '';
                    $auxAlumno[$n]['reprobada2' . $curso['id']] = '';
                    $auxAlumno[$n]['reprobada3' . $curso['id']] = '';
                    $auxAlumno[$n]['reprobadap' . $curso['id']] = '';
                    $auxAlumno[$n]['asistencia' . $curso['id']] = '';
//                    if ($totalAsistencias != 0 || $totalFaltas != 0) {
//                        $porcentaje = (100 * $totalAsistencias ) / ($totalAsistencias + $totalFaltas);
//                        $porcentaje = number_format($porcentaje, 0, '.', '');
//                        $auxAlumno[$n]['asistencia' . $curso['id']] = $porcentaje . ' %';
//                    }

                    foreach ($listaparciales as $key => $rowCalif) {
                        if ($rowCalif['matricula'] == $alumno['matricula']) {

                            $auxAlumno[$n]['primero' . $curso['id']] = $rowCalif['primero'];
                            $auxAlumno[$n]['segundo' . $curso['id']] = $rowCalif['segundo'];
                            $auxAlumno[$n]['tercero' . $curso['id']] = $rowCalif['tercero'];

                            $promedio = ($rowCalif['primero'] + $rowCalif['segundo'] + $rowCalif['tercero']) / 3;
                            $auxAlumno[$n]['promedio' . $curso['id']] = number_format($promedio, 2, '.', '');


                            if ($rowCalif['primero'] < 7) {
                                $reprobadasP1++;
                                $auxAlumno[$n]['reprobadaP1' . $curso['id']] = 'reprobado';
                            }
                            if ($rowCalif['segundo'] < 7) {
                                $reprobadasP2++;
                                $auxAlumno[$n]['reprobadaP2' . $curso['id']] = 'reprobado';
                            }
                            if ($rowCalif['tercero'] < 7) {
                                $reprobadasP3++;
                                $auxAlumno[$n]['reprobadaP3' . $curso['id']] = 'reprobado';
                            }

                            if ($promedio < 7.0) {
                                $auxAlumno[$n]['final' . $curso['id']] = 6;
                                $reprobadasp++;
                                $promedioG += 6;
                                $materias++;
                                $auxAlumno[$n]['reprobadaP' . $curso['id']] = 'reprobado';
                            } else {
                                $final = round($promedio, 0);
                                $promedioG += $final;
                                $materias++;
                                $auxAlumno[$n]['final' . $curso['id']] = $final;
                            }

                            break;
                        }
                    }
                }
                $promedioG = $promedioG / $materias;
                $auxAlumno[$n]['promedio'] = number_format($promedioG, 2, '.', '');

                $clase1 = '';
                $clase2 = '';
                $clase3 = '';
                $clasep = '';
                if ($reprobadasP1 > 2) {
                    $clase1 = 'reprobado';
                }
                if ($reprobadasP2 > 2) {
                    $clase2 = 'reprobado';
                }
                if ($reprobadasP3 > 2) {
                    $clase3 = 'reprobado';
                }
                if ($reprobadasp > 2) {
                    $clasep = 'reprobado';
                }
                $auxAlumno[$n]['reprobadasP1'] = $reprobadasP1;
                $auxAlumno[$n]['reprobadasP2'] = $reprobadasP2;
                $auxAlumno[$n]['reprobadasP3'] = $reprobadasP3;
                $auxAlumno[$n]['reprobadasp'] = $reprobadasp;
                $auxAlumno[$n]['clase1'] = $clase1;
                $auxAlumno[$n]['clase2'] = $clase2;
                $auxAlumno[$n]['clase3'] = $clase3;
                $auxAlumno[$n]['clasep'] = $clasep;
                $n++;
            }

            $this->vista->parciales($auxAlumno, $asignaturas, $modulo);
        } else {
            header('Location: /' . $modulo . '/home');
        }
    }

    public function confirmar($mensaje = '') {

        echo $mensaje;
    }

}
