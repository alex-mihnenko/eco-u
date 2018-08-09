<?php
// Init
	error_reporting(E_ALL);
	ini_set('display_errors', '1');

	include("../config.php");

	$db = new mysqli(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

	if ($db->connect_error) {
	    die("Connection failed to db: " . $db->connect_error);
	}

	$db->set_charset("utf8");

	define('AUTH_DATA', 'admin@mail195:b41fd841edc5');
	define('RETAILCRM_KEY', 'AuNf4IgJFHTmZQu7PwTKuPNQch5v03to');


	$config = json_decode(file_get_contents('ms-product-delivery-config.json'));


	$log = [];
	$currenttime = mktime(0, 0, 0, date('n',time()), date('j',time()), date('Y',time()));
// ---

// Set query
	$url = "https://online.moysklad.ru/api/remap/1.1/entity/purchaseorder?limit=100&offset=0";
	$qdata = array();
// ---

// Request
	$orders = connectMSAPI($url,AUTH_DATA);
	$log['orderes'] = count($orders['rows']);

	if( $config->current < 0 ) { $config->current = 0; }
	else { $config->current = $config->current + 1; }
	$config->count = count($orders['rows']);

	file_put_contents('ms-product-delivery-config.json',json_encode($config));
// ---


// Processing
	$id = $orders['rows'][$config->current]['id'];
	$log['Purchaseorder'] = $id;

	if( isset($orders['rows'][$config->current]['deliveryPlannedMoment']) ) {
		$deliveryPlannedMomentDateTime = $orders['rows'][$config->current]['deliveryPlannedMoment'];
		$deliveryPlannedMomentArr = explode(' ', $deliveryPlannedMomentDateTime);
		$deliveryPlannedMoment = $deliveryPlannedMomentArr[0];

		// Get UNIX time
			$dateTmp = explode('-', $deliveryPlannedMoment);

			$deliveryUnixtime = mktime(0, 0, 0, int($dateTmp[1]), int($dateTmp[2]), $dateTmp[0]);
		// ---

		$log['deliveryPlannedMoment'] = $deliveryPlannedMoment;

		if( $deliveryUnixtime <= $currenttime ) {
			// ---
				$res['log'] = json_encode($log);
				$res['mess']='Planned moment is passed';

				// Check config
					if( $config->current >= $config->count-1  ) {
							
						$config->current = -1;
						$config->count = -1;
						file_put_contents('ms-product-delivery-config.json',json_encode($config));
					}
				// ---

				echo json_encode($res); exit;
				exit;
			// ---
		}
	}
	else {
		// ---
			$res['log'] = json_encode($log);
			$res['mess']='No delivery planned date';

			// Check config
				if( $config->current >= $config->count-1  ) {
						
					$config->current = -1;
					$config->count = -1;
					file_put_contents('ms-product-delivery-config.json',json_encode($config));
				}
			// ---
					
			echo json_encode($res); exit;
			exit;
		// ---
	}


	// Get order
	$url = "https://online.moysklad.ru/api/remap/1.1/entity/purchaseorder/{$id}/positions";
	$order = connectMSAPI($url,AUTH_DATA);
	$log['positions'] = count($order['rows']);

	foreach ($order['rows'] as $key => $product) {
		// ---
			// Get MS product id
				$urlProduct=parse_url($product['assortment']['meta']['href']);
				$hrefArr=explode("/",$urlProduct['path']);
				$msProducId=$hrefArr[6];
			// ---


			// Get OC product id
	            $q = "SELECT * FROM `ms_products` WHERE `ms_id`='".$msProducId."';";
	            $msproductQ = $db->query($q);

	            if ( $msproductQ->num_rows > 0 ) {
	            	// Update OC product
	            		$msproductRow = $msproductQ->fetch_assoc();
	            		$product_id = $msproductRow['product_id'];
	            		
						$q = "
							UPDATE `oc_product` SET 
							`date_available`= '".$deliveryPlannedMoment."'
							WHERE `product_id` = ".$product_id.";
						";
						
						if ($db->query($q) === TRUE) {
						    $log[$product_id] = "Success update";
						} else {
							$log[$product_id] = "Error update: ".$db->error;
						}
					// ---
	            }
			// ---
		// ---
	}
// ---

// Check config
	if( $config->current >= $config->count-1  ) {
			
		$config->current = -1;
		$config->count = -1;
		file_put_contents('ms-product-delivery-config.json',json_encode($config));

		exit;
	}
// ---

$res['log'] = json_encode($log);
$res['mess']='OK';
echo json_encode($res); exit;



// Functions
	function connectPostAPI($url, $qdata, $cookie='') {

		$data = http_build_query($qdata);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");  
		curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_COOKIE, $cookie);
		$headers = ['Content-Type: application/x-www-form-urlencoded'];
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_HEADER, false);

		// Output
		$output = curl_exec($ch);
		$result = json_decode($output);

		// Result
		if( $result != null ){
			curl_close ($ch);
			return $result;
		}
		else {
			curl_close ($ch);
			return false;
		}

	}

	function connectGetAPI($url, $qdata) {

		$data = http_build_query($qdata);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_URL,$url.'?'.$data);
		curl_setopt($ch, CURLOPT_TIMEOUT, 80);

		// Output
		$output = curl_exec($ch);
		$result = json_decode($output);

		// Result
		if( $result != null ){
			curl_close ($ch);
			return $result;
		}
		else {
			curl_close ($ch);
			return false;
		}

	}


	function connectMSAPI($url, $auth){

		$curl=curl_init(); 
		curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client/1.0');
		curl_setopt($curl,CURLOPT_URL,$url);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		curl_setopt($curl,CURLOPT_POST,0);
		curl_setopt($curl,CURLOPT_USERPWD,$auth);
		curl_setopt($curl,CURLOPT_HEADER,false);
		curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
		curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);
		$out=curl_exec($curl);
		curl_close($curl);

		$json=json_decode($out, JSON_UNESCAPED_UNICODE);

		return $json;

	}
// ---