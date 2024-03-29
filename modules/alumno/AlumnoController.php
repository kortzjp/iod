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
    public function comenzarevaluacion($arg = array()) {
        HandlerSession()->check_session(USER_ALUM);

        $idUsuario = $_SESSION['id'];

        // si ya contesto la evaluacion docente
        $dimensiones = $this->modelo->contestoevaluacion($idUsuario);
        if (count($dimensiones) == 0) {
            $mensaje = "Gracias por contestar la evaluación.";
            $this->vista->evaluacion($mensaje);
        } else {

            $matricula = $_SESSION['usuario'];
            $this->modelo = new AlumnoModel();
            $cuatrimestreModelo = new CuatrimestreModel();
            $cuatrimestre = $cuatrimestreModelo->get(1);
            $cursos = $this->modelo->asignaturas_alumno($matricula, $cuatrimestre[0]['id']);
            if (count($cursos) == 0) {
                $mensaje = "Gracias por participar.";
                $this->vista->evaluacion($mensaje);
            } else {
                $this->modelo = new AlumnoModel();
                $preguntas = $this->modelo->getPreguntas($dimensiones[0]['idDimension']);

                $this->vista->comenzarevaluacion($cursos, $preguntas);
            }
        }
    }
    
    
    public function comenzar_encuesta($arg = array()) {
        HandlerSession()->check_session(USER_ALUM);
        
        $idUsuario= $_SESSION['id'];
        $datos= array();
        $mensaje='';
        $tipo='';
        $listaDocentes= array();
        
        if (isset($arg[0]) && $arg[0] == 'success') {
            $mensaje = 'Has contestado esta encuesta. <br> Gracias por tu participación';
            $tipo = 'callout-success';
        } else if (isset($arg[0]) && $arg[0] == 'error') {
            $mensaje = 'Ha ocurrido un error al contestar la encuesta.';
            $tipo = 'callout-danger';
        }
        
        // si ya contesto la encuesta
        $respuesta = $this->modelo->validarEncuesta($idUsuario);

        if ( $respuesta > 0) {
            $mensaje = "Gracias por contestar la encuesta";
            $tipo = 'callout-success';
            $this->vista->encuesta($listaDocentes, $mensaje, $tipo);
        } else {
            
            $matricula= $_SESSION['usuario'];
            $cuatrimestreModel= new CuatrimestreModel();
            $datosCuatrimestre = $cuatrimestreModel->get(1);
            $cuatrimestre= $datosCuatrimestre[0]['id'];
            //obtener datos de las asignaturas cursadas
            $listaAsignaturas= $this->modelo->asignaturas_alumno($matricula, $cuatrimestre);
            
            $n=0;
            //obtener la lista de docentes de las asignaturas
            foreach($listaAsignaturas as $key => $value){
                $listaDocentes[$key]['docente']= $value['docente'];
                 $listaDocentes[$key]['idDocente']= $value['idDocente'];
                $n++;
            }
            //eliminar datos repetidos (imparte 2+ materias)
            for($i=0; $i<$n; $i++){
                for($j=1; $j<$n-$i-1; $j++){
                    if($listaDocentes[$i]['docente']==$listaDocentes[$j]['docente']){
                        unset($listaDocentes[$j]);
                    }
                }
            }
            
            $this->vista->encuesta($listaDocentes, $mensaje, $tipo);
        }
    }
    
    public function encuesta(){
        HandlerSession()->check_session(USER_ALUM);
        
        $idAlumno= $_SESSION['id'];
        $pregunta1= $_POST['pregunta1'];
        $pregunta2= recoge('pregunta2'); //$_POST['pregunta2'];
        $pregunta3= recoge('pregunta3');
        
        $guardar= $this->modelo->respuestaEncuesta($idAlumno, $pregunta1, $pregunta2, $pregunta3);
        if($guardar==true){
            header('Location: /alumno/comenzar_encuesta/success');
        } else{
            header('Location: /alumno/comenzar_encuesta/error');
        }
        
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

        header("location: /alumno/comenzarevaluacion");
    }

    public function reporte($arg = array()) {
      //  if (empty($arg))
            HandlerSession()->check_session(USER_TUTOR);
    //    else
      //      HandlerSession()->check_session(USER_ALUM);

        $matricula = "";
        $modulo = "tutor";
        //si el tutor modifica un parcial, se manda la matricula como arg
        if (count($arg)>0) {
                $matricula= $arg[0];
            }
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
            if (count($alumnos) == 0) {
                header('Location: /' . $modulo . '/home');
            } else {
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
                                $auxAlumno[$n]['idalumno'] = $rowCalif['id'];
                                $auxAlumno[$n]['primero' . $curso['id']] = $rowCalif['primero'];
                                $auxAlumno[$n]['segundo' . $curso['id']] = $rowCalif['segundo'];
                                $auxAlumno[$n]['tercero' . $curso['id']] = $rowCalif['tercero'];

                                $promedio = ($rowCalif['primero'] + $rowCalif['segundo'] + $rowCalif['tercero']) / 3;
                                $auxAlumno[$n]['promedio' . $curso['id']] = number_format($promedio, 2, '.', '');


                                if ($rowCalif['primero'] < 70) {
                                    $reprobadasP1++;
                                    $auxAlumno[$n]['reprobadaP1' . $curso['id']] = 'reprobado';
                                }
                                if ($rowCalif['segundo'] < 70) {
                                    $reprobadasP2++;
                                    $auxAlumno[$n]['reprobadaP2' . $curso['id']] = 'reprobado';
                                }
                                if ($rowCalif['tercero'] < 70) {
                                    $reprobadasP3++;
                                    $auxAlumno[$n]['reprobadaP3' . $curso['id']] = 'reprobado';
                                }

                                if ($promedio < 70) {
                                    $auxAlumno[$n]['final' . $curso['id']] = 60;
                                    $reprobadasp++;
                                    $promedioG += 60;
                                    $materias++;
                                    $auxAlumno[$n]['reprobadaP' . $curso['id']] = 'reprobado';
                                } else {
                                    $final = $this->redondear($promedio);
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
            }
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
            'paterno' => recoge('paterno'),
            'materno' => recoge('materno'),
            'nombre' => recoge('nombre'),
            'genero' => recoge('genero'));

        $this->modelo->edit($data);
        //exit();
        header('Location: /alumno/home');
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


                            if ($rowCalif['primero'] < 70) {
                                $reprobadasP1++;
                                $auxAlumno[$n]['reprobadaP1' . $curso['id']] = 'reprobado';
                            }
                            if ($rowCalif['segundo'] < 70) {
                                $reprobadasP2++;
                                $auxAlumno[$n]['reprobadaP2' . $curso['id']] = 'reprobado';
                            }
                            if ($rowCalif['tercero'] < 70) {
                                $reprobadasP3++;
                                $auxAlumno[$n]['reprobadaP3' . $curso['id']] = 'reprobado';
                            }

                            if ($promedio < 70) {
                                $auxAlumno[$n]['final' . $curso['id']] = 60;
                                $reprobadasp++;
                                $promedioG += 60;
                                $materias++;
                                $auxAlumno[$n]['reprobadaP' . $curso['id']] = 'reprobado';
                            } else {
                                $final = $this->redondear($promedio);
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
    
    //convierte el promedio de una calificaci��n final m��ltiplo de 10
    private function redondear($dato)
    {
        $dato= number_format($dato,0);
        if ($dato<70) {
            $calificacion= 60;
        } else{
            $residuo= $dato%10;
            if ($residuo<5) {
                $calificacion= $dato-$residuo;
            } else{
                $calificacion= $dato+10-$residuo;
            }
        }
        return $calificacion;
    }

}
