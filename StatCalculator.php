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
        $driver_id = $data_array["driver_id"];
        $millage_log = $this->getMillageByDriverId($driver_id);
        $cost_log = $this->getTotalCostOnFuel($driver_id);
        $vehicles = $this->getMostUsedVehicleByDriverId($driver_id);
        $longest_millage  = $this->getLongestMillageByDriverId($driver_id);
        $fuel_cost = $this->getTotalFuelCostByDriverId($driver_id);

        if($millage_log != "CON_ER" AND $cost_log != "CON_ER" AND $vehicles != "CON_ER" AND $longest_millage != "CON_ER" AND $fuel_cost != "CON_ER"){
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
            $query = "SELECT SUM(a.total_price) AS cost,YEAR(a.fill_date) as year,MONTH(a.fill_date) as month FROM (SELECT fill_up.* FROM fill_up INNER JOIN trip ON trip.id = fill_up.trip_id WHERE trip.driver_id = '$driver_id') a GROUP BY MONTH(a.fill_date) ORDER BY month";
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

    public function getVehicleStatSummary($vehicle_id){
        $fav_driver =  $this->getFavouriteDriver($vehicle_id);
        $b_f_con = $this->getBestFuelConsumption($vehicle_id);
        $longest_trip = $this->getLongestTrip($vehicle_id);
        $shortest_trip = $this->getShortestTrip($vehicle_id);
        $last_trip = $this->getLastTrip($vehicle_id);

        if($fav_driver != "CON_ER" AND $b_f_con != "CON_ER" AND $longest_trip != "CON_ER" ){
            $response_json = '{
                    "error_code" : "0",
                    "fav_driver" : "'.$fav_driver.'",
                    "best_fuel_consumption" : "'.$b_f_con.'",
                    "lg_t_length" : "'.$longest_trip["dis"].'",
                    "lg_t_date" : "'.$longest_trip["date"].'",
                    "lg_t_description" : "'.$longest_trip["description"].'",
                    "sh_t_length" : "'.$shortest_trip["dis"].'",
                    "sh_t_date" : "'.$shortest_trip["date"].'",
                    "sh_t_description" : "'.$shortest_trip["description"].'",
                    "ls_t_length" : "'.$last_trip["dis"].'",
                    "ls_t_date" : "'.$last_trip["date"].'",
                    "ls_t_description" : "'.$last_trip["description"].'"
            }';
            $this->closeConnecion();
            return $response_json;
        }
    }

    private function getBestFuelConsumption($vehicle_id){
        if($this->connection != null){
            $query = "select a.* from fill_up a inner join (select * from trip where vehicle_id='$vehicle_id') b on a.trip_id = b.id order by (a.id)";
            $result = $this->connection->query($query);
            $cost_array = array();

            $num_rows = mysqli_num_rows($result);

            while($row =mysqli_fetch_assoc($result))
            {
                $cost_array[] = $row;
            }

            $best_con =0;
            for($i=1;$i<$num_rows;$i++){

                $consumption = ($cost_array[$i]["odo_meter"] - $cost_array[$i-1]["odo_meter"])/$cost_array[$i]["amount"];

                if($i==1){
                    $best_con = $consumption;
                }
                elseif($consumption > $best_con){
                    $best_con = $consumption;
                }
            }
            return $best_con;
        }else{
            return "CON_ER";
        }
    }

    private function getFavouriteDriver($vehicle_id){
        if($this->connection != null){
            $query = "select b.driver_id from driver b inner join (select a.* from (select driver_id,COUNT(driver_id) as cnt from trip where vehicle_id ='$vehicle_id' group by driver_id) a order by a.cnt desc limit 1) c on b.id = c.driver_id";
            $result = $this->connection->query($query);
            $data_array = array();
            while($row =mysqli_fetch_assoc($result))
            {
                $data_array[] = $row;
            }
            return $data_array[0]["driver_id"];

        }else{
            return "CON_ER";
        }

    }

    private function getLongestTrip($vehicle_id){
        if($this->connection != null){
            $query = "SELECT *,(end_odometer-start_odometer) as dis FROM `trip` WHERE vehicle_id = '1' and on_trip = 'OFF' order by dis desc limit 1";
            $result = $this->connection->query($query);
            $longest_trip_array = array();
            while($row =mysqli_fetch_assoc($result))
            {
                $longest_trip_array[] = $row;
            }
            return $longest_trip_array[0];

        }else{
            return "CON_ER";
        }
    }

    private function getShortestTrip($vehicle_id){
        if($this->connection != null){
            $query = "SELECT *,(end_odometer-start_odometer) as dis FROM `trip` WHERE vehicle_id = '1' and on_trip = 'OFF' order by dis limit 1";
            $result = $this->connection->query($query);
            $shortest_trip_array = array();
            while($row =mysqli_fetch_assoc($result))
            {
                $shortest_trip_array[] = $row;
            }
            return $shortest_trip_array[0];

        }else{
            return "CON_ER";
        }
    }

    private function getLastTrip($vehicle_id){
        if($this->connection != null){
            $query = "select *,(end_odometer-start_odometer) as dis from `trip` where vehicle_id='$vehicle_id' order by date desc limit 1";
            $result = $this->connection->query($query);
            $last_trip_array = array();
            while($row =mysqli_fetch_assoc($result))
            {
                $last_trip_array[] = $row;
            }
            return $last_trip_array[0];

        }else{
            return "CON_ER";
        }
    }
}