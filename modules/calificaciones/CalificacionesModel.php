<?php

require_once "./core/handlers.php";
require_once './core/DataBase.php';

class ParcialModel extends DataBase {

    public function set($parcial_data = array()) {
        foreach ($ms_data as $key => $value) {
            $$key = $value;
        }

        $this->query = "REPLACE INTO parciales SET "
                . "$parcial  = $calificacion "
                . " WHERE cuatrimestre = $cuatrimestre "
                . " AND curso = $curso "
                . " AND alumno = '$alumno' ";

        $this->set_query();
    }

    public function update($parcial_data = array()) {
        foreach ($parcial_data as $key => $value) {
            $$key = $value;
        }

        $this->query = "UPDATE parciales SET "
                . "$parcial  = $calificacion "
                . " WHERE id = $id ";

        return $this->set_query();
    }

    public function get($docente = '') {
        $this->query = ($docente != '') ? "SELECT c.id, a.nombre, c.grupo "
                . " FROM asignaturas a, cursos c, cuatrimestres cu, cursan cn"
                . " WHERE a.id = c.asignatura AND c.docente = $docente AND cu.estado=1 "
                . " AND cu.id=cn.cuatrimestre AND cn.curso= c.id GROUP BY cn.curso ORDER BY c.grupo" 
                : "SELECT c.id, a.nombre, c.estado FROM "
                . " asignaturas a, cursos c "
                . " WHERE a.id = c.asignatura ";

        $this->get_query();

        $data = array();
        foreach ($this->rows as $key => $value) {
            array_push($data, $value);
        }

        return $data;
    }

    public function lista_alumnos($curso) {
        $this->query = "SELECT c.id, a.usuario as 'matricula', a.nombre, c.estado "
                . " FROM usuarios a, cursan c "
                . " WHERE a.id = c.alumno "
                . " AND c.curso = $curso "
                . " ORDER BY c.estado, a.nombre ASC";

        $this->get_query();
        $data = array();
        foreach ($this->rows as $key => $value) {
            array_push($data, $value);
        }
        return $data;
    }

    public function lista_calificaciones($curso, $parcial) {
        if ($parcial != 'final') {
            $this->query = "SELECT p.id, a.usuario as 'matricula', a.nombre, c.estado, p.$parcial"
                    . " FROM cursan c, usuarios a, parciales p"
                    . " WHERE c.curso = $curso"
                    . " AND c.id = p.id"
                    . " AND c.alumno = a.id"
                    . " ORDER BY c.estado, a.nombre";
        } else {
            $this->query = "SELECT a.id, a.usuario as 'matricula', a.nombre, p.primero, p.segundo, p.tercero, c.estado "
                    . " FROM cursan c, usuarios a, parciales p"
                    . " WHERE c.curso = $curso"
                    . " AND c.id = p.id"
                    . " AND c.alumno = a.id"
                    . " ORDER BY c.estado, a.nombre";
        }

        $this->get_query();
        $num_rows = count($this->rows);
        $data = array();

        foreach ($this->rows as $key => $value) {
            array_push($data, $value);
        }

        return $data;
    }

    public function delete() {
        
    }

    protected function edit() {
        
    }

}
