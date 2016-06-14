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
            if(mysqli_query($this->connection,"INSERT INTO fill_up (amount,total_price,fill_date,fill_time,odo_meter,trip_id) VALUES ('$data_array[amount]','$data_array[total_price]','$data_array[f_date]','$data_array[f_time]','$data_array[odo_meter]','$data_array[trip_id]');")){
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