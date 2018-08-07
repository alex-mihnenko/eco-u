<?php
// Init
	header("Access-Control-Allow-Origin: *");
	
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

	if( !isset($result->order) || !isset($result->order->externalId) || !isset($result->order->customer) ) {
		// ---
			$log[] = 'No CRM order';

			$res['log'] = $log;
			$res['mess']='Success';
			echo json_encode($res); exit;
		// ---
	}

	$order = $result->order;
	$customer = $result->order->customer;
	
	if( $order->status != 'confim' ){
		// ---
			$log[] = 'Order status not confirm';

			$res['log'] = $log;
			$res['mess']='Success';
			echo json_encode($res); exit;
		// ---
	}
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


	// Save adddress
		$row_order = $rows_order->fetch_assoc();


		// Get order address
			$order_address = '';

			if( isset($order->delivery->address) ){
				// ---
					// Region and City
					if( isset($order->delivery->address->cityType) ) { $order_address .= $order->delivery->address->cityType.' '; }
					else if( isset($order->delivery->address->region) ) { $order_address .= $order->delivery->address->region.', '; }
					if( isset($order->delivery->address->city) ) { $order_address .= $order->delivery->address->city.', '; }

					// Street
					if( isset($order->delivery->address->streetType) ) { $order_address .= $order->delivery->address->streetType.' '; }
					if( isset($order->delivery->address->street) ) { $order_address .= $order->delivery->address->street.', '; }

					// Add
					if( isset($order->delivery->address->building) ) { $order_address .= 'д. '.$order->delivery->address->building.', '; }
					if( isset($order->delivery->address->flat) ) { $order_address .= 'кв./офис '.$order->delivery->address->flat.', '; }
					if( isset($order->delivery->address->block) ) { $order_address .= 'под. '.$order->delivery->address->block.', '; }
					if( isset($order->delivery->address->floor) ) { $order_address .= 'эт. '.$order->delivery->address->floor.', '; }

					// Fix
					$order_address = mb_substr($order_address,0,mb_strlen($order_address)-2);
				// ---
			}
		// ---
			
		// Save address
			$q = "SELECT * FROM `".DB_PREFIX."address` WHERE `customer_id`='".$row_order['customer_id']."' AND address_1='".$order_address."';";
			$rows_address = $db->query($q);

			if ($rows_address->num_rows == 0 && !empty($order_address) ) {
				// To OC
					$q = "
						INSERT INTO `".DB_PREFIX."address` SET 
						`customer_id` = '".$row_order['customer_id']."',
						`firstname` = '".$row_order['firstname']."',
						`lastname` = '".$row_order['lastname']."',
						`company` = '',
						`address_1` = '".$order_address."',
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
					$customerData = array();

					if( !isset($customer->address) ){
						// ---
							$customerAddress = array();
							
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

							$customerData['address'] = $customerAddress;
						// ---
					}
					else{
						// ---
							$customerCustomFields = array();

							if( !isset($customer->customFields->addition_address_first) && !isset($customer->customFields->addition_address_second) && !isset($customer->customFields->addition_address_third) ){
								$customerCustomFields['addition_address_first'] = $order_address;
							}
							else if( isset($customer->customFields->addition_address_first) && !isset($customer->customFields->addition_address_second) && !isset($customer->customFields->addition_address_third) ){
								$customerCustomFields['addition_address_second'] = $order_address;
							}
							else {
								$customerCustomFields['addition_address_third'] = $order_address;
							}

							$customerData = array(
								'customFields' => $customerCustomFields
							);
						// ---
					}


					$url = 'https://eco-u.retailcrm.ru/api/v5/customers/'.$customer->id.'/edit';

					

					$data = array(
						'apiKey' => RCRM_KEY,
						'by' => 'id',
						'customer' => json_encode($customerData)
					);

					$result = connectPostAPI($url, $data);

					$log[] = 'CRM customer address ['.$row_order['customer_id'].'] update: '.json_encode($result);
				// ---
			}
			else {
				$log[] = 'OC customer address ['.$row_order['customer_id'].'] already exist';
			}
		// ---
	// ---

	// Save custom fields
		$customerData = array();

		// Get order custom fields
			$order_intercom = '';

			if( isset($order->customFields->intercom) ){
				$order_intercom = $order->customFields->intercom;
			}
		// ---

		// Save custom fields
			$customerCustomFields = array();

			if( $order_intercom != '' ){
				// ---
					$customerCustomFields['intercom'] = $order_intercom;
				// ---
			}


			$customerData = array(
				'customFields' => $customerCustomFields
			);

			$url = 'https://eco-u.retailcrm.ru/api/v5/customers/'.$customer->id.'/edit';

								
			$data = array(
				'apiKey' => RCRM_KEY,
				'by' => 'id',
				'customer' => json_encode($customerData)
			);

			$result = connectPostAPI($url, $data);

			$log[] = 'CRM customer custom fields ['.$row_order['customer_id'].'] update: '.json_encode($result);
		// ---
	// ---
// ---

/* DEBUG */ file_put_contents('crm-change-order-log.txt', $row_order['order_id']." : ".date('d.m.Y H:i:s')." : ".json_encode($log)."\n\n", FILE_APPEND | LOCK_EX);

// Response
	$res['log'] = $log;
	$res['mess']='Success';
	echo json_encode($res); exit;
// ---