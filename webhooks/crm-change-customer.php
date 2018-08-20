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
				if( isset($customer->customFields->customer_delivery_address_type) && $customer->customFields->customer_delivery_address_type != false ){
					$customer_address = addressCrmToOc($customer->address, true);
				}
				else {
					$customer_address = addressCrmToOc($customer->address, false);
				}


				// Save
					if( !empty($customer_address['text']) || !empty($customer_address['obj']) ){
						// ---
							$q = "SELECT * FROM `".DB_PREFIX."address` WHERE `customer_id`='".$row_customer['customer_id']."' AND address_1='".$customer_address['text']."' AND custom_field='primary';";
							$rows_address = $db->query($q);

							if ($rows_address->num_rows == 0 ) {
								// Insert
									$q = "
										INSERT INTO `".DB_PREFIX."address` SET 
										`customer_id` = '".$row_customer['customer_id']."',
										`firstname` = '".$row_customer['firstname']."',
										`lastname` = '".$row_customer['lastname']."',
										`company` = '',
										`address_1` = '".$customer_address['text']."',
										`address_2` = '".json_encode($customer_address['obj'],JSON_UNESCAPED_UNICODE)."',
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
										`address_1` = '".$customer_address['text']."',
										`address_2` = '".json_encode($customer_address['obj'],JSON_UNESCAPED_UNICODE)."',
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
	// ---
// ---

/* DEBUG  */  file_put_contents('./crm-change-customer.log', date("d.m.Y H:i", time()) . "" . json_encode($log,JSON_UNESCAPED_UNICODE)."\n\n", FILE_APPEND | LOCK_EX);

// Response
	$res['log'] = $log;
	$res['mess']='Success';
	echo json_encode($res); exit;
// ---