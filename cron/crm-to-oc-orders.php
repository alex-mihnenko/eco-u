﻿<?php
// Init
	include("../_lib.php");

	header('Content-Type: text/html; charset=utf-8');

	$config = json_decode(file_get_contents('crm-to-oc-orders.json'));
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
			$customer = $order->customer;

			if( isset($order->externalId) ){
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
								    $log[] = 'OC customer addresses ['.$row_order['customer_id'].'] has been updated';
								} else {
									$log[] = 'OC customer addresses ['.$row_order['order_id'].'] has been not updated: '.$db->error;
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
									`address_1` = '".$order_address['text']."',
									`address_2` = '".json_encode($order_address['obj'],JSON_UNESCAPED_UNICODE)."',
									`custom_field` = '".$oc_address_type."' 
									WHERE `customer_id`='".$row_order['customer_id']."' AND `address_id`='".$row_address['address_id']."'
								;";

								if ($db->query($q) === TRUE) {
								    $log[] = 'OC customer addresses ['.$row_order['customer_id'].'] has been updated';
								} else {
									$log[] = 'OC customer addresses ['.$row_order['order_id'].'] has been not updated: '.$db->error;
								}

								$log[] = 'OC customer address ['.$row_order['customer_id'].'] already exist or empty';
							}
						// ---
					// ---
				// ---

				$count++;
			}
		// ---
	}

	$log[] = 'Has been proccessed '.$count.' rows';

	// Update config
		if( $count > 0 ){
			$config->page = $config->page + 1;
			file_put_contents('crm-to-oc-orders.json',json_encode($config));
		}
		else {
			$config->page = 1;
			file_put_contents('crm-to-oc-orders.json',json_encode($config));
		}
	// ---
// ---


// Response
	$res['log'] = $log;
	$res['mess']='Success';
	echo json_encode($res); exit;
// ---