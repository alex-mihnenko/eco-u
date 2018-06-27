<?php
class ModelToolAddon extends Model {
	public function callbackAdd($telephone, $roistat_visit) {
		$this->db->query("INSERT INTO `tb_callback` SET `telephone` = '" . $this->db->escape($telephone) . "', `roistat_visit` = '" . $this->db->escape($roistat_visit) . "', `date_added` = NOW()");
	}

	public function getOrderProducts($order_id) {
		// ---
			$sql = "SELECT * FROM `".DB_PREFIX."order_product` op WHERE op.order_id='".$order_id."';";

			$query = $this->db->query($sql);

			return $query->rows;
		// ---
	}
}
