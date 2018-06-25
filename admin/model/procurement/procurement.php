<?php
class ModelProcurementProcurement extends Model {
	public function getProcurement($filter = array()) {
		
		$sql = "
			SELECT pt.procurement_id, pt.date_added, pt.status  
			FROM ".DB_PREFIX."procurement pt 
			WHERE pt.procurement_id > 0
		";

		// Filter
			if (!empty($filter['filter_date_added'])) {
				$sql .= " AND pt.date_added LIKE '%" . $filter['filter_date_added'] . "%'";
			}
		// ---

		if (isset($data['limit'])) {
			$sql .= " LIMIT " . (int)$data['start'];
		}

		$query = $this->db->query($sql);

		return $query->row;
	}

	public function sendProcurement($procurement_id) {
		// ---
			$msauth = 'admin@mail195:134679';
			$log = array();

			// Get MS meta
				// Organization
				$url = 'https://online.moysklad.ru/api/remap/1.1/entity/organization';
				$data = array('limit' => 100, 'offset' => 0);

				$result = $this->connectGetAPI($url, $data, $msauth);

				$organization = array(
					"meta" => array(
				      "href" => $result->rows[0]->meta->href,
				      "metadataHref" => "https://online.moysklad.ru/api/remap/1.1/entity/organization/metadata",
				      "type" => "organization",
				      "mediaType" => "application/json"
				    )
				);

				// Store
				$url = 'https://online.moysklad.ru/api/remap/1.1/entity/store';
				$data = array('limit' => 100, 'offset' => 0);

				$result = $this->connectGetAPI($url, $data, $msauth);

				$store = array(
					"meta" => array(
				      "href" => $result->rows[0]->meta->href,
				      "metadataHref" => "https://online.moysklad.ru/api/remap/1.1/entity/store/metadata",
				      "type" => "store",
				      "mediaType" => "application/json"
				    )
				);
			// ---

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
				foreach ($query->rows as $key => $supply) {
					// ---
						// Create supply
							$agent = array(
								"meta" => array(
							      "href" => 'https://online.moysklad.ru/api/remap/1.1/entity/counterparty/'.$supply['ms_id'],
							      "metadataHref" => "https://online.moysklad.ru/api/remap/1.1/entity/counterparty/metadata",
							      "type" => "counterparty",
							      "mediaType" => "application/json"
							    )
							);

							$url = 'https://online.moysklad.ru/api/remap/1.1/entity/supply';
							
							$data = array(
								'name' => '',
								'applicable' => false,
								'organization' => $organization,
								'agent' => $agent,
								'store' => $store,
							);

							$ms_supply = $this->connectPostAPI($url, $data, $msauth);

							$log[] = 'Create supply: '.json_encode($ms_supply);

							$supply_id = $ms_supply->id;
						// ---

						// Create positions
							// Get positions
								$sql = "
									SELECT pp.procurement_product_id, pp.product_id, pp.quantity, pp.purchased, pp.not_purchased, pd.name, pp.purchase_price, pp.total_price, 
									pp.weight_class_id as weight_class_id, wcd.unit as weight_class,
									p.manufacturer_id, p.minimum, p.weight, p.image_preview as image,
									sr.supplier_id as supplier_id, sr.name as supplier, 
									msp.ms_id 
									FROM ".DB_PREFIX."procurement_product pp 
									LEFT JOIN ".DB_PREFIX."procurement pt ON pt.procurement_id=pp.procurement_id 
									LEFT JOIN ".DB_PREFIX."product p ON p.product_id=pp.product_id 
									LEFT JOIN ".DB_PREFIX."product_description pd ON pd.product_id=pp.product_id 
									LEFT JOIN ".DB_PREFIX."product_to_category ptc ON ptc.product_id=pp.product_id 
									LEFT JOIN ".DB_PREFIX."product_to_supplier ptsr ON ptsr.product_id=pp.product_id 
									LEFT JOIN ".DB_PREFIX."supplier sr ON sr.supplier_id=ptsr.supplier_id 
									LEFT JOIN ".DB_PREFIX."weight_class_description wcd ON wcd.weight_class_id=pp.weight_class_id 
									LEFT JOIN ms_products msp ON msp.product_id=pp.product_id 
									WHERE pp.procurement_id = '".$procurement_id."' AND pp.purchased = '1' AND sr.supplier_id = '".$supply['supplier_id']."' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND wcd.language_id = '" . (int)$this->config->get('config_language_id') . "' 
									GROUP BY pp.product_id
								";

								$positions_query = $this->db->query($sql);
							// ---

							if(isset($positions_query->num_rows) && $positions_query->num_rows>0) {
								$url = 'https://online.moysklad.ru/api/remap/1.1/entity/supply/'.$supply_id.'/positions';
								
								foreach ($positions_query->rows as $key => $position) {
									// ---
										// Check quantity
											if( $position['minimum'] > $position['quantity'] ) {
												$position['quantity'] = intval($position['minimum']);
											}
											else { $position['quantity'] = intval($position['quantity']); }

											if( $position['quantity'] < 1 ) {
												$position['quantity'] = 1;
											}
										// ---

										$assortment = array(
											"meta" => array(
										      "href" => 'https://online.moysklad.ru/api/remap/1.1/entity/product/'.$position['ms_id'],
										      "metadataHref" => "https://online.moysklad.ru/api/remap/1.1/entity/product/metadata",
										      "type" => "product",
										      "mediaType" => "application/json"
										    )
										);

										$data = array(
											'assortment' => $assortment,
											'quantity' => $position['quantity'],
											'price' => ($position['purchase_price']*100),
											'vat' => 0,
										);

										$ms_supply_position = $this->connectPostAPI($url, $data, $msauth);
										$log[] = 'Create supply position: '.json_encode($ms_supply_position);
									// ---
								}
							}
						// ---
					// ---
				}

				// Update procurement
					$this->db->query("
						UPDATE `".DB_PREFIX."procurement` SET 
						status = '1' 
						WHERE procurement_id = '" . $procurement_id . "'
					");
				// ---

				return $log;
			}
			else{
				return false;
			}

			
			return true;
		// ---
	}

	public function connectPostAPI($url, $qdata, $auth='', $cookie='') {
		// ---
			$data = json_encode($qdata);

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,$url);
			curl_setopt($ch, CURLOPT_POST, 0);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");  
			curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			if( !empty($auth) ){
				curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
				curl_setopt($ch, CURLOPT_USERPWD, $auth);
			}
			curl_setopt($ch, CURLOPT_COOKIE, $cookie);
			$headers = ['Content-Type: application/json'];
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
		// ---
	}

	public function connectGetAPI($url, $qdata, $auth='') {
		// ---
			$data = http_build_query($qdata);

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			if( !empty($auth) ){
				curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
				curl_setopt($ch, CURLOPT_USERPWD, $auth);
			}
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
		// ---
	}
}