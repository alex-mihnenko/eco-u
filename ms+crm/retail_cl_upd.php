<?php

include("opencart_inc.php");

$log = [];

if($_GET['type']){
	// ---

		$link="https://eco-u.retailcrm.ru/api/v5/customers/{$_GET['id']}?by=id&apiKey=".$retail_key;
		$json=crm_query($link);

		// Set data
			$externalId = $json['customer']['externalId'];
			$firstName = $json['customer']['firstName'];
			$lastName = $json['customer']['lastName'];
			$email = $json['customer']['email'];

			if( isset($json['customer']['personalDiscount']) ){
				$personalDiscount = intval($json['customer']['personalDiscount']);
			}
			else{
				$personalDiscount = 0;	
			}

			if( isset($json['customer']['address']['city']) ){
				$address = $json['customer']['address']['city'].', '.$json['customer']['address']['text'];	
			}
			else { $address = $json['customer']['address']['text']; }
		// ---

		// Get customer id
			if ( $qCustomer = mysql_query("SELECT * FROM `oc_customer` WHERE `email`='".$email."';") ) $nCustomer = mysql_num_rows($qCustomer);
			else $nCustomer = 0;

			if( $nCustomer==0 ){
				$res['log'] = $log;
				$res['mess']='No customers...';
				echo json_encode($res); exit;
			}

			$rowCustomer = mysql_fetch_assoc($qCustomer);
			$customer_id = $rowCustomer['customer_id'];
		// ---

		// Update customer and order
			$qUpdate = mysql_query("
				UPDATE `oc_customer` SET 
				`firstname` = '$firstName',
				`lastname` = '$lastName',
				`email` = '$email'
				WHERE `customer_id`=".$customer_id.";
			" );

			if( $qUpdate ){ $log[] = "Success update customer data"; }
			else { $log[] = "Error update customer data"; }

			if( $personalDiscount > 0 ) {
				$qUpdateDiscount = mysql_query("
					UPDATE `oc_customer` SET 
					`discount` = '$personalDiscount'
					WHERE `customer_id`=".$customer_id.";
				" );

				if( $qUpdateDiscount ){ $log[] = "Success update customer discount"; }
				else { $log[] = "Error update customer discount"; }
			}

			$qUpdate = mysql_query("
				UPDATE `oc_order` SET 
				`firstname` = '$firstName',
				`lastname` = '$lastName',
				`payment_address_1` = '$address',
				`shipping_address_1` = '$address' 
				WHERE `customer_id`=".$customer_id.";
			" );

			if( $qUpdate ){ $log[] = "Success update order data"; }
			else { $log[] = "Error update order data"; }
		// ---

		// Update addresses
			// Delete user addreses
			$qDelete = mysql_query("DELETE FROM `oc_address` WHERE `customer_id`=".$customer_id.";" );

			// Add address
			$qInsert = mysql_query("INSERT INTO `oc_address`
				(
					`customer_id`,
					`firstname`,
					`lastname`,
					`company`,
					`address_1`,
					`address_2`,
					`city`,
					`postcode`,
					`country_id`,
					`zone_id`,
					`custom_field`
				)
				values
				(
					'".$customer_id."',
					'".$firstName."',
					'".$lastName."',
					'',
					'".$address."',
					'',
					'',
					'',
					0,
					0,
					''
				);"
			);

			if( $qUpdate ){ $log[] = "Success update address data"; }
			else { $log[] = "Error update address data"; }
		// ---

		$res['log'] = json_encode($log);
		$res['mess']='OK';
		echo json_encode($res); exit;

	// ---
}