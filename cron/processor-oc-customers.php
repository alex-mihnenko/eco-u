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

		// Check last
			$unix_today = mktime(0, 0, 0, date('n',time()), date('j',time()), date('Y',time()));
			$unix_today_ago_two_week = mktime(0, 0, 0, date('n',time()-1209600), date('j',time()-1209600), date('Y',time()-1209600));

			$q = "
				SELECT * FROM `".DB_PREFIX."order` o 
				WHERE o.customer_id='".$row_customer['customer_id']."' AND o.date_added >= '" . date('Y-m-d 00:00:00',$unix_today_ago_two_week) . "' LIMIT 1
			;";

			$rows_last = $db->query($q);


			if ($rows_last->num_rows > 0) {
				$ecoin_amount = 100;
			}
			else {
				$ecoin_amount = 0;
			}

			// Add bonus
				if( $ecoin_amount > 0 ) {
					// ---
						$q = "
							INSERT INTO `".DB_PREFIX."bonus_history` SET 
							`bonus_account_id` = '0',
							`customer_id` = '".$row_customer['customer_id']."',
							`order_id` = '0',
							`amount` = '".$ecoin_amount."',
							`comment` = 'Автоматическое начисление за последний заказ',
							`time` = '".time()."'
						";
						
						if ($db->query($q) === TRUE) {
						    $log[] = 'OC customer address ['.$row_customer['customer_id'].'] has been inserted';
						} else {
							$log[] = 'OC customer address ['.$row_customer['customer_id'].'] has been not inserted: '.$db->error;
						}
					// ---
				}
			// ---
		// ---

		// Check total
			$q = "SELECT SUM(o.total) as total FROM `".DB_PREFIX."order` o WHERE o.customer_id='".$row_customer['customer_id']."' AND o.order_status_id=5;";
			$rows_total = $db->query($q);

			if ($rows_total->num_rows > 0) {
				$row_total = $rows_total->fetch_assoc();
				
				if ( $row_total['total'] >= 40000 && $row_total['total'] < 80000 ) {
					$ecoin_amount = 500;
				}
				else if ( $row_total['total'] >= 80000 && $row_total['total'] < 120000 ) {
					$ecoin_amount = 800;
				}
				else if ( $row_total['total'] >= 120000 ) {
					$ecoin_amount = 1400;
				}
				else {
					$ecoin_amount = 0;
				}

				// Add bonus
					if( $ecoin_amount > 0 ) {
						// ---
							$q = "
								INSERT INTO `".DB_PREFIX."bonus_history` SET 
								`bonus_account_id` = '0',
								`customer_id` = '".$row_customer['customer_id']."',
								`order_id` = '0',
								`amount` = '".$ecoin_amount."',
								`comment` = 'Автоматическое начисление за общую сумму заказов (".$row_total['total']." р.)',
								`time` = '".time()."'
							";
							
							if ($db->query($q) === TRUE) {
							    $log[] = 'OC customer address ['.$row_customer['customer_id'].'] has been inserted';
							} else {
								$log[] = 'OC customer address ['.$row_customer['customer_id'].'] has been not inserted: '.$db->error;
							}
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