<?php
class ModelToolAddon extends Model {
	public function callbackAdd($telephone, $roistat_visit, $teleo) {
		$this->db->query("
			INSERT INTO `tb_callback` SET 
			`telephone` = '" . $this->db->escape($telephone) . "', 
			`roistat_visit` = '" . $this->db->escape($roistat_visit) . "', 
			`date_added` = '".time()."',
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
				DELETE FROM `".DB_PREFIX."address`  
				WHERE address_id='".$address_id."'
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


	// Customer one off coupon
		public function checkCustomerOneOffCoupon($phone) {
			// ---
				$sql = "
					SELECT * FROM `".DB_PREFIX."customer` c 
					LEFT JOIN `".DB_PREFIX."order` o ON c.customer_id = o.customer_id 
					WHERE c.telephone='".$phone."'
				;";

				$query = $this->db->query($sql);

				if($query->row) {
		            return $query->row;
		        } else {
		            return false;
		        }
			// ---
		}

		public function createCustomerOneOffCoupon($phone, $coupon) {
			// ---
				$sql = "
					DELETE FROM `".DB_PREFIX."coupon` 
					WHERE name='".$phone."' AND `type` = 'F'
				;";

				$query = $this->db->query($sql);
				

				$sql = "
					INSERT INTO `".DB_PREFIX."coupon` SET 
					`name` = '".$phone."',
					`code` = '".$coupon."',
					`type` = 'F',
					`discount` = '200.0000',
					`logged` = '0',
					`shipping` = '0',
					`total` = '0.0000',
					`date_start` = 'NOW()',
					`date_end` = '".date("Y-m-d", (time()+7776000))."',
					`uses_total` = '1',
					`uses_customer` = '1',
					`status` = '1',
					`date_added` = 'NOW()'
				;";

				$query = $this->db->query($sql);
				
		        return true;
			// ---
		}
	// ---

	// Testimonials
		public function addTestimonail($user_id, $author, $text, $parent_id=0, $good=0) {
			// ---
				$query = $this->db->query("
					INSERT INTO `" . DB_PREFIX . "testimonials` SET 
					`customer_id` = '0', 
					`user_id` = '" . $user_id . "', 
					`author` = '" . $this->db->escape($author) . "', 
					`text` = '" . $this->db->escape($text) . "', 
					`parent_id` = '".$parent_id."', 
					`good` = '" . $good . "', 
					`date_added` = '" . time() . "'
				;");

				return $query;
			// ---
		}

		public function getTestimonails($customer_id) {
			// ---
				$query = $this->db->query("
					SELECT 
						t.testimonials_id, t.customer_id, t.author, t.text, t.parent_id, t.good, t.date_added 
					FROM `" . DB_PREFIX . "testimonials` t  
					LEFT JOIN `" . DB_PREFIX . "customer` c ON c.customer_id = t.customer_id 
					WHERE c.rcrm_id = '" . $customer_id . "' AND t.parent_id = 0 ORDER BY t.date_added ASC
				;");

				if( $query->num_rows > 0) {
					return $query->rows;
				}
				else {
					return false;
				}
			// ---
		}

		public function getChildsTestimonails($testimonials_id) {
			// ---
				$query = $this->db->query("
					SELECT 
						t.testimonials_id, t.customer_id, t.user_id, t.author, t.text, t.parent_id, t.good, t.date_added, 
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

		public function getUserByEmail($email) {
			// ---
				$query = $this->db->query("
					SELECT 
						u.user_id, u.firstname  
					FROM `" . DB_PREFIX . "user` u 
					WHERE u.email = '" . $email . "' LIMIT 1
				;");

				if( $query->num_rows > 0) {
					return $query->row;
				}
				else {
					return false;
				}
			// ---
		}
	// ---
}
