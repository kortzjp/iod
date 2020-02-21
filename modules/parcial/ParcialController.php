<?php

require_once './core/HandlerSession.php';
require_once './core/handlers.php';

require_once 'ParcialView.php';
require_once 'ParcialModel.php';

require_once './modules/curso/CursoModel.php';
require_once './modules/alumno/AlumnoModel.php';
require_once './modules/reportes/ReportesModel.php';
require_once './modules/cuatrimestre/CuatrimestreModel.php';

class ParcialController {

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
            header('Location: /docente/parciales');
        } else {
            $idCurso = $arg[0];
            $idDocenet = $_SESSION['id'];

            $cursoModelo = new CursoModel();
            $curso = $cursoModelo->getCursoAsignatura($idCurso, $idDocenet);

            if (empty($curso)) {
                header('Location: /docente/parciales');
            } else {

                $parcialModelo = new ParcialModel();
                $alumnos = $parcialModelo->lista_calificaciones($idCurso, 'final');
                $auxAlumno = array();
                $n = 0;

                foreach ($alumnos as $row => $alumno) {
                    // para cambiar de nombre en la lista
                    $auxAlumno[$n]['n'] = ($n + 1);
                    $auxAlumno[$n]['id'] = $alumno['id'];
                    $auxAlumno[$n]['matricula'] = $alumno['matricula'];
                    $auxAlumno[$n]['nombre'] = $alumno['nombre'];
                    $auxAlumno[$n]['estado'] = $alumno['estado'];

                    $promedio = NULL;
                    if ($alumno['primero'] == NULL) {
                        $auxAlumno[$n]['primero'] = ' - ';
                    } else {
                        $auxAlumno[$n]['primero'] = $alumno['primero'];
                        $promedio = $alumnos[$n]['primero'];
                    }
                    if ($alumnos[$n]['segundo'] == NULL) {
                        $auxAlumno[$n]['segundo'] = ' - ';
                    } else {
                        $auxAlumno[$n]['segundo'] = $alumno['segundo'];
                        if ($promedio == NULL) {
                            $promedio = $alumnos[$n]['segundo'];
                        } else {
                            $promedio += $alumnos[$n]['segundo'];
                        }
                    }
                    if ($alumnos[$n]['tercero'] == NULL) {
                        $auxAlumno[$n]['tercero'] = ' - ';
                    } else {
                        $auxAlumno[$n]['tercero'] = $alumno['tercero'];
                        if ($promedio == NULL) {
                            $promedio = $alumnos[$n]['tercero'];
                        } else {
                            $promedio += $alumnos[$n]['tercero'];
                        }
                    }
                    $auxAlumno[$n]['clase'] = 'aprobado';
                    if ($promedio == NULL) {
                        $promedio = '-';
                    } else {
                      $promedio = $promedio / 3; 
                      $promedio = number_format($promedio, 1); // decidir decimal   

                        if ($promedio < 70) {
                            $final = 60;
                            $auxAlumno[$n]['clase'] = 'reprobado';
                        } else {
                            //round eliminado
                            $final = $this->redondear($promedio);
                        }
                    }
                    $auxAlumno[$n]['promedio'] = $promedio;
                    $auxAlumno[$n]['final'] = $final;
                    $n++;
                }
                $data = array('asignatura' => $curso[0]['nombre']);
                $this->vista->resultados($auxAlumno, $data);
            }
        }
    }

    public function registrar($arg = array()) {
        HandlerSession()->check_session(USER_DOC);

        if (empty($arg) || $arg[0] == '') {
            header('Location: /docente/parciales');
        } else {
            $idCurso = $arg[0];
            $idDocenet = $_SESSION['id'];

            $cursoModelo = new CursoModel();
            $curso = $cursoModelo->getCursoAsignatura($idCurso, $idDocenet);

            if (empty($curso)) {
                header('Location: /docente/parciales');
            } else {
                //echo "lista de calificaciones perciales";
                $parcial = strtolower(recoge('parcial'));
                $parcialModelo = new ParcialModel();
                $alumnos = $parcialModelo->lista_calificaciones($idCurso, $parcial);

                if (empty($alumnos)) {
                    header("location: /docente/parciales/error_lista");
                } else {
                    // obtener la lista de alumnos

                    $auxAlumno = array();
                    $n = 0;

                    foreach ($alumnos as $row => $alumno) {
                        // para cambiar de nombre en la lista
                        $auxAlumno[$n]['n'] = ($n + 1);
                        $auxAlumno[$n]['id'] = $alumno['id'];
                        $auxAlumno[$n]['matricula'] = $alumno['matricula'];
                        $auxAlumno[$n]['nombre'] = $alumno['nombre'];
                        $auxAlumno[$n]['estado'] = $alumno['estado'];

                        if ($alumnos[$n][$parcial] == NULL) {
                            //removido (\.\d{2}) type number onblur="return validarRango(this); min 0 max 100
                            $auxAlumno[$n]['calificacion'] = '<input type="number" name="alumno[' . $alumnos[$n]['id'] . ']"
                        size="6" required  pattern="\d{0,3}" min="0" max="100" >';
                        } else {
                            $auxAlumno[$n]['calificacion'] = $alumnos[$n][$parcial];
                        }
                        $n++;
                    }
                    $parcialNombre = strtoupper($parcial);
                    if (strcmp($parcialNombre, "PRIMERO") == 0 || strcmp($parcialNombre, "TERCERO") == 0) {
                        $parcialNombre = substr($parcialNombre, 0, -1);
                    }
                    $data = array('curso' => $idCurso, 'asignatura' => $curso[0]['nombre'], 'parcial' => $parcial, 'parcialName' => $parcialNombre);
                    $this->vista->mostrar_lista($auxAlumno, $data);
                }
            }
        }
    }

    public function guardar() {

        $parcialModel = new ParcialModel();

        $save_calificaciones = array();
        foreach ($_REQUEST['alumno'] as $alumno => $value) {
            $save_calificaciones["id"] = $alumno;
            $save_calificaciones["parcial"] = strtolower($_POST['parcial']);
            $save_calificaciones["calificacion"] = $value;

            $calificaciones = $parcialModel->update($save_calificaciones);
        }
        $curso = $_POST['curso'];
        header("Location: /parcial/calificaciones/" . $curso);
    }

    public function mostrar($arg = array()) {
        HandlerSession()->check_session(USER_TUTOR);

        $grupo = $_POST['grupo'];
        $estado = $_POST['estado'];

        // obtener cuatrimestre activo
        $cuatrimestreModel = new CuatrimestreModel();
        $cuatrimestre = $cuatrimestreModel->get(1);
        
        // lista de alumnos del grupo solicitado
        $reportesModel = new ReportesModel();
        $alumnos = $reportesModel->lista($grupo, $cuatrimestre[0]['id'], $estado );


        // lista de asignaturas del grupo solicitado
        $reportesModel = new ReportesModel();
        $listacursos = $reportesModel->lista_asignaturas($grupo, $cuatrimestre[0]['id']);

        $cursos = array();

        $auxAlumno = array();
        $n = 0;

        foreach ($alumnos as $row => $alumno) {
            // para cambiar de nombre en la lista
            $auxAlumno[$n]['n'] = ($n + 1);
            $auxAlumno[$n]['id'] = $alumno['id'];
            $auxAlumno[$n]['matricula'] = $alumno['matricula'];
            $auxAlumno[$n]['nombre'] = $alumno['nombre'];
            $reprobadasP1 = 0;
            $reprobadasP2 = 0;
            $reprobadasP3 = 0;
            $reprobadasp = 0;
            $promedio = 0;
            $promedioG = 0;
            $materias = 0;
            foreach ($listacursos as $key => $curso) {

                $parcialModel = new ParcialModel();
                $listaparciales = $parcialModel->lista_calificaciones($curso['id'], 'final');

                $auxAlumno[$n]['primero' . $curso['id']] = 'NC';
                $auxAlumno[$n]['reprobadaP1' . $curso['id']] = '';
                $auxAlumno[$n]['segundo' . $curso['id']] = 'NC';
                $auxAlumno[$n]['reprobadaP2' . $curso['id']] = '';
                $auxAlumno[$n]['tercero' . $curso['id']] = 'NC';
                $auxAlumno[$n]['reprobadaP3' . $curso['id']] = '';
                $auxAlumno[$n]['promedio' . $curso['id']] = 'NC';
                $auxAlumno[$n]['final' . $curso['id']] = 'NC';

                foreach ($listaparciales as $key => $rowCalif) {
                    if ($rowCalif['matricula'] == $alumno['matricula']) {

                        $auxAlumno[$n]['primero' . $curso['id']] = $rowCalif['primero'];
                        $auxAlumno[$n]['segundo' . $curso['id']] = $rowCalif['segundo'];
                        $auxAlumno[$n]['tercero' . $curso['id']] = $rowCalif['tercero'];
                        $promedio = ($rowCalif['primero'] + $rowCalif['segundo'] + $rowCalif['tercero']) / 3;
                        $auxAlumno[$n]['promedio' . $curso['id']] = number_format($promedio, 2); //promedio aun sin redondeo

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
                        } else {  // eliminar round
                            $final = $this->redondear($promedio);
                            $promedioG += $final;
                            $materias++;
                            $auxAlumno[$n]['final' . $curso['id']] = number_format($final, 0); //agregado number format
                        }
                       // break;
                    }
                }
            }

            $promedioG = $promedioG / $materias;
            $auxAlumno[$n]['promedio'] = number_format($promedioG, 0); ;         //modificado format promedio general

            $auxAlumno[$n]['clase1'] = '';
            if ($reprobadasP1 > 2) {
                $auxAlumno[$n]['clase1'] = 'reprobado';
            }
            $auxAlumno[$n]['clase2'] = '';
            if ($reprobadasP2 > 2) {
                $auxAlumno[$n]['clase2'] = 'reprobado';
            }
            $auxAlumno[$n]['clase3'] = '';
            if ($reprobadasP3 > 2) {
                $auxAlumno[$n]['clase3'] = 'reprobado';
            }
            $auxAlumno[$n]['clasep'] = '';
            if ($reprobadasp > 2) {
                $auxAlumno[$n]['clasep'] = 'reprobado';
            }

            $auxAlumno[$n]['reprobadasP1'] = $reprobadasP1;
            $auxAlumno[$n]['reprobadasP2'] = $reprobadasP2;
            $auxAlumno[$n]['reprobadasP3'] = $reprobadasP3;
            $auxAlumno[$n]['reprobadasp'] = $reprobadasp;

            $n++;
        }

        $this->vista->parcialesAlumnos($auxAlumno, $listacursos);
    }

    //convierte el promedio de una calificación final múltiplo de 10
    private function redondear($dato){
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
    
    //editar una calificacion parcial
    public function editar(){
        $idalumno= $_POST['idalumno'];
        $idcurso= $_POST['curso'];
        $parcial= $_POST['parcial'];
        $valor= $_POST['valor'];
        $matricula= $_POST['matricula'];
        $this->modelo->editar($idalumno, $idcurso, $parcial, $valor);
        header('Location: /alumno/reporte/'.$matricula);
    }
}
