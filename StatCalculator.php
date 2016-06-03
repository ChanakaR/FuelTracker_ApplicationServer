<?php

/**
 * Created by PhpStorm.
 * User: bmCSoft
 * Date: 2016-05-31
 * Time: 11:37 AM
 */
require_once ("./JsonMessages.php");

class StatCalculator
{
    private $db=null;
    private $connection = null;

    public function __construct()
    {
        require_once ("./DBConnection.php");
        $this->db= new DBConnection();
        $this->connection = $this->db->openConnection();
    }

    public function getMyProgressForDriver($data_array){
        //$driver_id = $data_array["driver_id"];
        $driver_id = '4';
        $millage_log = $this->getMillageByDriverId($driver_id);
        $cost_log = $this->getTotalCostOnFuel($driver_id);
        $vehicles = $this->getMostUsedVehicleByDriverId($driver_id);
        $longest_millage  = $this->getLongestMillageByDriverId($driver_id);
        $fuel_cost = $this->getTotalFuelCostByDriverId($driver_id);

        if($millage_log != "CON_ER" AND $cost_log != "CON_ER"){
            $response_json = '{
                    "error_code" : "0",
                    "longest_millage" : '.$longest_millage.',
                    "vehicles" : '.$vehicles.',
                    "fuel_cost" : '.$fuel_cost.',
                    "millage" : '.$millage_log.',
                    "cost_log" : '.$cost_log.'
            }';
            $this->closeConnecion();
            return $response_json;
        }
        else{
            return CONNECTION_ERROR;
        }

    }

    private function getLongestMillageByDriverId($driver_id){
        if($this->connection != null){
            $query = "SELECT b.distance,a.licence_plate,b.description,b.date FROM vehicle a INNER JOIN (SELECT (end_odometer-start_odometer) AS distance,description,date,vehicle_id FROM trip WHERE driver_id='$driver_id' ORDER BY distance DESC LIMIT 2) b ON b.vehicle_id= a.id";
            $result = $this->connection->query($query);
            $millage_array = array();
            while($row =mysqli_fetch_assoc($result))
            {
                $millage_array[] = $row;
            }
            $json_data = json_encode($millage_array,true);
            return $json_data;
        }else{
            return "CON_ER";
        }
    }

    private function getMostUsedVehicleByDriverId($driver_id){
        if($this->connection != null){
            $query = "select a.licence_plate,a.v_class,b.u_times FROM vehicle a INNER JOIN (SELECT COUNT(vehicle_id) AS u_times,vehicle_id FROM `trip` WHERE driver_id ='$driver_id' GROUP BY vehicle_id ORDER BY COUNT(vehicle_id) DESC LIMIT 3) b ON a.id = b.vehicle_id";
            $result = $this->connection->query($query);
            $millage_array = array();
            while($row =mysqli_fetch_assoc($result))
            {
                $millage_array[] = $row;
            }
            $json_data = json_encode($millage_array,true);
            return $json_data;
        }else{
            return "CON_ER";
        }
    }

    private function getTotalFuelCostByDriverId($driver_id){
        if($this->connection != null){
            $query = "SELECT SUM(fill_up.total_price) AS cost FROM `trip` INNER JOIN fill_up ON trip.id = fill_up.trip_id WHERE trip.driver_id = '$driver_id'";
            $result = $this->connection->query($query);
            $millage_array = array();
            while($row =mysqli_fetch_assoc($result))
            {
                $millage_array[] = $row;
            }
            $json_data = json_encode($millage_array,true);
            return $json_data;
        }else{
            return "CON_ER";
        }
    }

    private function getMillageByDriverId($driver_id){

        if($this->connection != null){
            $query = "SELECT SUM(end_odometer-start_odometer) AS distance ,MONTH(date) AS month,YEAR(date) as year FROM `trip` WHERE on_trip = 'OFF' AND driver_id='$driver_id' GROUP BY driver_id,MONTH(date)";
            $result = $this->connection->query($query);
            $millage_array = array();
            while($row =mysqli_fetch_assoc($result))
            {
                $millage_array[] = $row;
            }
            $json_data = json_encode($millage_array,true);
            return $json_data;
        }else{
            return "CON_ER";
        }
    }

    private function getTotalCostOnFuel($driver_id){
        if($this->connection != null){
            $query = "SELECT SUM(a.total_price) AS cost,YEAR(a.fill_date) as year,MONTH(a.fill_date) as month FROM (SELECT fill_up.* FROM fill_up INNER JOIN trip ON trip.id = fill_up.trip_id WHERE trip.driver_id = '$driver_id') a GROUP BY MONTH(a.fill_date)";
            $result = $this->connection->query($query);
            $cost_array = array();
            while($row =mysqli_fetch_assoc($result))
            {
                $cost_array[] = $row;
            }
            $json_data = json_encode($cost_array,true);
            return $json_data;
        }else{
            return "CON_ER";
        }
    }

    private function closeConnecion(){
        if($this->connection != null){
            $this->db->closeConnection($this->connection);
        }
    }
}