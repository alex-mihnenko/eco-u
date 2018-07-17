<?php
// Init
	include("_lib.php");

	$log = [];


	// Get managers
		$managers = [];

		$url = 'https://eco-u.retailcrm.ru/api/v5/users';
		$qdata = array('apiKey' => RCRM_KEY,'limit' => 100);

		$response = connectGetAPI($url,$qdata);

		foreach ($response->users as $key => $user) {
			if( $user->isManager == 1 ){
				$managers[] = $user->id;
			}
		}
	// ---


	// Get free shipping
		$free_shipping_total_value = 1000000;

		$q = "SELECT * FROM `oc_setting` WHERE `code`='free';";
		$rows_setting = $db->query($q);

		if ($rows_setting->num_rows > 0) {
			// ---
				while ( $row_setting = $rows_setting->fetch_assoc() ) {
					if( $row_setting['key'] == 'free_total' ) { $free_shipping_total_value = $row_setting['value']; }
				}
			// ---
		}
	// ---
// ---


// Get orders
	$q = "
		SELECT 
		payment_method,
		customer_id,
		order_id,
		customer_id,
		firstname,
		lastname,
		email,
		telephone,
		comment,
		total, 
		order_status_id,
		date_added,
		payment_code,
		shipping_address_1,
		shipping_method,
		shipping_code,
		delivery_time

		FROM ".DB_PREFIX."order o 

		WHERE o.rcrm_status='' AND o.order_status_id>0 ORDER BY o.date_modified DESC LIMIT 50;
    ";

	$rows_order = $db->query($q);

	if ($rows_order->num_rows == 0) {
		// ---
			$log[] = 'No orders';

			$res['log'] = $log;
			$res['mess']='Success';
			echo json_encode($res); exit;
		// ---
	}
// ---


// Go-round orders
	while ( $row_order = $rows_order->fetch_assoc() ) {
		$order = [];
		$log[] = '#Start [IM'.$row_order['order_id'].'] from '.$row_order['date_added'];


		// Check customer
			$customer = [];

			$customer['externalId'] = $row_order['email'];
			$customer['email'] = $row_order['email'];
			$customer['firstName'] = $row_order['firstname'];
			$customer['lastName'] = $row_order['lastname'];
			$customer['phone'] = $row_order['telephone'];

			if( $row_order['email'] == '' && $row_order['telephone'] != '' ){
				// ---
					$row_order['email'] = $row_order['telephone'].'@eco-u.ru';

					$q = "UPDATE `".DB_PREFIX."customer` SET `email` = '".$row_order['email']."' WHERE `customer_id`='".$row_order['customer_id']."';";

					if ($db->query($q) === TRUE) {
					    $log[] = 'OC customer ['.$row_order['customer_id'].'] has been updated';
					} else {
						$log[] = 'OC customer ['.$row_order['customer_id'].'] has been not updated: '.$db->error;
					}
				// ---
			}


			$q = "SELECT * FROM `retailCRM_customers` WHERE `email`='".$row_order['email']."' LIMIT 1;";
			$rows_customer = $db->query($q);


			if ($rows_customer->num_rows == 0) {
				// Create CRM customer
					$url='https://eco-u.retailcrm.ru/api/v5/customers/create?apiKey='.RCRM_KEY;
					$data['customer'] = json_encode($customer);

					$response=connectPostAPI($url,$data);
					$customer['id'] = $response['id'];

					$q = "
						INSERT INTO `retailCRM_customers` SET 
						`id_internal`='".intval($customer_internal_id)."',
						`id_external`='".$row_order['email']."',
						`firstname`='".$row_order['firstname']."',
						`email`='".$row_order['email']."',
						`dublicates`=0,
						`created`=NOW()
					";
					
					if ($db->query($q) === TRUE) {
						$id_customer = $db->insert_id;
					    $log[] = 'Customer '.$id_customer.' has been created';
					} else {
						$log[] = 'Customer has been not inserted: '.$db->error;
					}
				// ---
			}
			else{
				$row_customer = $rows_customer->fetch_assoc();
				$customer['id'] = $row_customer['id_internal'];

				$log[] = 'Customer already exist';
			}

			$log[] = 'Customer ['.$row_order['customer_id'].'] '.$row_order['firstname'].'. Internal id ['.$customer['id'].']';
		// ---

		// Set general data
			$order['number'] = 'IM'.$row_order['order_id'];
			$order['externalId'] = $row_order['order_id'];
			$order['createdAt'] = $row_order['date_added'];

			$order['discountManualAmount'] = 0; // Will be changed -> Set discounts
			$order['discountManualPercent'] = 0; // Will be changed -> Set discounts
			$order['managerComment'] = '';

			// Set discounts
				$q = "SELECT * FROM `".DB_PREFIX."order_total` WHERE `order_id`='".$row_order['order_id']."' AND (`code` LIKE '%discount%' OR `code` LIKE '%coupon%');";
				$rows_discounts = $db->query($q);


				if ($rows_discounts->num_rows > 0) {
					while ( $row_discounts = $rows_discounts->fetch_assoc() ) {
						if( $row_discounts['code'] == 'discount' ) {
							$order_discount_manual_amount = floatval($row_discounts['value']);
						}
						else if( $row_discounts['code'] == 'coupon' ) {
							$order_discount_manual_amount = floatval($row_discounts['value']);
							$order['managerComment'] = 'Скидка '.$row_discounts['value'].' по купону';
						}
						else if(
							$row_discounts['code'] == 'discount_percentage' ) { $order_discount_manual_percent = floatval($row_discounts['value']);
						}
					}

					if ( isset($order_discount_manual_amount) && !isset($order_discount_manual_percent) ) { $order['discountManualAmount'] = $order_discount_manual_amount; }
					else if ( !isset($order_discount_manual_amount) && isset($order_discount_manual_percent) ) { $order['discountManualPercent'] = $order_discount_manual_percent; }
					else if ( isset($order_discount_manual_amount) && isset($order_discount_manual_percent) ) { $order['discountManualPercent'] = $order_discount_manual_percent; }
				}
			// ---

			$order['lastName'] = $row_order['lastname'];
			$order['firstName'] = $row_order['firstname'];
			$order['phone'] = $row_order['telephone'];
			$order['email'] = $row_order['email'];
			$order['customerComment'] = $row_order['comment'];

			$order['weight'] = 0; // Will be changed -> Set items

			$order['orderMethod'] = "shopping-cart";

			$order['customer'] = $customer;

			$order['status'] = 'new';
		// ---

		// Set items
			$items = [];
			$items_count = 0;
			$items_weight = 0;

			$q = "
				SELECT 
					op.product_id, op.name, op.quantity, op.amount, op.variant, op.price, op.total,  
					p.date_added, p.weight, p.weight_class_id
				FROM `".DB_PREFIX."order_product` op
				LEFT JOIN `".DB_PREFIX."product` p ON p.product_id = op.product_id 
				WHERE op.order_id='".$row_order['order_id']."';
			";
			$rows_product = $db->query($q);

			
			if ($rows_product->num_rows > 0) {
				while ( $row_product = $rows_product->fetch_assoc() ) {
					// ---
						// Weight processing
							if( $row_product['weight_class_id'] == 2 ){ // Gramm
								$item_weight = $row_product['weight'] * $row_product['quantity'];
							}
							else if( $row_product['weight_class_id'] == 9 ){ // Kilogramm
								$item_weight = $row_product['weight'] * 1000 * $row_product['quantity'];
							}
							else if( $row_product['weight_class_id'] == 1 ){ // Piece
								$item_weight = $row_product['weight'] * $row_product['quantity'];
							}
							else{ 
								$item_weight = 0;
							}

							$items_weight = $items_weight + $item_weight;
						// ---

						// Price
							$item_price = floatval($row_product['total']) * ( 1/floatval($row_product['quantity']) );
						// ---

						// Properties
							$item_properties = array();

							if( $row_product['weight_class_id'] == 2 || $row_product['weight_class_id'] == 9 ){ // Gramm OR Kilogramm
								$item_properties[] = array('name' => 'Фасовка', 'value' => $row_product['variant'].' кг. X '.$row_product['amount']);
							}
							else if( $row_product['weight_class_id'] == 1 ){ // Piece
								$item_properties[] = array('name' => 'Фасовка', 'value' => $row_product['amount'].' шт.');
							}
						// ---

						$items[] = array(
							'initialPrice'=> $item_price,
							'createdAt'=>$row_product['date_added'],
							'quantity'=>floatval($row_product['quantity']),
							'properties'=>$item_properties,
							'offer' => array(
								'externalId'=>$row_product['product_id']
							),
							'productName'=>$row_product['name'],
						);

						$items_count++;
					// ---
				}
			}

			$order['items'] = $items;
			$order['weight'] = $items_weight;

			$log[] = 'Has been added '.$items_count.' items';
			$log[] = 'Items weight '.$items_weight.' gramms';
			$log[] = 'Items total '.$row_order['total'];
		// ---

		// Set delivery
			// Code
				$delivery_code = '';

				if($row_order['shipping_code'] == "mkadout") {
					$delivery_code="mkad";
				}
				else if($row_order['shipping_code'] == "free") {
					$delivery_code="flat";
				}
				else if($row_order['shipping_code'] == "flat") {
					$delivery_code="flat-pay";
				}
			// ---

			// Cost
				$delivery_cost = 0;

				$q = "SELECT * FROM ".DB_PREFIX."order_total WHERE order_id='".$row_order['order_id']."' AND code = 'shipping' LIMIT 1;";
				$rows_total = $db->query($q);


				if ($rows_total->num_rows > 0) {
					$row_total = $rows_total->fetch_assoc();
					$delivery_cost = $row_total['value'];
				}
			// ---

			// Netcost
				$delivery_netcost = 0;

				$q = "SELECT * FROM ".DB_PREFIX."setting WHERE code='".$row_order['shipping_code']."';";
				$rows_setting = $db->query($q);


				if ($rows_setting->num_rows > 0) {
					$this_cost = 0;
					$this_netcost = 0;

					while ( $row_setting = $rows_setting->fetch_assoc() ) {
						// ---
							if( $row_setting['key'] == $row_order['shipping_code'].'_cost' ) { $this_cost = $row_setting['value']; }
							if( $row_setting['key'] == $row_order['shipping_code'].'_netcost' ) { $this_netcost = $row_setting['value']; }
						// ---
					}

					// Ceck netcost config
						if( $this_netcost != 0 ) {
							$netcost_value = 0;
							$weight_value = $items_weight / 1000;

							$netcost_config_list = json_decode( html_entity_decode($this_netcost, ENT_QUOTES, 'UTF-8') );


							foreach($netcost_config_list as $key => $item) {
								if( $weight_value > intval($item->from) && $weight_value <= intval($item->to) ) {
									$netcost_value = intval($item->cost);
									break;
								}
							}

							$this_netcost = $netcost_value;
						}
					// ---

					// Check methods
						if($row_order['shipping_code'] == "flat") {
							$delivery_netcost = $this_netcost;
						}
						else{
							// ---
								if( $row_order['total'] >= $free_shipping_total_value ){
									// Free shipping
										$delivery_netcost = $this_netcost + $delivery_cost;
									// ---
								}
								else{
									// Pay shipping
										$delivery_netcost = $this_netcost + ($delivery_cost - $this_cost);
									// ---
								}
							// ---
						}
					// ---
				}
			// ---

			// Date and Time 
				$delivery_date = '';

				$delivery_time_tmp = explode(' ', $row_order['delivery_time']);

				$delivery_date_tmp = explode('.', $delivery_time_tmp[0]);
				$delivery_time_tmp = explode('-', $delivery_time_tmp[1]);


				$delivery_date = $delivery_date_tmp[2].'-'.$delivery_date_tmp[1].'-'.$delivery_date_tmp[0];
				$delivery_time_form = $delivery_time_tmp[0];
				$delivery_time_to = $delivery_time_tmp[1];
				$delivery_time_custom = $delivery_time_tmp[0].'-'.$delivery_time_tmp[1];
			// ---

			$delivery = array(
				'code' => $delivery_code,
				'cost' => $delivery_cost,
				'netCost' => $delivery_netcost,
				'date' => $delivery_date,
				'time' => array('from' => $delivery_time_form, 'to' => $delivery_time_to, 'custom' => $delivery_time_custom),
				'address' => array('text' => $row_order['shipping_address_1']),
				'shipmentStore' => 'eco-u'
			);

			$order['delivery'] = $delivery;
		// ---

		// Set payments
			// Amount
				$amount = $row_order['total'];
			// ---

			// Type
				if( $row_order['payment_code'] == 'rbs' ) { $type = 'e-money'; }
				else { $type = 'cash'; }
			// ---

			$payments = array();

			if( $type == 'e-money' && $row_order['payment_code'] == 20 ) {
				$payments[] = array('externalId' => $row_order['order_id'], 'amount' => $amount, 'paidAt' => $row_order['date_added'], 'type' => $type, 'status' => 'paid');
			}
			else {
				$payments[] = array('externalId' => $row_order['order_id'], 'amount' => $amount, 'paidAt' => $row_order['date_added'], 'type' => $type, 'status' => 'not-paid');
			}

			$order['payments'] = $payments;
		// ---

		// Set custom
			$q = "SELECT * FROM `".DB_PREFIX."order_roistat` WHERE order_id='".$row_order['order_id']."' LIMIT 1;";
			$rows_roistat = $db->query($q);

			if ($rows_roistat->num_rows > 0) {
				$row_roistat = $rows_roistat->fetch_assoc();
				$order['customFields']['roistat'] = $row_roistat['order_roistat_visit_id'];
			}
		// ---


		// Send CRM order and OC update
			$url='https://eco-u.retailcrm.ru/api/v5/orders/create?apiKey='.RCRM_KEY;
			$data = array(
				'site' => 'eco-u-ru',
				'order' => json_encode($order)
			);

			$response=connectPostAPI($url,$data);

			if( isset($response->success) && $response->success!= false ){
				$log[] = '#SUCCESS order ['.$response->id.'] has been created';
			}
			else{
				$log[] = '#ERROR message: '.$response->errorMsg;
				if( isset($response->errors) ) { $log[] = '#ERROR details: '.json_encode($response->errors); }
			}


			$q = "UPDATE `".DB_PREFIX."order` SET `rcrm_status` = 'sended' WHERE `order_id`='".$row_order['order_id']."';";

			if ($db->query($q) === TRUE) {
			    $log[] = 'OC order ['.$row_order['order_id'].'] has been updated';
			} else {
				$log[] = 'OC order ['.$row_order['order_id'].'] has been not updated: '.$db->error;
			}

			$log[] = '#END [IM'.$row_order['order_id'].'] - complete';
		// ---
	}
// ---


// Response
	$res['log'] = $log;
	$res['mess']='Success';
	echo json_encode($res); exit;
// ---