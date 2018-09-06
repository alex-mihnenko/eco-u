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

	if( !isset($result->order) || !isset($result->order->customer) ) {
		// ---
			$log[] = 'No CRM order';

			$res['log'] = $log;
			$res['mess']='Success';
			echo json_encode($res); exit;
		// ---
	}

	$order = $result->order;
	$customer = $result->order->customer;
// ---

// Proccessing
	// Check customer
		if( !isset($customer->externalId) ){
			// ---
				// Set data
					$id_internal = 0;
					$firstName = '';
					$lastName = '';
					$email = '';
					$createdAt = '';
					$telephone = '';

					if( isset($customer->id) && !empty($customer->id) ) { $id_internal = $customer->id; }
					if( isset($customer->firstName) && !empty($customer->firstName) ) { $firstName = $customer->firstName; }
					if( isset($customer->lastName) && !empty($customer->lastName) ) { $lastName = $customer->firstName; }
					if( isset($customer->email) && !empty($customer->email) ) { $email = $customer->email; }
					if( isset($customer->createdAt) && !empty($customer->createdAt) ) { $createdAt = $customer->createdAt; }

					// Get telephone
						if( isset($customer->phones) && !empty($customer->phones) ) {
							foreach ($customer->phones as $key => $phone) {
								// ---
									$telephone = preg_replace("/[^0-9,.]/", "", $phone->number);
								// ---
							}
						}
					// ---
				// ---

				// Save OC customer
					$q = "
						INSERT INTO `".DB_PREFIX."customer` SET 
						`customer_group_id`='1',
						`store_id`='0',
						`language_id`='1',
						`firstname`='".$firstName."',
						`lastname`='".$lastName."',
						`email`='".$email."',
						`telephone`='".$telephone."',
						`fax`='',
						`password`='ddd640995cf6c8f10cad5c1ab5b3a48f95c1afde',
						`salt`='kUMxyuh3a',
						`cart`='',
						`wishlist`='',
						`newsletter`='0',
						`address_id`='0',
						`custom_field`='',
						`ip`='',
						`status`='1',
						`approved`='1',
						`safe`='0',
						`token`='',
						`code`='',
						`discount`='0',
						`date_added`='".$createdAt."',
						`rcrm_id`='".$id_internal."'
					";
					
					if ($db->query($q) === TRUE) {
						$customer_id = $db->insert_id;

						// Edit CRM customer
							$url = 'https://eco-u.retailcrm.ru/api/v5/customers/'.$id_internal.'/edit';

							$customer_data = array();
							$customer_data['externalId'] = $customer_id;

							$data = array(
								'apiKey' => RCRM_KEY,
								'by' => 'id',
								'customer' => json_encode($customer_data)
							);

							$result = connectPostAPI($url, $data);

							$log[] = 'CRM customer ['.$id_internal.'] updated: '.json_encode($result);
						// ---

					    $log[] = 'OC customer ['.$customer_id.'] has been inserted';
					} else {
						$log[] = 'OC customer has been not inserted: '.$db->error;

						$res['log'] = $log;
						$res['mess']='Success';
						echo json_encode($res); exit;
					}
				// ---
			// ---
		}
		else{
			// ---
				// Set data
					$id_internal = 0;
					$firstName = '';
					$lastName = '';
					$email = '';
					$createdAt = '';
					$telephone = '';

					if( isset($customer->id) && !empty($customer->id) ) { $id_internal = $customer->id; }
					if( isset($customer->firstName) && !empty($customer->firstName) ) { $firstName = $customer->firstName; }
					if( isset($customer->lastName) && !empty($customer->lastName) ) { $lastName = $customer->firstName; }
					if( isset($customer->email) && !empty($customer->email) ) { $email = $customer->email; }
					if( isset($customer->createdAt) && !empty($customer->createdAt) ) { $createdAt = $customer->createdAt; }

					$customer_id = $customer->externalId;
				// ---
			// ---
		}
	// ---

	// Check order
		$order_subtotal = 0;
		$order_total = $order->totalSumm;

		$order_discount_percentage = 0;
		$order_discount = 0;

		// Order
			$customerComment = '';
			if( isset($order->customerComment) ) { $customerComment = $order->customerComment; }

			$totalSumm = '';
			if( isset($order->totalSumm) ) { $totalSumm = $order->totalSumm; }

			$createdAt = '';
			if( isset($order->createdAt) ) { $createdAt = $order->createdAt; }

			$q = "
				INSERT INTO `".DB_PREFIX."order` SET 
				`invoice_no` = '0',
				`invoice_prefix` = 'INV-2016-00',
				`store_id` = '0',
				`store_name` = 'Магазин органических товаров Eco-U',
				`store_url` = 'http://eco-u.ru/',
				`customer_id` = '".$customer_id."',
				`customer_group_id` = '0',
				`firstname` = '".$firstName."',
				`lastname` = '".$lastName."',
				`email` = '".$email."',
				`telephone` = '".$telephone."',
				`fax` = '',
				`custom_field` = '',
				`payment_firstname` = '".$firstName."',
				`payment_lastname` = '".$lastName."',
				`payment_company` = '',
				`payment_address_1` = '',
				`payment_address_2` = '',
				`payment_city` = '',
				`payment_postcode` = '',
				`payment_country` = '',
				`payment_country_id` = '0',
				`payment_zone` = '',
				`payment_zone_id` = '0',
				`payment_address_format` = '',
				`payment_custom_field` = '',
				`payment_method` = '',
				`payment_code` = '',
				`shipping_firstname` = '".$firstName."',
				`shipping_lastname` = '".$lastName."',
				`shipping_company`  = '',
				`shipping_address_1` = '',
				`shipping_address_2` = '',
				`shipping_city` = '',
				`shipping_postcode` = '',
				`shipping_country` = '',
				`shipping_country_id` = '0',
				`shipping_zone` = '',
				`shipping_zone_id` = '0',
				`shipping_address_format` = '',
				`shipping_custom_field` = '',
				`shipping_method` = '',
				`shipping_code` = '',
				`comment` = '".$customerComment."',
				`total` = '".$totalSumm."',
				`order_status_id` = '1',
				`affiliate_id` = '0',
				`commission` = '0.0000',
				`marketing_id` = '0',
				`tracking` = '',
				`language_id` = '1',
				`currency_id` = '1',
				`currency_code` = 'RUB',
				`currency_value` = '1.00000000',
				`ip` = '',
				`forwarded_ip` = '',
				`user_agent` = '',
				`accept_language` = '',
				`date_added` = '".$createdAt."',
				`date_modified` = '".$createdAt."',
				`delivery_time` = '',
				`rcrm_status` = 'sended'
			";
			
			if ($db->query($q) === TRUE) {
				$order_id = $db->insert_id;

			    $log[] = 'OC order ['.$order_id.'] has been inserted';

			    // Edit CRM order
					$url = 'https://eco-u.retailcrm.ru/api/v5/orders/'.$crm_order_id.'/edit';

					$order_data = array();
					$order_data['externalId'] = $order_id;

					$data = array(
						'apiKey' => RCRM_KEY,
						'by' => 'id',
						'order' => json_encode($order_data)
					);

					$result = connectPostAPI($url, $data);

					$log[] = 'CRM order ['.$crm_order_id.'] updated: '.json_encode($result);
				// ---
			} else {
				$log[] = 'OC order has been not inserted: '.$db->error;

				$res['log'] = $log;
				$res['mess']='Success';
				echo json_encode($res); exit;
			}
		// ---

		// Products
			if( isset($order->items) ){
				foreach ($order->items as $item_key => $item) {
					if( isset($item->offer->externalId) ){
						// ---
							$q = "
								SELECT * FROM `".DB_PREFIX."product` p 
								LEFT JOIN `".DB_PREFIX."product_description` pd ON pd.product_id = p.product_id 
								WHERE p.product_id='".$item->offer->externalId."'
							;";
							$rows_product = $db->query($q);

							if ($rows_product->num_rows > 0) {
								// ---
									$row_product = $rows_product->fetch_assoc();

									// Set data
										$product_total = floatval($item->quantity)*floatval($item->initialPrice);
										$order_subtotal = $order_subtotal + $product_total;

										if( isset($item->offer->discountTotal) ){
											$order_discount = $order_discount + floatval($item->offer->discountTotal);
											$order_discount_percentage = $order_discount * 100 / floatval($item->initialPrice);
										}
									// ---

									$q = "
										INSERT INTO `".DB_PREFIX."order_product` SET 
										`order_id` = '".$order_id."',
										`product_id` = '".$item->offer->externalId."',
										`name` = '".$item->offer->name."',
										`model` = '".$item->offer->name."',
										`quantity` = '".$item->quantity."',
										`amount` = '1',
										`variant` = '".$item->quantity."',
										`price` = '".floatval($item->initialPrice)."',
										`total` = '".$product_total."',
										`tax` = '0.0000',
										`reward` = '0'
									;";
									
									if ($db->query($q) === TRUE) {
										$order_product_id = $db->insert_id;

									    $log[] = 'OC order product ['.$order_product_id.'] has been inserted';
									} else {
										$log[] = 'OC order product has been not inserted: '.$db->error;

										$res['log'] = $log;
										$res['mess']='Success';
										echo json_encode($res); exit;
									}
								// ---
							}
						// ---
					}
				}
			}
		// ---
	// ---

	// Check delivery [CRM-OC delivery are not matched]
		if( isset($order->delivery) ){
			// ---`
				$delivery_code = $order->delivery->code;
				$delivery_cost = $order->delivery->cost;
				$delivery_netCost = $order->delivery->netCost;

				if ( $delivery_code == 'flat-pay' ) {
					$order_shipping_method = 'Доставка в пределах МКАД';
					$order_shipping_code = 'flat';
				}
				else if ( $delivery_code == 'flat' ) {
					$order_shipping_method = 'Бесплатная доставка';
					$order_shipping_code = 'free';
				}
				else if ( $delivery_code == 'mkad' ) {
					$order_shipping_method = 'Доставка за МКАД';
					$order_shipping_code = 'mkadout';
				}
				else if ( $delivery_code == 'shoplogistcs' ) {
					$order_shipping_method = 'Shop-Logistics';
					$order_shipping_code = 'shoplogistcs';
				}
				else if ( $delivery_code == 'self-delivery' ) {
					$order_shipping_method = 'Самовывоз';
					$order_shipping_code = 'pickup';
				}

				// Save adddress
					// Get order address
						$order_address_array = array();
						$order_address_text = '';

						if( isset($order->delivery->address) ){
							// ---
								if( isset($order->delivery->address->region) ){
									$order_address_array['region'] = $order->delivery->address->region;
									$order_address_text .= $order->delivery->address->region . ', '; // Область
								}
								if( isset($order->delivery->address->regionId) ){
									$order_address_array['regionId'] = $order->delivery->address->regionId;
									//$order_address_text .= $order->delivery->address->regionId; // Идентификатор области в geohelper
								}
								
								if( isset($order->delivery->address->city) && isset($order->delivery->address->cityType) ){
									$order_address_array['city'] = $order->delivery->address->city;
									$order_address_text .= $order->delivery->address->cityType . ' ' . $order->delivery->address->city . ', ' ; // Город
								}
								else if( isset($order->delivery->address->city) && !isset($order->delivery->address->cityType) ){
									$order_address_array['city'] = $order->delivery->address->city;
									$order_address_text .= $order->delivery->address->city . ', '; // Город
								}

								if( isset($order->delivery->address->cityId) ){
									$order_address_array['cityId'] = $order->delivery->address->cityId;
									//$order_address_text .= $order->delivery->address->cityId . ''; // Идентификатор города в geohelper
								}
								if( isset($order->delivery->address->street) && isset($order->delivery->address->streetType) ){
									$order_address_array['street'] = $order->delivery->address->street;
									$order_address_text .= $order->delivery->address->streetType . ' ' . $order->delivery->address->street . ', '; // Улица
								}
								if( isset($order->delivery->address->streetId) ){
									$order_address_array['streetId'] = $order->delivery->address->streetId;
									//$order_address_text .= '' . $order->delivery->address->streetId . ''; // Идентификатор улицы в geohelper
								}
								if( isset($order->delivery->address->building) ){
									$order_address_array['building'] = $order->delivery->address->building;
									$order_address_text .= 'д. ' . $order->delivery->address->building . ', '; // Номер дома
								}
								if( isset($order->delivery->address->flat) ){
									$order_address_array['flat'] = $order->delivery->address->flat;
									$order_address_text .= 'кв./офис ' . $order->delivery->address->flat . ', '; // Номер квартиры или офиса
								}
								if( isset($order->delivery->address->intercomCode) ){
									$order_address_array['intercomCode'] = $order->delivery->address->intercomCode;
									$order_address_text .= 'код домофона ' . $order->delivery->address->intercomCode . ', '; // Код домофона
								}
								if( isset($order->delivery->address->floor) ){
									$order_address_array['floor'] = $order->delivery->address->floor;
									$order_address_text .= 'эт. ' . $order->delivery->address->floor . ', '; // Этаж
								}
								if( isset($order->delivery->address->block) ){
									$order_address_array['block'] = $order->delivery->address->block;
									$order_address_text .= 'под. ' . $order->delivery->address->block . ', '; // Подъезд
								}
								if( isset($order->delivery->address->house) ){
									$order_address_array['house'] = $order->delivery->address->house;
									$order_address_text .= 'стр./корпус ' . $order->delivery->address->house . ', '; // Строение/корпус
								}
								if( isset($order->delivery->address->metro) ){
									$order_address_array['metro'] = $order->delivery->address->metro;
									$order_address_text .= 'метро ' . $order->delivery->address->metro . ', '; // Метро
								}

								// Fix
								$order_address_text = mb_substr($order_address_text,0,mb_strlen($order_address_text)-2);


								if( isset($order->customFields->order_delivery_address_type) && $order->customFields->order_delivery_address_type != false ){
									$order_address_array['address_type'] = $order->customFields->order_delivery_address_type;
								}
							// ---
						}
					// ---
						
					// Save address
						$q = "SELECT * FROM `".DB_PREFIX."address` WHERE `customer_id`='".$customer_id."' AND address_1='".$order_address_text."';";
						$rows_address = $db->query($q);

						if ($rows_address->num_rows == 0 && !empty($order_address_text) ) {
							$oc_address_type = 'primary';

							// To CRM
								$customerData = array();

								if( !isset($customer->address) ){
									// ---
										$customerData['address'] = $order_address_array;

										if( isset($order->customFields->order_delivery_address_type) ){
											$customerCustomFields = array();

											$customerCustomFields['customer_delivery_address_type'] = $order->customFields->order_delivery_address_type;
											
											$customerData['customFields'] = $customerCustomFields;
										}

										$oc_address_type = 'primary';
									// ---
								}
								else{
									// ---
										$customerCustomFields = array();

										if( !isset($customer->customFields->addition_address_first) && !isset($customer->customFields->addition_address_second) && !isset($customer->customFields->addition_address_third) ){
											$customerCustomFields['addition_address_first'] = $order_address_text;
											$oc_address_type = 'addition_address_first';
										}
										else if( isset($customer->customFields->addition_address_first) && !isset($customer->customFields->addition_address_second) && !isset($customer->customFields->addition_address_third) ){
											$customerCustomFields['addition_address_second'] = $order_address_text;
											$oc_address_type = 'addition_address_second';
										}
										else {
											$customerCustomFields['addition_address_third'] = $order_address_text;
											$oc_address_type = 'addition_address_third';
										}

										$customerData['customFields'] = $customerCustomFields;
									// ---
								}


								$url = 'https://eco-u.retailcrm.ru/api/v5/customers/'.$customer->id.'/edit';

								

								$data = array(
									'apiKey' => RCRM_KEY,
									'by' => 'id',
									'customer' => json_encode($customerData)
								);

								$result = connectPostAPI($url, $data);

								$log[] = 'CRM customer address ['.$customer_id.'] update: '.json_encode($result);
							// ---

							// To OC
								$q = "
									INSERT INTO `".DB_PREFIX."address` SET 
									`customer_id` = '".$customer_id."',
									`firstname` = '".$firstName."',
									`lastname` = '".$lastName."',
									`company` = '',
									`address_1` = '".$order_address_text."',
									`address_2` = '".json_encode($order_address_array,JSON_UNESCAPED_UNICODE)."',
									`city` = '',
									`postcode` = '',
									`country_id` = '0',
									`zone_id` = '0',
									`custom_field` = '".$oc_address_type."'
								";
								
								if ($db->query($q) === TRUE) {
								    $log[] = 'OC customer address ['.$customer_id.'] has been inserted';
								} else {
									$log[] = 'OC customer address ['.$customer_id.'] has been not inserted: '.$db->error;
								}
							// ---
						}
						else {
							$log[] = 'OC customer address ['.$customer_id.'] already exist';
						}
					// ---
				// ---

				// Edit order address
					$q = "
						UPDATE `".DB_PREFIX."order` SET 
						`payment_address_1` = '".$order_address_text."',
						`shipping_address_1` = '".$order_address_text."' 
						WHERE `order_id`='".$order_id."'
					;";

					if ($db->query($q) === TRUE) {
					    $log[] = 'OC order address ['.$order_id.'] has been updated';
					} else {
						$log[] = 'OC order address ['.$order_id.'] has been not updated: '.$db->error;
					}
				// ---

				// Edit order shipping
					if( isset($order_shipping_method) && isset($order_shipping_code) ){
						// ---
							$q = "
								UPDATE `".DB_PREFIX."order` SET 
								`shipping_method` = '".$order_shipping_method."',
								`shipping_code` = '".$order_shipping_code."' 
								WHERE `order_id`='".$order_id."'
							;";

							if ($db->query($q) === TRUE) {
							    $log[] = 'OC order shipping ['.$order_id.'] has been updated';
							} else {
								$log[] = 'OC order shipping ['.$order_id.'] has been not updated: '.$db->error;
							}
						// ---
					}


					if( isset($order->delivery->date) ) { $delivery_date = $order->delivery->date; }
					if( isset($order->delivery->time->from) ) { $delivery_time_from = $order->delivery->time->from; }
					if( isset($order->delivery->time->to) ) { $delivery_time_to = $order->delivery->time->to; }

					if( isset($delivery_date) && isset($delivery_time_from) && isset($delivery_time_to) ){
						// ---
							$delivery_date_arr = explode('-', $delivery_date);
							$delivery_date = $delivery_date_arr[2].'.'.$delivery_date_arr[1].'.'.$delivery_date_arr[0];

							$q = "
								UPDATE `".DB_PREFIX."order` SET 
								`delivery_time` = '".$delivery_date.' '.$delivery_time_from.'-'.$delivery_time_to."' 
								WHERE `order_id`='".$order_id."'
							;";

							if ($db->query($q) === TRUE) {
							    $log[] = 'OC order delivery_time ['.$order_id.'] has been updated';
							} else {
								$log[] = 'OC order delivery_time ['.$order_id.'] has been not updated: '.$db->error;
							}
						// ---
					}
				// ---

				// Create total
					$q = "
						INSERT INTO `".DB_PREFIX."order_total` SET 
						`order_id` = '".$order_id."',
						`code` = 'sub_total',
						`title` = 'Сумма',
						`value` = '".$order_subtotal."',
						`sort_order` = '1'
					";
					
					if ($db->query($q) === TRUE) {
					    $log[] = 'OC order sub_total ['.$order_id.'] has been inserted';
					} else {
						$log[] = 'OC order sub_total ['.$order_id.'] has been not inserted: '.$db->error;
					}

					$q = "
						INSERT INTO `".DB_PREFIX."order_total` SET 
						`order_id` = '".$order_id."',
						`code` = 'total',
						`title` = 'Итого',
						`value` = '".$order_total."',
						`sort_order` = '10'
					";
					
					if ($db->query($q) === TRUE) {
					    $log[] = 'OC order total ['.$order_id.'] has been inserted';
					} else {
						$log[] = 'OC order total ['.$order_id.'] has been not inserted: '.$db->error;
					}

					$q = "
						INSERT INTO `".DB_PREFIX."order_total` SET 
						`order_id` = '".$order_id."',
						`code` = 'discount_percentage',
						`title` = 'Процент скидки',
						`value` = '".$order_discount_percentage."',
						`sort_order` = '2'
					";
					
					if ($db->query($q) === TRUE) {
					    $log[] = 'OC order discount_percentage ['.$order_id.'] has been inserted';
					} else {
						$log[] = 'OC order discount_percentage ['.$order_id.'] has been not inserted: '.$db->error;
					}

					$q = "
						INSERT INTO `".DB_PREFIX."order_total` SET 
						`order_id` = '".$order_id."',
						`code` = 'discount',
						`title` = 'Скидка',
						`value` = '".$order_discount."',
						`sort_order` = '2'
					";
					
					if ($db->query($q) === TRUE) {
					    $log[] = 'OC order discount ['.$order_id.'] has been inserted';
					} else {
						$log[] = 'OC order discount ['.$order_id.'] has been not inserted: '.$db->error;
					}

					$q = "
						INSERT INTO `".DB_PREFIX."order_total` SET 
						`order_id` = '".$order_id."',
						`code` = 'shipping',
						`title` = '".$order_shipping_method."',
						`value` = '".$delivery_cost."',
						`sort_order` = '2'
					";
					
					if ($db->query($q) === TRUE) {
					    $log[] = 'OC order shipping ['.$order_id.'] has been inserted';
					} else {
						$log[] = 'OC order shipping ['.$order_id.'] has been not inserted: '.$db->error;
					}
				// ---
			// ---
		}
	// ---

	// Check payment [CRM-OC payments are not matched]
		if( !isset($order->payments) ){
			foreach ($order->payments as $key_payment => $payment) {
				// ---
					$payment_id = 0;
					$payment_status = '';
					$payment_type = '';
					$payment_externalId = '';
					$payment_amount = 0;
					$payment_paidAt = '0000-00-00 00:00:00';

					if( isset($payment->id) ) { $payment_id = $payment->id; }
					if( isset($payment->status) ) { $payment_status = $payment->status; }
                    if( isset($payment->type) ) { $payment_type = $payment->type; }
                    if( isset($payment->externalId) ) { $payment_externalId = $payment->externalId; }
                    if( isset($payment->amount) ) { $payment_amount = $payment->amount; }
                    if( isset($payment->paidAt) ) { $payment_paidAt = $payment->paidAt; }

                    // Check type
	                    if( $payment_type = 'e-money' ) { $payment_code = 'rbs'; $payment_method = 'Банковской картой на сайте'; }
	                    else if( $payment_type = 'cash' ) { $payment_code = 'cod'; $payment_method = 'Наличными курьеру'; }
	                    else if( $payment_type = 'bank-transfer' ) { $payment_code = 'bank_transfer'; $payment_method = 'Банковский перевод'; }
						else {
							$log[] = 'CRM payment type are not correct';
						}
					// ---

					// Check status
						if( $payment_status = 'not-paid' ) { $payment_status_id = 16; } // Не оплачен
	                    else if( $payment_status = 'paid' ) { $payment_status_id = 20; } // Оплачен
	                    else if( $payment_status = 'fail' ) { $payment_status_id = 21; } // Ошибка
						else {
							$log[] = 'CRM payments status are not correct';
						}
					// ---

					// Add payment
						if( isset($payment_method) && isset($payment_code) && isset($payment_status_id) ){
							// ---
								$q = "
									INSERT INTO `".DB_PREFIX."order_payments` SET 
									`id_paymant_uniq`='',
									`order_id`='".$order_id."',
									`order_status_id`='".$payment_status_id."',
									`total`='".$payment_amount."',
									`payment_method`='".$payment_method."',
									`payment_code`='".$payment_code."',
									`date_add`='".$payment_paidAt."',
									`processed`='1'
								";
								
								if ($db->query($q) === TRUE) {
								    $log[] = 'OC order ['.$order_id.'] payment has been inserted';
								} else {
									$log[] = 'OC order ['.$order_id.'] payment has been not inserted: '.$db->error;

									$res['log'] = $log;
									$res['mess']='Success';
									echo json_encode($res); exit;
								}
							// ---
						}
					// ---

					// Edit order payment
						if( isset($payment_code) && isset($payment_method) ){
							// ---
								$q = "
									UPDATE `".DB_PREFIX."order` SET 
									`payment_method` = '".$payment_method."',
									`payment_code` = '".$payment_code."' 
									WHERE `order_id`='".$order_id."'
								;";

								if ($db->query($q) === TRUE) {
								    $log[] = 'OC order payment ['.$order_id.'] has been updated';
								} else {
									$log[] = 'OC order payment ['.$order_id.'] has been not updated: '.$db->error;
								}
							// ---
						}
					// ---
				// ---
			}
		}
	// ---
// ---


/* DEBUG  */  file_put_contents('./crm-create-order.log', date("d.m.Y H:i", time()) . "" . json_encode($log,JSON_UNESCAPED_UNICODE)."\n\n", FILE_APPEND | LOCK_EX);

// Response
	$res['log'] = $log;
	$res['mess']='Success';
	echo json_encode($res); exit;
// ---