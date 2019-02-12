<?php

require_once './core/handlers.php';
require_once './core/DataBase.php';

class CuatrimestreModel extends DataBase {

    public function get($estado = 0) {
        if ($estado == 0) {
            $this->query = "SELECT * FROM cuatrimestres ORDER BY id DESC";
        } else {
            $this->query = "SELECT * FROM cuatrimestres WHERE estado = $estado";
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

    public function set( $data = array()) {

    }

}
