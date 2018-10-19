<?php
#	SET CUSTOMERS RCMR ID
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
		WHERE c.customer_id > 0 AND c.rcrm_id = 0 
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

		$url = 'https://eco-u.retailcrm.ru/api/v5/customers/'.$row_customer['customer_id'];
		$data = array('apiKey' => RCRM_KEY, 'by' => 'externalId');
		$result = connectGetAPI($url, $data);

		if( !isset($result->customer) ) {
			// ---
				$log[] = 'CRM customer ['.$row_customer['customer_id'].'] no exist';
			// ---
		}
		else {
			// ---
				$customer = $result->customer;

				$log[] = 'CRM customer ['.$customer->id.'] '.$customer->firstName.' exist';

				// $q = "
				// 	UPDATE `".DB_PREFIX."customer` SET 
				// 	`rcrm_id` = '".$row_customer['id']."' 
				// 	WHERE `customer_id`='".$customer->id."'
				// ;";

				// if ($db->query($q) === TRUE) {
				//     $log[] = 'OC customer ['.$row_customer['customer_id'].'] has been updated';
				// } else {
				// 	$log[] = 'OC customer ['.$row_customer['customer_id'].'] has been not updated: '.$db->error;
				// }
			// ---
		}
	}
// ---


// Response
	$res['log'] = $log;
	$res['mess']='Success';
	echo json_encode($res); exit;
// ---