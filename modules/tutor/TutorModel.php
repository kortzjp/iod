<?php

require_once "./core/handlers.php";
require_once './core/DataBase.php';

class TutorModel extends DataBase {

    public function guardar() {

        $nombre = recoge("nombre");
        $usuario = recoge("usuario");
        $password = recoge("password");
        $carrera = recoge("carrera");
        $estado = recoge("estado");

        if ($nombre == "" || $usuario == "" || $password == "" || $carrera == "" || $estado == "") {
            return "Error";
        } else {
            $conexion = new mysqli("localhost", "root", "", "sistemastutorias");

            if ($conexion->connect_errno) {
                die('Error en la conexion: ' . $conexion->connect_errno);
            }
            $password = hashPassword($password);
            $conexion->set_charset("utf8");
            $stmt = $conexion->prepare("INSERT INTO tutores( usuario, nombre, carrera, password, estado, intentos) VALUES( ?, ?, ?, ?, ?, 0)");
            $stmt->bind_param("ssisi", $usuario, $nombre, $carrera, $password, $estado);
            $stmt->execute();
            $stmt->close();
            return "correcto";
        }
    }


    public function get($id = '') {
        $this->query = " SELECT * FROM tutores"
                . " WHERE id = $id LIMIT 1";


        $this->get_query();
        $num_rows = count($this->rows);

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

    protected function set() {
        
    }

}
