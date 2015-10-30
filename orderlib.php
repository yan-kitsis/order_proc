<?php


class Order
{
  public $name;
  public $order_arr;
  public $fulfilled_arr;
  public $backord_arr;

  public function __construct($name_in)
  {
	  $this->name = $name_in;
	  $this->order_arr = Array(0,0,0,0,0);
	  $this->fulfilled_arr = Array(0,0,0,0,0);
	  $this->backord_arr = Array(0,0,0,0,0);
  }

  public  function add_to_order($storage, $item_name, $quantity) {
  
		$index=array_search($item_name, $storage->name_arr);
		$this->order_arr[$index] = 	$quantity;
		if($storage->subtractQantity($item_name,$quantity)){
			$this->fulfilled_arr[$index] = $quantity;
		}else{
			$this->backord_arr[$index] = $quantity;
		}
  }

  public  function save_in_file() {
		$file_name = 'orderslog';
		$st_file = fopen($file_name, 'a');
		$buf = $this->name . " : ";;
		for($i=0; $i < 5; $i++){
			$buf .= $this->order_arr[$i];
			if($i<4)
				$buf .=	',';
		}
		$buf .=	'::';
		for($i=0; $i < 5; $i++){
			$buf .= $this->fulfilled_arr[$i];
			if($i<4)
				$buf .=	',';
		}
		$buf .=	'::';
		for($i=0; $i < 5; $i++){
			$buf .= $this->backord_arr[$i];
			if($i<4)
				$buf .=	',';
		}
		fwrite($st_file, $buf);
		fclose($st_file);
  
  }
}


class Storage
{
  public $name;
  public $name_arr = array('A','B','C','D','E');
 
  public function __construct($name_in, $qty_a, $qty_b, $qty_c, $qty_d, $qty_e)
  {
	  $this->name = $name_in;
  
  }

  public function setQantity($qty_a, $qty_b, $qty_c, $qty_d, $qty_e)
  {
		$file_name = $this->name . '.rg';
		$st_file = fopen($file_name, "w");
		$str = $qty_a . ';' . $qty_b . ';' . $qty_c . ';' . $qty_d . ';' .  $qty_e;
		fwrite($st_file, $str);
		fclose($st_file);
  }

  public function is_there_something()
  {
		$file_name = $this->name . '.rg';
		if(!file_exists($file_name))
			return false;
		$st_file = fopen($file_name, "r");
		$qty_str = fread($st_file, filesize($file_name));
		$qty_arr = explode(";",$qty_str);
		fclose($st_file);  
		for($i=0; $i < 5; $i++){
			if( intval ($qty_arr[$i]) > 0)
				return true;
		}
		return false;
  }

  public function subtractQantity($product, $qty)
  {
		$file_name = $this->name . '.rg';
		$st_file = fopen($file_name, "r");
		$qty_str = fread($st_file, filesize($file_name));
		$qty_arr = explode(";",$qty_str);
		fclose($st_file);  
		$index = array_search($product, $this->name_arr);
		if(intval ($qty_arr[$index]) >= $qty ){
			$qty_arr[$index] -= $qty;
			$buf = '';
			for($i=0; $i < 5; $i++){
				$buf .= $qty_arr[$i] . ";";
			}
			$buf .= "\n";
			$st_file = fopen($file_name, "w");
			fwrite($st_file, $buf );
			fclose($st_file);
			return true;
		}else{
			return false; 
		}
  }
  
}
 
function print_log_file(){
	$file_name = 'orderslog';
	if(!file_exists($file_name)){
		echo "There is no log file";
		return;
	}		
	echo "<h2>Orders log file</h2>";
	$logfile = fopen($file_name, "r") or die("Unable to open file!");
	
	$buf='';
	while(! feof($logfile))
	{
		$buf .= fgets($logfile) . "<br />";
	}
	fclose($logfile);
	return $buf;

}


?>