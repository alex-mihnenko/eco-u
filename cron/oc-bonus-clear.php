<?php
// Init
	include("../_lib.php");

	$log = [];

	$currenttime = time();
// ---


// Get
	$q = "
		SELECT 
		* 
		FROM ".DB_PREFIX."bonus_history bh 

		WHERE status='1' ORDER BY bonus_history_id;
    ";

	$rows_bonus = $db->query($q);

	if ($rows_bonus->num_rows == 0) {
		// ---
			$log[] = 'No items';

			$res['log'] = $log;
			$res['mess']='Success';
			echo json_encode($res); exit;
		// ---
	}
// ---


// Go-round tasks
	while ( $row_bonus = $rows_bonus->fetch_assoc() ) {
		if( $currenttime-$row_bonus['time'] > 13824000 ) {
			$q = "UPDATE ".DB_PREFIX."bonus_history SET `status` = '0' WHERE `bonus_history_id`='".$row_bonus['bonus_history_id']."';";

			if ($db->query($q) === TRUE) {
			    $log[] = 'OC bonus ['.$row_bonus['bonus_history_id'].'] has beenupdated';
			} else {
				$log[] = 'OC bonus ['.$row_bonus['bonus_history_id'].'] has been not updated: '.$db->error;
			}
		}
	}
// ---


// Response
	$res['log'] = $log;
	$res['mess']='Success';
	echo json_encode($res); exit;
// ---