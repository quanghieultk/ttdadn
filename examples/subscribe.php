<?php
require_once("../connection.php"); 
require('../phpMQTT.php');
$isStop = false;
$server = '13.67.74.76';     // change if necessary
$port = 1883;                     // change if necessary
$username = '';                   // set your username
$password = '';                   // set your password
$client_id = 'phpMQTT-subscriber'; // make sure this is unique for connecting to sever - you could use uniqid()

$mqtt = new Bluerhinos\phpMQTT($server, $port, $client_id);
if(!$mqtt->connect(true, NULL, $username, $password)) {
	exit(1);
}

$mqtt->debug = true;

$topics['Topic/GPS'] = array('qos' => 0, 'function' => 'procMsg');
$mqtt->subscribe($topics, 0);

while($mqtt->proc()) {

}

$mqtt->close();

function procMsg($topic, $msg){?>
	<div>
			<h1><?php echo($msg);?></h1>
			
			<?php 
				
				$server_username = "root";
				$server_password = "";
				$server_host = "localhost";
				$database = 'ttdadn_gps';
				$conn= mysqli_connect($server_host,$server_username,$server_password,$database);
				$query = "INSERT INTO gps_message(id,mess,date_GPS) VALUES(NULL,'{$msg}',NULL)";
				$results = mysqli_query($conn,$query);
				kt_query($results,$query);

				$string =  $msg;
				$string = substr($string,1,-1);
				$str = json_decode($string,true);
				
				$latitude = $str['values'][0];
				$longitude = $str['values'][1];
				$query = "INSERT INTO deviceroute(id, id_device,latitude,longitude,decription,date_update) VALUES (NULL,6,'{$latitude}','{$longitude}',NULL,now())";
				$results = mysqli_query($conn,$query);
				kt_query($results,$query);
				$lat_min = 100;
				$lat_max = 110;
				$long_min = 10;
				$long_max = 15;
				if(($latitude<$lat_min)||$latitude>$lat_max||$longitude<$long_min||$longitude>$long_max)
				{
					$message = '[{"device_id":"LightD","values":["1","255"]}]';
					$cli_id = 'abc';
					$server_client = '13.67.74.76';     // change if necessary
					$port_client = 1883;   
					$mqtt_client = new Bluerhinos\phpMQTT($server_client, $port_client, $cli_id);
					if ($mqtt_client->connect(true, NULL, "abc", "") && $GLOBALS['isStop'] == false) {
						$GLOBALS['isStop'] = true;
						$mqtt_client->publish("Topic/GPS", $message,0);
						$mqtt_client->close();
						echo("Đã publish");
					} else {
						echo "Time out!\n";
					}
				}
				
			?>
		</div>
		<?php
}
?>