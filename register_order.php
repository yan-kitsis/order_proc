<?php
include_once ('orderlib.php');


	if($_REQUEST['mode'] =='create_storage'){
		$obj = new Storage('test');
		$obj->setQantity(150,150,100,100,200);
		echo "Storage created";
		return;
	}

	if($_REQUEST['mode'] =='print_log'){
		echo print_log_file();
		return;
	}


	
	$obj = new Storage('test');
	if(!$obj->is_there_something()){
		echo 'The storage is empty';
		return;
	}
	
	$str = $_REQUEST['order'];
	$json = json_decode($str, true); 
	
	if( sizeof($json['Lines']) == 0) {
			echo 'Invalid order';
			return;
	}
	foreach ($json['Lines'] as $value) {
		if($value['Quantity'] < 1 || $value['Quantity'] > 5 ){
			echo 'Invalid order';
			return;
		}
	}	

	$header = $json['Header'];
	$order = new Order("<br>" . $header . '____' . substr($_SERVER['REQUEST_TIME'], -6));
	foreach ($json['Lines'] as $value) {
		$order->add_to_order($obj, $value['Product'], $value['Quantity']); 
	}	
	$order->save_in_file();
	echo "Order received";	

?>

