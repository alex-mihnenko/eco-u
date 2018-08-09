<?php
// Init
	include("../_lib.php");

	$log = [];

	set_time_limit(0);
// ---


// Get customers
	$q = "
		SELECT 
		*
		FROM ".DB_PREFIX."customer c 

		ORDER BY c.customer_id DESC;
    ";

	$rows_customer = $db->query($q);

	if ($rows_customer->num_rows == 0) {
		// ---
			$log[] = 'No customers';

			$res['log'] = $log;
			$res['mess']='Success';
			echo json_encode($res); exit;
		// ---
	}
// ---


// Go-round customers
	while ( $row_customer = $rows_customer->fetch_assoc() ) {	
		$log[] = '#Start ['.$row_customer['customer_id'].']';

		// Update orders
			$q = "
				UPDATE `".DB_PREFIX."order` SET 
				`customer_id` = '".$row_customer['customer_id']."' 
				WHERE `telephone`='".$row_customer['telephone']."'
			;";

			if ($db->query($q) === TRUE) {
			    $log[] = 'OC customer ['.$row_customer['customer_id'].'] has been updated';
			} else {
				$log[] = 'OC customer ['.$row_customer['customer_id'].'] has been not updated: '.$db->error;
			}
		// ---
	}
// ---


// Response
	$res['log'] = $log;
	$res['mess']='Success';
	echo json_encode($res); exit;
// ---