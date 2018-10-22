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

			// Change delivery time
				if( isset($order->delivery->date) && isset($order->delivery->time->from) && isset($order->delivery->time->to) ){
					$date_arr = explode('-', $order->delivery->date);
					$delivery_date = $date_arr[2].'.'.$date_arr[1].'.'.$date_arr[0];
					$delivery_time = $delivery_date.' '.$order->delivery->time->from.'-'.$order->delivery->time->to;

					// Edit order delivery time
						$q = "
							UPDATE `".DB_PREFIX."order` SET 
							`delivery_time` = '".$delivery_time."' 
							WHERE `order_id`='".$row_order['order_id']."'
						;";

						if ($db->query($q) === TRUE) {
						    $log[] = 'OC order delivery time ['.$row_order['order_id'].'] has been updated';
						} else {
							$log[] = 'OC order delivery time ['.$row_order['order_id'].'] has been not updated: '.$db->error;
						}
					// ---
				}
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

			// Demands
				// OC - check demand
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

			// Custom for send-to-delivery
				if( $order->status == 'send-to-delivery' ){
					// OC Testimonials
						$q = "SELECT * FROM `".DB_PREFIX."testimonials` WHERE `order_id`='".$order->externalId."' LIMIT 1;";
						$rows_testimonials = $db->query($q);

						if ($rows_testimonials->num_rows == 0) {
							// ---
								// Create url
									$query = createShortURL('testimonials-add?o='.$row_order['order_id'].'&c='.$row_order['customer_id'], $db);
									$url = str_replace('http://', '', HTTP_SERVER).''.$query;
								// ---

								// Send SMS
									$q = "SELECT * FROM `".DB_PREFIX."url_short` WHERE `query`='".$query."' LIMIT 1;";
									$rows_url = $db->query($q);

									if ($rows_url->num_rows == 0) {
										// ---
											$q = "SELECT * FROM `".DB_PREFIX."setting` WHERE `key`='config_sms_testimonials_text' LIMIT 1;";
											$rows_setting = $db->query($q);

											if ($rows_setting->num_rows > 0) {
												$row_setting = $rows_setting->fetch_assoc();

												$message = mb_strtoupper(mb_substr($row_order['firstname'], 0, 1)) . mb_substr($row_order['firstname'], 1).', '.str_replace('[REPLACE]', $url, $row_setting['value']);

												$url = HTTP_SERVER.'index.php?route=/api/sms/send';
												$data = array('phone' => $row_order['telephone'], 'message' => $message);
												$result = opencartAPI($url, $data);

												$log[] = 'Send SMS status is ' . $result;
											}
										// ---
									}
								// ---
							// ---
						}
					// ---
				}
			// ---
					
			// Custom for complete
				if( $order->status == 'complete' ){
					// OC bonus for complere
						$q = "SELECT * FROM `".DB_PREFIX."bonus_account` ba WHERE ba.code='order_complete' AND ba.status='1' LIMIT 1;";
						$rows_ba = $db->query($q);

						if ($rows_ba->num_rows > 0) {
							$row_ba = $rows_ba->fetch_assoc();

							$ba_account_id = $row_ba['bonus_account_id'];
							$ba_name = $row_ba['name'];
							$ba_coin = $row_ba['coin'];
							$ba_rate = $row_ba['rate'];


							$q = "SELECT * FROM `".DB_PREFIX."order` o WHERE o.order_id='".$row_order['order_id']."' LIMIT 1;";
							$rows_o = $db->query($q);

							if ($rows_o->num_rows > 0) {
								$row_o = $rows_o->fetch_assoc();
								
								$amount = $ba_coin * round($row_o['total'] / $ba_rate);
							}
							else {
								$amount = 0;
							}

							$q = "
								INSERT INTO `".DB_PREFIX."bonus_history` SET 
								`bonus_account_id` = '" . $ba_account_id . "', 
								`customer_id` = '" . $row_order['customer_id'] . "', 
								`order_id` = '" . $row_order['order_id'] . "',
								`amount` = '" . $amount . "',
								`comment` = '',
								`time` = '" . time() . "'
							";
							
							if ($db->query($q) === TRUE) {
								$bonus_history_id = $db->insert_id;

							    $log[] = 'OC bonus account history ['.$bonus_history_id.'] has been inserted';
							} else {
								$log[] = 'OC bonus account history has been not inserted: '.$db->error;
							}
						}
					// ---

					// OC bonus weekly
						$unix_today = mktime(0, 0, 0, date('n',time()), date('j',time()), date('Y',time()));
						$unix_today_ago_week = mktime(0, 0, 0, date('n',time()-604800), date('j',time()-604800), date('Y',time()-604800));
						$unix_today_ago_two_week = mktime(0, 0, 0, date('n',time()-1209600), date('j',time()-1209600), date('Y',time()-1209600));
						$unix_today_ago_four_week = mktime(0, 0, 0, date('n',time()-2419200), date('j',time()-2419200), date('Y',time()-2419200));

						$q = "
							SELECT * FROM `".DB_PREFIX."bonus_account` ba 
							WHERE ba.code='order_weekly' AND ba.status='1' LIMIT 1
						;";

						$rows_bonus_account = $db->query($q);
						
						if ($rows_bonus_account->num_rows > 0) {
							$row_bonus_account = $rows_bonus_account->fetch_assoc();

							$ba_account_id = $row_bonus_account['bonus_account_id'];
							$ba_name = $row_bonus_account['name'];
							$ba_coin = intval($row_bonus_account['coin']);
							$ba_rate = $row_bonus_account['rate'];
							
							// One per week
								$q = "
									SELECT * FROM `".DB_PREFIX."bonus_history` bh 
									LEFT JOIN `".DB_PREFIX."bonus_account` ba ON ba.bonus_account_id = bh.bonus_account_id
									WHERE bh.customer_id = '".$row_order['customer_id']."' AND ba.code = 'order_weekly' AND bh.time > '" . $unix_today_ago_week . "' LIMIT 1
								;";
								
								$rows_bonus_history = $this->db->query($q);

								if ($rows_bonus_history->num_rows > 0) {
									$q = "
										SELECT * FROM `".DB_PREFIX."order` o 
										WHERE o.customer_id = '".$row_order['customer_id']."' AND o.date_added >= '" . date('Y-m-d 00:00:00',$unix_today_ago_two_week) . "' AND o.date_added <= '" . date('Y-m-d 00:00:00',$unix_today_ago_week) . "' LIMIT 1
									;";
									
									$rows_order = $this->db->query($sql);
									
									if ($rows_order->num_rows > 0) {
										$bh_amount = $ba_coin;
									}
									else {
										$bh_amount = 0;
									}

								}
							// ---

							// Add history
								if( $bh_amount != 0 ) {
									$q = "
										INSERT INTO `".DB_PREFIX."bonus_history` SET 
										`bonus_account_id` = '" . $ba_account_id . "', 
										`customer_id` = '" . $row_order['customer_id'] . "', 
										`order_id` = '" . $row_order['order_id'] . "',
										`amount` = '" . $bh_amount . "',
										`comment` = '',
										`time` = '" . time() . "'
									";
									
									if ($db->query($q) === TRUE) {
										$bonus_history_id = $db->insert_id;

									    $log[] = 'OC bonus account history ['.$bonus_history_id.'] has been inserted';
									} else {
										$log[] = 'OC bonus account history has been not inserted: '.$db->error;
									}
								}
							// ---

							// One per two week
								$q = "
									SELECT * FROM `".DB_PREFIX."bonus_history` bh 
									LEFT JOIN `".DB_PREFIX."bonus_account` ba ON ba.bonus_account_id = bh.bonus_account_id
									WHERE bh.customer_id = '".$row_order['customer_id']."' AND ba.code = 'order_monthly' AND bh.time > '" . $unix_today_ago_two_week . "' LIMIT 1
								;";
								
								$rows_bonus_history = $this->db->query($q);

								if ($rows_bonus_history->num_rows > 0) {
									$q = "
										SELECT * FROM `".DB_PREFIX."order` o 
										WHERE o.customer_id = '".$row_order['customer_id']."' AND o.date_added >= '" . date('Y-m-d 00:00:00',$unix_today_ago_four_week) . "' AND o.date_added <= '" . date('Y-m-d 00:00:00',$unix_today_ago_two_week) . "' LIMIT 1
									;";
									
									$rows_order = $this->db->query($sql);
									
									if ($rows_order->num_rows > 0) {
										$bh_amount = $ba_coin;
									}
									else {
										$bh_amount = 0;
									}
								}
							// ---

							// Add history
								if( $bh_amount != 0 ) {
									$q = "
										INSERT INTO `".DB_PREFIX."bonus_history` SET 
										`bonus_account_id` = '" . $ba_account_id . "', 
										`customer_id` = '" . $row_order['customer_id'] . "', 
										`order_id` = '" . $row_order['order_id'] . "',
										`amount` = '" . $bh_amount . "',
										`comment` = '',
										`time` = '" . time() . "'
									";
									
									if ($db->query($q) === TRUE) {
										$bonus_history_id = $db->insert_id;

									    $log[] = 'OC bonus account history ['.$bonus_history_id.'] has been inserted';
									} else {
										$log[] = 'OC bonus account history has been not inserted: '.$db->error;
									}
								}
							// ---
						}
					// ---

					// MS Reserved
					// ---
				}
			// ---

		// ---
	break;
}


/* DEBUG  */  /* file_put_contents('./crm-change-order.log', date("d.m.Y H:i", time()) . "" . json_encode($log,JSON_UNESCAPED_UNICODE)."\n\n", FILE_APPEND | LOCK_EX); */

// Response
	$res['log'] = $log;
	$res['mess']='Success';
	echo json_encode($res); exit;
// ---