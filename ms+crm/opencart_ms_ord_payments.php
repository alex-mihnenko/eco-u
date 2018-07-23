<?php

// Init
	error_reporting(E_ALL);
	ini_set('display_errors', '1');

	include("../config.php");

	$db = new mysqli(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

	if ($db->connect_error) {
	    die("Connection failed to db: " . $db->connect_error);
	}

	$db->set_charset("utf8");

	define('AUTH_DATA', 'admin@mail195:b41fd841edc5');
	define('RETAILCRM_KEY', 'AuNf4IgJFHTmZQu7PwTKuPNQch5v03to');

	$log = [];
// ---


// Get OC payments
	$q = "SELECT * FROM `".DB_PREFIX."order_payments` WHERE `processed`=0 ORDER BY date_add DESC LIMIT 20;";
	$result = $db->query($q);

	if ($result->num_rows == 0) {
		$res['log'] = $log;
		$res['mess']='No items...';
		echo json_encode($res); exit;
	}
// ---


// Get managers
	$managers = [];

	$url = 'https://eco-u.retailcrm.ru/api/v5/users';
	$qdata = array('apiKey' => RETAILCRM_KEY,'limit' => 100);

	$response = connectGetAPI($url,$qdata);

	foreach ($response->users as $key => $user) {
		if( $user->isManager == 1 ){
			$managers[] = $user->id;
		}
	}
// ---


while ( $row = $result->fetch_assoc() ) {
	// ---
		// Get paymentId
			$url = 'https://eco-u.retailcrm.ru/api/v5/orders/'.$row['order_id'];
			$data = array('apiKey' => RETAILCRM_KEY,'by' => 'externalId','externalId' => $row['order_id']);

			$response=connectGetAPI($url,$data);

			foreach ($response->order->payments as $key => $val) {
				$paymentId = $val->id;
				$paymentType = $val->type;
				break;
			}
		// ---

		// Init data
			// Type
				if( $row['payment_code'] == 'rbs' ) {
					$type = 'e-money';
				}
				else {$type = 'cash'; }
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
						$url='https://eco-u.retailcrm.ru/api/v5/orders/payments/'.$paymentId.'/edit?apiKey='.RETAILCRM_KEY;
						
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

						// Set task
							if( $row['order_status_id'] == 20 ) {
								// ---
									$url = 'https://eco-u.retailcrm.ru/api/v5/tasks/create?apiKey='.RETAILCRM_KEY;

									foreach ($managers as $key => $manager_id) {
										// Set data
											$order['externalId'] = $row['order_id'];
											$task['text'] = 'Заказ №'.$row['order_id'].' оплачен';
											$task['datetime'] = date('Y-m-d H:i', (time()+3600) );
											$task['performerId'] = $manager_id;
											$task['order'] = $order;
											$data['task'] = json_encode($task);
										// ---
										
										$response=connectPostAPI($url,$data);
									}
								// ---
							}
						// ---
					// ---
				}
				else {
					// Delete payment
						$url='https://eco-u.retailcrm.ru/api/v5/orders/payments/'.$paymentId.'/delete?apiKey='.RETAILCRM_KEY;
						$data['id'] = $paymentId;

						$response=connectPostAPI($url,$data);

						if (!$response->success)  { $log[] = 'Error delete ['.$row['id_paymant'].'] '.json_encode($response); }
						else { $log[] = 'Success delete ['.$row['id_paymant'].'] '; }
					// ---

					// Add payment
						$url='https://eco-u.retailcrm.ru/api/v5/orders/payments/create?apiKey='.RETAILCRM_KEY;
						
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
				// Add payment
					$url='https://eco-u.retailcrm.ru/api/v5/orders/payments/create?apiKey='.RETAILCRM_KEY;
					
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
		// ---
	// ---
}


$res['log'] = json_encode($log);
$res['mess']='OK';
echo json_encode($res); exit;


function connectPostAPI($url, $qdata, $cookie='') {

	$data = http_build_query($qdata);

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");  
	curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_COOKIE, $cookie);
	$headers = ['Content-Type: application/x-www-form-urlencoded'];
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_HEADER, false);

	// Output
	$output = curl_exec($ch);
	$result = json_decode($output);

	// Result
	if( $result != null ){
		curl_close ($ch);
		return $result;
	}
	else {
		curl_close ($ch);
		return false;
	}

}

function connectGetAPI($url, $qdata) {

	$data = http_build_query($qdata);

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_URL,$url.'?'.$data);
	curl_setopt($ch, CURLOPT_TIMEOUT, 80);

	// Output
	$output = curl_exec($ch);
	$result = json_decode($output);

	// Result
	if( $result != null ){
		curl_close ($ch);
		return $result;
	}
	else {
		curl_close ($ch);
		return false;
	}

}