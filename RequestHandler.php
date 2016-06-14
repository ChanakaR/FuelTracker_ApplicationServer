<?php
/**
 * Created by PhpStorm.
 * User: bmCSoft
 * Date: 2016-04-24
 * Time: 8:55 AM
 */
require_once ("./VehicleAccess.php");
require_once ("./DriverAccess.php");
require_once ("./OfficerAccess.php");
require_once ("./StatCalculator.php");
require_once ("./TripAccess.php");

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
        decode_get_request();
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
                    break;
                case "OFFICER":
                    $oa = new OfficerAccess();
                    echo $oa->selectAll();
                    break;
                case "TRIP":
                    $ta = new TripAccess();
                    echo $ta->selectAll();
                    break;
                default:
                    break;
            }
        }
        elseif($array['select'] == 'N_ALL'){
            $class = $array["class"];
            switch($class){
                case "VEHICLE":
                    if($array['data'] == "AVA_VEHICLE"){
                        $va = new VehicleAccess();
                        echo $va->selectAll();
                    }
                    break;

                case "DRIVER":
                    if($array['data'] == "AVA_DRIVER"){
                        $da = new DriverAccess();
                        echo $da->selectAll();
                    }
                    break;
                default:
                    break;
            }
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
                break;
            case "OFFICER":
                $oa = new OfficerAccess();
                echo $oa->insertRow($array['data']);
                break;
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

function decode_get_request(){
    $method = $_GET["method"];
    if($method == "vehicle-stat"){
        $stat_cal = new StatCalculator();
        http_response_code(200);
        echo $stat_cal->getVehicleStatSummary($_GET["vehicle_id"]);

    }
}