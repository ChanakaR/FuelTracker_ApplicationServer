<?php

/**
 * Created by PhpStorm.
 * User: bmCSoft
 * Date: 2016-05-27
 * Time: 11:06 PM
 */
require_once ("./JsonMessages.php");
require_once ("./Access.php");

class TripAccess implements Access
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
        if($this->connection != null){
            if(mysqli_query($this->connection,"INSERT INTO trip (start_time,end_time,date,description,driver_id,vehicle_id,start_odometer,end_odometer) VALUES ('$data_array[start_time]','$data_array[end_time]','$data_array[date]','$data_array[description]','$data_array[driver_id]','$data_array[vehicle_id]','$data_array[start_odometer]','$data_array[end_odometer]');")){
                $result = $this->connection->query("SELECT * FROM trip ORDER BY id DESC LIMIT 1");
                $result_array = array();
                while($row =mysqli_fetch_assoc($result))
                {
                    $result_array = $row;
                }
                $json_data = json_encode($result_array,true);
                $response_json = '{
                            "error_code" : "0",
                            "message" : '.$json_data.'
                          }';
                $this->disconnect();
                return $response_json;
            }else{
                return OPERATION_UNSUCCESSFUL;
            }
        }else{
            return CONNECTION_ERROR;
        }
        // TODO: Implement insertRow() method.
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
        $query = null;
        if(array_key_exists("trip_id",$data_array)){
            $trip_id = $data_array["trip_id"];
            $query = "SELECT * FROM trip WHERE id='$trip_id'";
        }
        elseif(array_key_exists("driver_id",$data_array)){
            $driver_id = $data_array["driver_id"];
            $query = "SELECT a.id,a.start_time,a.end_time,a.date,a.start_odometer,a.end_odometer,a.description,b.licence_plate,b.v_class FROM trip a INNER JOIN vehicle b ON a.vehicle_id=b.id WHERE a.driver_id = '$driver_id'";
        }
        $result = $this->selectQueryExecutor($query);
        return $result;
    }

    public function selectOnGoingTrip($data_array){
        $driver_id = $data_array["driver_id"];
        $query = "SELECT * FROM trip WHERE driver_id='$driver_id' AND on_trip = 'ON' ";
        $result = $this->selectQueryExecutor($query);
        return $result;

    }

    private function selectQueryExecutor($query){
        if($query !=  null){
            if($this->connection != null){
                $result = $this->connection->query($query);
                if($result->num_rows == 1){
                    $trip_data = array();
                    while($row =mysqli_fetch_assoc($result))
                    {
                        $trip_data[] = $row;
                    }
                    $json_data = json_encode($trip_data,true);
                    $response_json = '{
                            "error_code" : "0",
                            "message" : '.$json_data.'
                          }';
                    $this->disconnect();
                    return $response_json;
                }
                else{
                    return RESULT_NOT_FOUND;
                }
            }
            else{
                return CONNECTION_ERROR;
            }
        }
        else{
            return NULL_QUERY;
        }
    }

    public function endTrip($data_array){
        $query = null;
        if(array_key_exists("trip_id",$data_array)){
            $trip_id = $data_array["trip_id"];
            $query = "UPDATE trip SET on_trip = 'OFF' WHERE id='$trip_id'";

        }

    }

    public function disconnect()
    {
        if($this->connection != null){
            $this->db->closeConnection($this->connection);
        }
    }
}