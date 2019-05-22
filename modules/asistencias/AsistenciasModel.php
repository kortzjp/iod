<?php

require_once "./core/handlers.php";
require_once './core/DataBase.php';

class AsistenciasModel extends DataBase {

    public function set($data = array()) {
        foreach ($data as $key => $value) {
            $$key = $value;
        }

        //$this->query = "REPLACE INTO asistencias ( id, dia, asistencia )"
        $this->query = "INSERT INTO asistencias ( id, dia, asistencia )"
                . " VALUES ($id, '$dia', $asistencia)";

        $this->set_query();
    }

    // curso del docente
    public function get($docente = '') {
        $this->query = ($docente != '') ? "SELECT c.id, a.nombre, c.grupo "
                . " FROM asignaturas a, cursos c, cuatrimestres cu, cursan cn"
                . " WHERE a.id = c.asignatura AND c.docente = $docente AND cu.estado = 1 "
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

    // alumnos del curso
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
    
    public function lista_asistencias($curso, $inicioFecha, $finFecha) {

        $this->query = "SELECT c.id, a.usuario as 'matricula', a.nombre, c.estado, aa.dia, aa.asistencia
                 FROM usuarios a, cursan c, asistencias aa
                 WHERE c.curso = $curso   
                 AND a.id = c.alumno
                 AND c.id = aa.id
                 AND aa.dia BETWEEN '$inicioFecha' AND '$finFecha'
                 ORDER BY a.nombre, aa.dia ASC";

        $this->get_query();
        $num_rows = count($this->rows);
        $data = array();

        foreach ($this->rows as $key => $value) {
            array_push($data, $value);
        }

        return $data;
    }
    
    // alumnos del curso y sus asitencias
    public function lista_asistencias_datos($curso, $inicioFecha, $finFecha) {

        $this->query = "SELECT cr.id, cr.curso, cr.alumno, asis.dia, asis.asistencia"
                . " FROM cursan as cr LEFT JOIN ("
                . " SELECT aa.id, aa.dia, aa.asistencia "
                . " FROM cursan c, asistencias aa "
                . " WHERE c.curso = $curso "
                . " AND c.id = aa.id"
                . " AND aa.dia BETWEEN '$inicioFecha' AND '$finFecha'"
                . " ORDER BY aa.id ASC ) as asis"
                . " ON cr.id = asis.id "
                . " WHERE cr.curso = $curso "
                . " ORDER BY cr.alumno, asis.dia  ASC";

        $this->get_query();

        $num_rows = count($this->rows);

        $data = array();

        foreach ($this->rows as $key => $value) {
            array_push($data, $value);
        }

        return $data;
    }
    
    public function asistencias($curso, $alumno, $inicioFecha, $finFecha) {

        $this->query = "SELECT aa.id FROM cursan c, asistencias aa "
                . " WHERE c.curso = $curso "
                . " AND c.alumno = $alumno "
                . " AND c.id = aa.id "
                . " AND aa.asistencia = 1 "
                . " AND aa.dia BETWEEN '$inicioFecha' AND '$finFecha'";

        $this->get_query();
        $num_rows = count($this->rows);

        return $num_rows;
    }

    public function faltas($curso, $alumno, $inicioFecha, $finFecha) {

        $this->query = "SELECT aa.id FROM cursan c, asistencias aa "
                . " WHERE c.curso = $curso "
                . " AND c.alumno = $alumno "
                . " AND c.id = aa.id "
                . " AND aa.asistencia = 0 "
                . " AND aa.dia BETWEEN '$inicioFecha' AND '$finFecha'";

        $this->get_query();
        $num_rows = count($this->rows);

        return $num_rows;
    }

    public function horario($curso, $mes) {
        // consulta para saber cuantos dias hay entre las fechas de inico de semestre y fin de semestre
        $this->query = "SELECT h.id, h.lunes, h.martes, h.miercoles, h.jueves, h.viernes, h.sabado,"
                . " c.nombre, c.inicio, c.fin "
                . " FROM horarios h, cuatrimestres c "
                . " WHERE h.id = $curso "
                . " AND c.estado = 1 LIMIT 1";

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

        $auxMes = new DateTime($inicio);
        $finMes = $auxMes->modify('last day of this month');

        if ($mes == 'SEGUNDO') {
            $inicioMes = $auxMes->add(new DateInterval('P1D'));
            $finMes = new DateTime($inicioMes->format('Y-m-d'));
            $finMes->modify('last day of this month');
        } else if ($mes == 'TERCERO') {
            $inicioMes = $auxMes->add(new DateInterval('P2M'));
            $finMes = new DateTime($inicioMes->format('Y-m-d'));
            $inicioMes->modify('first day of this month');
            $finMes->modify('last day of this month');
        } else if ($mes == 'CUARTO') {
            $finMes = new DateTime($fin);
            $inicioMes = $finMes->modify('first day of this month');
            $finMes = new DateTime($fin);
        }

        $interval = $inicioMes->diff($finMes);
        $diasTotal = $interval->format('%a');

        $diasClaseSemana = array(0, $lunes, $martes, $miercoles, $jueves, $viernes, $sabado);

        $hoy = date("Y-m-j");
        $hora = date('H:i:s');
        $horaActual = strtotime($hora);

        $hoyTope = new DateTime($hoy);
        $fechas = array();
        $clasesTotales = 0;
        $i = 0;
        while ($inicioMes <= $finMes && $inicioMes <= $hoyTope) {

            $dia = date("N", strtotime($inicioMes->format('Y-m-d')));

            if ($dia <= 6)
                if (!empty($diasClaseSemana[$dia])) {

                    if ($hoyTope != $inicioMes) {
                        $fechas[$i] = $inicioMes->format('Y-m-d');
                        $i++;
                        $clasesTotales++;
                    } else {
                        $horaClase = strtotime($diasClaseSemana[$dia]);

                        if ($horaActual >= $horaClase) {

                            $fechas[$i] = $inicioMes->format('Y-m-d');
                            $i++;
                            $clasesTotales++;
                        }
                    }
                }
            $inicioMes->add(new DateInterval('P1D'));
        }

        return $fechas;
    }

    public function horario_quincena($curso, $inicioq, $finq) {
        // consuta para saber cuantos dias hay entre las fechas de inico de semestre y fin de semestre
        $this->query = "SELECT h.id, h.lunes, h.martes, h.miercoles, h.jueves, h.viernes, h.sabado,"
                . " c.nombre, c.inicio, c.fin "
                . " FROM horarios h, cuatrimestres c "
                . " WHERE h.id = $curso "
                . " AND c.estado = 1 LIMIT 1";

        $this->get_query();
        $num_rows = count($this->rows);

        $data = array();

        foreach ($this->rows as $key => $value) {
            array_push($data, $value);
        }

        foreach ($data[0] as $key => $value) {
            $$key = $value;
        }

        $inicioMes = new DateTime($inicioq);
        $finMes = new DateTime($finq);


        $diasClaseSemana = array(0, $lunes, $martes, $miercoles, $jueves, $viernes, $sabado);


        $fechas = array();
        $clasesTotales = 0;
        $i = 0;
        while ($inicioMes <= $finMes) {
            $dia = date("N", strtotime($inicioMes->format('Y-m-d')));
            if ($dia <= 6)
                if (!empty($diasClaseSemana[$dia])) {
                    //echo "<br>" . $i . " - " . $inicioCuatrimestre->format('d-m-Y');
                    //echo $diasSemana[$dia];
                    $fechas[$i] = $inicioMes->format('Y-m-d');
                    $i++;
                    $clasesTotales++;
                }
            $inicioMes->add(new DateInterval('P1D'));
        }

        return $fechas;
    }

    public function asistenciasRegistradas($curso, $mes) {
        // consulta para saber cuantos dias hay entre las fechas de inico de semestre y fin de semestre
        $this->query = "SELECT h.id, h.lunes, h.martes, h.miercoles, h.jueves, h.viernes, h.sabado,"
                . " c.nombre, c.inicio, c.fin "
                . " FROM horarios h, cuatrimestres c "
                . " WHERE h.id = $curso "
                . " AND c.estado = 1 LIMIT 1";

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

        $auxMes = new DateTime($inicio);
        $finMes = $auxMes->modify('last day of this month');

        if ($mes == 'SEGUNDO') {
            $inicioMes = $auxMes->add(new DateInterval('P1D'));
            $finMes = new DateTime($inicioMes->format('Y-m-d'));
            $finMes->modify('last day of this month');
        } else if ($mes == 'TERCERO') {
            $inicioMes = $auxMes->add(new DateInterval('P2M'));
            $finMes = new DateTime($inicioMes->format('Y-m-d'));
            $inicioMes->modify('first day of this month');
            $finMes->modify('last day of this month');
        } else if ($mes == 'CUARTO') {
            $finMes = new DateTime($fin);
            $inicioMes = $finMes->modify('first day of this month');
            $finMes = new DateTime($fin);
        }

        $interval = $inicioMes->diff($finMes);
        $diasTotal = $interval->format('%a');
        
        $fechaInicio = $inicioMes->format('Y-m-d');
        $fechaFin = $finMes->format('Y-m-d');
        $this->query = "SELECT DISTINCT(aa.dia) FROM cursan c, asistencias aa"
                . " WHERE c.curso =  $curso "
                . " AND c.id = aa.id "
                . " AND aa.dia BETWEEN '$fechaInicio' AND '$fechaFin'";

        $this->get_query();
        $num_rows = count($this->rows);

        $fechas = array();

        foreach ($this->rows as $key => $value) {
            array_push($fechas, $value);
        }
        
        return $fechas;
    }
    
    public function delete() {
        
    }

    protected function edit() {
        
    }

}
