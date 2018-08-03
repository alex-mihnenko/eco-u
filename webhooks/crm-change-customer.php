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
		$q = "SELECT * FROM `".DB_PREFIX."customer` WHERE `email`='".$customer->externalId."' LIMIT 1;";
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

	// Save adddress
		// Delete all records
			$q = "DELETE FROM `".DB_PREFIX."address` WHERE `customer_id`='".$row_customer['customer_id']."';";

			if ($db->query($q) === TRUE) {
			    $log[] = 'OC addresses ['.$row_customer['customer_id'].'] has been deleted';
			} else {
				$log[] = 'OC addresses ['.$row_customer['customer_id'].'] has been not deleted: '.$db->error;
			}
		// ---

		if( isset($customer->address) ){
			// ---
				$address = '';

				
				if( !isset($customer->address->text) ){
					// Region and City
					if( isset($customer->address->cityType) ) { $address .= $customer->address->cityType.' '; }
					else if( isset($customer->address->region) ) { $address .= $customer->address->region.', '; }
					if( isset($customer->address->city) ) { $address .= $customer->address->city.', '; }

					// Street
					if( isset($customer->address->streetType) ) { $address .= $customer->address->streetType.' '; }
					if( isset($customer->address->street) ) { $address .= $customer->address->street.', '; }

					// Add
					if( isset($customer->address->building) ) { $address .= 'д. '.$customer->address->building.', '; }
					if( isset($customer->address->flat) ) { $address .= 'кв./офис '.$customer->address->flat.', '; }
					if( isset($customer->address->block) ) { $address .= 'под. '.$customer->address->block.', '; }
					if( isset($customer->address->floor) ) { $address .= 'эт. '.$customer->address->floor.', '; }

					// Fix
					$address = mb_substr($address,0,mb_strlen($address)-2);
				}
				else {
					$address = $customer->address->text;
				}

				$q = "
					INSERT INTO `".DB_PREFIX."address` SET 
					`customer_id` = '".$row_customer['customer_id']."',
					`firstname` = '".$row_customer['firstname']."',
					`lastname` = '".$row_customer['lastname']."',
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
				    $log[] = 'OC customer address ['.$row_customer['customer_id'].'] has been inserted';
				} else {
					$log[] = 'OC customer address ['.$row_customer['customer_id'].'] has been not inserted: '.$db->error;
				}
			// ---
		}
		else {
			// ---
				if( isset($customer->customFields->addition_address_first) ){
					// ---
						$q = "
							INSERT INTO `".DB_PREFIX."address` SET 
							`customer_id` = '".$row_customer['customer_id']."',
							`firstname` = '".$row_customer['firstname']."',
							`lastname` = '".$row_customer['lastname']."',
							`company` = '',
							`address_1` = '".$customer->customFields->addition_address_first."',
							`address_2` = '',
							`city` = '',
							`postcode` = '',
							`country_id` = '0',
							`zone_id` = '0',
							`custom_field` = ''
						";
						
						if ($db->query($q) === TRUE) {
						    $log[] = 'OC customer additional address 1 ['.$row_customer['customer_id'].'] has been inserted';
						} else {
							$log[] = 'OC customer additional address 1 ['.$row_customer['customer_id'].'] has been not inserted: '.$db->error;
						}
					// ---
				}

				if( isset($customer->customFields->addition_address_second) ){
					// ---
						$q = "
							INSERT INTO `".DB_PREFIX."address` SET 
							`customer_id` = '".$row_customer['customer_id']."',
							`firstname` = '".$row_customer['firstname']."',
							`lastname` = '".$row_customer['lastname']."',
							`company` = '',
							`address_1` = '".$customer->customFields->addition_address_second."',
							`address_2` = '',
							`city` = '',
							`postcode` = '',
							`country_id` = '0',
							`zone_id` = '0',
							`custom_field` = ''
						";
						
						if ($db->query($q) === TRUE) {
						    $log[] = 'OC customer additional address 2 ['.$row_customer['customer_id'].'] has been inserted';
						} else {
							$log[] = 'OC customer additional address 2 ['.$row_customer['customer_id'].'] has been not inserted: '.$db->error;
						}
					// ---
				}

				if( isset($customer->customFields->addition_address_third) ){
					// ---
						$q = "
							INSERT INTO `".DB_PREFIX."address` SET 
							`customer_id` = '".$row_customer['customer_id']."',
							`firstname` = '".$row_customer['firstname']."',
							`lastname` = '".$row_customer['lastname']."',
							`company` = '',
							`address_1` = '".$customer->customFields->addition_address_third."',
							`address_2` = '',
							`city` = '',
							`postcode` = '',
							`country_id` = '0',
							`zone_id` = '0',
							`custom_field` = ''
						";
						
						if ($db->query($q) === TRUE) {
						    $log[] = 'OC customer additional address 3 ['.$row_customer['customer_id'].'] has been inserted';
						} else {
							$log[] = 'OC customer additional address 3 ['.$row_customer['customer_id'].'] has been not inserted: '.$db->error;
						}
					// ---
				}
			// ---
		}
	// ---
// ---


// Response
	$res['log'] = $log;
	$res['mess']='Success';
	echo json_encode($res); exit;
// ---