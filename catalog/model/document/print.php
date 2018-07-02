<?php
class ModelDocumentPrint extends Model {
	public function createCoupon($order_id, $customer_id) {
		// Create coupon code
			$timestart = time();
			$timeend = $timestart+1209600;
			
			$coupon_code = 'C-'.$customer_id.'-'.$order_id;
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
}