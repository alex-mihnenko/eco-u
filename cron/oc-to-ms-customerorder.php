<?php
// Init
	set_time_limit(0);

	include("../_lib.php");

	$log = [];
// ---

// Get orders
	$q = "
		SELECT 
		msd.demand_id, 
		msd.ms_demand_id, 
		msd.ms_customer_order_id, 
		msd.customer_order_data, 
		msd.date_added, 
		msd.order_id, 
		o.order_status_id 
		FROM ms_demand msd 
		LEFT JOIN ".DB_PREFIX."order o ON msd.order_id = o.order_id
		WHERE msd.order_id>'0' AND o.order_status_id=5 GROUP BY msd.order_id ORDER BY msd.demand_id DESC LIMIT 50;
    ";
	// WHERE msd.order_id>'0' AND msd.ms_customer_order_status='0' AND o.order_status_id=5 GROUP BY msd.order_id ORDER BY msd.demand_id DESC LIMIT 50;

	$rows_demand = $db->query($q);

	if ($rows_demand->num_rows == 0) {
		// ---
			$log[] = 'No customer orders';

			$res['log'] = $log;
			$res['mess']='Success';
			echo json_encode($res); exit;
		// ---
	}
// ---


// Go-round orders
	$count = 0;

	while ( $row_demand = $rows_demand->fetch_assoc() ) {
		$log[] = '#Start ['.$row_demand['order_id'].'] from '.date("Y-m-d",$row_demand['date_added']);

		// ---
			if( !empty($row_demand['ms_customer_order_id']) ){
				// ---
					$url = "https://online.moysklad.ru/api/remap/1.1/entity/customerorder/".$row_demand['ms_customer_order_id'].'/positions';
					$data = array();
					$response_positions = connectMSAPI($url, $data, 'GET', MS_AUTH);

					foreach ($response_positions->rows as $key_position => $position) {
						// ---
							$url = "https://online.moysklad.ru/api/remap/1.1/entity/customerorder/".$row_demand['ms_customer_order_id'].'/positions/'.$position->id;
							$data = array('reserve' => 0);
							$response_position = connectMSAPI($url, json_encode($data), 'PUT', MS_AUTH);

							$log[] = 'MS positions ['.$position->id.'] has been updated: '.json_encode($response_position,JSON_UNESCAPED_UNICODE);
						// ---
					}

					// OC - update demamds
						$q = "
								UPDATE `ms_demand` SET 
								`ms_customer_order_status` = '1' 
								WHERE `order_id`='".$row_demand['order_id']."'
							;";

							if ($db->query($q) === TRUE) {
							    $log[] = 'OC demand order ['.$row_demand['order_id'].'] has been updated';
							} else {
								$log[] = 'OC demand order ['.$row_demand['order_id'].'] has been not updated: '.$db->error;
							}
					// ---

					$count++;
				// ---
			}
		// ---
	}

	$log[] = 'Has been proccessed '.$count.' rows';
// ---


// Response
	$res['log'] = $log;
	$res['mess']='Success';
	echo json_encode($res); exit;
// ---