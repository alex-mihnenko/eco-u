<?php
// Init
	include("../_lib.php");

	$config = json_decode(file_get_contents('ms-to-oc-product-delivery.json'));
// ---

// Request
	$url = 'https://online.moysklad.ru/api/remap/1.1/entity/purchaseorder';
	$data = array('limit' => 100, 'offset' => (int)$config->page*100);

	$result = connectGetAPI($url, $data, MS_AUTH);

	// Update config
		if( count($result->rows) > 0 ){
			$log[] = 'Has been getted '.count($result->rows).' rows';
		}
		else {
			$log[] = 'No rows';

			$config->page = 0;
			file_put_contents('ms-to-oc-product-delivery.json',json_encode($config));

			$res['log'] = $log;
			$res['mess']='Success';
			echo json_encode($res); exit;
		}
	// ---

	$log[] = 'Current step '.$config->page;

	$currenttime = mktime(0, 0, 0, date('n',time()), date('j',time()), date('Y',time()));
// ---

// Processing
	$count = 0;

	foreach ($result->rows as $key => $row) {
		// ---
			$purchaseorder_id = $row->id;
			$log[] = 'Purchaseorder id: '.$purchaseorder_id;

			if( isset($row->deliveryPlannedMoment) && $row->deliveryPlannedMoment != '' ) {
				$deliveryPlannedMomentDateTime = $row->deliveryPlannedMoment;
				$deliveryPlannedMomentArr = explode(' ', $deliveryPlannedMomentDateTime);
				$deliveryPlannedMoment = $deliveryPlannedMomentArr[0];

				// Get UNIX time
					$dateTmp = explode('-', $deliveryPlannedMoment);

					$deliveryUnixtime = mktime(0, 0, 0, intval($dateTmp[1]), intval($dateTmp[2]), $dateTmp[0]);
				// ---

				$log[] = 'deliveryPlannedMoment: '.$deliveryPlannedMoment;

				if( $deliveryUnixtime < $currenttime ) {
					$log[]='Planned moment is passed';
				}
				else {
					// ---
						// Get order positions
							$url = 'https://online.moysklad.ru/api/remap/1.1/entity/purchaseorder/'.$purchaseorder_id.'/positions';
							$data = array('limit' => 100, 'offset' => (int)$config->page*100);

							$positions = connectGetAPI($url, $data, MS_AUTH);
						// --

						// Processing positions
							foreach ($positions->rows as $key_position => $position) {
								// ---
									// Get MS product id
										$urlProduct=parse_url($position->assortment->meta->href);
										$hrefArr=explode("/",$urlProduct['path']);
										$msProducId=$hrefArr[6];
									// ---


									// Get OC product id
							            $q = "SELECT * FROM `ms_products` WHERE `ms_id`='".$msProducId."';";
							            $products_rows = $db->query($q);

							            if ( $products_rows->num_rows > 0 ) {
							            	// Update OC product
							            		$products_row = $products_rows->fetch_assoc();
							            		$product_id = $products_row['product_id'];
							            		
							            		// Available in time
							            			$available_in_time = '';
							            			$days = intval(date('j',$deliveryUnixtime-$currenttime));

							            			if( $days > 0 ) {
							            				$available_in_time = $days;
							            			}
							            		// ---

												$q = "
													UPDATE `".DB_PREFIX."product` SET 
													`date_available`= '".$deliveryPlannedMoment."',
													`stock_status_id`='6',
													`available_in_time`='".$available_in_time."' 
													WHERE `product_id` = ".$product_id.";
												";
												
												if ($db->query($q) === TRUE) {
												    $log[$product_id] = "Success update product [".$product_id."]. Day for available ".$days;
												} else {
													$log[$product_id] = "Error update: ".$db->error;
												}
											// ---
							            }
									// ---
								// ---
							}
						// ---

						$count++;

						$log[]='Planned moment has been updated';
					// ---
				}
			}
			else {
				$log[] = 'No delivery planned date';
			}
		// ---
	}

	$log[] = 'Has been proccessed '.$count.' rows';

	// Update config
		if( $count > 0 ){
			$config->page = $config->page + 1;
			file_put_contents('ms-to-oc-product-delivery.json',json_encode($config));
		}
		else {
			$config->page = 0;
			file_put_contents('ms-to-oc-product-delivery.json',json_encode($config));
		}
	// ---
// ---


// Response
	$res['log'] = $log;
	$res['mess']='Success';
	echo json_encode($res); exit;
// ---