<?php

require_once "./core/handlers.php";
require_once './core/DataBase.php';

class CursoModel extends DataBase {

    public function guardar() {
        
    }

    public function delete() {
        
    }

    public function edit($datos = array()) {

    }

    public function get($id = 0, $docente = 0) {
        $tipo = USER_DOC;
        $this->query = "SELECT * FROM cursos WHERE id = $id AND docente = $docente LIMIT 1";

        $this->get_query();
        $num_rows = count($this->rows);
        $data = array();

        foreach ($this->rows as $key => $value) {
            array_push($data, $value);
        }

        return $data;
    }
    
     public function getCursoAsignatura($id = 0, $docente = 0) {
        $tipo = USER_DOC;
        $this->query = "SELECT * FROM cursos cr, asignaturas asig "
                . " WHERE cr.id = $id "
                . " AND cr.docente = $docente"
                . " AND cr.asignatura = asig.id LIMIT 1";

        $this->get_query();
        $num_rows = count($this->rows);
        $data = array();

        foreach ($this->rows as $key => $value) {
            array_push($data, $value);
        }

        return $data;
    }

    public function getCursosDocente($id) {
        $this->query = "SELECT c.id, a.nombre, c.grupo "
                . " FROM asignaturas a, cursos c, cuatrimestres cu "
                . " WHERE a.id = c.asignatura AND c.docente = $id "
                . " AND cu.estado = 1 AND cu.id = c.cuatrimestre "
                . " ORDER BY c.grupo";

        $this->get_query();

        $data = array();
        foreach ($this->rows as $key => $value) {
            array_push($data, $value);
        }

        return $data;
    }

    public function getCursosTutor($carrera) {
        if ($carrera != 4) {
            $this->query = "SELECT cr.id, cr.grupo as 'grupo', ag.nombre as 'asignatura', doc.nombre as 'docente' "
                    . " FROM cuatrimestres cu, cursos cr, asignaturas ag, usuarios doc "
                    . " WHERE cu.estado = 1 "
                    . " AND cu.id = cr.cuatrimestre "
                    . " AND doc.id = cr.docente "
                    . " AND ag.carrera = $carrera "
                    . " AND cr.asignatura = ag.id "
                    . " ORDER BY cr.grupo, ag.nombre ASC";
        } else {
            $this->query = " SELECT id, siglas FROM carreras"
                    . " WHERe id <> 4";
        }

        $this->get_query();
        $num_rows = count($this->rows);

        $data = array();
        foreach ($this->rows as $key => $value) {
            array_push($data, $value);
        }

        return $data;
    }

    public function set($datos = array()) {
        foreach ($datos as $key => $value) {
            $$key = $value;
        }

        //$conexion = new mysqli("107.180.58.66", "academia", "iseAcademia?18", "sistemastutorias");
        $conexion = new mysqli("localhost", "root", "", "sistemastutorias");
        if ($conexion->connect_errno) {
            die('Error en la conexion: ' . $conexion->connect_errno);
        }
        $conexion->set_charset("utf8");
        $conexion->begin_transaction();

        $sql = "INSERT INTO cursos ( cuatrimestre, asignatura, docente, grupo )"
                . " VALUES ( ?, ?, ?, ? )";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("iiis", $cuatrimestre, $asignatura, $docente, $grupo);
        if (!$stmt->execute()) {
            $conexion->rollback();
            return 'error';
        }

        $sql = "INSERT INTO horarios ( id, lunes, martes, miercoles, jueves, viernes, sabado )"
                . " VALUES ( ?, ?, ?, ?, ?, ?, ? )";
        $idCurso = $stmt->insert_id;
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("issssss", $idCurso, $lunes, $martes, $miercoles, $jueves, $viernes, $sabado);
        if (!$stmt->execute()) {
            $conexion->rollback();
            return 'error';
        }

        //$conexion->commit();
        $conexion->commit();
        $stmt->close();
        $conexion->close();
        return 'creado';
    }

}
