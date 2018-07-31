<?php
// Init
	include("_lib.php");
// ---


// Get tasks
	$q = "
		SELECT 
		* 
		FROM ms_demand msd 

		WHERE msd.completed = 0 ORDER BY msd.demand_id ASC;
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
		if( $row_demand['demand_id']+300 < time() ) {
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
								$resoponseDemandDelete = connectDeleteAPI($urlDemandDelete, $dataDemandDelete, MS_AUTH);

								/* DEBUG */ file_put_contents('../ms+crm/log-ms-demand.txt', $row_demand_completed['order_id']." : entity/demand/delete : ".json_encode($resoponseDemandDelete)."\n\n", FILE_APPEND | LOCK_EX);
								
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
					$urlDemandPut = "https://online.moysklad.ru/api/remap/1.1/entity/demand/new";
					$dataDemandPut = (array)json_decode($row_demand['customer_order_data']);

					$resoponseDemandPut = connectPutAPI($urlDemandPut, $dataDemandPut, MS_AUTH);

					/* DEBUG */ file_put_contents('../ms+crm/log-ms-demand.txt', $row_demand['order_id']." : entity/demand/new : ".json_encode($resoponseDemandPut)."\n", FILE_APPEND | LOCK_EX);
				// ---

				// Create MS demand
					$urlDemandPost = "https://online.moysklad.ru/api/remap/1.1/entity/demand";

					$resoponseDemandPost = connectPostAPI($urlDemandPost, $resoponseDemandPut, MS_AUTH);
					
					/* DEBUG */ file_put_contents('../ms+crm/log-ms-demand.txt', $row_demand['order_id']." : entity/demand : ".json_encode($resoponseDemandPost)."\n\n", FILE_APPEND | LOCK_EX);
				// ---

				// Update OC demand
					if( isset($resoponseDemandPost['id']) ) {
						$q = "UPDATE `ms_demand` SET `ms_demand_id` = '".$resoponseDemandPost['id']."', `completed` = '1' WHERE `demand_id`='".$row_demand['demand_id']."';";

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