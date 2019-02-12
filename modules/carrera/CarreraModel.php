<?php

require_once "./core/handlers.php";
require_once './core/DataBase.php';

class CarreraModel extends DataBase {

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

        if (empty($id)) {
            $this->query = "SELECT * FROM carreras";
        } else {
            $this->query = "SELECT * FROM carreras WHERE id = $id" ;
        }
        
        $this->get_query();

        $data = array();
        foreach ($this->rows as $key => $value) {
            array_push($data, $value);
        }

        return $data;
    }

    protected function delete() {
        
    }

    protected function edit() {
        
    }

    public function get() {

        $this->query = " SELECT * FROM carreras"
                . " WHERE id <> 4";

        $this->get_query();

        $data = array();

        foreach ($this->rows as $key => $value) {
            array_push($data, $value);
        }

        return $data;
    }

    protected function set() {
        
    }

}
