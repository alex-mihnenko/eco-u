<?php

include("opencart_inc.php");

$time=time();

$log = [];



if ( $qItems = mysql_query("SELECT * FROM `oc_order_payments` WHERE `processed`=0 ORDER BY date_add DESC LIMIT 20;") ) $nItems = mysql_num_rows($qItems);
else $nItems = 0;

if( $nItems==0 ){
	$res['log'] = $log;
	$res['mess']='No items...';
	echo json_encode($res); exit;
}


// Get managers
	$managers = [];

	$url = 'https://eco-u.retailcrm.ru/api/v5/users?apiKey='.$retail_key;
	$response=crm_query($url);

	foreach ($response['users'] as $key => $user) {
		if( $user['isManager'] == 1 ){
			$managers[] = $user['id'];
		}
	}
// ---

while ($row = mysql_fetch_assoc($qItems)) {
	// ---
		// Get paymentId
			$url = 'https://eco-u.retailcrm.ru/api/v5/orders/'.$row['order_id'].'?apiKey='.$retail_key;
			$data['by'] = 'externalId';
			$data['externalId'] = $row['order_id'];

			$response=crm_query($url,$data);

			foreach ($response['order']['payments'] as $key => $val) {
				$paymentId = $key;
				break;
			}
		// ---

		// Edit payment		
			if( isset($paymentId) ){
				// ---
					$url='https://eco-u.retailcrm.ru/api/v5/orders/payments/'.$paymentId.'/edit?apiKey='.$retail_key;
					
					// Set data
						// Status
							if( $row['order_status_id'] == 20 ) {
								$status = 'paid';
							}
							else if( $row['order_status_id'] == 21 ) {
								$status = 'fail';
							}
							else {$status = 'new'; }
						// ---

						// Comment
							$dataTime = explode(' ', $row['date_add']);
							$comment = 'Время: '.$dataTime[1];
						// ---

						$data['by'] = 'id';

						$payment['externalId'] = $row['id_paymant'];
						$payment['amount'] = $row['total'];
						$payment['paidAt'] = $row['date_add'];
						$payment['comment'] = $comment;
						$payment['status'] = $status;

						$data['payment'] = json_encode($payment);
					// ---

					$response=crm_query_send($url,$data);

					if (!$response['success'])  { $log[$row['id_paymant']] = $response; }
					else { $log[$row['id_paymant']] = 'Success update'; }

					// Set proccessed
						$qUpdate = mysql_query("UPDATE `oc_order_payments` SET `processed` = 1 WHERE `id_paymant`=".$row['id_paymant'].";" );
					// ---

					// Set task
						if( $row['order_status_id'] == 20 ) {
							// ---
								$url = 'https://eco-u.retailcrm.ru/api/v5/tasks/create?apiKey='.$retail_key;

								foreach ($managers as $key => $manager_id) {
									// Set data
										$order['externalId'] = $row['order_id'];
										$task['text'] = 'Заказ №'.$row['order_id'].' оплачен';
										$task['datetime'] = date('Y-m-d H:i', (time()+3600) );
										$task['performerId'] = $manager_id;
										$task['order'] = $order;
										$data['task'] = json_encode($task);
									// ---
									
									$response=crm_query_send($url,$data);
								}
							// ---
						}
					// ---
				// ---
			}
		// ---
	// ---
}


$res['log'] = json_encode($log);
$res['mess']='OK';
echo json_encode($res); exit;