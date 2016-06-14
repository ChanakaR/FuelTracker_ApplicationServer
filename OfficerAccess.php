<?php

/**
 * Created by PhpStorm.
 * User: bmCSoft
 * Date: 2016-06-13
 * Time: 7:38 AM
 */

require_once ("./JsonMessages.php");
require_once ("./Access.php");

class OfficerAccess implements Access
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
        if($this->connection != null){
            $query = "SELECT * FROM member natural join officer ";
            $result = $this->connection->query($query);
            $vehicle_array = array();
            while($row =mysqli_fetch_assoc($result))
            {
                $vehicle_array[] = $row;
            }
            $json_data = json_encode($vehicle_array,true);
            $response_json = '{
                            "error_code" : "0",
                            "message" : '.$json_data.'
                          }';
            $this->disconnect();
            return $response_json;
        }else{
            return CONNECTION_ERROR;
        }
    }

    public function insertRow($data_array)
    {
        try{
            mysqli_query($this->connection,"START TRANSACTION;");
            mysqli_query($this->connection,"INSERT INTO member (f_name,l_name,address,contact_no,gender,nic) VALUES ('$data_array[first_name]','$data_array[last_name]','$data_array[address]','$data_array[contact_no]','$data_array[gender]','$data_array[nic]');");
            $last_id =$this->connection->insert_id;
            mysqli_query($this->connection,"INSERT INTO officer (id,officer_id) VALUES ('$last_id','$data_array[officer_id]');");
            mysqli_query($this->connection,"COMMIT");
            return OPERATION_SUCCESS;
        }
        catch(Exception $ex){
            echo $ex->getMessage();
            return OPERATION_UNSUCCESSFUL;
        }
    }

    public function updateRow($data_array)
    {
        // TODO: Implement updateRow() method.
    }

    public function deleteRow($data_array)
    {
        // TODO: Implement deleteRow() method.
    }

    public function select($data_array)
    {
        // TODO: Implement select() method.
    }

    public function disconnect()
    {
        if($this->db != null){
            $this->db->closeConnection($this->connection);
        }
    }
}