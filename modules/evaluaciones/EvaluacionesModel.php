<?php

require_once "./core/handlers.php";
require_once './core/DataBase.php';

class EvaluacionesModel extends DataBase {

    public function set($data = array()) {
        foreach ($data as $key => $value) {
            $$key = $value;
        }

        $this->query = "INSERT INTO evaluaciones ( id, dia, calificacion )"
                . " VALUES ($id, '$dia', $calificacion)";

        $this->set_query();
    }

    public function get($docente_id = '') {
        $this->query = ($docente_id != '') ? "SELECT c.id, a.nombre, c.grupo "
                . " FROM asignaturas a, cursos c, cuatrimestres cu, cursan cn"
                . " WHERE a.id = c.asignatura AND c.docente = $docente_id AND cu.estado=1 "
                . " AND cu.id=cn.cuatrimestre AND cn.curso= c.id GROUP BY cn.curso ORDER BY c.grupo" : "SELECT c.id, a.nombre, c.estado FROM "
                . " asignaturas a, cursos c "
                . " WHERE a.id = c.asignatura ";

        $this->get_query();

        $num_rows = count($this->rows);

        $data = array();

        foreach ($this->rows as $key => $value) {
            array_push($data, $value);
        }

        return $data;
    }

    public function dia($curso_id = '', $dia = '') {
        $this->query = "SELECT DISTINCT(e.dia) "
                . " FROM cursan c, evaluaciones e "
                . " WHERE c.curso = $curso_id "
                . " AND c.id = e.id "
                . " AND e.dia <= '$dia'";

        $this->get_query();

        $num_rows = count($this->rows);

        $data = array();

        foreach ($this->rows as $key => $value) {
            array_push($data, $value);
        }

        return $data;
    }

    public function lista_evaluaciones($curso, $inicio, $fin) {
        if (empty($inicio) || empty($fin)) {
            $this->query = "SELECT c.id, a.usuario as 'matricula', a.nombre, c.estado, ev.dia, ev.calificacion
                 FROM usuarios a, cursan c, evaluaciones ev, cuatrimestres cu
                 WHERE c.curso = $curso   
                 AND a.id = c.alumno
                 AND c.id = ev.id
                 AND cu.estado = 1
                 AND ev.dia BETWEEN cu.inicio AND NOW()
                 ORDER BY a.nombre, ev.dia ASC";
        } else {
            $this->query = "SELECT c.id, a.usuario as 'matricula', a.nombre, c.estado, ev.dia, ev.calificacion
                 FROM usuarios a, cursan c, evaluaciones ev, cuatrimestres cu
                 WHERE c.curso = $curso   
                 AND a.id = c.alumno
                 AND c.id = ev.id
                 AND cu.estado = 1
                 AND ev.dia BETWEEN '$inicio' AND '$fin'
                 ORDER BY a.nombre, ev.dia ASC";
        }

        $this->get_query();

        $num_rows = count($this->rows);

        $data = array();

        foreach ($this->rows as $key => $value) {
            array_push($data, $value);
        }

        return $data;
    }
    
    public function lista_asistencias($curso, $inicio, $fin) {
        
            $this->query = "SELECT aa.dia FROM asistencias aa "
                . " WHERE aa.id IN "
                ." (SELECT cr.id FROM cursan cr WHERE cr.curso = 33)"
                ." AND aa.dia BETWEEN '2019-01-03' AND '2019-01-15'" 
                ." GROUP BY aa.dia";

        $this->get_query();

        $num_rows = count($this->rows);

        $data = array();

        foreach ($this->rows as $key => $value) {
            array_push($data, $value);
        }

        return $data;
    }

    public function del($status_id = '') {
        $this->query = "DELETE FROM status WHERE status_id = $status_id";
        $this->set_query();
    }

    protected function delete() {
        
    }

    protected function edit() {
        
    }

}
