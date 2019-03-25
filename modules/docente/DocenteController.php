<?php

require_once './core/HandlerSession.php';
require_once './core/handlers.php';

require_once 'DocenteView.php';
require_once 'DocenteModel.php';

require_once './modules/asignatura/AsignaturaModel.php';
require_once './modules/carrera/CarreraModel.php';
require_once './modules/cuatrimestre/CuatrimestreModel.php';
require_once './modules/curso/CursoModel.php';
require_once './modules/alumno/AlumnoModel.php';
require_once './modules/parcial/ParcialModel.php';
require_once './modules/horario/HorarioModel.php';
require_once './modules/asistencias/AsistenciasModel.php';

class DocenteController {

    private $vista;
    private $modelo;

    function __construct($metodo, $arg) {

        $this->vista = new DocenteView();
        $this->modelo = new DocenteModel();

        if (method_exists($this, $metodo)) {
            call_user_func(array($this, $metodo), $arg);
        } else {
            //print 'Recurso inexistente';
            $this->home();
        }
    }

    public function home() {
        HandlerSession()->check_session(USER_DOC);
        //$cursos = $this->modelo->getCursos($_SESSION['id']);
        $this->vista->home();
    }

    public function alumnos() {
        HandlerSession()->check_session(USER_DOC);
        $cursoModelo = new CursoModel();
        $cursos = $cursoModelo->getCursosDocente($_SESSION['id']);
        if (empty($cursos)) {
            $tipo = 'danger';
            $titulo = 'Cursos';
            $mensaje = 'Por el momento no hay cursos,'
                    . ' le sugerimos ponerse en contacto con algún tutor de carrera'
                    . ' para verificar esto.';
            $this->vista->mensaje($tipo, $titulo, $mensaje);
        } else {
            $mensaje = '';
            $tipo = '';
            $this->vista->alumnos($cursos, $mensaje, $tipo);
        }
    }

    public function lista_alumnos($arg = array()) {
        HandlerSession()->check_session(USER_DOC);
        if (empty($arg) || $arg[0] == '') {
            header('Location: /docente/alumnos');
        } else {
            $idCurso = $arg[0];
            $idDocenet = $_SESSION['id'];

            $cursoModelo = new CursoModel();
            $curso = $cursoModelo->get($idCurso, $idDocenet);

            if (empty($curso)) {
                header('Location: /docente/alumnos');
            } else if (isset($_POST['buscar_g']) || isset($_POST['buscar_m'])) {
                $this->buscarAlumnos($curso);
            } else {
                $parcialModelo = new ParcialModel();
                $listaAlumnos = $parcialModelo->lista_alumnos($idCurso);

                $carreraModelo = new CarreraModel();
                $carreras = $carreraModelo->get();

                $asignaturaModelo = new AsignaturaModel();
                $asignatura = $asignaturaModelo->get($curso[0]['asignatura']);

                $mensaje = '';
                $tipo = '';
                if (isset($arg[1]) && $arg[1] == 'agregado') {
                    $mensaje = 'Los alumnos se agregaron correctamente al curso.';
                    $tipo = 'callout-success';
                } else if (isset($arg[1]) && $arg[1] == 'borrado') {
                    $mensaje = 'El alumno ha sido eliminado del curso.';
                    $tipo = 'callout-danger';
                }

                $datosContenido = array(
                    'curso' => $idCurso,
                    'grupo' => $curso[0]['grupo'],
                    'asignatura' => $asignatura[0]['nombre'],
                    'mensaje' => $mensaje,
                    'tipo' => $tipo
                );

                $this->vista->lista_alumnos_eliminar($listaAlumnos, $carreras, $datosContenido);
            }
        }
    }

    public function buscarAlumnos($datosCurso) {

        if (isset($_POST['buscar_g'])) {
            $carrera = recoge('carrera');
            $generacion = recoge('generacion');
            $curso = recoge('curso');

            $alumnoModelo = new AlumnoModel();
            $listaAlumnos = $alumnoModelo->get('', $carrera, $generacion, $curso);

            $carreraModelo = new CarreraModel();
            $carreras = $carreraModelo->get();

            $datosContenido = array(
                'curso' => $curso,
                'grupo' => $_POST['grupo'],
                'asignatura' => $_POST['asignatura'],
                'mensaje' => '',
                'tipo' => ''
            );

            $this->vista->lista_alumnos_agregar($listaAlumnos, $carreras, $datosContenido);
        } else if (isset($_POST['buscar_m'])) {

            $matricula = recoge('matricula');
            $curso = recoge('curso');

            $alumnoModelo = new AlumnoModel();
            $listaAlumnos = $alumnoModelo->get($matricula, 0, 0, $curso);

            $carreraModelo = new CarreraModel();
            $carreras = $carreraModelo->get();

            $datosContenido = array(
                'curso' => $curso,
                'grupo' => $_POST['grupo'],
                'asignatura' => $_POST['asignatura'],
                'mensaje' => '',
                'tipo' => ''
            );

            $this->vista->lista_alumnos_agregar($listaAlumnos, $carreras, $datosContenido);
        }
    }

    public function asistencias() {
        HandlerSession()->check_session(USER_DOC);
        $cursos = $this->modelo->getCursos($_SESSION['id']);

        if (empty($cursos)) {
            $tipo = 'danger';
            $titulo = 'Cursos';
            $mensaje = 'Por el momento no hay cursos,'
                    . ' le sugerimos ponerse en contacto con algún tutor de carrera'
                    . ' para verificar esto.';
            $this->vista->mensaje($tipo, $titulo, $mensaje);
        } else {
            $cuatrimestreModelo = new CuatrimestreModel();
            $cuatrimestre = $cuatrimestreModelo->get(1);

            $mes = new DateTime($cuatrimestre[0]['inicio']);
            $meses = array("ENERO", "FEBRERO", "MARZO", "ABRIL", "MAYO", "JUNIO", "JULIO",
                "AGOSTO", "SEPTIEMBRE", "OCTUBRE", "NOVIEMBRE", "DICIEMBRE");
            $m = date("n", strtotime($mes->format('Y-m-d')));

            $datosMes = array(
                'primero' => $meses[$m - 1],
                'segundo' => $meses[$m],
                'tercero' => $meses[$m + 1],
                'cuarto' => $meses[$m + 2]);

            $mensaje = '';
            $tipo = '';
            $this->vista->asistencias($cursos, $datosMes, $mensaje, $tipo);
        }
    }

    public function evaluaciones() {
        HandlerSession()->check_session(USER_DOC);
        $cursos = $this->modelo->getCursos($_SESSION['id']);

        if (empty($cursos)) {
            $tipo = 'danger';
            $titulo = 'Cursos';
            $mensaje = 'Por el momento no hay cursos,'
                    . ' le sugerimos ponerse en contacto con algún tutor de carrera'
                    . ' para verificar esto.';
            $this->vista->mensaje($tipo, $titulo, $mensaje);
        } else {


            $mensaje = '';
            $tipo = '';
            $this->vista->evaluaciones($cursos, $mensaje, $tipo);
        }
    }

    public function parciales() {
        HandlerSession()->check_session(USER_DOC);
        $cursos = $this->modelo->getCursos($_SESSION['id']);

        if (empty($cursos)) {
            $tipo = 'danger';
            $titulo = 'Cursos';
            $mensaje = 'Por el momento no hay cursos,'
                    . ' le sugerimos ponerse en contacto con algún tutor de carrera'
                    . ' para verificar esto.';
            $this->vista->mensaje($tipo, $titulo, $mensaje);
        } else {


            $mensaje = '';
            $tipo = '';
            $this->vista->parciales($cursos, $mensaje, $tipo);
        }
    }

    // evalucion docente
    public function resultados() {
        HandlerSession()->check_session(USER_DOC);
        $docente = $_SESSION['id'];
        $cursos = $this->modelo->getCursos($docente);
        // total de alumnos encuestados
        $totalEcuestados = 0;
        foreach ($cursos as $curso) {
            // aquí se podria sacar un total por curso
            $this->modelo = new DocenteModel();
            $totalEcuestados += $this->modelo->encuestados($curso["id"]);
        }

       // echo "Total de encuestados " . $totalEcuestados . "<br>";
        $datos = array();
        $mensaje = "";
        $tipo = "";
        if( $totalEcuestados == 0 ) {
            $mensaje = "Por el momento aún no hay resultados.";
            $tipo = "callout-warning";
            $this->vista->resultados($datos, 0, 'red', $mensaje, $tipo);
        }
        else {
        $this->modelo = new DocenteModel();
        $dimensiones = $this->modelo->getDimensiones();
        $sumaPromedio = 0;
        $numeroDimensiones = 0;
        foreach ($dimensiones as $key => $dimension) {

            $this->modelo = new DocenteModel();
            $encuestadosDimension = $this->modelo->puntosDimension($docente, $dimension["idDimension"]);
            // numero de puntos por dimension
            $puntosDimension = $dimension["puntos"];
            if ($encuestadosDimension[0]['obtenidos'] != NULL) {
                $puntosObtenidos = $encuestadosDimension[0]['obtenidos'];
                $porcentaje = (100 * $puntosObtenidos) / ($puntosDimension * $totalEcuestados);
                //echo "Dimension ". $dimension["idDimension"]. " ".$puntosDimension . " -  " . $puntosDimension * $totalEcuestados . " $puntosObtenidos - $porcentaje <br>";
                $sumaPromedio += round($porcentaje);
                $numeroDimensiones++;
                $color = "danger";
                if ($porcentaje >= 90)
                    $color = "success";
                else if ($porcentaje >= 70)
                    $color = "info";
                else if ($porcentaje >= 50)
                    $color = "warning";
                $datos[] = array("id" => $dimension["idDimension"], "dimension" => $dimension["dimension"], "color" => $color, "porcentaje" => round($porcentaje));
            }
        }
        //exit();
        $promedio = $sumaPromedio / $numeroDimensiones;
        $color = "danger";
        if ($promedio >= 90)
            $color = "success";
        else if ($promedio >= 70)
            $color = "info";
        else if ($promedio >= 50)
            $color = "warning";

        $this->vista->resultados($datos, $promedio, $color, $mensaje, $tipo);
        }
    }

    public function crear() {
        HandlerSession()->check_session(USER_TUTOR);

        $usuario = recoge('usuario');
        $nombre = recoge('nombre');
        $correo = recoge('correo');
        $estado = recoge('estado');

        if ($usuario == '' || $nombre == '' || $correo == '' || $estado == '') {
            header('Location: /tutor/asignaturas/error');
        } else {
            $password = hashPassword($usuario);
            $datos = array(
                'usuario' => $usuario,
                'password' => $password,
                'nombre' => $nombre,
                'correo' => $correo,
                'activacion' => $estado);

            $respuesta = $this->modelo->set($datos);

            header('Location: /tutor/docentes/' . $respuesta);
        }
    }

    public function guardar() {
        HandlerSession()->check_session(USER_TUTOR);

        $id = recoge('id');
        $usuario = recoge('usuario');
        $nombre = recoge('nombre');
        $correo = recoge('correo');
        $estado = recoge('estado');

        if ($correo == '' || $usuario == '' || $nombre == '' || $estado == '') {
            header('Location: /tutor/docentes/error');
        } else {
            $datos = array(
                'id' => $id,
                'usuario' => $usuario,
                'nombre' => $nombre,
                'correo' => $correo,
                'activacion' => $estado);

            $respuesta = $this->modelo->edit($datos);

            header('Location: /tutor/docentes/' . $respuesta);
        }
    }

    public function confirmar($mensaje = '') {

        echo $mensaje;
    }

}
