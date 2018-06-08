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

	define('AUTH_DATA', 'admin@mail195:134679');
	define('RETAILCRM_KEY', 'AuNf4IgJFHTmZQu7PwTKuPNQch5v03to');


	$config = json_decode(file_get_contents('retailcrm-processor-config.json'));


	$log = [];
// ---

// Get edited orders
	# Get itemes
		$q = "SELECT * FROM `oc_order` WHERE `rcrm_status`='edited' AND `order_id`=22678 LIMIT 5;";
		$orders = $db->query($q);
	# ...


	if ($orders->num_rows > 0) {
		// ---
			while ($order = $orders->fetch_assoc()) {
			    // ---
			    	// Calculate weight
						$q = "SELECT `oc_order_product`.`quantity`, `oc_order_product`.`variant`, `oc_product`.`weight_class_id`, `oc_product`.`weight`, `oc_product`.`weight_package` FROM `oc_order_product` LEFT JOIN `oc_product` ON `oc_product`.`product_id`=`oc_order_product`.`product_id` WHERE `oc_order_product`.`order_id`=".$order['order_id'].";;";
						$products = $db->query($q);

						$weight=0;

						if ($products->num_rows > 0) {
							// ---
								while ($product = $products->fetch_assoc()) {
								    // ---
										if($product['weight']=="0.00000000" && $product['weight_class_id']==9){
											$product['weight']=1;
										}
										
										if($product['weight']!="0.00000000"){
											if($product['weight_class_id']==8 || $product['weight_class_id']==2 || $product['weight_class_id']==1 || $product['weight_class_id']==7) {
												$weight=$weight+round(($product['quantity']*$product['weight']));
												
											}

											//Если килограммы, то тоже самое но умножаем на 1000
											if($product['weight_class_id']==9) {
												$weight=$weight+(round(($product['quantity']*$product['weight'])*1000));
												
											}

											// Add package weight
												if( $product['weight_package'] != '' ) {
													$wpArr = (array) json_decode(html_entity_decode($product['weight_package']));

													// if( isset($wpArr[$product['variant']) ) {
													// 	if($product['weight_class_id']==2 || $product['weight_class_id']==9) {
													// 		$weight=$weight + floatval($wpArr[$product['variant']);
													// 	}
													// }
												}
											// ---
										}
								    // ---
								}
							// ---
						}
			    	// ---

					$log[] = 'Total weight: ' . $weight;

					// Update order
						if( $weight > 0 ){
							$url='https://eco-u.retailcrm.ru/api/v5/orders/IM'.$order['order_id'].'/edit?apiKey='.RETAILCRM_KEY;
							
							$data = array();
							$data_order = array();

							$data_order['externalId'] = 'IM'.$order['order_id'];
							$data_order['weight'] = $weight;

							$data['by']='externalId';
							$data['order']=json_encode($data_order);

							$res=connectPostAPI($url,$data);


							$q = "UPDATE `oc_order` SET `rcrm_status` = '' WHERE `order_id`=".$order['order_id'].";";

							if ($db->query($q) === TRUE) {
							    $log[] = 'Success update: '.$order['order_id'];
							} else {
							    $log[] = 'Error updating: '.$order['order_id'].' ['. $db->error.']';
							}
						}
			    	// ---
			    // ---
			}
		// ---
	}
// ---

$res['log'] = json_encode($log);
$res['mess']='OK';
echo json_encode($res); exit;




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