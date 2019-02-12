<?php

require_once 'settings.php';

abstract class DataBase {

    private static $db_host = DB_HOST;
    private static $db_user = DB_USER;
    private static $db_pass = DB_PASS;
    protected $db_name = DB_NAME;
    protected $query;
    protected $afectadas_rows;
    protected $rows = array();
    private $conn;

    abstract protected function get();

    abstract protected function set();

    abstract protected function edit();

    abstract protected function delete();

    private function open_connection() {
        //if ($this->conn == NULL) {
            $this->conn = new mysqli(self::$db_host, self::$db_user, self::$db_pass, $this->db_name);
            $this->conn->set_charset('utf8');
        //}
    }

    private function close_connection() {
        $this->conn->close();
    }

    protected function set_query() {
        $this->open_connection();
        $this->conn->query($this->query);
        $this->afectadas_rows = $this->conn->affected_rows;
        return $this->conn->insert_id;
        //$this->close_connection();
    }

    protected function get_query() {
        $this->open_connection();
        $result = $this->conn->query($this->query);
        while ($this->rows[] = $result->fetch_assoc());
        $result->close();
        $this->close_connection();
        array_pop($this->rows);
    }

    protected function db_autocommit($dato) {
        $this->open_connection();
        $this->conn->autocommit($dato);
    }

    protected function db_begintransaction() {
        $this->open_connection();
        $this->conn->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
    }

    protected function db_commit() {
        $this->conn->commit();
    }

    protected function db_rollback() {
        $this->conn->rollback();
    }

}
