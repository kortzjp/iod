<?php

require_once "./core/handlers.php";
require_once './core/DataBase.php';

class AlumnoModel extends DataBase {

    public function delete($alumno = 0) {

        $idCursan = $alumno;

        $this->db_autocommit(false);

        try {
            $this->query = "DELETE FROM parciales WHERE id = $idCursan";
            $this->set_query();
            $this->query = "DELETE FROM asistencias WHERE id = $idCursan";
            $this->set_query();
            $this->query = "DELETE FROM evaluaciones WHERE id = $idCursan";
            $this->set_query();
            $this->query = "DELETE FROM cursan WHERE id = $idCursan";
            $this->set_query();
            $this->db_commit();
        } catch (Exception $e) {
            $this->db_rollback();
            $this->db_autocommit(true);
            return 'Something fails: ' . $e->getMessage() . "\n";
        }
        $this->db_autocommit(true);
        return 'eliminado';
    }

    public function edit($datos = array()) {

        foreach ($datos as $key => $value) {
            $$key = $value;
        }

        try {
            $this->query = "REPLACE INTO alumnos( id, paterno, materno, nombre, genero) "
                    . " VALUES( $id, '$paterno', '$materno', '$nombre', '$genero' ) ";
            $this->set_query();
        } catch (Exception $e) {
            return 'Something fails: ' . $e->getMessage() . "\n";
        }

        return 'actualizado';
    }

    public function alumno($matricula) {
        $this->query = "SELECT al.id, al.usuario as 'matricula', al.nombre "
                . " FROM usuarios al WHERE al.usuario = '$matricula'";

        $this->get_query();
        $num_rows = count($this->rows);

        $data = array();

        foreach ($this->rows as $key => $value) {
            array_push($data, $value);
        }

        return $data;
    }

    public function contestoevaluacion($idUsuario) {
        $this->query = "SELECT DISTINCT(preguntas.idDimension) FROM preguntas"
                . " WHERE preguntas.idDimension NOT IN ("
                . " SELECT DISTINCT(pre.iddimension) FROM "
                . " respuestas res,preguntas pre, cursan cur "
                . " WHERE pre.idpregunta = res.pregunta AND cur.id = res.cursan"
                . " AND cur.alumno = $idUsuario GROUP BY RES.pregunta )";

        $this->get_query();
        $num_rows = count($this->rows);
        // si ya no hay dimenciones por contestar el arreglo datat se enviara vacio  
        $data = array();

        foreach ($this->rows as $key => $value) {
            array_push($data, $value);
        }

        return $data;
    }

    public function asignaturas_alumno($matricula, $cuatrimestre = 0) {
        if ($cuatrimestre == 0) {
            $this->query = "SELECT c.id, a.clave, a.nombre as 'asignatura', d.nombre as 'docente', c.grupo, cr.id as 'cursan', a.cuatrimestre"
                    . " FROM usuarios al, cursan cr, cursos c, usuarios d, asignaturas a"
                    . " WHERE al.usuario = '$matricula'"
                    . " AND cr.alumno = al.id "
                    . " AND c.id = cr.curso "
                    . " AND c.docente = d.id "
                    . " AND c.asignatura = a.id"
                    . " ORDER BY a.cuatrimestre DESC";
        } else {
            $this->query = "SELECT c.id, a.clave, a.nombre as 'asignatura', d.nombre as 'docente', c.grupo, cr.id as 'cursan'"
                    . " FROM usuarios al, cursan cr, cursos c, usuarios d, asignaturas a"
                    . " WHERE al.usuario = '$matricula'"
                    . " AND cr.alumno = al.id "
                    . " AND c.id = cr.curso "
                    . " AND c.cuatrimestre = $cuatrimestre "
                    . " AND c.docente = d.id "
                    . " AND c.asignatura = a.id";
        }

        $this->get_query();
        $num_rows = count($this->rows);

        $data = array();

        foreach ($this->rows as $key => $value) {
            array_push($data, $value);
        }

        return $data;
    }

    public function getPreguntas($dimension) {

        $this->query = "SELECT * FROM preguntas WHERE idDimension = " . $dimension;

        $this->get_query();
        $num_rows = count($this->rows);

        $data = array();

        foreach ($this->rows as $key => $value) {
            array_push($data, $value);
        }

        return $data;
    }

    public function get($matricula = '', $carrera = 0, $generacion = 0, $curso = 0) {
        if (!empty($matricula)) {
            $this->query = "SELECT id, usuario as 'matricula', nombre FROM usuarios "
                    . " WHERE usuario = '$matricula' "
                    . " AND id NOT IN( SELECT alumno FROM cursan WHERE curso = $curso)";
        } else {
            $where = "";
            if ($carrera == 1) {
                if ($generacion < 18) {
                    $where = " usuario LIKE '" . $generacion . "__ISE%'";
                } else if ($generacion == 18) {
                    $where = " usuario LIKE '__" . $generacion . "072%'";
                } else {
                    $where = " usuario LIKE '__" . $generacion . "082%'";
                }
            } else if ($carrera == 2) {
                if ($generacion < 18) {
                    $where = " usuario LIKE '" . $generacion . "__IRT%'";
                } else {
                    $where = " usuario LIKE '__" . $generacion . "061%'";
                }
            } else if ($carrera == 3) {
                if ($generacion < 18) {
                    $where = " usuario LIKE '" . $generacion . "__ITM%'";
                } else {
                    $where = " usuario LIKE '__" . $generacion . "073%'";
                }
            } else if ($carrera == 5) {
                if ($generacion < 18) {
                    $where = " usuario LIKE '" . $generacion . "__LAP%'";
                } else if ($generacion == 18) {
                    $where = " usuario LIKE '__" . $generacion . "054%'";
                } else {
                    $where = " usuario LIKE '__" . $generacion . "064%'";
                }
            } else if ($carrera == 6) {
                if ($generacion < 18) {
                    $where = " usuario LIKE '" . $generacion . "__LTF%'";
                } else {
                    $where = " usuario LIKE '__" . $generacion . "025%'";
                }
            }
            $this->query = "SELECT id, usuario as 'matricula', nombre FROM usuarios WHERE"
                    . $where
                    . " AND id NOT IN( SELECT alumno FROM cursan WHERE curso = $curso)"
                    . " ORDER BY nombre ";
        }
        $this->get_query();

        $data = array();
        foreach ($this->rows as $key => $value) {
            array_push($data, $value);
        }
        return $data;
    }

    public function set($data = array()) {
        foreach ($data as $key => $value) {
            $$key = $value;
        }

        try {
            $this->query = "INSERT INTO cursan ( cuatrimestre, curso, alumno, estado)"
                    . " VALUES ( $cuatrimestre, $curso, $alumno, '$estado' )";
            $idCursan = $this->set_query();
            $this->query = "INSERT INTO parciales(id) VALUES( $idCursan )";
            $this->set_query();
        } catch (Exception $e) {
            return 'Something fails: ' . $e->getMessage() . "\n";
        }

        return 'creado';
    }

    public function setRespuestas($data = array()) {
        foreach ($data as $key => $value) {
            $$key = $value;
        }

        $this->query = "INSERT INTO respuestas ( cursan, pregunta, respuesta)"
                . " VALUES ( $cursan, $pregunta, $respuesta )";
        $this->set_query();
    }

    public function lista($curso) {
        $this->query = "SELECT c.id, a.usuario as 'matricula', a.nombre, c.estado "
                . " FROM usuarios a, cursan c "
                . " WHERE a.id = c.alumno "
                . " AND c.curso = $curso "
                . " ORDER BY c.estado, a.nombre ASC";

        $this->get_query();
        $num_rows = count($this->rows);

        $data = array();

        foreach ($this->rows as $key => $value) {
            array_push($data, $value);
        }

        return $data;
    }

}
