<?php
#	SET CUSTOMERS BONUS
#	28.09.2018

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
		WHERE c.customer_id > 0 
		ORDER BY c.customer_id DESC LIMIT 10000;
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

		// Edit orders
			if( !empty($row_customer['telephone']) ) {
				$q = "
					UPDATE `".DB_PREFIX."order` SET 
					`customer_id` = '".$row_customer['customer_id']."' 
					WHERE `telephone` = '".$row_customer['telephone']."'
				";
				
				if ($db->query($q) === TRUE) {
				    $log[] = 'OC customer orders ['.$row_customer['customer_id'].'] has been inserted';
				} else {
					$log[] = 'OC customer orders ['.$row_customer['customer_id'].'] has been not inserted: '.$db->error;
				}
			}
		// ---
	}
// ---


// Response
	$res['log'] = $log;
	$res['mess']='Success';
	echo json_encode($res); exit;
// ---