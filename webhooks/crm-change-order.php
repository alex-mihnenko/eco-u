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
	
	$log[] = 'Order status is '.$order->status;

	$action = '';

	if( $order->status == 'confim' || $order->status == 'send-to-assembling' ){ // Подтвержден или на сборку
		$action = '#1';
	}
	else if( $order->status == 'delivering' || $order->status == 'send-to-delivery' || $order->status == 'complete' ){ // Доставляется или Доставлен или Завершен
		$action = '#2';
	}

	if( empty($action) ){
		// ---
			$log[] = 'Order status not allowd';

			$res['log'] = $log;
			$res['mess']='Success';
			echo json_encode($res); exit;
		// ---
	}
// ---

switch ($action) {
	case '#1':
		// ---
			// Check
				$q = "SELECT * FROM `".DB_PREFIX."order` WHERE `order_id`='".$order->externalId."' LIMIT 1;";
				$rows_order = $db->query($q);

				if ($rows_order->num_rows == 0) {
					$log[] = 'No OC order';

					$res['log'] = $log;
					$res['mess']='Success';
					echo json_encode($res); exit;
				}

				$row_order = $rows_order->fetch_assoc();
			// ---

			// Save adddress
				// Get order address
					if( isset($order->delivery->address) ){
						// ---
							if( isset($order->customFields->order_delivery_address_type) && $order->customFields->order_delivery_address_type != false ){
								$order_address = addressCrmToOc($order->delivery->address, true);
							}
							else {
								$order_address = addressCrmToOc($order->delivery->address, false);
							}
						// ---
					}
				// ---
					
				// Save address
					// To CRM
						if( isset($customer->address) ){
							// Clear main address
		                    	$url = 'https://eco-u.retailcrm.ru/api/v5/customers/'.$customer->id.'/edit';

		                    	$data = array(
		                        	'apiKey' => RCRM_KEY,
		                        	'by' => 'id',
		                        	'customer' => json_encode(array('address' => array()))
		                    	);

		                    	$result = connectPostAPI($url, $data);

								$log[] = 'CRM customer address ['.$row_order['customer_id'].'] delete: '.json_encode($result);
		                  	// ---
						}

						$customerData = array();

						// Set data
							$customerData['address'] = $order_address['obj'];

							if( isset($order->customFields->order_delivery_address_type) ){
								$customerCustomFields = array();

								$customerCustomFields['customer_delivery_address_type'] = $order->customFields->order_delivery_address_type;
								
								$customerData['customFields'] = $customerCustomFields;
							}
						// ---

						
						// Save  address
							$url = 'https://eco-u.retailcrm.ru/api/v5/customers/'.$customer->id.'/edit';

							$data = array(
								'apiKey' => RCRM_KEY,
								'by' => 'id',
								'customer' => json_encode($customerData)
							);

							$result = connectPostAPI($url, $data);

							$log[] = 'CRM customer address ['.$row_order['customer_id'].'] update: '.json_encode($result);
						// ---
					// ---

					// To OC
						// Edit customer addresses
							$q = "
								UPDATE `".DB_PREFIX."address` SET 
								`custom_field` = '' 
								WHERE `customer_id`='".$row_order['customer_id']."'
							;";

							if ($db->query($q) === TRUE) {
							    $log[] = 'OC customer addresses ['.$customer_id.'] has been updated';
							} else {
								$log[] = 'OC customer addresses ['.$order_id.'] has been not updated: '.$db->error;
							}
						// ---

						$oc_address_type = 'primary';

						$q = "SELECT * FROM `".DB_PREFIX."address` WHERE `customer_id`='".$row_order['customer_id']."' AND `address_1` = '".$order_address['text']."';";
						$rows_address = $db->query($q);

						if ($rows_address->num_rows == 0 && !empty($order_address['text']) ) {
							// ---
								$q = "
									INSERT INTO `".DB_PREFIX."address` SET 
									`customer_id` = '".$row_order['customer_id']."',
									`firstname` = '".$row_order['firstname']."',
									`lastname` = '".$row_order['lastname']."',
									`company` = '',
									`address_1` = '".$order_address['text']."',
									`address_2` = '".json_encode($order_address['obj'],JSON_UNESCAPED_UNICODE)."',
									`city` = '',
									`postcode` = '',
									`country_id` = '0',
									`zone_id` = '0',
									`custom_field` = '".$oc_address_type."'
								";
								
								if ($db->query($q) === TRUE) {
								    $log[] = 'OC customer address ['.$row_order['customer_id'].'] has been inserted';
								} else {
									$log[] = 'OC customer address ['.$row_order['customer_id'].'] has been not inserted: '.$db->error;
								}
							// ---
						}
						else {
							$row_address = $rows_address->fetch_assoc();

							$q = "
								UPDATE `".DB_PREFIX."address` SET 
								`custom_field` = '".$oc_address_type."' 
								WHERE `customer_id`='".$row_order['customer_id']."' AND `address_id`='".$row_address['address_id']."'
							;";

							if ($db->query($q) === TRUE) {
							    $log[] = 'OC customer addresses ['.$customer_id.'] has been updated';
							} else {
								$log[] = 'OC customer addresses ['.$order_id.'] has been not updated: '.$db->error;
							}

							$log[] = 'OC customer address ['.$row_order['customer_id'].'] already exist or empty';
						}
					// ---
				// ---
			// ---
		// ---
	break;

	case '#2':
		// ---
			// OC - check order
				$q = "SELECT * FROM `".DB_PREFIX."order` WHERE `order_id`='".$order->externalId."' LIMIT 1;";
				$rows_order = $db->query($q);

				if ($rows_order->num_rows == 0) {
					$log[] = 'No OC order';

					$res['log'] = $log;
					$res['mess']='Success';
					echo json_encode($res); exit;
				}

				$row_order = $rows_order->fetch_assoc();
			// ---

			// OC - get customer order
				$q = "SELECT * FROM `ms_demand` WHERE `order_id`='".$order->externalId."' AND `ms_demand_id`<> '' AND `ms_customer_order_id`<> '' AND `customer_order_data`<> '' LIMIT 1;";
				$rows_demand = $db->query($q);

				if ($rows_demand->num_rows == 0) {
					$log[] = 'No OC demand';

					$res['log'] = $log;
					$res['mess']='Success';
					echo json_encode($res); exit;
				}

				$row_demand = $rows_demand->fetch_assoc();
			// ---

			// OC - create demand task
				$q = "
					INSERT INTO `ms_demand` SET 
					`ms_demand_id` = '',
					`ms_customer_order_id` = '".$row_demand['ms_customer_order_id']."',
					`order_id` = '".$row_demand['order_id']."',
					`customer_order_data` = '".$row_demand['customer_order_data']."',
					`date_added` = '".time()."',
					`deleted` = '0',
					`completed` = '0'
				";
				
				if ($db->query($q) === TRUE) {
					$demand_id = $db->insert_id;

				    $log[] = 'OC demand ['.$demand_id.'] has been inserted';
				} else {
					$log[] = 'OC demand has been not inserted: '.$db->error;
				}
			// ---
		// ---
	break;
}


/* DEBUG  */  file_put_contents('./crm-change-order.log', date("d.m.Y H:i", time()) . "" . json_encode($log,JSON_UNESCAPED_UNICODE)."\n\n", FILE_APPEND | LOCK_EX);

// Response
	$res['log'] = $log;
	$res['mess']='Success';
	echo json_encode($res); exit;
// ---