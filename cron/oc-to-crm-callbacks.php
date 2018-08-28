<?php
// Init
	include("../_lib.php");

	$log = [];

	// Get CRM managers
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

    $currenttime = time();
// ---


// Get callbacks
	$q = "
		SELECT *
		FROM `tb_callback` c 
		WHERE c.status = '0' 
		ORDER BY id
	;";

	$rows_callbacks = $db->query($q);

	if ($rows_callbacks->num_rows == 0) {
		// ---
			$log[] = 'No callbacks';

			$res['log'] = $log;
			$res['mess']='Success';
			echo json_encode($res); exit;
		// ---
	}
// ---

// Get Telphin client id
	$url = 'https://' . TELPHIN_SERVER_NAME . '/api/ver1.0/client/@me/extension/'.TELPHIN_EXTENSION_ID;
	$data = array();

	$response = telphinRequest($url, $data, 'GET');

	if( !isset($response['client_id']) ){
		$log[] = 'No client id';

		$res['log'] = $log;
		$res['mess']='Success';
		echo json_encode($res); exit;
	}

	$client_id = $response['client_id'];
// ---
	

	while ( $row_callbacks = $rows_callbacks->fetch_assoc() ) {
		// ----
			if( $row_callbacks['date_added'] + 1800 < $currenttime ) {
				$teleo = json_decode($row_callbacks['response']); // $teleo->call_id && $teleo->call_api_id

				
				$url = 'https://' . TELPHIN_SERVER_NAME . '/api/ver1.0/client/'.$client_id.'/call_history/'.$teleo->call_id;
				$data = array();

				$response = telphinRequest($url, $data, 'GET');

				if( !isset($response['result']) ) {
					$log[] = 'OC callback ['.$row_callbacks['id'].'] result ['.$teleo->call_id.'] error: '.json_encode($response);
				}
				else{
					// ---
						if( $response['result'] != 'answered' ) {
					        // Set task to managers
					            $url = 'https://eco-u.retailcrm.ru/api/v5/tasks/create?apiKey='.RCRM_KEY;

					            // Create commonID
					              $commonId = uniqid();
					              $taskText = "ROIstat ID: ".$row_callbacks['roistat_visit'].". Обратный звонок на номер +".$row_callbacks['telephone']." [".$commonId."]";
					              $commentaryText = "ROIstat ID: ".$row_callbacks['roistat_visit'].". \nВремя заявки: ".$row_callbacks['date_added'];
					            // ---

					            foreach ($managers as $key => $manager_id) {
					              // Set data
					                $task['text'] = $taskText;
					                $task["commentary"] = $commentaryText;
					                $task["datetime"] = date("Y-m-d H:i", (time()+1800) );
					                $task["phone"] = $row_callbacks['telephone'];
					                $task["performerId"] = $manager_id;
					                $data['task'] = json_encode($task);
					              // ---
					              
					              $response=connectPostAPI($url,$data);

					              if( isset($response->success) && $response->success!= false && isset($response->id) ){
					                // Save task
					                  $q = "
					                    INSERT INTO `rcrm_tasks` SET 
					                    `commonId`='".$commonId."', 
					                    `internalId`='".$response->id."', 
					                    `orderNumber`='', 
					                    `customer`='', 
					                    `text`='".$taskText."', 
					                    `status`='performing', 
					                    `processed`='0'
					                  ;";

					                  if ($db->query($q) === TRUE) {
					                      $log[] = 'OC task log has been created';
					                  } else {
					                    $log[] = 'OC task log has been not created: '.$db->error;
					                  }
					                // ---
					              }

					            }
					        // ---
						}

						$q = "UPDATE `tb_callback` SET `status` = '1' WHERE `id`='".$row_callbacks['id']."';";

						if ($db->query($q) === TRUE) {
						    $log[] = 'OC callback ['.$row_callbacks['id'].'] has been updated';
						} else {
							$log[] = 'OC callback ['.$row_callbacks['id'].'] has been not updated: '.$db->error;
						}
					// ---
				}
			}
		// ----
	}


// Response
	$res['log'] = $log;
	$res['mess']='Success';
	echo json_encode($res); exit;
// ---