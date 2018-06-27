<?php
class ModelToolAddon extends Model {
	public function callbackAdd($telephone, $roistat_visit) {
		$this->db->query("INSERT INTO `tb_callback` SET `telephone` = '" . $this->db->escape($telephone) . "', `roistat_visit` = '" . $this->db->escape($roistat_visit) . "', `date_added` = NOW()");
	}

	public function getOrderProducts($order_id) {
		// ---
			$sql = "
				SELECT op.product_id, pd.name, op.quantity, p.weight, p.quantity as stock, p.weight_class_id, p.stock_status_id, p.date_available, msp.ms_id FROM `".DB_PREFIX."order_product` op 
				LEFT JOIN `".DB_PREFIX."product` p ON p.product_id = op.product_id 
				LEFT JOIN `".DB_PREFIX."product_description` pd ON pd.product_id = op.product_id 
				LEFT JOIN ms_products msp ON msp.product_id = op.product_id 
				WHERE op.order_id='".$order_id."' AND p.stock_status_id<>'7'
			;";

			$query = $this->db->query($sql);

			if($query->rows) {
	            return $query->rows;
	        } else {
	            return array();
	        }
		// ---
	}
}
