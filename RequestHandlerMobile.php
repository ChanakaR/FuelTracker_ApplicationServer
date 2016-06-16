<?php 

require_once ("./VehicleAccess.php");
require_once ("./FillUpAccess.php");
require_once ("./UserValidator.php");
require_once ("./TripAccess.php");
require_once ("./StatCalculator.php");
require_once ("./DriverAccess.php");
require_once ("./UserAccess.php");

$method = $_GET['method'];


if($method == "view_all_vehicle"){
	$va = new VehicleAccess();
	echo $va->selectAll();
}

if($method == "add_fill_up"){

	$data =array();
	$data['amount'] = $_POST["amount"];
	$data['total_price'] = $_POST["total_price"];
	$data['f_date'] = $_POST["date"];
	$data['f_time'] = "11.03";
	$data['odo_meter'] = $_POST["odo_meter"];
	$data['trip_id'] = $_POST["trip_id"];

	$fu = new FillUpAccess();
	echo $fu->insertRow($data);
}

if($method == "login"){
	$data = array();
	$data['username']=$_POST["user_name"];
	$data['password']=$_POST["password"];

	$user_check = new UserValidator();
	echo $user_check->checkUser($data);
}

if($method == "new_trip"){
	$data = array();
	$data['driver_id'] = $_POST["driver_id"];
	$data['vehicle_id'] = $_POST["vehicle_id"];
	$data['description'] = $_POST["description"];
	$data['start_odometer'] = $_POST["start_odometer"];
	$data['end_odometer'] = $_POST["start_odometer"];
	$data['start_time'] = "11.05"; 	//get current time of the server
	$data['end_time'] = "11.05";
	$data['date'] = "2016-05-27";	//get current date from the server
	$data['end_date'] ='0000-00-00';

	$trip = new TripAccess();
	echo $trip->insertRow($data);
}

if($method == "a_trip"){
	$data = array();
	$data["trip_id"] = $_POST["trip_id"];

	$trip = new TripAccess();
	echo $trip->select($data);
}

if($method == "a_vehicle"){
	$data = array();
	$data["vehicle_id"] = $_POST["vehicle_id"];

	$vehicle = new VehicleAccess();
	echo $vehicle->select($data);
}

if($method == "trip_check"){
	$data = array();
	$data["driver_id"] = $_POST["driver_id"];

	$trip = new TripAccess();
	echo $trip->selectOnGoingTrip($data);
}

if($method == "my_stat"){
	$data = array();
	$data["driver_id"] = $_POST["driver_id"];

	$stat = new StatCalculator();
	echo $stat->getMyProgressForDriver($data);
}

if($method == "my_trip_list"){
	$data = array();
	$data["driver_id"] = $_POST["driver_id"];

	$trip = new TripAccess();
	echo $trip->select($data);
}

if($method == "update_account"){
	$data = array();
	$data["from"] = "driver";
	$data["id"] = $_POST["id"];
	$data["contact_no"] = $_POST["contact_no"];
	$data["nic"] = $_POST["nic"];
	$data["address"] = $_POST["address"];
	$data["licence_no"] =  $_POST["licence_no"];

	$driver= new DriverAccess();
	echo $driver->updateRow($data,"driver");
}

if($method == "update_user"){
	$data = array();
	$data["id"] = $_POST["id"];
	$data["user_name"] = $_POST["user_name"];
	$data["password"] = $_POST["password"];

	$user = new UserAccess();
	echo $user->updateRow($data);
}

if($method == "end_trip"){
	$data = array();
	$data["method"] = "end_trip";
	$data["end_odometer"] = $_POST["end_odometer"];
	$data["trip_id"] = $_POST["trip_id"];
	$data["end_date"] = "2016-06-12";
	$data["end_time"] = "23.54";

	$trip = new TripAccess();
	echo $trip->updateRow($data);
}