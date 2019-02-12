<?php

require_once './core/DataBase.php';

class UsuarioModel extends DataBase {

    public function get($email = '') {
        $this->query = "SELECT * FROM usuarios WHERE correo = '$email' LIMIT 1";
        $this->get_query();
        
        $data = array();
        foreach ($this->rows as $key => $value) {
            array_push($data, $value);
        }

        return $data;
    }

    public function set() {
        
    }

    public function edit($user_data = array()) {

        foreach ($user_data as $key => $value) {
            $$key = $value;
        }

        $this->query = "UPDATE usuarios SET "
                . " password = '$password', token_password = '', password_request = 0"
                . " WHERE id = $id AND token_password = '$token'";

        $this->set_query();
        return $this->afectadas_rows;
    }

    public function delete() {
        
    }

    public function generaTokenPass($usuario) {
        $token = md5(uniqid(mt_rand(), false));
        
        $this->query = "UPDATE usuarios SET "
                . " token_password = '$token', "
                . " password_request = 1 "
                . " WHERE id = $usuario";
        
        $this->set_query();
        if ($this->afectadas_rows > 0) {
            return $token;
        } else {
            return 'error';
        }
    }

    public function verificaTokenPass($user_id, $token) {

        $this->query = "SELECT activacion FROM usuarios "
                . " WHERE id = $user_id "
                . " AND token_password = '$token' "
                . " AND password_request = 1 LIMIT 1";
        $this->get_query();
        $num = count($this->rows);

        if ($num > 0) {
            $data = array();
            foreach ($this->rows as $key => $value) {
                array_push($data, $value);
            }
            if ($data[0]['activacion'] == 1) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

}
