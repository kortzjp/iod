<?php

require_once "./core/handlers.php";
require_once './core/DataBase.php';

class CuadrantesModel extends DataBase {

    public function set($data = array()) {
        foreach ($data as $key => $value) {
            $$key = $value;
        }

        $this->query = "REPLACE INTO evaluaciones ( id, dia, calificacion )"
                . " VALUES ($id, '$dia', $calificacion)";

        $this->set_query();
    }

    public function get($carrera = 0, $cuatrimestre = 0) {
        if (!empty($carrera) && $carrera != 4) {
            $this->query = "SELECT DISTINCT(grupo) as grupo
                FROM carreras ca, asignaturas ag, cursos cu 
                WHERE ca.id = $carrera 
                AND ca.id = ag.carrera 
                AND ag.id = cu.asignatura 
                AND cu.cuatrimestre = $cuatrimestre ORDER BY grupo ASC";
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

    public function get_ingles($carrera, $cuatrimestre) {

        $this->query = "SELECT c.id, a.nombre as 'asignatura', d.nombre as 'docente', c.grupo 
                 FROM cursos c, docentes d, asignaturas a
                 WHERE a.clave LIKE 'ING%'
                 AND c.estado = $cuatrimestre
                 AND a.carrera = $carrera 
                 AND c.docente = d.id 
                 AND c.asignatura = a.id 
                 ORDER BY c.id ASC";

        $this->get_query();
        $num_rows = count($this->rows);

        $data = array();

        foreach ($this->rows as $key => $value) {
            array_push($data, $value);
        }

        return $data;
    }

    public function alumnos_ingles($cursos = array(), $cuatrimestre) {
        $sentencia = "SELECT cu.id as 'curso', COUNT(cr.id) as 'alumnos'
                 FROM cursan cr, cursos cu
                 WHERE cu.id IN ( ";
        $total = count($cursos);
        $n = 0;
        foreach ($cursos as $key => $value) {
            if ($n == 0)
                $sentencia .= $value;
            else
                $sentencia .= ", " . $value;
            $n++;
        }
        $sentencia .= " ) AND cu.estado = $cuatrimestre
                 AND cr.curso = cu.id
                 GROUP by cr.curso
                 ORDER BY cu.id ASC";

        $this->query = $sentencia;
        $this->get_query();
        $num_rows = count($this->rows);

        $data = array();

        foreach ($this->rows as $key => $value) {
            array_push($data, $value);
        }

        return $data;
    }

    public function lista($grupo, $cuatrimestre) {
        $this->query = "SELECT c.id, a.nombre as 'asignatura', d.nombre as 'docente', c.grupo "
                . " FROM cursos c, usuarios d, asignaturas a"
                . " WHERE c.grupo LIKE '%$grupo%'"
                . " AND c.cuatrimestre = $cuatrimestre "
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

    public function alumnos_asignatura($grupo, $cuatrimestre) {
        $this->query = "SELECT cu.id as 'curso', COUNT(cr.id) as 'alumnos'"
                . " FROM cursan cr, cursos cu"
                . " WHERE cu.grupo LIKE '%$grupo%'"
                . " AND cu.cuatrimestre = $cuatrimestre"
                . " AND cr.curso = cu.id"
                . " GROUP by cr.curso"
                . " ORDER BY cu.id ASC";

        $this->get_query();
        $num_rows = count($this->rows);

        $data = array();

        foreach ($this->rows as $key => $value) {
            array_push($data, $value);
        }

        return $data;
    }

    public function quincenas_cuadrante() {
        // consulta para saber cuantos dias hay entre las fechas de inico de semestre y fin de semestre
        $this->query = "SELECT c.nombre, c.inicio, c.fin "
                . " FROM cuatrimestres c "
                . " WHERE c.estado = 1 LIMIT 1";

        $this->get_query();
        $num_rows = count($this->rows);

        $data = array();

        foreach ($this->rows as $key => $value) {
            array_push($data, $value);
        }

        foreach ($data[0] as $key => $value) {
            $$key = $value;
        }

        $inicioMes = new DateTime($inicio);
        $finMes = new DateTime($fin);

        $hoy = date("Y-m-d");

        $hoyTope = new DateTime($hoy);
        $fechas = array();
        $i = 1;

        //$inicioMes->modify('first day of this month');
        $auxInicio = new DateTime($inicioMes->format('Y-m-d'));

        $dias = 15 - $inicioMes->format('d');        
        $rango = 'P'.$dias.'D';
        
        $inicioMes->add(new DateInterval($rango));
        $auxFin = new DateTime($inicioMes->format('Y-m-d'));

        if ($auxFin <= $hoyTope) {
            $inicioMes->add(new DateInterval('P1D'));
            $fechas[$i]['inicio'] = $auxInicio->format('Y-m-d');
            $fechas[$i]['fin'] = $auxFin->format('Y-m-d');
        }

        $i++;

        $auxInicio = new DateTime($inicioMes->format('Y-m-d'));
        $inicioMes->modify('last day of this month');
        $auxFin = new DateTime($inicioMes->format('Y-m-d'));

        if ($auxFin <= $hoyTope) {
            $fechas[$i]['inicio'] = $auxInicio->format('Y-m-d');
            $fechas[$i]['fin'] = $auxFin->format('Y-m-d');
        }

        $inicioMes->add(new DateInterval('P1D'));
        $i++;

        while ($inicioMes <= $finMes && $inicioMes <= $hoyTope) {

            $inicioMes->modify('first day of this month');
            $auxInicio = new DateTime($inicioMes->format('Y-m-d'));

            $inicioMes->add(new DateInterval('P14D'));
            $auxFin = new DateTime($inicioMes->format('Y-m-d'));

            if ($auxFin <= $hoyTope) {
                $inicioMes->add(new DateInterval('P1D'));
                $fechas[$i]['inicio'] = $auxInicio->format('Y-m-d');
                $fechas[$i]['fin'] = $auxFin->format('Y-m-d');
            }

            $i++;

            $auxInicio = new DateTime($inicioMes->format('Y-m-d'));
            $inicioMes->modify('last day of this month');
            $auxFin = new DateTime($inicioMes->format('Y-m-d'));

            if ($auxFin <= $hoyTope) {
                $fechas[$i]['inicio'] = $auxInicio->format('Y-m-d');
                $fechas[$i]['fin'] = $auxFin->format('Y-m-d');
            }

            $inicioMes->add(new DateInterval('P1D'));
            $i++;
        }

        return $fechas;
    }

    public function delete($status_id = '') {
        $this->query = "DELETE FROM status WHERE status_id = $status_id";
        $this->set_query();
    }

    protected function edit() {
        
    }

}
