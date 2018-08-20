<?php
class ModelToolAddon extends Model {
	public function callbackAdd($telephone, $roistat_visit, $teleo) {
		$this->db->query("
			INSERT INTO `tb_callback` SET 
			`telephone` = '" . $this->db->escape($telephone) . "', 
			`roistat_visit` = '" . $this->db->escape($roistat_visit) . "', 
			`date_added` = NOW(),
			`response` = '" . json_encode($teleo) . "',
			`status` = '0'
		;");
	}

	public function getOrderProducts($order_id) {
		// ---
			$sql = "
				SELECT op.product_id, pd.name, op.quantity, p.weight, p.quantity as stock, p.weight_class_id, p.stock_status_id, p.date_available, msp.ms_id FROM `".DB_PREFIX."order_product` op 
				LEFT JOIN `".DB_PREFIX."product` p ON p.product_id = op.product_id 
				LEFT JOIN `".DB_PREFIX."product_description` pd ON pd.product_id = op.product_id 
				LEFT JOIN ms_products msp ON msp.product_id = op.product_id 
				WHERE op.order_id='".$order_id."'
			;";

			$query = $this->db->query($sql);

			if($query->rows) {
	            return $query->rows;
	        } else {
	            return array();
	        }
		// ---
	}

	public function getCustomer($externalId) {
		// ---
			$sql = "
				SELECT * FROM `".DB_PREFIX."customer` c 
				WHERE c.customer_id='".$externalId."' LIMIT 1
			;";

			$query = $this->db->query($sql);

			if($query->row) {
	            return $query->row;
	        } else {
	            return false;
	        }
		// ---
	}


	public function getCustomerAddresses($customer_id) {
		// ---
			$sql = "
				SELECT * FROM `".DB_PREFIX."address` a 
				WHERE a.customer_id='".$customer_id."'
			;";

			$query = $this->db->query($sql);

			if($query->rows) {
	            return $query->rows;
	        } else {
	            return array();
	        }
		// ---
	}

	public function getCustomerAddress($customer_id, $code) {
		// ---
			$sql = "
				SELECT * FROM `".DB_PREFIX."address` a 
				WHERE a.customer_id='".$customer_id."' AND a.custom_field='".$code."'
			;";

			$query = $this->db->query($sql);

			if($query->row) {
	            return $query->row;
	        } else {
	            return false;
	        }
		// ---
	}

	public function deleteCustomerAddress($address_id) {
		// ---
			$sql = "
				DELETE FROM `".DB_PREFIX."address` a 
				WHERE a.address_id='".$address_id."'
			;";

			$query = $this->db->query($sql);
			
	        return true;
		// ---
	}

	public function editCustomerAddress($address_id, $code) {
		// ---
			$sql = "
				UPDATE `".DB_PREFIX."address` a 
				SET `custom_field`='".$code."' 
				WHERE a.address_id='".$address_id."'
			;";

			$query = $this->db->query($sql);
			
	        return true;
		// ---
	}
}
