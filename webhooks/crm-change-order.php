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
			$order_address_array = array();
			$order_address_text = '';

			if( isset($order->delivery->address) ){
				// ---
					if( isset($order->delivery->address->region) ){
						$order_address_array['region'] = $order->delivery->address->region;
						$order_address_text .= $order->delivery->address->region . 'обл.'; // Область
					}
					if( isset($order->delivery->address->regionId) ){
						$order_address_array['regionId'] = $order->delivery->address->regionId;
						//$order_address_text .= $order->delivery->address->regionId; // Идентификатор области в geohelper
					}
					if( isset($order->delivery->address->city) && isset($order->delivery->address->cityType) ){
						$order_address_array['city'] = $order->delivery->address->city;
						$order_address_text .= $order->delivery->address->cityType . ' ' . $order->delivery->address->city ; // Город
					}
					if( isset($order->delivery->address->cityId) ){
						$order_address_array['cityId'] = $order->delivery->address->cityId;
						//$order_address_text .= $order->delivery->address->cityId . ''; // Идентификатор города в geohelper
					}
					if( isset($order->delivery->address->street) && isset($order->delivery->address->streetType) ){
						$order_address_array['street'] = $order->delivery->address->street;
						$order_address_text .= $order->delivery->address->streetType . ' ' . $order->delivery->address->street . ''; // Улица
					}
					if( isset($order->delivery->address->streetId) ){
						$order_address_array['streetId'] = $order->delivery->address->streetId;
						//$order_address_text .= '' . $order->delivery->address->streetId . ''; // Идентификатор улицы в geohelper
					}
					if( isset($order->delivery->address->building) ){
						$order_address_array['building'] = $order->delivery->address->building;
						$order_address_text .= 'д. ' . $order->delivery->address->building . ''; // Номер дома
					}
					if( isset($order->delivery->address->flat) ){
						$order_address_array['flat'] = $order->delivery->address->flat;
						$order_address_text .= 'кв./офис ' . $order->delivery->address->flat . ''; // Номер квартиры или офиса
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
						$order_address_text .= '(Доставка в офис)';
					}
				// ---
			}
		// ---
			
		// Save address
			$q = "SELECT * FROM `".DB_PREFIX."address` WHERE `customer_id`='".$row_order['customer_id']."' AND address_1='".$order_address_text."';";
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

					$log[] = 'CRM customer address ['.$row_order['customer_id'].'] update: '.json_encode($result);
				// ---

				// To OC
					$q = "
						INSERT INTO `".DB_PREFIX."address` SET 
						`customer_id` = '".$row_order['customer_id']."',
						`firstname` = '".$row_order['firstname']."',
						`lastname` = '".$row_order['lastname']."',
						`company` = '',
						`address_1` = '".$order_address_text."',
						`address_2` = '".json_encode($order_address_array)."',
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