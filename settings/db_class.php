<?php
include_once 'db_cred.php';

if (!class_exists('db_connection')) {
    class db_connection
    {
        public $db = null;
        public $results = null;

        function db_connect()
        {
            $this->db = mysqli_connect(SERVER, USERNAME, PASSWD, DATABASE);
            return !mysqli_connect_errno();
        }

        function db_conn()
        {
            $this->db = mysqli_connect(SERVER, USERNAME, PASSWD, DATABASE);
            return mysqli_connect_errno() ? false : $this->db;
        }

        function db_query($sqlQuery)
        {
            if (!$this->db_connect() || $this->db == null) {
                return false;
            }
            $this->results = mysqli_query($this->db, $sqlQuery);
            return $this->results !== false;
        }

        function db_write_query($sqlQuery)
        {
            if (!$this->db_connect() || $this->db == null) {
                return false;
            }
            $result = mysqli_query($this->db, $sqlQuery);
            return $result !== false;
        }

        function db_fetch_one($sql)
        {
            if (!$this->db_query($sql)) {
                return false;
            }
            return mysqli_fetch_assoc($this->results);
        }

        function db_fetch_all($sql)
        {
            if (!$this->db_query($sql)) {
                return false;
            }
            return mysqli_fetch_all($this->results, MYSQLI_ASSOC);
        }

        function db_count()
        {
            if ($this->results == null || $this->results == false) {
                return false;
            }
            return mysqli_num_rows($this->results);
        }

        function last_insert_id()
        {
            return mysqli_insert_id($this->db);
        }
    }
}

