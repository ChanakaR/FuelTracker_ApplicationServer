<?php
/**
 * Created by PhpStorm.
 * User: bmCSoft
 * Date: 2016-04-24
 * Time: 8:55 AM
 */
require_once ("./VehicleAccess.php");
require_once ("./DriverAccess.php");

$method = $_SERVER['REQUEST_METHOD'];
$json = file_get_contents('php://input');
$json_array = json_decode($json,true);

/*
 * handle request methods
 */

switch ($method) {
    case 'PUT':
        decode_put_request($json_array);
        break;

    case 'POST':
        decode_post_request($json_array);
        break;

    case 'GET':
        break;

    case 'DELETE':
        decode_delete_request($json_array);
        break;

    case 'OPTIONS':

        break;

    default:

        break;
}

/*
 * decode the post request and query into relevant data
 *
 */
function decode_post_request($array){
    if($array['method'] == "SELECT"){
        if($array['select'] == "ALL"){
            $class = $array["class"];
            switch($class){
                case "VEHICLE":
                    $va = new VehicleAccess();
                    echo $va->selectAll();
                    break;
                case "DRIVER":
                    $dr = new DriverAccess();
                    echo $dr->selectAll();
                    //echo "huhuh";
                    break;
                default:
                    break;
            }
        }
        elseif($array['select'] == 'NALL'){

        }
    }
    elseif($array['method'] == "INSERT"){
        $class = $array["class"];
        switch($class){
            case "VEHICLE":
                $va = new VehicleAccess();
                echo $va->insertRow($array['data']);
                break;
            case "DRIVER":
                $dr = new DriverAccess();
                echo $dr->insertRow($array['data']);
            default:
                break;
        }
    }
}

function decode_put_request($array){
    if($array['method']=='UPDATE'){
        $class = $array['class'];
        switch($class){
            case "VEHICLE":
                $va = new VehicleAccess();
                echo $va->updateRow($array['data']);
                break;
            default:
                break;
        }
    }
}

function decode_delete_request($array){
    if($array['method']=='DELETE'){
        $class = $array['class'];
        switch($class){
            case "VEHICLE":
                $va = new VehicleAccess();
                echo $va->deleteRow($array['data']);
                break;
            default:
                break;
        }
    }
}