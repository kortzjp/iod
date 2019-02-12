<?php

require_once "./core/handlers.php";
require_once './core/DataBase.php';

class HorarioModel extends DataBase {

    protected function delete() {
        
    }

    protected function edit() {
        
    }

    public function get($curso = 0, $mes = '') {
        // consulta para saber cuantos dias hay entre las fechas de inicio y fin de cuatrimestre
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

    public function set($datos = array()) {
        foreach ($datos as $key => $value) {
            $$key = $value;
        }

        $tipo = USER_DOC;
        $this->query = "INSERT INTO horario ( id, lunes, martes, miercoles, jueves, viernes, sabado )"
                . " VALUES ( $id, $lunes, $martes, $miercoles, $jueves, $viernes, $sabado )";

        return $this->set_query();
    }

}
