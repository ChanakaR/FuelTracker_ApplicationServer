<?php

/**
 * Created by PhpStorm.
 * User: bmCSoft
 * Date: 2016-06-04
 * Time: 11:50 AM
 */
require_once ("./Access.php");
require_once ("./JsonMessages.php");

class UserAccess implements Access
{
    private $db =null;
    private $connection = null;

    function __construct()
    {
        require_once(__DIR__.'/DBConnection.php');
        $this->db = new DBConnection();
        $this->connection = $this->db->openConnection();
    }


    public function selectAll()
    {
        // TODO: Implement selectAll() method.
    }

    public function insertRow($data_array)
    {
        // TODO: Implement insertRow() method.
    }

    public function updateRow($data_array)
    {
        $id = $data_array["id"];
        $user_name = $data_array["user_name"];
        $password = md5($data_array["password"]);

        $query = "UPDATE user SET user_name ='$user_name',password='$password' WHERE id='$id'";
        if($this->connection->query($query)){
            $this->disconnect();
            return OPERATION_SUCCESS;
        }else{
            $this->disconnect();
            return DATA_UPDATE_ERROR;
        }
    }

    public function deleteRow($data_array)
    {
        // TODO: Implement deleteRow() method.
    }

    public function select($data_array)
    {
        // TODO: Implement select() method.
    }

    public function disconnect(){
        if($this->db != null){
            $this->db->closeConnection($this->connection);
        }
    }
}