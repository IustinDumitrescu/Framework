<?php

namespace App\Database;

class Database
{

    public \mysqli $connection;
    
    public function __construct($host='localhost',$username= 'root',$parola='',$dbaname= 'mySite')
    {
        $this->connection = new \mysqli($host,$username,$parola,$dbaname);

        if ($this->connection->connect_error) 
        {
            die("Connection failed: " . $this->connection->connect_error);
        }

    }

    public function getConnection(): \mysqli
    {
        return $this->connection;
    }

}

?>