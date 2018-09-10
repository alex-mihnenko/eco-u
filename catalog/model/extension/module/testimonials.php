<?php
class ModelExtensionModuleTestimonials extends Model {

	// Index
		public function getTotalItems() {
			// ---
				$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "testimonials` WHERE `parent_id` = 0 AND `status` = 1 ORDER BY date_added ASC;");
			
				return $query->num_rows;
			// ---
		}

		public function getItems($filter_data) {
			// ---
				$sql = "SELECT * FROM `" . DB_PREFIX . "testimonials` WHERE `parent_id` = 0 AND `status` = 1 ORDER BY date_added ASC";

				if (isset($filter_data['start']) || isset($filter_data['limit'])) {
					if ($filter_data['start'] < 0) {
						$filter_data['start'] = 0;
					}

					if ($filter_data['limit'] < 1) {
						$filter_data['limit'] = 20;
					}

					$sql .= " LIMIT " . (int)$filter_data['start'] . "," . (int)$filter_data['limit'];
				}

				$query = $this->db->query($sql);

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
	// ---

	// Add
		public function getOrderById($order_id, $customer_id) {
			// ---
				$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order` WHERE `order_id` = '".$order_id."' AND `customer_id` = '".$customer_id."' LIMIT 1;");
			
				if( $query->num_rows > 0) {
					return $query->rows;
				}
				else {
					return false;
				}
			// ---
		}

		public function getCustomerById($customer_id) {
			// ---
				$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "customer` WHERE `customer_id` = '".$customer_id."' LIMIT 1;");
			
				if( $query->num_rows > 0) {
					return $query->rows;
				}
				else {
					return false;
				}
			// ---
		}
	// ---
}
