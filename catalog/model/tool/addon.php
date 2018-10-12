<?php
class ModelToolAddon extends Model {
	// Callback
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
	// ---

	// Order
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
	// ---

	// Customer
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

		public function getCustomerByCRMId($internalId) {
			// ---
				$sql = "
					SELECT * FROM `".DB_PREFIX."customer` c 
					WHERE c.rcrm_id='".$internalId."' LIMIT 1
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
	// ---

	// One off coupon
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
		public function addTestimonail($user_id, $author, $text, $parent_id=0, $rating=0) {
			// ---
				$query = $this->db->query("
					INSERT INTO `" . DB_PREFIX . "testimonials` SET 
					`customer_id` = '0', 
					`user_id` = '" . $user_id . "', 
					`author` = '" . $this->db->escape($author) . "', 
					`text` = '" . $this->db->escape($text) . "', 
					`parent_id` = '".$parent_id."', 
					`rating` = '" . $rating . "', 
					`date_added` = '" . time() . "'
				;");

				return $query;
			// ---
		}

		public function getTestimonails($customer_id) {
			// ---
				$query = $this->db->query("
					SELECT 
						t.testimonials_id, t.customer_id, t.author, t.text, t.parent_id, t.rating, t.date_added, t.status 
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

		public function approveTestimonial($testimonials_id) {
			// ---
				$query = $this->db->query("
					UPDATE `" . DB_PREFIX . "testimonials` t  
					SET t.status = '1'  
					WHERE t.testimonials_id = '".$testimonials_id."'
				;");

				return true;
			// ---
		}
	// ---

	// Bonus account
		public function setBonus($code, $customer_id, $order_id=0, $amount=0, $comment='') {
			// ---
				$unix_today = mktime(0, 0, 0, date('n',time()), date('j',time()), date('Y',time()));
				$unix_today_ago_week = mktime(0, 0, 0, date('n',time()-604800), date('j',time()-604800), date('Y',time()-604800));
				$unix_today_ago_two_week = mktime(0, 0, 0, date('n',time()-1209600), date('j',time()-1209600), date('Y',time()-1209600));
				$unix_today_ago_four_week = mktime(0, 0, 0, date('n',time()-2419200), date('j',time()-2419200), date('Y',time()-2419200));

				if( $code != 'widget' && $code != 'apply' ){
					$sql = "
						SELECT * FROM `".DB_PREFIX."bonus_account` ba 
						WHERE ba.code='".$code."' AND ba.status='1' LIMIT 1
					;";

					$query = $this->db->query($sql);
					
					if($query->row) {
						$ba_account_id = $query->row['bonus_account_id'];
						$ba_name = $query->row['name'];
						$ba_coin = intval($query->row['coin']);
						$ba_rate = $query->row['rate'];
					} else {
		            	return false;
		        	}
				}
				else{
					$ba_account_id = 0;
				}

				// Processing
					switch ($code) {
						case 'order_complete':
							// ---
								if( $order_id > 0 ) {
									// ---
										$sql = "
											SELECT * FROM `".DB_PREFIX."order` o 
											WHERE o.order_id='".$order_id."' LIMIT 1
										;";

										$query = $this->db->query($sql);

										if($query->row) {
											$bh_amount = $ba_coin * round($query->row['total'] / $ba_rate);
										}
										 else {
								            $bh_amount = 0;
								        }
									// ---
								}

							// ---
						break;

						case 'order_weekly':
							// ---
								if( $order_id > 0 ) {
									// One per week
										$sql = "
											SELECT * FROM `".DB_PREFIX."bonus_history` bh 
											LEFT JOIN `".DB_PREFIX."bonus_account` ba ON ba.bonus_account_id = bh.bonus_account_id
											WHERE bh.customer_id = '".$customer_id."' AND ba.code = 'order_weekly' AND bh.time > '" . $unix_today_ago_week . "' LIMIT 1
										;";
										
										$query = $this->db->query($sql);

										if($query->row) {
										    $bh_amount = 0;
										}
										else {
											$sql = "
												SELECT * FROM `".DB_PREFIX."order` o 
												WHERE o.customer_id = '".$customer_id."' AND o.date_added >= '" . date('Y-m-d 00:00:00',$unix_today_ago_two_week) . "' AND o.date_added <= '" . date('Y-m-d 00:00:00',$unix_today_ago_week) . "' LIMIT 1
											;";
											
											$query = $this->db->query($sql);
											
											if($query->row) {
												$bh_amount = $ba_coin;
											}
											else {
												$bh_amount = 0;
											}

										}
									// ---
			
									// One per two week
										$sql = "
											SELECT * FROM `".DB_PREFIX."bonus_history` bh 
											LEFT JOIN `".DB_PREFIX."bonus_account` ba ON ba.bonus_account_id = bh.bonus_account_id
											WHERE bh.customer_id = '".$customer_id."' AND ba.code = 'order_monthly' AND bh.time > '" . $unix_today_ago_two_week . "' LIMIT 1
										;";
										
										$query = $this->db->query($sql);

										if($query->row) {
										    $bh_amount = 0;
										}
										else {
											$sql = "
												SELECT * FROM `".DB_PREFIX."order` o 
												WHERE o.customer_id = '".$customer_id."' AND o.date_added >= '" . date('Y-m-d 00:00:00',$unix_today_ago_four_week) . "' AND o.date_added <= '" . date('Y-m-d 00:00:00',$unix_today_ago_two_week) . "' LIMIT 1
											;";
											
											$query = $this->db->query($sql);
											
											if($query->row) {
												$bh_amount = $ba_coin;
											}
											else {
												$bh_amount = 0;
											}
										}
									// ---
								}
							// ---
						break;

						case 'testimonials':
							// ---
								$bh_amount = $ba_coin;
							// ---
						break;

						case 'widget':
							// ---
								$bh_amount = $amount;
							// ---
						break;

						case 'apply':
							// ---
								$bh_amount = -$amount;
							// ---
						break;
						
						default:
							$bh_amount = 0;
						break;
					}
				// ---

            	// Add history
					if( $bh_amount != 0 ) {
						$this->db->query("
							INSERT INTO `".DB_PREFIX."bonus_history` SET 
							`bonus_account_id` = '" . $ba_account_id . "', 
							`customer_id` = '" . $customer_id . "', 
							`order_id` = '" . $order_id . "',
							`amount` = '" . $bh_amount . "',
							`comment` = '" . $comment . "',
							`time` = '" . time() . "'
						;");
            			
            			return true;
					}
					else{
            			return false;
					}
				// --
		        
			// ---
		}

		public function getCustomerTotalBonusAmount($customer_id) {
			$sql = "
				SELECT SUM(bh.amount) as bonus FROM `".DB_PREFIX."bonus_history` bh 
				WHERE bh.customer_id='".$customer_id."'
			;";

			$query = $this->db->query($sql);

			if($query->row) {
				return $query->row;
			}
			else {
				return false;
			}
		}

		public function getCustomerHystory($customer_id) {
			$sql = "
				SELECT * FROM `".DB_PREFIX."bonus_history` bh 
				LEFT JOIN `".DB_PREFIX."bonus_account` ba ON ba.bonus_account_id = bh.bonus_account_id  
				WHERE bh.customer_id='".$customer_id."' ORDER BY bh.time DESC
			;";

			$query = $this->db->query($sql);

			if($query->rows) {
				return $query->rows;
			}
			else {
				return false;
			}
		}
	// ---
}
