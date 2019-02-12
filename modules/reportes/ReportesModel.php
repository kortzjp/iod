<?php
require_once "./core/handlers.php";
require_once './core/DataBase.php';
class ReportesModel extends DataBase {

    public function set($data = array()) {
        foreach ($data as $key => $value) {
            $$key = $value;
        }

        $this->query = "REPLACE INTO evaluaciones ( id, dia, calificacion )"
                . " VALUES ($id, '$dia', $calificacion)";

        $this->set_query();
    }

    public function get( $carrera = '', $cuatrimestre = '') {
        
        if (!empty($carrera) && $carrera != 4 ) {
            $this->query = "SELECT DISTINCT(grupo) as grupo
                FROM carreras ca, asignaturas ag, cursos cu 
                WHERE ca.id = $carrera 
                AND ca.id = ag.carrera 
                AND ag.id = cu.asignatura 
                AND cu.estado = $cuatrimestre ORDER BY grupo ASC";
        } else {
            $this->query = " SELECT id, siglas FROM carreras"
                    . " WHERE id <> 4";
        }
        
        $this->get_query();
        $data = array();
        
        foreach ($this->rows as $key => $value) {
            array_push($data, $value);
        }

        return $data;
    }

    public function lista($grupo, $estado = '') {
        $this->query = ($estado != 'Todos') ? "SELECT * FROM alumnos "
                . " WHERE id IN ("
                . " SELECT DISTINCT(alumno) "
                . " FROM cursos c, cursan cn "
                . " WHERE c.grupo = '$grupo' "
                . " AND c.id = cn.curso AND cn.estado = '$estado') "
                . " ORDER BY nombre" 
                : "SELECT * FROM alumnos "
                . " WHERE id IN ("
                . " SELECT DISTINCT(alumno) "
                . " FROM cursos c, cursan cn "
                . " WHERE c.grupo LIKE '$grupo%' "
                . " AND c.id = cn.curso ) "
                . " ORDER BY nombre";

        $this->get_query();
        $num_rows = count($this->rows);

        $data = array();

        foreach ($this->rows as $key => $value) {
            array_push($data, $value);
        }

        return $data;
    }
    
    public function lista_asignaturas($grupo, $estado){
        $this->query = "SELECT c.id, a.clave, a.nombre as 'asignatura', d.nombre as 'docente', c.grupo "
                . " FROM cursos c, docentes d, asignaturas a"
                . " WHERE c.grupo LIKE '%$grupo%'"
                . " AND c.estado = $estado "
                . " AND c.docente = d.id "
                . " AND c.asignatura = a.id "
                . " ORDER BY c.id ASC";

        $this->get_query();
        $num_rows = count($this->rows);

        $data = array();

        foreach ($this->rows as $key => $value) {
            array_push($data, $value);
        }

        return $data;
    }

    public function alumno($matricula){
         $this->query = "SELECT al.id, al.matricula, al.nombre "
                 . " FROM alumnos al WHERE al.matricula='$matricula'";

        $this->get_query();
        $num_rows = count($this->rows);

        $data = array();

        foreach ($this->rows as $key => $value) {
            array_push($data, $value);
        }

        return $data;
    }

    public function asignaturas_alumno($matricula){
        $this->query = "SELECT c.id, a.clave, a.nombre as 'asignatura', d.nombre as 'docente', c.grupo"
                . " FROM alumnos al, cursan cr, cursos c, docentes d, asignaturas a"
                . " WHERE al.matricula='$matricula'"
                . " AND cr.alumno = al.id "
                . " AND c.id = cr.curso "
                . " AND c.estado = 2 "
                . " AND c.docente = d.id "
                . " AND c.asignatura = a.id";

        $this->get_query();
        $num_rows = count($this->rows);

        $data = array();

        foreach ($this->rows as $key => $value) {
            array_push($data, $value);
        }

        return $data;
    }
    
    public function delete($status_id = '') {
        $this->query = "DELETE FROM status WHERE status_id = $status_id";
        $this->set_query();
    }    

    protected function edit() {
        
    }

}
