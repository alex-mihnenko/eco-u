<?php
// Init
	include("../_lib.php");

	header('Content-Type: text/html; charset=utf-8');

	$config = json_decode(file_get_contents('crm-to-oc-customers.json'));

	$log = [];

	if( $config->page == -1 ) { exit; }
// ---

// Request
	$url = 'https://eco-u.retailcrm.ru/api/v5/customers';
	$data = array('apiKey' => RCRM_KEY, 'limit' => 100, 'page' => (int)$config->page);
	$results = connectGetAPI($url, $data);
	
	// Update config
		if( count($results->customers) > 0 ){
			$log[] = 'Has been getted '.count($results->customers).' rows';
		}
		else {
			$config->page = -1;
			file_put_contents('crm-to-oc-customers.json',json_encode($config));

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
	$customers = $results->customers;

	foreach ($customers as $key => $customer) {
		// ---
			// Init
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

			// Save and edit
	            $q = "SELECT * FROM `".DB_PREFIX."customer` WHERE `telephone`='".$telephone."' LIMIT 1;";
				$rows_customer = $db->query($q);

				if ($rows_customer->num_rows == 0) {
					/*
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
								`rcrm_id`='".$id_internal."',
								`vegan_card`=''
							";
							
							if ($db->query($q) === TRUE) {
								$count++;
								$customer_id = $db->insert_id;
							    $log[] = 'OC customer ['.$customer_id.'] has been inserted';
							} else {
								$log[] = 'OC customer ['.$customer_id.'] has been not inserted: '.$db->error;
							}
						// ---

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
					*/
				}
				else {
					$row_customer = $rows_customer->fetch_assoc();

					$q = "
						UPDATE `".DB_PREFIX."customer` SET
						`rcrm_id` = '".$id_internal."' 
						WHERE `customer_id`=".$row_customer["customer_id"].";
					";

					if ($db->query($q) === TRUE) {
					    $log[] = 'OC customer success update '.$row_customer["customer_id"];
					} else {
					    $log[] = 'OC customer error updating record: ' . $db->error;
					}
				}
			// ---

			// Addresses
				/*
					if( isset($customer->address) && isset($customer_id) ){
						// ---
							$customer_address_array = array();
							$customer_address_text = '';

							// Check address
								if( isset($customer->address->region) ){
									$customer_address_array['region'] = $customer->address->region;
									$customer_address_text .= $customer->address->region . 'обл.'; // Область
								}
								if( isset($customer->address->regionId) ){
									$customer_address_array['regionId'] = $customer->address->regionId;
									//$customer_address_text .= $customer->address->regionId; // Идентификатор области в geohelper
								}
								if( isset($customer->address->city) && isset($customer->address->cityType) ){
									$customer_address_array['city'] = $customer->address->city;
									$customer_address_text .= $customer->address->cityType . ' ' . $customer->address->city ; // Город
								}
								if( isset($customer->address->cityId) ){
									$customer_address_array['cityId'] = $customer->address->cityId;
									//$customer_address_text .= $customer->address->cityId . ''; // Идентификатор города в geohelper
								}
								if( isset($customer->address->street) && isset($customer->address->streetType) ){
									$customer_address_array['street'] = $customer->address->street;
									$customer_address_text .= $customer->address->streetType . ' ' . $customer->address->street . ''; // Улица
								}
								if( isset($customer->address->streetId) ){
									$customer_address_array['streetId'] = $customer->address->streetId;
									//$customer_address_text .= '' . $customer->address->streetId . ''; // Идентификатор улицы в geohelper
								}
								if( isset($customer->address->building) ){
									$customer_address_array['building'] = $customer->address->building;
									$customer_address_text .= 'д. ' . $customer->address->building . ''; // Номер дома
								}
								if( isset($customer->address->flat) ){
									$customer_address_array['flat'] = $customer->address->flat;
									$customer_address_text .= 'кв./офис ' . $customer->address->flat . ''; // Номер квартиры или офиса
								}
								if( isset($customer->address->intercomCode) ){
									$customer_address_array['intercomCode'] = $customer->address->intercomCode;
									$customer_address_text .= 'код домофона ' . $customer->address->intercomCode . ', '; // Код домофона
								}
								if( isset($customer->address->floor) ){
									$customer_address_array['floor'] = $customer->address->floor;
									$customer_address_text .= 'эт. ' . $customer->address->floor . ', '; // Этаж
								}
								if( isset($customer->address->block) ){
									$customer_address_array['block'] = $customer->address->block;
									$customer_address_text .= 'под. ' . $customer->address->block . ', '; // Подъезд
								}
								if( isset($customer->address->house) ){
									$customer_address_array['house'] = $customer->address->house;
									$customer_address_text .= 'стр./корпус ' . $customer->address->house . ', '; // Строение/корпус
								}
								if( isset($customer->address->metro) ){
									$customer_address_array['metro'] = $customer->address->metro;
									$customer_address_text .= 'метро ' . $customer->address->metro . ', '; // Метро
								}

								// Fix
								$customer_address_text = mb_substr($customer_address_text,0,mb_strlen($customer_address_text)-2);


								if( isset($customer->customFields->customer_delivery_address_type) && $customer->customFields->customer_delivery_address_type != false ){
									$customer_address_array['address_type'] = $customer->customFields->customer_delivery_address_type;
									$customer_address_text .= '(Доставка в офис)';
								}

								if( isset($customer->address->text) ){
									$customer_address_text .= $customer->address->text;
								}
							// ---

							// Save
								$q = "SELECT * FROM `".DB_PREFIX."address` WHERE `customer_id`='".$customer_id."' AND address_1='".$customer_address_text."' AND custom_field='primary';";
								$rows_address = $db->query($q);

								if ($rows_address->num_rows == 0 && $customer_address_text != '' ) {
									// Insert
										$q = "
											INSERT INTO `".DB_PREFIX."address` SET 
											`customer_id` = '".$customer_id."',
											`firstname` = '".$firstName."',
											`lastname` = '".$lastName."',
											`company` = '',
											`address_1` = '".$customer_address_text."',
											`address_2` = '".json_encode($customer_address_array)."',
											`city` = '',
											`postcode` = '',
											`country_id` = '0',
											`zone_id` = '0',
											`custom_field` = 'primary'
										";
										
										if ($db->query($q) === TRUE) {
											$address_id = $db->insert_id;
										    $log[] = 'OC customer primary address ['.$customer_id.'] has been inserted';
											
											// Update customer
												$q = "
													UPDATE `".DB_PREFIX."customer` SET 
													`address_id` = '".$address_id."' 
													WHERE `customer_id`='".$customer_id."'
												;";

												if ($db->query($q) === TRUE) {
												    $log[] = 'OC customer primary address ['.$customer_id.'] has been updated';
												} else {
													$log[] = 'OC customer primary address ['.$customer_id.'] has been not updated: '.$db->error;
												}
											// ---
										} else {
											$log[] = 'OC customer primary address ['.$customer_id.'] has been not inserted: '.$db->error;
										}
									// ---

								}
							// ---
						// ---
					}

					if( isset($customer->customFields->addition_address_first) && isset($customer_id) ){
						// ---
							$q = "SELECT * FROM `".DB_PREFIX."address` WHERE `customer_id`='".$customer_id."' AND address_1='".$customer->customFields->addition_address_first."' AND custom_field='addition_address_first';";
							$rows_address = $db->query($q);

							if ($rows_address->num_rows == 0 ) {
								// Insert
									$q = "
										INSERT INTO `".DB_PREFIX."address` SET 
										`customer_id` = '".$customer_id."',
										`firstname` = '".$firstName."',
										`lastname` = '".$lastName."',
										`company` = '',
										`address_1` = '".$customer->customFields->addition_address_first."',
										`address_2` = '".json_encode(array())."',
										`city` = '',
										`postcode` = '',
										`country_id` = '0',
										`zone_id` = '0',
										`custom_field` = 'addition_address_first'
									";
									
									if ($db->query($q) === TRUE) {
									    $log[] = 'OC customer addition_address_first address ['.$customer_id.'] has been inserted';
									} else {
										$log[] = 'OC customer addition_address_first address ['.$customer_id.'] has been not inserted: '.$db->error;
									}
								// ---
							}
						// ---
					}

					if( isset($customer->customFields->addition_address_second) && isset($customer_id) ){
						// ---
							$q = "SELECT * FROM `".DB_PREFIX."address` WHERE `customer_id`='".$customer_id."' AND address_1='".$customer->customFields->addition_address_second."' AND custom_field='addition_address_second';";
							$rows_address = $db->query($q);

							if ($rows_address->num_rows == 0 ) {
								// Insert
									$q = "
										INSERT INTO `".DB_PREFIX."address` SET 
										`customer_id` = '".$customer_id."',
										`firstname` = '".$firstName."',
										`lastname` = '".$lastName."',
										`company` = '',
										`address_1` = '".$customer->customFields->addition_address_second."',
										`address_2` = '".json_encode(array())."',
										`city` = '',
										`postcode` = '',
										`country_id` = '0',
										`zone_id` = '0',
										`custom_field` = 'addition_address_second'
									";
									
									if ($db->query($q) === TRUE) {
									    $log[] = 'OC customer addition_address_second address ['.$customer_id.'] has been inserted';
									} else {
										$log[] = 'OC customer addition_address_second address ['.$customer_id.'] has been not inserted: '.$db->error;
									}
								// ---
							}
						// ---
					}
					
					if( isset($customer->customFields->addition_address_third) && isset($customer_id) ){
						// ---
							$q = "SELECT * FROM `".DB_PREFIX."address` WHERE `customer_id`='".$customer_id."' AND address_1='".$customer->customFields->addition_address_third."' AND custom_field='addition_address_third';";
							$rows_address = $db->query($q);

							if ($rows_address->num_rows == 0 ) {
								// Insert
									$q = "
										INSERT INTO `".DB_PREFIX."address` SET 
										`customer_id` = '".$customer_id."',
										`firstname` = '".$firstName."',
										`lastname` = '".$lastName."',
										`company` = '',
										`address_1` = '".$customer->customFields->addition_address_third."',
										`address_2` = '".json_encode(array())."',
										`city` = '',
										`postcode` = '',
										`country_id` = '0',
										`zone_id` = '0',
										`custom_field` = 'addition_address_third'
									";
									
									if ($db->query($q) === TRUE) {
									    $log[] = 'OC customer addition_address_third address ['.$customer_id.'] has been inserted';
									} else {
										$log[] = 'OC customer addition_address_third address ['.$customer_id.'] has been not inserted: '.$db->error;
									}
								// ---
							}
						// ---
					}
				*/
			// ---
		// ---
	}

	$log[] = 'Has been proccessed '.$count.' rows';

	// Update config
		if( $count > 0 ){
			$config->page = $config->page + 1;
			file_put_contents('crm-to-oc-customers.json',json_encode($config));
		}
		else {
			$config->page = -1;
			file_put_contents('crm-to-oc-customers.json',json_encode($config));
		}
	// ---
// ---

/* DEBUG  */  file_put_contents('./crm-to-oc-customers.log', date("d.m.Y H:i", time()) . "\n" . json_encode($log,JSON_UNESCAPED_UNICODE)."\n\n", FILE_APPEND | LOCK_EX);

// Response
	$res['log'] = $log;
	$res['mess']='Success';
	echo json_encode($res); exit;
// ---