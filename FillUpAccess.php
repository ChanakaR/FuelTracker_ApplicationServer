<?php

/**
 * Created by PhpStorm.
 * User: bmCSoft
 * Date: 2016-05-04
 * Time: 6:48 PM
 */
class FillUpAccess
{
    private $db =null;
    private $connection = null;

    public function __construct()
    {
        require_once(__DIR__.'/DBConnection.php');
        $this->db = new DBConnection();
        $this->connection = $this->db->openConnection();
    }

    public function insertRow($data_array){
        if($this->connection != null){
            if(mysqli_query($this->connection,"INSERT INTO fill_up (fuel_type,amount,unit_price,total_price,fill_date,vehicle_id,driver_id,odo_meter) VALUES ('$data_array[fuel_type]','$data_array[amount]','$data_array[unit_price]','$data_array[total_price]','$data_array[f_date]','$data_array[vehicle_id]','$data_array[driver_id]','$data_array[odo_meter]');")){
                $this->disconnect();
                return OPERATION_SUCCESS;
            }else{
                return OPERATION_UNSUCCESSFUL;
            }
        }else{
            return CONNECTION_ERROR;
        }

    }

    public function disconnect(){
        if($this->db != null){
            $this->db->closeConnection($this->connection);
        }
    }
}