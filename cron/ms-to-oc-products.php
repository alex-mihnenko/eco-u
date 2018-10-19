<?php
// Init
	include("../_lib.php");

	$config = json_decode(file_get_contents('ms-to-oc-products.json'));
// ---

// Request
	$url = 'https://online.moysklad.ru/api/remap/1.1/entity/product';
	$data = array('limit' => 100, 'offset' => (int)$config->page*100);

	$result = connectGetAPI($url, $data, MS_AUTH);

	// Update config
		if( count($result->rows) > 0 ){
			$log[] = 'Has been getted '.count($result->rows).' rows';
		}
		else {
			$log[] = 'No rows';

			$config->page = 0;
			file_put_contents('ms-to-oc-products.json',json_encode($config));

			$res['log'] = $log;
			$res['mess']='Success';
			echo json_encode($res); exit;
		}
	// ---

	$log[] = 'Current step '.$config->page;
// ---

// Proccessing
	$count = 0;

	foreach ($result->rows as $key => $row) {
		// ---
			// Check supplier
				if( !isset($row->supplier) ) { $supplier_id=0; }
				else{
					$href = $row->supplier->meta->href;
					$parts = parse_url($href);
					$supplier_ms_id = str_replace('/api/remap/1.1/entity/counterparty/', '', $parts['path']);

					// Get supplier
						$q = "SELECT * FROM `".DB_PREFIX."supplier` WHERE `ms_id`='".$supplier_ms_id."';";
						$result = $db->query($q);

						if ($result->num_rows > 0) {
							// ---
								$supplier = $result->fetch_assoc();
								$supplier_id=$supplier['supplier_id'];
							// ---
						}
						else{ $supplier_id=0; }
					// ---
				}
			// ---


			// Update
				if( !isset($row->minimumBalance) ) { $minimumBalance=0; }
				else { $minimumBalance=$row->minimumBalance; }

				if( !isset($row->buyPrice) ) { $buyPrice=0; }
				else { $buyPrice=floatval($row->buyPrice->value)/100; }
				
				// Get product
					$q = "SELECT * FROM `ms_products` WHERE `ms_id`='".$row->id."';";
					$result = $db->query($q);

					if ($result->num_rows > 0) {
						// ---
							$product = $result->fetch_assoc();
							$product_id=$product['product_id'];
							
							$q = "
								UPDATE `".DB_PREFIX."product` SET 
								`purchase_price` = '".$buyPrice."' 
								WHERE `product_id` = '".$product_id."'
							";
							
							if ($db->query($q) === TRUE) {
							    $log[] = $product_id.' '.$row->name.' has been updated';
							} else {
								$log[] = $product_id.' '.$row->name.' has been not updated: '.$db->error;
							}

							if ($supplier_id != 0) {
								// ---
									$q = "
										INSERT INTO `".DB_PREFIX."product_to_supplier` SET 
										`product_id` = '".$product_id."',
										`supplier_id` = '".$supplier_id."'
									";
									
									if ($db->query($q) === TRUE) {
									    $log[] = $product_id.' '.$row->name.' has been inserted';
									} else {
										$log[] = $product_id.' '.$row->name.' has been not inserted: '.$db->error;
									}
								// ---
							}
						
						$count++;

						// ---
					}
				// ---
			// ---
		// ---
	}

	$log[] = 'Has been proccessed '.$count.' rows';

	// Update config
		if( $count > 0 ){
			$config->page = $config->page + 1;
			file_put_contents('ms-to-oc-products.json',json_encode($config));
		}
		else {
			$config->page = 0;
			file_put_contents('ms-to-oc-products.json',json_encode($config));
		}
	// ---
// ---


// Response
	$res['log'] = $log;
	$res['mess']='Success';
	echo json_encode($res); exit;
// ---