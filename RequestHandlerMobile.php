<?php 

require_once ("./VehicleAccess.php");
require_once ("./FillUpAccess.php");

$method = $_GET['method'];


if($method == "view_all_vehicle"){
	$va = new VehicleAccess();
	echo $va->selectAll();
}

if($method == "add_fill_up"){

	$data =array();
	$data['fuel_type'] = $_POST["fuel_type"];
	$data['amount'] = $_POST["amount"];
	$data['unit_price'] = $_POST["unit_price"];
	$data['total_price'] = $_POST["total_price"];
	$data['f_date'] = $_POST["date"];
	$data['vehicle_id'] = $_POST["vehicle_id"];
	$data['driver_id'] = $_POST["driver_id"];
	$data['odo_meter'] = $_POST["odo_meter"];

	$fu = new FillUpAccess();
	echo $fu->insertRow($data);
}

