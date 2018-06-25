<?php
// Init
	include("_lib.php");

	$config = json_decode(file_get_contents('ms-to-oc-supliers-config.json'));
// ---



$procurement_id = 5;


// Get suppliers
		$sql = "
			SELECT ptp.procurement_product_id, ptp.procurement_id, ptp.product_id,
			sr.supplier_id, sr.name, sr.ms_id
			FROM ".DB_PREFIX."procurement_product ptp 
			LEFT JOIN ".DB_PREFIX."procurement pr ON pr.procurement_id=ptp.procurement_id 
			LEFT JOIN ".DB_PREFIX."product p ON p.product_id=ptp.product_id 
			LEFT JOIN ".DB_PREFIX."product_to_supplier ptsr ON ptsr.product_id=ptp.product_id 
			LEFT JOIN ".DB_PREFIX."supplier sr ON sr.supplier_id=ptsr.supplier_id 
			WHERE pr.procurement_id = '".$procurement_id."' AND ptp.purchased = '1'
			GROUP BY sr.supplier_id
		";

		$query = $this->db->query($sql);
	// ---
	

	if(isset($query->num_rows) && $query->num_rows>0) {
		foreach ($query->rows as $key => $supplier) {
			// ---
				print_r($query->rows); exit;
				
				$url = 'https://online.moysklad.ru/api/remap/1.1/entity/supply';
				
				$data = array(
					'name' => '',
					'organization' => '',
					'agent' => '',
					'store' => ''
				);

				$result = connectGetAPI($url, $data, MS_AUTH);
			// ---
		}
	}
	else{
		return false;
	}


// Response
	$res['log'] = $log;
	$res['mess']='Success';
	echo json_encode($res); exit;
// ---