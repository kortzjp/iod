<?php

require_once "./core/handlers.php";
require_once './core/DataBase.php';

class AsignaturaModel extends DataBase {

    public function guardar() {

        $nombre = recoge("nombre");
        $siglas = recoge("siglas");

        if ($nombre == "" || $siglas == "") {
            return "error";
        } else {
            $conexion = new mysqli("localhost", "root", "", "sistemastutorias");

            if ($conexion->connect_errno) {
                die('Error en la conexion: ' . $conexion->connect_errno);
            }

            $conexion->set_charset("utf8");
            $stmt = $conexion->prepare("INSERT INTO carreras( nombre, siglas) VALUES( ?, ?)");
            $stmt->bind_param("ss", $nombre, $siglas);
            $stmt->execute();
            $stmt->close();
            return "correcto";
        }
    }

    public function darCarrera($id = '') {
        $conexion = new mysqli("localhost", "root", "", "sistemastutorias");

        if ($conexion->connect_errno) {
            die('Error en la conexion: ' . $conexion->connect_errno);
        }

        $conexion->set_charset("utf8");
        if (empty($id)) {
            $result = $conexion->query("SELECT * FROM carreras");
        } else {
            $result = $conexion->query("SELECT * FROM carreras WHERE id = $id");
        }

        while ($rows[] = $result->fetch_assoc());

        $result->close();
        $conexion->close();
        array_pop($rows);
        return $rows;
    }

    protected function delete() {
        
    }

    public function edit( $datos =  array() ) {
         foreach ($datos as $key => $value) {
            $$key = $value;
        }

        $this->query = "UPDATE asignaturas SET "
                . " clave = '$clave', "
                . " nombre = '$nombre', "
                . " carrera = $carrera, "
                . " cuatrimestre = $cuatrimestre "
                . " WHERE id = $id";

        $this->set_query();
        if( $this->afectadas_rows > 0)
            return 'editada';

        return 'no_editada';
    }

    public function get($id = 0, $carrera = 0) {
        
        if( $id == 0 ) {
        $this->query = "SELECT *  FROM asignaturas"
                . " WHERE carrera = $carrera "
                . " ORDER BY nombre ASC";
        }
        else {
            $this->query = "SELECT *  FROM asignaturas"
                . " WHERE id = $id LIMIT 1";
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

        $this->query = "INSERT INTO asignaturas ( clave, nombre, carrera, cuatrimestre)"
                . " VALUES ( '$clave', '$nombre', $carrera, $cuatrimestre )";

        if ($this->set_query() > 0)
            return 'success';

        return 'danger';
    }

}
