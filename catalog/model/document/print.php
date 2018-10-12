<?php
class ModelDocumentPrint extends Model {
	public function createCoupon($order_id, $customer_id, $document_code) {
		// Create coupon code
			$timestart = time();
			$timeend = $timestart+1209600;
			
			$coupon_code = $document_code.'-'.$customer_id.'-'.$order_id;
			$discount = 5;
			$date_start = date('Y-m-d', $timestart);
			$date_end = date('Y-m-d', $timeend);

			$coupon = $this->db->query("SELECT * FROM `" . DB_PREFIX . "coupon` WHERE code = '" . $coupon_code . "'");

			if ( $coupon->num_rows == 0 ) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "coupon` SET 
					name = '" . $coupon_code . "',
					code = '" .$coupon_code  . "',
					type = 'P',
					discount = '" . $discount . "',
					logged = '0',
					shipping = '0',
					total = '0',
					date_start = '" .$date_start  . "',
					date_end = '" .$date_end  . "',
					uses_total = '0',
					uses_customer = '0',
					status = '1',
					date_added = NOW()
				");
                
				$coupon_id = $this->db->getLastId();
			}
			else{
				$coupon_code = $coupon->row['code'];
				$coupon_discount = $coupon->row['discount'];

				$timeendArr = explode('-', $coupon->row['date_end']);

				$timeend = mktime(0, 0, 0, intval($timeendArr[1]), intval($timeendArr[2]), $timeendArr[0]);
			}
		// ---


		return array('coupon_code' => $coupon_code, 'coupon_discount' => $discount, 'coupon_end' => date('d.m.Y', $timeend) );
	}

	public function getOrders($order_status_ids=array()){
		// ---
			$sql = "SELECT * FROM `" . DB_PREFIX . "order` WHERE order_id>0";

			foreach ($order_status_ids as $key => $order_status_id) {
				$sql .= " AND order_status_id = '" . $order_status_id . "'";
			}
			
			$sql .= " ORDER BY `order_id` DESC";

			$orders = $this->db->query($sql);

			return $orders->rows;
		// ---
	}

	public function getOrderProducts($order_id){
		// ---
			$sql = "
				SELECT op.product_id, op.name, op.amount, op.variant, wcd.unit  FROM `" . DB_PREFIX . "order_product` op 
				LEFT JOIN `" . DB_PREFIX . "product` p ON p.product_id = op.product_id 
				LEFT JOIN `" . DB_PREFIX . "weight_class_description` wcd ON wcd.weight_class_id = p.weight_class_id 
				WHERE order_id='".$order_id."' 
				ORDER BY op.name
			";

			$products = $this->db->query($sql);

			return $products->rows;
		// ---
	}

	public function getProductCategory($product_id){
		// ---
			$sql = "
				SELECT *
				FROM ".DB_PREFIX."product_to_category ptc 
				LEFT JOIN ".DB_PREFIX."category c ON ptc.category_id = c.category_id 
				WHERE ptc.product_id = '".$product_id."' AND c.parent_id = 0
				LIMIT 1
			";

			$query = $this->db->query($sql);

			if(isset($query->num_rows) && $query->num_rows>0) {
				// ---
					$sql = "
						SELECT ptc.category_id
						FROM ".DB_PREFIX."product_to_category ptc 
						LEFT JOIN ".DB_PREFIX."category c ON ptc.category_id = c.category_id 
						WHERE ptc.product_id = '".$product_id."' AND c.parent_id = '".$query->row['category_id']."'
						LIMIT 1
					";

					$query = $this->db->query($sql);

					return $query->rows;
				// ---
			}
			else {
				return array();
			}
		// ---
	}
}