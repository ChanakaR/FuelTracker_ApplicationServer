<?php

/**
 * Created by PhpStorm.
 * User: bmCSoft
 * Date: 2016-03-25
 * Time: 10:48 AM
 */

require_once ("./JsonMessages.php");
require_once ("./Access.php");

class DriverAccess implements Access
{
    private $db =null;
    private $connection = null;

    function __construct()
    {
        require_once(__DIR__.'/DBConnection.php');
        $this->db = new DBConnection();
        $this->connection = $this->db->openConnection();
    }

    public function insertRow($data_array){
        try{
            mysqli_query($this->connection,"START TRANSACTION;");
            mysqli_query($this->connection,"INSERT INTO member (f_name,l_name,address,contact_no,gender,nic) VALUES ('$data_array[first_name]','$data_array[last_name]','$data_array[address]','$data_array[contact_no]','$data_array[gender]','$data_array[nic]');");
            $last_id =$this->connection->insert_id;
            mysqli_query($this->connection,"INSERT INTO driver (id,driver_id,driving_licence_no) VALUES ('$last_id','$data_array[driver_id]','$data_array[licence_no]')");
            mysqli_query($this->connection,"COMMIT");
        }
        catch(Exception $ex){
            echo $ex->getMessage();
        }

    }


    public function disconnect(){
        if($this->db != null){
            $this->db->closeConnection($this->connection);
        }
    }


    public function selectAll()
    {
        if($this->connection != null){
            $query = "SELECT * FROM member natural join driver ";
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

    public function selectAvailableDrivers(){

    }

    public function updateRow($data_array)
    {
        $id = $data_array["id"];
        $licence_no =  $data_array["licence_no"];
        $contact_no = $data_array["contact_no"];
        $nic = $data_array["nic"];
        $address = $data_array["address"];

        $query_member = "UPDATE member SET nic = '$nic',contact_no = '$contact_no',address='$address' WHERE id='$id'";
        $query_driver = "UPDATE driver SET driving_licence_no = '$licence_no' WHERE id='$id'";

        if($this->connection->query($query_member)){
            if($this->connection->query($query_driver)){
                $this->disconnect();
                return OPERATION_SUCCESS;
            }
            else{
                $this->disconnect();
                return DATA_UPDATE_ERROR;
            }
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
}