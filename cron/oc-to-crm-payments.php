<?php
// Init
	include("_lib.php");

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


// Get payments
	$q = "SELECT * FROM `".DB_PREFIX."order_payments` WHERE `processed`=0 ORDER BY date_add ASC LIMIT 50;";
	$result = $db->query($q);

	if ($result->num_rows == 0) {
		$res['log'] = $log;
		$res['mess']='No items...';
		echo json_encode($res); exit;
	}
// ---

while ( $row = $result->fetch_assoc() ) {
	// ---
		// Get paymentId
			$url = 'https://eco-u.retailcrm.ru/api/v5/orders/'.$row['order_id'];
			$data = array('apiKey' => RCRM_KEY,'by' => 'externalId','externalId' => $row['order_id']);

			$response=connectGetAPI($url,$data);

			if( isset($response->success) && $response->success!= false && isset($response->id) ){
				foreach ($response->order->payments as $key => $val) {
					$paymentId = $val->id;
					$paymentType = $val->type;
					break;
				}
			}
			else{
				continue;
			}
		// ---

		// Init data
			// Type
				if( $row['payment_code'] == 'rbs' ) { $type = 'e-money'; }
				else { $type = 'cash'; }
			// ---

			// Status
				if( $row['order_status_id'] == 20 ) {
					$status = 'paid';
				}
				else if( $row['order_status_id'] == 21 ) {
					$status = 'fail';
				}
				else { $status = 'not-paid'; }
			// ---

			// Comment
				$dataTime = explode(' ', $row['date_add']);
				$comment = 'Время: '.$dataTime[1];
			// ---
		// ---

		// Processing
			if( isset($paymentId) ){
				if( $paymentType == $type ){
					// Edit payment		
						$url='https://eco-u.retailcrm.ru/api/v5/orders/payments/'.$paymentId.'/edit?apiKey='.RCRM_KEY;
						
						// Set data
							$data['by'] = 'id';

							$payment['externalId'] = $row['id_paymant'];
							$payment['amount'] = $row['total'];
							$payment['paidAt'] = $row['date_add'];
							$payment['comment'] = $comment;
							$payment['type'] = $type;
							$payment['status'] = $status;

							$data['payment'] = json_encode($payment);
						// ---

						$response=connectPostAPI($url,$data);

						if (!$response->success)  { $log[] = '['.$row['id_paymant'].']: error edit '.json_encode($response); }
						else { $log[] = '['.$row['id_paymant'].']: success edit'; }

						// Set proccessed
							$q = "UPDATE `".DB_PREFIX."order_payments` SET `processed` = 1 WHERE `id_paymant`=".$row['id_paymant'].";";

							if ($db->query($q) === TRUE) {
							    $log[] = '['.$row['id_paymant'].']: success update db';
							} else {
							    $log[] = '['.$row['id_paymant'].']: error update db '. $db->error;
							}
						// ---
					// ---
				}
				else {
					// Delete payment
						$url='https://eco-u.retailcrm.ru/api/v5/orders/payments/'.$paymentId.'/delete?apiKey='.RCRM_KEY;
						$data['id'] = $paymentId;

						$response=connectPostAPI($url,$data);

						if (!$response->success)  { $log[] = 'Error delete ['.$row['id_paymant'].'] '.json_encode($response); }
						else { $log[] = 'Success delete ['.$row['id_paymant'].'] '; }
					// ---

					// Create payment
						$url='https://eco-u.retailcrm.ru/api/v5/orders/payments/create?apiKey='.RCRM_KEY;
						
						// Set data
							$order['externalId'] = $row['order_id'];

							$payment['externalId'] = $row['id_paymant'];
							$payment['amount'] = $row['total'];
							$payment['paidAt'] = $row['date_add'];
							$payment['comment'] = $comment;
							$payment['type'] = $type;
							$payment['status'] = $status;
							$payment['order'] =  $order;

							$data['payment'] = json_encode($payment);
						// ---

						$response=connectPostAPI($url,$data);

						if (!$response->success)  { $log[] = '['.$row['id_paymant'].']: error create '.json_encode($response); }
						else { $log[] = '['.$row['id_paymant'].']: success create'; }

						// Set proccessed
							$q = "UPDATE `".DB_PREFIX."order_payments` SET `processed` = 1 WHERE `id_paymant`=".$row['id_paymant'].";";

							if ($db->query($q) === TRUE) {
							    $log[] = '['.$row['id_paymant'].']: success update db';
							} else {
							    $log[] = '['.$row['id_paymant'].']: error update db '. $db->error;
							}
						// ---
					// ---
				}
			}
			else{
				// Create payment
					$url='https://eco-u.retailcrm.ru/api/v5/orders/payments/create?apiKey='.RCRM_KEY;
					
					// Set data
						$order['externalId'] = $row['order_id'];

						$payment['externalId'] = $row['id_paymant'];
						$payment['amount'] = $row['total'];
						$payment['paidAt'] = $row['date_add'];
						$payment['comment'] = $comment;
						$payment['type'] = $type;
						$payment['status'] = $status;
						$payment['order'] =  $order;

						$data['payment'] = json_encode($payment);
					// ---

					$response=connectPostAPI($url,$data);

					if (!$response->success)  { $log[] = '['.$row['id_paymant'].']: error create '.json_encode($response); }
					else { $log[] = '['.$row['id_paymant'].']: success create'; }

					// Set proccessed
						$q = "UPDATE `".DB_PREFIX."order_payments` SET `processed` = 1 WHERE `id_paymant`=".$row['id_paymant'].";";

						if ($db->query($q) === TRUE) {
						    $log[] = '['.$row['id_paymant'].']: success update db';
						} else {
						    $log[] = '['.$row['id_paymant'].']: error update db '. $db->error;
						}
					// ---
				// ---
			}

			// Set task to managers
				$url = 'https://eco-u.retailcrm.ru/api/v5/tasks/create?apiKey='.RCRM_KEY;

				// Create commonID
					$commonId = uniqid();
					
					if( $row['order_status_id'] == 20 ) {
						$taskText = 'Заказ №'.$row['order_id'].' - Оплачен - ['.$commonId.']';
					}
					else if( $row['order_status_id'] == 21 ) {
						$taskText = 'Заказ №'.$row['order_id'].' - Не оплачен - ['.$commonId.']';
					}
					else{
						$taskText = 'Заказ №'.$row['order_id'].' - Ошибка оплаты - ['.$commonId.']';
					}
				// ---

				foreach ($managers as $key => $manager_id) {
					// Set data
						$task['text'] = $taskText;
						$task['datetime'] = date('Y-m-d H:i', (time()+3600) );
						$order['externalId'] = $row['order_id'];
						$task['order'] = $order;
						$task['performerId'] = $manager_id;
						$data['task'] = json_encode($task);
					// ---
					
					$response=connectPostAPI($url,$data);

					if( isset($response->success) && $response->success!= false && isset($response->id) ){
						// Save task
							$q = "
								INSERT INTO `rcrm_tasks` SET 
								`commonId`='".$commonId."', 
								`internalId`='".$response->id."', 
								`orderNumber`='".$row['order_id']."', 
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
		// ---
	// ---
}


$res['log'] = json_encode($log);
$res['mess']='OK';
echo json_encode($res); exit;