<?php

require_once "./core/handlers.php";
require_once './core/DataBase.php';

class DocenteModel extends DataBase {

    public function guardar() {
        
    }

    public function getCursos($id) {
        $this->query = "SELECT c.id, a.nombre, c.grupo "
                . " FROM asignaturas a, cursos c, cuatrimestres cu, cursan cn"
                . " WHERE a.id = c.asignatura AND c.docente = $id AND cu.estado = 1 "
                . " AND cu.id=cn.cuatrimestre AND cn.curso= c.id GROUP BY cn.curso ORDER BY c.grupo";

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

    public function edit( $datos = array() ) {
        foreach ($datos as $key => $value) {
            $$key = $value;
        }

        $this->query = "UPDATE usuarios SET "
                . " usuario = '$usuario', "
                . " nombre = '$nombre', "
                . " correo = '$correo', "
                . " activacion = $activacion "
                . " WHERE id = $id";

        $this->set_query();
        if ($this->afectadas_rows > 0)
            return 'editada';

        return 'no_editada';
    }

    public function get( $activo = 0 ) {
        $tipo = USER_DOC;
        if( $activo == 0)
        $this->query = "SELECT * FROM usuarios WHERE id_tipo = $tipo ORDER BY usuario";
        else
            $this->query = "SELECT * FROM usuarios "
                . " WHERE id_tipo = $tipo "
                . " AND activacion = 1 ORDER BY usuario";

        $this->get_query();
        $num_rows = count($this->rows);
        $data = array();

        foreach ($this->rows as $key => $value) {
            array_push($data, $value);
        }

        return $data;
    }

    public function set ($datos = array()) {
        foreach ($datos as $key => $value) {
            $$key = $value;
        }
        
        $tipo = USER_DOC;
        $this->query = "INSERT INTO usuarios ( usuario,password, nombre, correo, activacion, id_tipo )"
                . " VALUES ( '$usuario','$password', '$nombre', '$correo', $activacion, $tipo )";

        if ($this->set_query() > 0)
            return 'success';

        return 'danger';
    }

}