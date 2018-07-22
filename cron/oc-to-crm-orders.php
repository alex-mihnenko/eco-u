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

		WHERE o.rcrm_status='' AND o.order_id>0 AND o.order_status_id>0 ORDER BY o.date_modified DESC LIMIT 50;
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

			//$customer['externalId'] = $row_order['email'];
			$customer['email'] = $row_order['email'];
			$customer['firstName'] = $row_order['firstname'];
			$customer['lastName'] = $row_order['lastname'];
			$customer['phone'] = $row_order['telephone'];



			$q = "SELECT * FROM `retailCRM_customers` WHERE `email`='".$row_order['email']."' LIMIT 1;";
			$rows_customer = $db->query($q);


			if ($rows_customer->num_rows == 0) {
				// Create CRM customer
					$url='https://eco-u.retailcrm.ru/api/v5/customers/create?apiKey='.RCRM_KEY;
					$data['customer'] = json_encode($customer);

					$response=connectPostAPI($url,$data);

					if( isset($response->success) && $response->success!= false && isset($response->id) ){
						// ---
							$customer['id'] = $response->id;

							$q = "
								INSERT INTO `retailCRM_customers` SET 
								`id_internal`='".intval($customer['id'])."',
								`id_external`='".$row_order['email']."',
								`firstname`='".$row_order['firstname']."',
								`email`='".$row_order['email']."',
								`dublicates`=0,
								`created`=NOW()
							";
							
							if ($db->query($q) === TRUE) {
								$id_customer = $db->insert_id;
							    $log[] = 'OC/CRM Customer '.$id_customer.' has been created';
							} else {
								$log[] = 'OC/CRM Customer has been not inserted: '.$db->error;
							}
						// ---
					}
					else{
						$log[] = 'OC/CRM Customer has been not created: '.json_encode($response);
					}
					
				// ---
			}
			else{
				$row_customer = $rows_customer->fetch_assoc();
				$customer['id'] = $row_customer['id_internal'];

				$log[] = 'Customer already exist';
			}

			$log[] = 'Customer ['.$row_order['customer_id'].']. Internal id ['.$customer['id'].']';
		// ---

		// Set general data
			$order['number'] = 'IM'.$row_order['order_id'];
			$order['externalId'] = $row_order['order_id'];
			$order['createdAt'] = $row_order['date_added'];

			$order['discountManualAmount'] = 0;
			$order['discountManualPercent'] = 0;
			$order['managerComment'] = '';

			// Set discounts
				$order_discount_manual_amount = 0;
				$order_discount_manual_percent = 0;

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
			$count = 0;
			$weight = 0;

			$totaldiscount = 0;
			$totalproducts = 0;
			$total = 0;

			$q = "
				SELECT 
					op.product_id, op.name, op.quantity as quantity, op.amount as amount, op.variant as variant, op.price as price, op.total as total,  
					p.date_added, p.weight, p.weight_class_id, p.weight_variants
				FROM `".DB_PREFIX."order_product` op
				LEFT JOIN `".DB_PREFIX."product` p ON p.product_id = op.product_id 
				WHERE op.order_id='".$row_order['order_id']."';
			";
			$rows_product = $db->query($q);

			
			if ($rows_product->num_rows > 0) {
				// Create temp products array
					$tmp_products = array();

					while ( $row_product = $rows_product->fetch_assoc() ) {
						// ---
						    if( !isset($tmp_products[$row_product['product_id']])){
						      // ---
						        $tmp_products[$row_product['product_id']] = array(
						          'name' => $row_product['name'],
						          'weight_class_id' => $row_product['weight_class_id'],
						          'weight_variants' => $row_product['weight_variants'],
						          'weight_variant' => $row_product['variant'],
						          'weight' => $row_product['weight'],
						          'packing' => array()
						        );

						        $tmp_products[$row_product['product_id']]['packing'][] = array(
						          'total' => $row_product['total'],
						          'price' => $row_product['price'],
						          'quantity' => $row_product['quantity'],
						          'amount' => $row_product['amount']
						        );
						      // ---
						    }
						    else{
						      // ---
						        $tmp_products[$row_product['product_id']]['packing'][] = array(
						          'total' => $row_product['total'],
						          'price' => $row_product['price'],
						          'quantity' => $row_product['quantity'],
						          'amount' => $row_product['amount']
						        );
						      // ---
						    }
						// ---
					}
				// ---

				// Create fixed products array
					$fix_products = array();

					foreach ($tmp_products as $product_id => $product) {
						// ---
						    // Calculates
						      $product_total = 0;
						      $product_price = 0;
						      $product_quantity = 0;
						      $product_amount = 0;
						      $product_discount_price = 0;
						      $product_discount_total = 0;
						      
						      foreach ($product['packing'] as $key_pack => $pack) {
						        $product_quantity = $product_quantity + $pack['quantity'];
						        $product_amount = $product_amount + $pack['amount'];
						        $product_total = $product_total + $pack['total'];
						        $product_price = $product_price + $pack['price'];
						      }
						      
						      // Set price
						        if( $product['weight_class_id'] == 1 ){ // Piece
						          $product_price = $product_total / $product_amount;
						        }
						        else{
						          $product_price = $product_total / $product_quantity;
						        }
						      // ---

						      // Set discount
						        if( isset($order_discount_manual_percent) ){
						          if( $product['weight_class_id'] == 1 ){ // Piece
						            $product_discount_price = ($product_total / $product_amount) * ($order_discount_manual_percent/100);
						            $product_discount_total = $product_discount_price * $product_amount;
						          }
						          else{
						            $product_discount_price = ($product_total / $product_quantity) * ($order_discount_manual_percent/100);
						            $product_discount_total = $product_discount_price * $product_quantity;
						          }
						        }
						      // ---
						    // ---

						    // Total
						        // Weight
						          if( $product['weight_class_id'] == 2 || $product['weight_class_id'] == 9 ){ // Gramms OR Kilogramms
						    		$weight = $weight + ($product_quantity * 1000);
						          }
						          else if( $product['weight_class_id'] == 1 ){ // Piece
						          	if( $product['weight'] == 0 ){ $weight = $weight + 1000; }
						    		else{ $weight = $weight + ($product_quantity * $product['weight']); }
						          }
						        // ---

						    	$totalproducts = $totalproducts + $product_total;
						    	$totaldiscount = $totaldiscount + $product_discount_total;
						    // ---

						    $fix_products[] = array(
						      'product_id' => $product_id,
						      'name' => $product['name'],
						      'price' => $product_price,
						      'total' => $product_total,
						      'quantity' => $product_quantity,
						      'amount' => $product_amount,
						      'discount_price' => $product_discount_price,
						      'discount_total' => $product_discount_total,
						      'packing' => $product['packing'],
						      'weight_class_id' => $product['weight_class_id'],
						      'weight_variants' => $product['weight_variants'],
						      'variant' => $product['weight_variant'],
						      'weight' => $product['weight'],
						    );
					  	// ---
					}
				// ---

				// Add items
					foreach ($fix_products as $key => $product) {
						// Properties
							$properties = array();
							$properties_count = 1;

							foreach ($product['packing'] as $key_pack => $pack) {
								// ---
									if( $product['weight_class_id'] == 2 || $product['weight_class_id'] == 9 ){ // Gramm OR Kilogramm
										$properties[] = array('name' => 'Фасовка '.$properties_count, 'value' => $pack['variant'].' кг. X '.$pack['amount']);
									}
									else if( $product['weight_class_id'] == 1 ){ // Piece
										$properties[] = array('name' => 'Фасовка '.$properties_count, 'value' => $pack['amount'].' шт.');
									}

									$properties_count++;
								// ---
						    }
						// ---

						$items[] = array(
							'initialPrice'=> $product['price'],
							'discountManualAmount'=> 0,
							'discountManualPercent'=> $order_discount_manual_percent,
							'quantity'=>$product['quantity'],
							'properties'=>$properties,
							'offer' => array(
								'externalId'=>$product['product_id']
							),
							'productName'=>$product['name'],
						);

						$count++;
					}
				// ---
			}

			$order['items'] = $items;
			$log[] = 'Items:'.json_encode($items);

			$order['weight'] = $weight;

			$totaldiscount = round($totaldiscount,2);
			$totalproducts = round($totalproducts,2);
			
			$log[] = 'Has been added: '.$count.' items';
			$log[] = 'Items weight: '.$weight.' gramms';
			$log[] = 'Items total: '.$totalproducts;
			$log[] = 'Items discount: '.$totaldiscount;
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
							$weight_value = $weight / 1000;

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

				if( $row_order['delivery_time'] ){
					$delivery_time_tmp = explode(' ', $row_order['delivery_time']);

					$delivery_date_tmp = explode('.', $delivery_time_tmp[0]);
					$delivery_time_tmp = explode('-', $delivery_time_tmp[1]);

					$delivery_date = $delivery_date_tmp[2].'-'.$delivery_date_tmp[1].'-'.$delivery_date_tmp[0];
					$delivery_time_form = $delivery_time_tmp[0];
					$delivery_time_to = $delivery_time_tmp[1];
					$delivery_time_custom = $delivery_time_tmp[0].'-'.$delivery_time_tmp[1];
				} 
			// ---

			$delivery = array(
				'code' => $delivery_code,
				'cost' => $delivery_cost,
				'netCost' => $delivery_netcost,
				'address' => array('text' => $row_order['shipping_address_1']),
				'shipmentStore' => 'eco-u'
			);

			if( $row_order['delivery_time'] ){
				$delivery['date'] = $delivery_date;
				$delivery['time'] = array('from' => $delivery_time_form, 'to' => $delivery_time_to, 'custom' => $delivery_time_custom);
			}

			$order['delivery'] = $delivery;


			$total = round($totalproducts - $totaldiscount,2) + $delivery_cost;
			$log[] = 'Total: '.$total;
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
				// ---
					$log[] = '#SUCCESS order ['.$response->id.'] has been created';
					
					$q = "UPDATE `".DB_PREFIX."order` SET `rcrm_status` = 'sended' WHERE `order_id`='".$row_order['order_id']."';";

					if ($db->query($q) === TRUE) {
					    $log[] = 'OC order ['.$row_order['order_id'].'] has been updated';
					} else {
						$log[] = 'OC order ['.$row_order['order_id'].'] has been not updated: '.$db->error;
					}

					// Create payment
						// Amount
							$amount = floatval($row_order['total']);
						// ---

						// Type
							if( $row_order['payment_code'] == 'rbs' ) { $type = 'e-money'; }
							else { $type = 'cash'; }
						// ---

						$payment = array();


						if( $type == 'e-money' && $row_order['payment_code'] == 20 ) {
							$payment = array(
								'externalId' => $row_order['order_id'],
								'amount' => $amount,
								'paidAt' => $row_order['date_added'],
								'order' => array(
									'id' => $response->id,
									'externalId' => $row_order['order_id'],
									'number' => 'IM'.$row_order['order_id']
								),
								'type' => $type,
								'status' => 'paid'
							);
						}
						else {
							$payment = array(
								'externalId' => $row_order['order_id'],
								'amount' => $amount,
								'paidAt' => $row_order['date_added'],
								'order' => array(
									'id' => $response->id,
									'externalId' => $row_order['order_id'],
									'number' => 'IM'.$row_order['order_id']
								),
								'type' => $type,
								'status' => 'not-paid'
							);
						}


						$url='https://eco-u.retailcrm.ru/api/v5/orders/payments/create?apiKey='.RCRM_KEY;
						$data = array(
							'site' => 'eco-u-ru',
							'payment' => json_encode($payment)
						);

						$response=connectPostAPI($url,$data);

						$log[] = 'Payment status '.json_encode($response);
						$log[] = 'Items payment '.$amount;
					// ---
				// ---
			}
			else{
				if( $response->errorMsg == 'Order already exists.' ){
					// ---
						$q = "UPDATE `".DB_PREFIX."order` SET `rcrm_status` = 'sended' WHERE `order_id`='".$row_order['order_id']."';";

						if ($db->query($q) === TRUE) {
						    $log[] = 'OC order ['.$row_order['order_id'].'] has been updated';
						} else {
							$log[] = 'OC order ['.$row_order['order_id'].'] has been not updated: '.$db->error;
						}
					// ---
				}
				else{
					// ---
						// Add log
							$q = "SELECT * FROM `retailCRM_errors` WHERE `id_order`='".$row_order['order_id']."';";
							$rows_log = $db->query($q);

							
							if ($rows_log->num_rows == 0) {
								// ---
									$q = "INSERT INTO `retailCRM_errors` SET `id_order`='".$row_order['order_id']."', `id_externalid`='IM".$row_order['order_id']."', `message`='".$response->errorMsg."';";

									if ($db->query($q) === TRUE) {
									    $log[] = 'OC error log has been inserted';
									} else {
										$log[] = 'OC error log has been not inserted: '.$db->error;
									}


									// Set task to managers
										$url = 'https://eco-u.retailcrm.ru/api/v5/tasks/create?apiKey='.RCRM_KEY;

										foreach ($managers as $key => $manager_id) {
											// Set data
												$task['text'] = 'Заказ №'.$row_order['order_id'].' не выгружен в CRM';
												$task['datetime'] = date('Y-m-d H:i', (time()+3600) );
												$task['performerId'] = $manager_id;
												$data['task'] = json_encode($task);
											// ---
											
											$response=connectPostAPI($url,$data);
										}
									// ---
								// ---
							}
						// ---
					// ---
				}

				$log[] = '#ERROR message: '.json_encode($response);
				if( isset($response->errors) ) { $log[] = '#ERROR details: '.json_encode($response->errors); }
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