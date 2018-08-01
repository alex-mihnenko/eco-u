<?php
// Init
	include("../_lib.php");

	header('Content-Type: text/html; charset=utf-8');

	$log = [];

	if( !isset($_GET['id']) ){
		$log[] = 'Empty request data';

		$res['log'] = $log;
		$res['mess']='Success';
		echo json_encode($res); exit;
	}

	$crm_order_id = $_GET['id'];
// ---


// Request
	$url = 'https://eco-u.retailcrm.ru/api/v5/orders/'.$crm_order_id;
	$data = array('apiKey' => RCRM_KEY, 'by' => 'id');
	$result = connectGetAPI($url, $data);

	if( !isset($result->order) || !isset($result->order->externalId) ) {
		// ---
			$log[] = 'No CRM order';

			$res['log'] = $log;
			$res['mess']='Success';
			echo json_encode($res); exit;
		// ---
	}

	$order = $result->order;
// ---

// Proccessing
	// Check
		$q = "SELECT * FROM `".DB_PREFIX."order` WHERE `order_id`='".$order->externalId."' LIMIT 1;";
		$rows_order = $db->query($q);

		if ($rows_order->num_rows == 0) {
			$log[] = 'No OC order';

			$res['log'] = $log;
			$res['mess']='Success';
			echo json_encode($res); exit;
		}
	// ---


	// Save order adddress
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
		// ---

		// Save address
			$q = "SELECT * FROM `".DB_PREFIX."address` WHERE `customer_id`='".$row_order['customer_id']."' AND address_1='".$address."';";
			$rows_address = $db->query($q);

			if ($rows_address->num_rows == 0 && !empty($address) ) {
				// To OC
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
					    $log[] = 'OC customer address ['.$row_order['customer_id'].'] has been inserted';
					} else {
						$log[] = 'OC customer address ['.$row_order['customer_id'].'] has been not inserted: '.$db->error;
					}
				// ---

				// To CRM
					// Edit customer
						if( isset($order->customer) ) {
							// ---
								$customer = $order->customer;

								$customerData = array();
								$customerAddress = array();
								$customerCustomFields = array();


								if( !isset($customer->address->text) ){
									// ---
										//$customerAddress['index'] = '';
										if( isset($order->delivery->address->countryIso) ) { $customerAddress['countryIso'] = $order->delivery->address->countryIso; }
										if( isset($order->delivery->address->region) ) { $customerAddress['region'] = $order->delivery->address->region; }
										if( isset($order->delivery->address->regionId) ) { $customerAddress['regionId'] = $order->delivery->address->regionId; }
										if( isset($order->delivery->address->city) ) { $customerAddress['city'] = $order->delivery->address->city; }
										if( isset($order->delivery->address->cityId) ) { $customerAddress['cityId'] = $order->delivery->address->cityId; }
										if( isset($order->delivery->address->cityType) ) { $customerAddress['cityType'] = $order->delivery->address->cityType; }
										if( isset($order->delivery->address->street) ) { $customerAddress['street'] = $order->delivery->address->street; }
										if( isset($order->delivery->address->streetId) ) { $customerAddress['streetId'] = $order->delivery->address->streetId; }
										if( isset($order->delivery->address->streetType) ) { $customerAddress['streetType'] = $order->delivery->address->streetType; }
										if( isset($order->delivery->address->building) ) { $customerAddress['building'] = $order->delivery->address->building; }
										if( isset($order->delivery->address->flat) ) { $customerAddress['flat'] = $order->delivery->address->flat; }
										if( isset($order->delivery->address->intercomCode) ) { $customerAddress['intercomCode'] = $order->delivery->address->intercomCode; }
										if( isset($order->delivery->address->floor) ) { $customerAddress['floor'] = $order->delivery->address->floor; }
										if( isset($order->delivery->address->block) ) { $customerAddress['block'] = $order->delivery->address->block; }
										if( isset($order->delivery->address->house) ) { $customerAddress['house'] = $order->delivery->address->house; }
										if( isset($order->delivery->address->metro) ) { $customerAddress['metro'] = $order->delivery->address->metro; }
										//$customerAddress['notes'] = '';
									// ---
								}
								else {
									// Set main address
										if( isset($customer->address->countryIso) ) { $customerAddress['countryIso'] = $customer->address->countryIso; }
										if( isset($customer->address->region) ) { $customerAddress['region'] = $customer->address->region; }
										if( isset($customer->address->regionId) ) { $customerAddress['regionId'] = $customer->address->regionId; }
										if( isset($customer->address->city) ) { $customerAddress['city'] = $customer->address->city; }
										if( isset($customer->address->cityId) ) { $customerAddress['cityId'] = $customer->address->cityId; }
										if( isset($customer->address->cityType) ) { $customerAddress['cityType'] = $customer->address->cityType; }
										if( isset($customer->address->street) ) { $customerAddress['street'] = $customer->address->street; }
										if( isset($customer->address->streetId) ) { $customerAddress['streetId'] = $customer->address->streetId; }
										if( isset($customer->address->streetType) ) { $customerAddress['streetType'] = $customer->address->streetType; }
										if( isset($customer->address->building) ) { $customerAddress['building'] = $customer->address->building; }
										if( isset($customer->address->flat) ) { $customerAddress['flat'] = $customer->address->flat; }
										if( isset($customer->address->intercomCode) ) { $customerAddress['intercomCode'] = $customer->address->intercomCode; }
										if( isset($customer->address->floor) ) { $customerAddress['floor'] = $customer->address->floor; }
										if( isset($customer->address->block) ) { $customerAddress['block'] = $customer->address->block; }
										if( isset($customer->address->house) ) { $customerAddress['house'] = $customer->address->house; }
										if( isset($customer->address->metro) ) { $customerAddress['metro'] = $customer->address->metro; }
									// ---

									if( !isset($customer->customFields->addition_address_first) && !isset($customer->customFields->addition_address_second) ){
										$customerCustomFields['addition_address_first'] = $address;
									}
									else if( isset($customer->customFields->addition_address_first) && !isset($customer->customFields->addition_address_second) ){
										$customerCustomFields['addition_address_second'] = $address;
									}
									else {
										$customerCustomFields['addition_address_third'] = $address;
									}
								}


								$url = 'https://eco-u.retailcrm.ru/api/v5/customers/'.$customer->id.'/edit';

								$customerData = array(
									'address' => $customerAddress,
									'customFields' => $customerCustomFields
								);

								$data = array(
									'apiKey' => RCRM_KEY,
									'by' => 'id',
									'customer' => json_encode($customerData)
								);

								$result = connectPostAPI($url, $data);

								$log[] = 'CRM customer address ['.$row_order['customer_id'].'] update: '.json_encode($result);
							// ---
						}
					// ---
				// ---
			}
			else {
				$log[] = 'OC customer address ['.$row_order['customer_id'].'] already exist';
			}
		// ---
	// ---
// ---


// Response
	$res['log'] = $log;
	$res['mess']='Success';
	echo json_encode($res); exit;
// ---