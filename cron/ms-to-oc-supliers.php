<?php
// Init
	include("_lib.php");

	$config = json_decode(file_get_contents('ms-to-oc-supliers-config.json'));
// ---

// Request
	$url = 'https://online.moysklad.ru/api/remap/1.1/entity/counterparty';
	$data = array('limit' => 100, 'offset' => (int)$config->page*100);

	$result = connectGetAPI($url, $data, MS_AUTH);

	// Update config
		if( count($result->rows) > 0 ){
			$log[] = 'Has been getted '.count($result->rows).' rows';
		}
		else {
			$log[] = 'No rows';

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
			// Check
				$q = "SELECT * FROM `".DB_PREFIX."supplier` WHERE `ms_id`='".$row->id."';";
				$result = $db->query($q);

				if ($result->num_rows > 0) { continue; }
			// ---

			// Save
				if( !isset($row->actualAddress) ) { $actualAddress=''; }
				else { $actualAddress=$row->actualAddress; }

				$q = "
					INSERT INTO `".DB_PREFIX."supplier` SET 
					`ms_id` = '".$row->id."',
					`name` = '".$row->name."',
					`address` = '".$actualAddress."',
					`image` = '',
					`sort_order` = '0'
				";
				
				if ($db->query($q) === TRUE) {
					$count++;
				    $log[] = $row->id.' '.$row->name.' has been inserted';
				} else {
					$log[] = $row->id.' '.$row->name.' has been not inserted: '.$db->error;
				}
		// ---
	}

	$log[] = 'Has been proccessed '.$count.' rows';

	// Update config
		if( $count > 0 ){
			$config->page = $config->page + 1;
			file_put_contents('ms-to-oc-supliers-config.json',json_encode($config));
		}
		else {
			$config->page = 0;
			file_put_contents('ms-to-oc-supliers-config.json',json_encode($config));
		}
	// ---
// ---


// Response
	$res['log'] = $log;
	$res['mess']='Success';
	echo json_encode($res); exit;
// ---