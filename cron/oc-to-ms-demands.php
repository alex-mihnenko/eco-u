<?php
// Init
	include("../_lib.php");
// ---


// Get tasks
	$q = "
		SELECT 
		* 
		FROM ms_demand msd 

		WHERE msd.order_id > 0 AND msd.completed = 0 ORDER BY msd.date_added ASC;
    ";

	$rows_demand = $db->query($q);

	if ($rows_demand->num_rows == 0) {
		// ---
			$log[] = 'No tasks';

			$res['log'] = $log;
			$res['mess']='Success';
			echo json_encode($res); exit;
		// ---
	}
// ---

// Go-round tasks
	while ( $row_demand = $rows_demand->fetch_assoc() ) {
		if( $row_demand['date_added']+600 < time() ) {
			// ---
				$log[] = '#Start ['.$row_demand['demand_id'].'] from '.$row_demand['date_added'];

				// Delete MS demand
					$q = "SELECT * FROM ms_demand msd WHERE msd.order_id = '".$row_demand['order_id']."' AND msd.deleted = 0 AND msd.completed = 1 ORDER BY msd.demand_id ASC;";

					$rows_demand_completed = $db->query($q);

					if ($rows_demand_completed->num_rows > 0) {
						while ( $row_demand_completed = $rows_demand_completed->fetch_assoc() ) {
							// ---
								$urlDemandDelete = 'https://online.moysklad.ru/api/remap/1.1/entity/demand/'.$row_demand_completed['ms_demand_id'];
								$dataDemandDelete = array();
								$resoponseDemandDelete = connectMSAPI($urlDemandDelete, $dataDemandDelete, 'DELETE', MS_AUTH);
								
								$q = "UPDATE `ms_demand` SET `deleted` = '1' WHERE `demand_id`='".$row_demand_completed['demand_id']."';";

								if ($db->query($q) === TRUE) {
								    $log[] = 'OC demand ['.$row_demand_completed['demand_id'].'] has been deleted and updated';
								} else {
									$log[] = 'OC demand ['.$row_demand_completed['demand_id'].'] has been not deleted and updated: '.$db->error;
								}
							// ---
						}
					}
				// ---

				// Get MS template
					$urlDemandPut = "https://online.moysklad.ru/api/remap/1.1/entity/demand/new?limit=100&offset=0";

					$dataDemandPut['customerOrder']["meta"] = array(
						"href" => $row_demand['customer_order_data'],
						"type" => 'customerorder',
						"mediaType" => 'application/json'
					);

					$resoponseDemandPut = connectMSAPI($urlDemandPut, json_encode($dataDemandPut), 'PUT', MS_AUTH);
				// ---

				// Create MS demand
					$urlDemandPost = "https://online.moysklad.ru/api/remap/1.1/entity/demand";

					$resoponseDemandPost = connectMSAPI($urlDemandPost, json_encode($resoponseDemandPut), 'POST', MS_AUTH);
				// ---

				// Update OC demand
					if( isset($resoponseDemandPost->id) ) {
						$q = "UPDATE `ms_demand` SET `ms_demand_id` = '".$resoponseDemandPost->id."', `completed` = '1' WHERE `demand_id`='".$row_demand['demand_id']."';";

						if ($db->query($q) === TRUE) {
						    $log[] = 'OC demand ['.$row_demand['demand_id'].'] has been updated';
						} else {
							$log[] = 'OC demand ['.$row_demand['demand_id'].'] has been not updated: '.$db->error;
						}
					}

					$log[] = '#Complete ['.$row_demand['demand_id'].'] has been proccessed';
				// ---
			// ---
		}
	}
// ---


// Response
	$res['log'] = $log;
	$res['mess']='Success';
	echo json_encode($res); exit;
// ---