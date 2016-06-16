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
        if($this->connection != null){
            $query = "select a.*,b.licence_plate from vehicle b inner join (select trip.id,trip.start_time,trip.end_time,trip.date as start_date,trip.end_date,trip.description,trip.vehicle_id as vehicle,(trip.end_odometer-trip.start_odometer) as distance, driver.driver_id as driver from trip inner join driver on driver.id = trip.driver_id) a on a.vehicle = b.id";
            $result = $this->connection->query($query);
            $data_array = array();
            while($row =mysqli_fetch_assoc($result))
            {
                $data_array[] = $row;
            }
            $json_data = json_encode($data_array,true);
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
        if($this->connection != null){
            if(mysqli_query($this->connection,"INSERT INTO trip (start_time,end_time,date,end_datedescription,driver_id,vehicle_id,start_odometer,end_odometer) VALUES ('$data_array[start_time]','$data_array[end_time]','$data_array[date]','$data_array[end_date]','$data_array[description]','$data_array[driver_id]','$data_array[vehicle_id]','$data_array[start_odometer]','$data_array[end_odometer]');")){
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
        if($data_array["method"] == "end_trip"){
            $query = "UPDATE trip SET end_odometer ='$data_array[end_odometer]',end_date='$data_array[end_date]',end_time='$data_array[end_time],on_trip='OFF' WHERE id='$data_array[trip_id]'";
            if($this->connection->query($query)){
                $this->disconnect();
                return OPERATION_SUCCESS;
            }else{
                $this->disconnect();
                return DATA_UPDATE_ERROR;
            }
        }
    }

    public function deleteRow($data_array)
    {
        // TODO: Implement deleteRow() method.
    }

    public function select($data_array)
    {
        $query = null;
        $result = null;
        if(array_key_exists("trip_id",$data_array)){
            $trip_id = $data_array["trip_id"];
            $query = "SELECT * FROM trip WHERE id='$trip_id'";
            $result = $this->selectQueryExecutor($query);
        }
        elseif(array_key_exists("driver_id",$data_array)){
            $driver_id = $data_array["driver_id"];
            $query = "SELECT a.id,a.start_time,a.end_time,a.date,a.end_date,a.start_odometer,a.end_odometer,a.description,b.licence_plate,b.v_class FROM trip a INNER JOIN vehicle b ON a.vehicle_id=b.id WHERE a.driver_id = '$driver_id'";
            $result = $this->getTripList($query);
        }
        elseif(array_key_exists("vehicle_id",$data_array)){
            $vehicle_id = $data_array["vehicle_id"];
            $query = "SELECT a.id,a.start_time,a.end_time,a.date,a.end_date,(a.end_odometer-a.start_odometer) as distance,a.description,b.driver_id FROM trip a INNER JOIN driver b ON a.driver_id=b.id WHERE a.vehicle_id = '$vehicle_id'";
            $result = $this->getTripList($query);
        }
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

    private function getTripList($query){
        if($query !=  null){
            if($this->connection != null){
                $result = $this->connection->query($query);
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