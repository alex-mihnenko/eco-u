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

	$get_id = $_GET['id'];
// ---


// Request
	$url = 'https://eco-u.retailcrm.ru/api/v5/customers/'.$get_id;
	$data = array('apiKey' => RCRM_KEY, 'by' => 'id');
	$result = connectGetAPI($url, $data);

	if( !isset($result->customer) || !isset($result->customer->externalId) ) {
		// ---
			$log[] = 'No CRM customer';

			$res['log'] = $log;
			$res['mess']='Success';
			echo json_encode($res); exit;
		// ---
	}

	$customer = $result->customer;
// ---

// Proccessing
	// Check
		$q = "SELECT * FROM `".DB_PREFIX."customer` WHERE `rcrm_id`='".$get_id."' LIMIT 1;";
		$rows_customer = $db->query($q);

		if ($rows_customer->num_rows == 0) {
			$log[] = 'No OC customers';

			$res['log'] = $log;
			$res['mess']='Success';
			echo json_encode($res); exit;
		}

		$row_customer = $rows_customer->fetch_assoc();
	// ---

	// Save customer data
		$firstName = '';
		$lastName = '';
		$email = '';

		if( isset($customer->firstName) ) { $firstName = $customer->firstName;  }
		if( isset($customer->lastName) ) { $lastName = $customer->lastName;  }
		if( isset($customer->email) ) { $email = $customer->email;  }

		$q = "
			UPDATE `".DB_PREFIX."customer` SET 
			`firstname` = '".$firstName."', 
			`lastname` = '".$lastName."', 
			`email` = '".$email."' 
			WHERE `customer_id`='".$row_customer['customer_id']."'
		;";

		if ($db->query($q) === TRUE) {
		    $log[] = 'OC customer ['.$row_customer['customer_id'].'] has been updated';
		} else {
			$log[] = 'OC customer ['.$row_customer['customer_id'].'] has been not updated: '.$db->error;
		}

		// Phones
			if ( isset($customer->phones) && count($customer->phones) > 0 ) {
				// ---
					foreach ($customer->phones as $key => $phone) {
						// ---
							$q = "
								UPDATE `".DB_PREFIX."customer` SET 
								`telephone` = '".$phone->number."' 
								WHERE `customer_id`='".$row_customer['customer_id']."'
							;";

							if ($db->query($q) === TRUE) {
							    $log[] = 'OC customer ['.$row_customer['customer_id'].'] telephone has been updated';
							} else {
								$log[] = 'OC customer ['.$row_customer['customer_id'].'] telephone has been not updated: '.$db->error;
							}
						// ---
					}
				// ---
			}
		// ---

		// Personal discount
			if( isset($customer->personalDiscount) && intval($customer->personalDiscount) > 0 ) {
				// ---
					$q = "
						UPDATE `".DB_PREFIX."customer` SET 
						`discount` = '".intval($customer->personalDiscount)."' 
						WHERE `customer_id`='".$row_customer['customer_id']."'
					;";

					if ($db->query($q) === TRUE) {
					    $log[] = 'OC customer ['.$row_customer['customer_id'].'] discount has been updated';
					} else {
						$log[] = 'OC customer ['.$row_customer['customer_id'].'] discount has been not updated: '.$db->error;
					}
				// ---
			}
		// ---
	// ---

	// Update orders data
		$q = "
			UPDATE `".DB_PREFIX."order` SET 
			`firstname` = '".$firstName."', 
			`lastname` = '".$lastName."', 
			`email` = '".$email."' 
			WHERE `customer_id`='".$row_customer['customer_id']."'
		;";

		if ($db->query($q) === TRUE) {
		    $log[] = 'OC oreders ['.$row_customer['customer_id'].'] has been updated';
		} else {
			$log[] = 'OC oreders ['.$row_customer['customer_id'].'] has been not updated: '.$db->error;
		}
	// ---

	// Edit adddresses
		if( isset($customer->address) ){
			// ---
				$customer_address_array = array();
				$customer_address_text = '';

				// Check address
					if( isset($customer->address->region) ){
						$customer_address_array['region'] = $customer->address->region;
						$customer_address_text .= $customer->address->region . ', '; // Область
					}
					if( isset($customer->address->regionId) ){
						$customer_address_array['regionId'] = $customer->address->regionId;
						//$customer_address_text .= $customer->address->regionId; // Идентификатор области в geohelper
					}
					if( isset($customer->address->city) && isset($customer->address->cityType) ){
						$customer_address_array['city'] = $customer->address->city;
						$customer_address_text .= $customer->address->cityType . ' ' . $customer->address->city . ', ' ; // Город
					}
					else if( isset($customer->address->city) && !isset($customer->address->cityType) ){
						$order_address_array['city'] = $customer->address->city;
						$order_address_text .= $customer->address->city . ', '; // Город
					}
					if( isset($customer->address->cityId) ){
						$customer_address_array['cityId'] = $customer->address->cityId;
						//$customer_address_text .= $customer->address->cityId . ''; // Идентификатор города в geohelper
					}
					if( isset($customer->address->street) && isset($customer->address->streetType) ){
						$customer_address_array['street'] = $customer->address->street;
						$customer_address_text .= $customer->address->streetType . ' ' . $customer->address->street . ', '; // Улица
					}
					if( isset($customer->address->streetId) ){
						$customer_address_array['streetId'] = $customer->address->streetId;
						//$customer_address_text .= '' . $customer->address->streetId . ''; // Идентификатор улицы в geohelper
					}
					if( isset($customer->address->building) ){
						$customer_address_array['building'] = $customer->address->building;
						$customer_address_text .= 'д. ' . $customer->address->building . ', '; // Номер дома
					}
					if( isset($customer->address->flat) ){
						$customer_address_array['flat'] = $customer->address->flat;
						$customer_address_text .= 'кв./офис ' . $customer->address->flat . ', '; // Номер квартиры или офиса
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
					}

					if( isset($customer->address->text) ){
						$customer_address_text .= $customer->address->text;
					}
				// ---


				// Save
					$q = "SELECT * FROM `".DB_PREFIX."address` WHERE `customer_id`='".$row_customer['customer_id']."' AND address_1='".$customer_address_text."' AND custom_field='primary';";
					$rows_address = $db->query($q);

					if ($rows_address->num_rows == 0 ) {
						// Insert
							$q = "
								INSERT INTO `".DB_PREFIX."address` SET 
								`customer_id` = '".$row_customer['customer_id']."',
								`firstname` = '".$row_customer['firstname']."',
								`lastname` = '".$row_customer['lastname']."',
								`company` = '',
								`address_1` = '".$customer_address_text."',
								`address_2` = '".json_encode($customer_address_array,JSON_UNESCAPED_UNICODE)."',
								`city` = '',
								`postcode` = '',
								`country_id` = '0',
								`zone_id` = '0',
								`custom_field` = 'primary'
							";
							
							if ($db->query($q) === TRUE) {
							    $log[] = 'OC customer primary address ['.$row_customer['customer_id'].'] has been inserted';
							} else {
								$log[] = 'OC customer primary address ['.$row_customer['customer_id'].'] has been not inserted: '.$db->error;
							}
						// ---
					}
					else {
						$row_address = $rows_address->fetch_assoc();

						// Update
							$q = "
								UPDATE `".DB_PREFIX."address` SET 
								`firstname` = '".$row_customer['firstname']."',
								`lastname` = '".$row_customer['lastname']."',
								`company` = '',
								`address_1` = '".$customer_address_text."',
								`address_2` = '".json_encode($customer_address_array,JSON_UNESCAPED_UNICODE)."',
								`city` = '',
								`postcode` = '',
								`country_id` = '0',
								`zone_id` = '0',
								`custom_field` = 'primary' 
								WHERE `customer_id` = '".$row_customer['customer_id']."' AND address_id='".$row_address['address_id']."'
							";
							
							if ($db->query($q) === TRUE) {
							    $log[] = 'OC customer primary address ['.$row_customer['customer_id'].'] has been updated';
							} else {
								$log[] = 'OC customer primary address ['.$row_customer['customer_id'].'] has been not updated: '.$db->error;
							}
						// ---
					}
				// ---
			// ---
		}
		else {
			// ---
				$q = "DELETE FROM `".DB_PREFIX."address` WHERE `customer_id`='".$row_customer['customer_id']."' AND custom_field='primary';";

				if ($db->query($q) === TRUE) {
				    $log[] = 'OC primary address ['.$row_customer['customer_id'].'] has been deleted';
				} else {
					$log[] = 'OC primary address ['.$row_customer['customer_id'].'] has been not deleted: '.$db->error;
				}
			// ---
		}

		if( !isset($customer->customFields->addition_address_first) ){
			// ---
				$q = "DELETE FROM `".DB_PREFIX."address` WHERE `customer_id`='".$row_customer['customer_id']."' AND custom_field='addition_address_first';";

				if ($db->query($q) === TRUE) {
				    $log[] = 'OC first address ['.$row_customer['customer_id'].'] has been deleted';
				} else {
					$log[] = 'OC first address ['.$row_customer['customer_id'].'] has been not deleted: '.$db->error;
				}
			// ---
		}

		if( !isset($customer->customFields->addition_address_second) ){
			// ---
				$q = "DELETE FROM `".DB_PREFIX."address` WHERE `customer_id`='".$row_customer['customer_id']."' AND custom_field='addition_address_second';";

				if ($db->query($q) === TRUE) {
				    $log[] = 'OC second address ['.$row_customer['customer_id'].'] has been deleted';
				} else {
					$log[] = 'OC second address ['.$row_customer['customer_id'].'] has been not deleted: '.$db->error;
				}
			// ---
		}
		
		if( !isset($customer->customFields->addition_address_third) ){
			// ---
				$q = "DELETE FROM `".DB_PREFIX."address` WHERE `customer_id`='".$row_customer['customer_id']."' AND custom_field='addition_address_third';";

				if ($db->query($q) === TRUE) {
				    $log[] = 'OC third address ['.$row_customer['customer_id'].'] has been deleted';
				} else {
					$log[] = 'OC third address ['.$row_customer['customer_id'].'] has been not deleted: '.$db->error;
				}
			// ---
		}
	// ---
// ---

/* DEBUG  */  file_put_contents('./crm-change-customer.log', date("d.m.Y H:i", time()) . "" . json_encode($log,JSON_UNESCAPED_UNICODE)."\n\n", FILE_APPEND | LOCK_EX);

// Response
	$res['log'] = $log;
	$res['mess']='Success';
	echo json_encode($res); exit;
// ---