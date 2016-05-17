<?php

/**
 * Created by PhpStorm.
 * User: bmCSoft
 * Date: 2016-03-25
 * Time: 7:51 AM
 */
require_once __DIR__.'/db_config.php';

class DBConnection
{
    private $connection;
    public function openConnection(){
        $this->connection = mysqli_connect(DB_SERVER,DB_USER,DB_PASSWORD,DB_DATABASE);
        return $this->connection;
    }

    public function closeConnection($con){
        if($con != null){
            mysqli_close($con);
        }
        else{
            // parse json
        }

    }
}