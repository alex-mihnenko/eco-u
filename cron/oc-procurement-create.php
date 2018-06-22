<?php
// Init
	include("_lib.php");
// ---


// Proccessing
	// Get
		$q = "
			SELECT op.product_id as product_id, p.model as model, p.weight_class_id as weight_class_id, op.quantity as quantity, 
			p.purchase_price as purchase_price 
			FROM `".DB_PREFIX."order` o 
			LEFT JOIN `" . DB_PREFIX . "order_product` op ON op.order_id = o.order_id 
			LEFT JOIN `" . DB_PREFIX . "product` p ON p.product_id = op.product_id 
	       	WHERE o.order_status_id = '13' AND p.quantity<0;
	    ";

		$rows_products = $db->query($q);

		if ($rows_products->num_rows == 0) {
			// ---
				$log[] = 'No products';

				$res['log'] = $log;
				$res['mess']='Success';
				echo json_encode($res); exit;
			// ---
		}
	// ---

	// Create procurement record
		$q = "
			INSERT INTO `".DB_PREFIX."procurement` SET 
			`date_added` = NOW()
		";
		
		if ($db->query($q) === TRUE) {
			$procurement_id = $db->insert_id;
		    $log[] = $procurement_id.' has been inserted';
		} else {
			$log[] = 'Record has been not inserted: '.$db->error;
		}
	// ---

	// Create procurement list
		$count = 0;

		if( isset($procurement_id) ){
			while ( $row_products = $rows_products->fetch_assoc() ) {
				// ---
					$q = "SELECT * FROM `".DB_PREFIX."procurement_product` WHERE `procurement_id`='".$procurement_id."' AND `product_id`='".$row_products['product_id']."' LIMIT 1;";
					$rows_product = $db->query($q);

					if ($rows_product->num_rows > 0) {
						// Update
							$row_product = $rows_product->fetch_assoc();

							$q = "UPDATE `".DB_PREFIX."procurement_product` SET quantity = '".($row_products['quantity']+$row_product['quantity'])."' WHERE `procurement_product_id`='".$row_product['procurement_product_id']."';";

							if ($db->query($q) === TRUE) {
							    $log[] = $row_products['product_id'].' '.$row_products['model'].' has been updated';
							} else {
								$log[] = $row_products['product_id'].' '.$row_products['model'].' has been not updated: '.$db->error;
							}
						// ---
					}
					else {
						// Insert
							$q = "
								INSERT INTO `".DB_PREFIX."procurement_product` SET 
								`procurement_id` = '".$procurement_id."',
								`product_id` = '".$row_products['product_id']."',
								`purchase_price` = '".$row_products['purchase_price']."',
								`quantity` = '".$row_products['quantity']."',
								`weight_class_id` = '".$row_products['weight_class_id']."',
								`purchased` = '0',
								`not_purchased` = '0'
							";
							
							if ($db->query($q) === TRUE) {
							    $log[] = $row_products['product_id'].' '.$row_products['model'].' has been inserted';
							} else {
								$log[] = $row_products['product_id'].' '.$row_products['model'].' has been not inserted: '.$db->error;
							}
							
							$count++;
						// ---
					}
				// ---
			}
		} else{ $log[] = 'No procurement record'; }
	// ---

	$log[] = 'Has been proccessed '.$count.' products';
// ---


// Response
	$res['log'] = $log;
	$res['mess']='Success';
	echo json_encode($res); exit;
// ---