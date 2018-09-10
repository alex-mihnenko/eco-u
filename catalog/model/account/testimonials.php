<?php
class ModelAccountTestimonials extends Model {
	public function addItem($customer_id, $author, $text, $parent_id=0, $rating=1, $order_id=0) {
		// ---
			$query = $this->db->query("
				INSERT INTO `" . DB_PREFIX . "testimonials` SET 
				`customer_id` = '" . intval($customer_id) . "', 
				`user_id` = '0', 
				`order_id` = '" . intval($order_id) . "', 
				`author` = '" . $this->db->escape($author) . "', 
				`text` = '" . $this->db->escape($text) . "', 
				`parent_id` = '" . $parent_id . "', 
				`rating` = '" . intval($rating) . "', 
				`date_added` = '" . time() . "',
				`status` = '0'
			;");

			return $query;
		// ---
	}

	public function getItems($customer_id) {
		// ---
			if( $customer_id > 0 ) { 
				$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "testimonials` WHERE `customer_id` = '" . $customer_id . "' AND `parent_id` = 0 ORDER BY date_added DESC;");
			}
			else {
				$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "testimonials` WHERE `parent_id` = 0 AND `status` = 1 ORDER BY date_added DESC;");
			}

			if( $query->num_rows > 0) {
				return $query->rows;
			}
			else {
				return false;
			}
		// ---
	}

	public function getChilds($testimonials_id) {
		// ---
			$query = $this->db->query("
				SELECT 
					t.testimonials_id, t.customer_id, t.user_id, t.author, t.text, t.parent_id, t.rating, t.date_added, 
					u.image 
				FROM `" . DB_PREFIX . "testimonials` t 
				LEFT JOIN `" . DB_PREFIX . "user` u ON u.user_id = t.user_id
				WHERE `parent_id` = '" . $testimonials_id . "' ORDER BY t.date_added ASC
			;");

			if( $query->num_rows > 0) {
				return $query->rows;
			}
			else {
				return false;
			}
		// ---
	}

	public function addTask($commonId, $internalId, $orderNumber='', $customer='', $text='') {
		// ---
			$query = $this->db->query("
				INSERT INTO `rcrm_tasks` SET 
				`commonId`='".$commonId."', 
				`internalId`='".$internalId."', 
				`orderNumber`='".$orderNumber."', 
				`customer`='".$customer."', 
				`text`='".$text."', 
				`status`='performing', 
				`processed`='0'
			;");

			return $query;
		// ---
	}
}
