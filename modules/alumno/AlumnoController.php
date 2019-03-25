<?php

require_once './core/HandlerSession.php';
require_once './core/handlers.php';

require_once 'AlumnoModel.php';
require_once 'AlumnoView.php';

require_once './modules/cuatrimestre/CuatrimestreModel.php';

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
     * Evaluación docente, llama a la vista para dar la veinvenida a la evaluación
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

        $this->modelo = new AlumnoModel();
        $preguntas = $this->modelo->getPreguntas();

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
        HandlerSession()->check_session(USER_TUTOR);
        if (isset($_POST['matricula']) && !empty($_POST['matricula'])) {
            $matricula = recoge('matricula');
            $alumnoModelo = new AlumnoModel();
            $alumno = $alumnoModelo->alumno($matricula);

            $alumnoModelo = new AlumnoModel();
            $asignaturas = $alumnoModelo->asignaturas_alumno($matricula);


            echo '<pre>';
            print_r($alumno);
            print_r($asignaturas);
            echo '</pre>';
        } else {
            header('Location: /tutor/home');
        }
    }

    public function agregar() {
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

    public function editar($id) {
        
    }

    public function confirmar($mensaje = '') {

        echo $mensaje;
    }

}
