<?php

namespace Notes\Database;

class Config 
{
    function __destruct()
    {
        $this->disconnect();
    }

    public function connect()
    {
        return mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
    }

    public function disconnect()
    {
        mysqli_close($this->connect());
    }
}