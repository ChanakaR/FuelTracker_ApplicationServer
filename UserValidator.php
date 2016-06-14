<?php

/**
 * Created by PhpStorm.
 * User: bmCSoft
 * Date: 2016-05-24
 * Time: 10:49 AM
 */

require_once ("./JsonMessages.php");
require_once ("./Access.php");

class UserValidator
{
    private $db=null;
    private $connection = null;

    function __construct(){
        require_once(__DIR__.'/DBConnection.php');
        $this->db = new DBConnection();
        $this->connection = $this->db->openConnection();
    }

    public function checkUser($data,$from){
        $username = $data["username"];
        $encrypt_pwd = md5($data["password"]);
        $query = "";
        if($from = "mobile"){
            $query = "SELECT c.*,d.driver_id,d.driving_licence_no FROM driver d INNER JOIN (SELECT a.* FROM `member` a INNER JOIN (SELECT * FROM `user` WHERE user_name='$username' and password = '$encrypt_pwd') b ON a.id = b.id) c ON c.id=d.id";
        }elseif($from == "web"){
            // implement query for web application user checking
        }
        if($this->connection != null){
            $result = $this->connection->query($query);
            if($result->num_rows == 1){
                $driver_data = array();
                while($row =mysqli_fetch_assoc($result))
                {
                    $driver_data[] = $row;
                }
                $json_data = json_encode($driver_data,true);
                $response_json = '{
                            "error_code" : "0",
                            "message" : '.$json_data.'
                          }';
                $this->disconnect();
                return $response_json;
            }
            else{
                return INVALID_USER;
            }
        }
        else{
            return CONNECTION_ERROR;
        }
    }

    private function disconnect(){
        if($this->db != null){
            $this->db->closeConnection($this->connection);
        }
    }
}