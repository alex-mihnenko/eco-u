<?php
// Init
	include("_lib.php");

	header('Content-Type: text/html; charset=utf-8');

	$config = json_decode(file_get_contents('crm-to-oc-orders-config.json'));
// ---

// Request
	$url = 'https://eco-u.retailcrm.ru/api/v5/orders';
	$data = array('apiKey' => RCRM_KEY, 'limit' => 100, 'page' => (int)$config->page);
	$result = connectGetAPI($url, $data);
	
	// Update config
		if( count($result->orders) > 0 ){
			$log[] = 'Has been getted '.count($result->orders).' rows';
		}
		else {
			$log[] = 'No rows';

			$res['log'] = $log;
			$res['mess']='Success';
			echo json_encode($res); exit;
		}
	// ---

	$log[] = 'Current step '.$config->page;
// ---

// Proccessing
	$count = 0;

	foreach ($result->orders as $key => $order) {
		// ---

			// Save
				if( isset($order->externalId) && isset($order->customer->address->text) ) {
					// ---
						// Check
							$q = "SELECT * FROM `".DB_PREFIX."order` WHERE `order_id`='".$order->externalId."' LIMIT 1;";
							$rows_order = $db->query($q);

							
							if ($rows_order->num_rows > 0) {
								// ---
									$row_order = $rows_order->fetch_assoc();

									// Fixed address
										$address = '';

										if( isset($order->delivery->address) ){
											// ---
												// Region and City
												if( isset($order->delivery->address->cityType) ) { $address .= $order->delivery->address->cityType.' '; }
												else if( isset($order->delivery->address->region) ) { $address .= $order->delivery->address->region.', '; }
												if( isset($order->delivery->address->city) ) { $address .= $order->delivery->address->city.', '; }

												// Street
												if( isset($order->delivery->address->streetType) ) { $address .= $order->delivery->address->streetType.' '; }
												if( isset($order->delivery->address->street) ) { $address .= $order->delivery->address->street.', '; }

												// Add
												if( isset($order->delivery->address->building) ) { $address .= 'д. '.$order->delivery->address->building.', '; }
												if( isset($order->delivery->address->flat) ) { $address .= 'кв./офис '.$order->delivery->address->flat.', '; }
												if( isset($order->delivery->address->block) ) { $address .= 'под. '.$order->delivery->address->block.', '; }
												if( isset($order->delivery->address->floor) ) { $address .= 'эт. '.$order->delivery->address->floor.', '; }

												// Fix
												$address = mb_substr($address,0,mb_strlen($address)-2);
											// ---
										}
			
										// $address = $order->customer->address->text;
										// $length = strlen($address);
										
										// if( $length > 0 ){
										// 	for ($i=0; $i<$length; $i++) {
										// 	    if( !isCyrilicLetter( mb_substr($address,0,1) ) ){
										// 	    	$address = mb_substr($address,1,strlen($address)-1);
										// 	    }
										// 	    else{ break; }
										// 	}
										// }
									// ---

									// Save address
										$q = "SELECT * FROM `".DB_PREFIX."address` WHERE `customer_id`='".$row_order['customer_id']."' AND address_1='".$address."';";
										$rows_address = $db->query($q);

										if ($rows_address->num_rows == 0 && !empty($address) ) {
											$q = "
												INSERT INTO `".DB_PREFIX."address` SET 
												`customer_id` = '".$row_order['customer_id']."',
												`firstname` = '".$row_order['firstname']."',
												`lastname` = '".$row_order['lastname']."',
												`company` = '',
												`address_1` = '".$address."',
												`address_2` = '',
												`city` = '',
												`postcode` = '',
												`country_id` = '0',
												`zone_id` = '0',
												`custom_field` = ''
											";
											
											if ($db->query($q) === TRUE) {
												$count++;
											    $log[] = '['.$row_order['customer_id'].'] '.$order->externalId.' has been inserted';
											} else {
												$log[] = '['.$row_order['customer_id'].'] '.$order->externalId.' has been not inserted: '.$db->error;
											}
										}
									// ---
								// ---
							}
						// ---
					// ---
				}

		// ---
	}

	$log[] = 'Has been proccessed '.$count.' rows';

	// Update config
		if( $count > 0 ){
			$config->page = $config->page + 1;
			file_put_contents('crm-to-oc-orders-config.json',json_encode($config));
		}
		else {
			$config->page = 1;
			file_put_contents('crm-to-oc-orders-config.json',json_encode($config));
		}
	// ---
// ---


// Response
	$res['log'] = $log;
	$res['mess']='Success';
	echo json_encode($res); exit;
// ---