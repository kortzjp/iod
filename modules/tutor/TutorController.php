<?php

require_once './core/HandlerSession.php';
require_once 'TutorView.php';
require_once 'TutorModel.php';

require_once './modules/cuatrimestre/CuatrimestreModel.php';
require_once './modules/carrera/CarreraModel.php';
require_once './modules/asignatura/AsignaturaModel.php';
require_once './modules/docente/DocenteModel.php';
require_once './modules/curso/CursoModel.php';
require_once './modules/cuadrantes/CuadrantesModel.php';

class TutorController {

    private $vista;
    private $modelo;

    function __construct($metodo, $arg) {

        $this->vista = new TutorView();
        $this->modelo = new TutorModel();

        if (method_exists($this, $metodo)) {
            call_user_func(array($this, $metodo), $arg);
        } else {
            print 'Recurso inexistente';
        }
    }

    public function home() {
        HandlerSession()->check_session(USER_TUTOR);

        $this->vista->home();
    }

    public function cuadrantes($arg = array()) {
        HandlerSession()->check_session(USER_TUTOR);

        $tutor = $this->modelo->get($_SESSION['id']);
        $carrera = $tutor[0]['carrera'];

        $cuatrimestreModelo = new CuatrimestreModel();
        $cuatrimestre = $cuatrimestreModelo->get(1);  // cuatrimestre activo

        $cuadranteModelo = new CuadrantesModel();
        $grupos = $cuadranteModelo->get($carrera, $cuatrimestre[0]['id']);

        $cuadranteModelo = new CuadrantesModel();
        $quincenas = $cuadranteModelo->quincenas_cuadrante();

        $lista_quincenas = array();
        for ($n = 0; $n < count($quincenas); $n++) {
            $fecha_inicio = $quincenas[$n + 1]['inicio'];
            $fecha_fin = $quincenas[$n + 1]['fin'];
            $mensaje = "[ " . date("M j", strtotime($fecha_inicio)) . " a " . date("j", strtotime($fecha_fin)) . " ]";
            $registro = array('quincenas' => $n + 1, 'fecha' => $mensaje);
            $obj = (object) $registro;
            $lista_quincenas[] = $obj;
        }

        $mensaje = '';
        $tipo = '';

        $this->vista->cuadrantes($grupos, $lista_quincenas, $carrera, $cuatrimestre[0]['id'], $mensaje, $tipo);
    }

    public function parciales($arg = array()) {
        HandlerSession()->check_session(USER_TUTOR);

        $tutor = $this->modelo->get($_SESSION['id']);
        $carrera = $tutor[0]['carrera'];

        $cuatrimestreModelo = new CuatrimestreModel();
        $cuatrimestre = $cuatrimestreModelo->get(1);  // cuatrimestre activo

        $cuadranteModelo = new CuadrantesModel();
        $grupos = $cuadranteModelo->get($carrera, $cuatrimestre[0]['id']);

        $mensaje = '';
        $tipo = '';

        $this->vista->parciales($grupos, $mensaje, $tipo);
    }

    public function calificaciones($arg = array()) {
        HandlerSession()->check_session(USER_TUTOR);

        $tutor = $this->modelo->get($_SESSION['id']);
        $carrera = $tutor[0]['carrera'];

        $cuatrimestreModelo = new CuatrimestreModel();
        $cuatrimestre = $cuatrimestreModelo->get(1);  // cuatrimestre activo

        $cuadranteModelo = new CuadrantesModel();
        $grupos = $cuadranteModelo->get($carrera, $cuatrimestre[0]['id']);

        $mensaje = '';
        $tipo = '';

        $this->vista->listaGrupos($grupos, $mensaje, $tipo);
    }

    public function cursos($arg = array()) {
        HandlerSession()->check_session(USER_TUTOR);

        $tutor = $this->modelo->get($_SESSION['id']);

        $cuatriModelo = new CuatrimestreModel();
        $cuatrimestre = $cuatriModelo->get(0);

        $asignaturaModelo = new AsignaturaModel();
        $carrera = $tutor[0]['carrera'];
        $asignaturas = $asignaturaModelo->get(0, $carrera);

        $docenteModelo = new DocenteModel();
        $docentes = $docenteModelo->get(1);

        $cursoModelo = new CursoModel();
        $cursos = $cursoModelo->getCursosTutor($carrera);

        $mensaje = '';
        $tipo = '';
        if (isset($arg[0]) && $arg[0] == 'success') {
            $mensaje = 'Curso creado correctamente';
            $tipo = 'callout-success';
        } else if (isset($arg[0]) && $arg[0] == 'danger') {
            $mensaje = 'Curso no creado.';
            $tipo = 'callout-danger';
        } else if (isset($arg[0]) && $arg[0] == 'editada') {
            $mensaje = 'Curso editado correctamente.';
            $tipo = 'callout-success';
        } else if (isset($arg[0]) && $arg[0] == 'no_editada') {
            $mensaje = 'Curso no editado.';
            $tipo = 'callout-danger';
        } else if (isset($arg[0]) && $arg[0] == 'error') {
            $mensaje = 'Faltan datos para poder crear el curso.';
            $tipo = 'callout-danger';
        }

        $this->vista->listaCursos($cursos, $cuatrimestre, $asignaturas, $docentes, $mensaje, $tipo);
    }

    public function asignaturas($arg = array()) {
        HandlerSession()->check_session(USER_TUTOR);
        $tutor = $this->modelo->get($_SESSION['id']);

        $asignaturaModelo = new AsignaturaModel();
        $carrera = $tutor[0]['carrera'];
        $asignaturas = $asignaturaModelo->get(0, $carrera);
        $mensaje = '';
        $tipo = '';
        if (isset($arg[0]) && $arg[0] == 'success') {
            $mensaje = 'Asignatura creada correctamente';
            $tipo = 'callout-success';
        } else if (isset($arg[0]) && $arg[0] == 'danger') {
            $mensaje = 'Asignatura no creada.';
            $tipo = 'callout-danger';
        } else if (isset($arg[0]) && $arg[0] == 'editada') {
            $mensaje = 'Asignatura editada correctamente.';
            $tipo = 'callout-success';
        } else if (isset($arg[0]) && $arg[0] == 'no_editada') {
            $mensaje = 'Asignatura no editada.';
            $tipo = 'callout-danger';
        }

        $this->vista->listaAsignaturas($asignaturas, $carrera, $mensaje, $tipo);
    }

    public function docentes($arg = array()) {
        HandlerSession()->check_session(USER_TUTOR);

        $docenteModelo = new DocenteModel();
        $docentes = $docenteModelo->get();

        $mensaje = '';
        $tipo = '';
        if (isset($arg[0]) && $arg[0] == 'success') {
            $mensaje = 'Docente creado correctamente';
            $tipo = 'callout-success';
        } else if (isset($arg[0]) && $arg[0] == 'danger') {
            $mensaje = 'Docente no creado.';
            $tipo = 'callout-danger';
        } else if (isset($arg[0]) && $arg[0] == 'editada') {
            $mensaje = 'Docente editado correctamente.';
            $tipo = 'callout-success';
        } else if (isset($arg[0]) && $arg[0] == 'no_editada') {
            $mensaje = 'Docente no editado.';
            $tipo = 'callout-danger';
        }

        $this->vista->listaDocentes($docentes, $mensaje, $tipo);
    }

    // evalucion docente
    public function resultados($arg = array()) {
        HandlerSession()->check_session(USER_TUTOR);
        //$docente = $_SESSION['id'];
        $this->modelo = new DocenteModel();
        $datosDocente = $this->modelo->getDocente($arg[0]);
        
        $docente = $datosDocente[0]['id'];     
        $nombre = $datosDocente[0]['nombre'];
        
        $this->modelo = new DocenteModel();
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
        if ($totalEcuestados == 0) {
            $mensaje = "Por el momento aún no hay resultados.";
            $tipo = "callout-warning";
            $this->vista->resultados($datos, 0, 'red', $mensaje, $tipo, $nombre);
        } else {
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
                    else if ($porcentaje >= 80)
                        $color = "info";
                    else if ($porcentaje >= 70)
                        $color = "warning";
                    $datos[] = array("id" => $dimension["idDimension"], "dimension" => $dimension["dimension"], "color" => $color, "porcentaje" => round($porcentaje));
                }
            }
            //exit();
            $promedio = $sumaPromedio / $numeroDimensiones;
            $color = "danger";
            if ($promedio >= 90)
                $color = "success";
            else if ($promedio >= 80)
                $color = "info";
            else if ($promedio >= 70)
                $color = "warning";

            $this->vista->resultados($datos, $promedio, $color, $mensaje, $tipo, $nombre);
        }
    }

    public function crear() {
        HandlerSession()->check_session(USER_ADMIN);

        $carreraModelo = new CarreraModel( );
        $resultado = $carreraModelo->darCarrera();

        $usuario = $_SESSION['usuario'];
        $nombre = $_SESSION['nombre'];
        $this->vista->crear($resultado, $usuario, $nombre);
        //echo "Formulario para crear una Tutor";
    }

    public function guardar() {
        HandlerSession()->check_session(USER_ADMIN);

        $this->modelo = new TutorModel();
        $respuesta = $this->modelo->guardar();

        header('Location: /tutor/confirmar/' . $respuesta);
    }

    public function confirmar($mensaje = '') {

        echo $mensaje;
    }

}
