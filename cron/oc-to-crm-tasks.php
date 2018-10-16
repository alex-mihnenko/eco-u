<?php
// Init
	include("../_lib.php");

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
// ---

// Get tasks
	$q = "
		SELECT 
		*
		FROM rcrm_tasks t 
		WHERE t.processed='0' LIMIT 100
    ;";

	$rows_tasks = $db->query($q);

	if ($rows_tasks->num_rows == 0) {
		// ---
			$log[] = 'No tasks';

			$res['log'] = $log;
			$res['mess']='Success';
			echo json_encode($res); exit;
		// ---
	}
// ---


// Go-round
	while ( $row_tasks = $rows_tasks->fetch_assoc() ) {
		// ---
			// Get current task
				$q = "SELECT * FROM rcrm_tasks t WHERE t.id='".$row_tasks['id']."' LIMIT 1;";
				$rows_task = $db->query($q);
			// ---

			if ($rows_task->num_rows > 0) {
				// ---
					$row_task = $rows_task->fetch_assoc();

					if( $row_task['processed'] == 0 ){
						// ---
							$log[] = '#Start ['.$row_tasks['id'].']';

							// Get CRM task
								$url = 'https://eco-u.retailcrm.ru/api/v5/tasks/'.$row_tasks['internalId'];
								$qdata = array('apiKey' => RCRM_KEY,'limit' => 100);

								$response = connectGetAPI($url,$qdata);

								if( isset($response->success) && $response->success!= false && isset($response->task) ){
									// Check task
										if( $response->task->complete == true ){
											// ---
												// Get common tasks
													$q = "SELECT * FROM rcrm_tasks t WHERE t.commonId='".$row_task['commonId']."';";
													$rows_common_tasks = $db->query($q);

													if ($rows_common_tasks->num_rows > 0) {
														// Go round common tasks
															while ( $row_common_tasks = $rows_common_tasks->fetch_assoc() ) {
																// ---
																	// Update OC task
																		$q = "UPDATE `rcrm_tasks` SET `status`='completed', `processed`='1' WHERE `id`='".$row_common_tasks['id']."';";

																		if ($db->query($q) === TRUE) {
																		    $log[] = 'OC task ['.$row_common_tasks['id'].'] has been updated';
																		} else {
																			$log[] = 'OC task ['.$row_common_tasks['id'].'] has been not updated: '.$db->error;
																		}
																	// ---

																	// Update CRM task
																		$url='https://eco-u.retailcrm.ru/api/v5/tasks/'.$row_common_tasks['internalId'].'/edit?apiKey='.RCRM_KEY;

																		$task = array('complete' => true);
																		$data = array(
																			'site' => 'eco-u-ru',
																			'task' => json_encode($task)
																		);

																		$response=connectPostAPI($url,$data);

																		if( isset($response->success) && $response->success!= false ){
																			$log[] = 'CRM task ['.$row_common_tasks['internalId'].'] has been updated';
																		}
																		else{
																			$log[] = 'CRM task ['.$row_common_tasks['internalId'].'] has been not updated. Response: '.json_encode($response);
																		}
																	// ---
																// ---
															}
														// ---
													}
												// ---
											// ---
										}
									// ---
								}
							// ---
						// ---
					}
				// ---
			}
		// ---
	}
// ---


// Response
	$res['log'] = $log;
	$res['mess']='Success';
	echo json_encode($res); exit;
// ---