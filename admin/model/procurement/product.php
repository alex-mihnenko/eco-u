<?php
class ModelProcurementProduct extends Model {
	public function getProducts($filter = array()) {
		
		$sql = "
			SELECT pp.procurement_product_id, pp.product_id, pp.quantity, pp.purchased, pp.not_purchased, pd.name, pp.purchase_price, pp.total_price, 
			pp.weight_class_id as weight_class_id, wcd.unit as weight_class,
			p.manufacturer_id, p.minimum, p.weight, p.image_preview as image,
			ptc.category_id as category_id,
			sr.supplier_id as supplier_id, sr.name as supplier 
			FROM ".DB_PREFIX."procurement_product pp 
			LEFT JOIN ".DB_PREFIX."procurement pt ON pt.procurement_id=pp.procurement_id 
			LEFT JOIN ".DB_PREFIX."product p ON p.product_id=pp.product_id 
			LEFT JOIN ".DB_PREFIX."product_description pd ON pd.product_id=pp.product_id 
			LEFT JOIN ".DB_PREFIX."product_to_category ptc ON ptc.product_id=pp.product_id 
			LEFT JOIN ".DB_PREFIX."product_to_supplier ptsr ON ptsr.product_id=pp.product_id 
			LEFT JOIN ".DB_PREFIX."supplier sr ON sr.supplier_id=ptsr.supplier_id 
			LEFT JOIN ".DB_PREFIX."weight_class_description wcd ON wcd.weight_class_id=pp.weight_class_id 
			WHERE pp.procurement_id = '".$filter['procurement_id']."' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND wcd.language_id = '" . (int)$this->config->get('config_language_id') . "'
		";


		// Filter
			if (!empty($filter['filter_supplier'])) {
				$sql .= " AND sr.name LIKE '%" . $this->db->escape($filter['filter_supplier']) . "%'";
			}

			if (!empty($filter['filter_name'])) {
				$sql .= " AND pd.name LIKE '%" . $this->db->escape($filter['filter_name']) . "%'";
			}

			if (!empty($filter['filter_category'])) {
				$sql .= " AND ptc.category_id =" . $filter['filter_category'] . "";
			}
		// ---

		$sql .= " GROUP BY pp.procurement_product_id";

		// Sort
			if( isset($filter['sort']) && $filter['sort'] != null ){
				//$sql .= " ORDER BY " .$filter['sort']." ".$filter['order'];
				//$sql .= " ORDER BY supplier_id ASC";
			}
		// ---

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getProduct($procurement_product_id){
		$sql = "
			SELECT pp.procurement_product_id, pp.product_id, pp.quantity, pp.purchased, pp.not_purchased, pd.name, pp.purchase_price, pp.total_price, 
			pp.weight_class_id as weight_class_id, wcd.unit as weight_class,
			p.minimum, p.weight, p.image_preview as image, p.manufacturer_id as manufacturer_id,
			sr.supplier_id as supplier_id, sr.name as supplier, 
			mr.name as manufacturer
			FROM ".DB_PREFIX."procurement_product pp 
			LEFT JOIN ".DB_PREFIX."procurement pt ON pt.procurement_id=pp.procurement_id 
			LEFT JOIN ".DB_PREFIX."product p ON p.product_id=pp.product_id 
			LEFT JOIN ".DB_PREFIX."product_description pd ON pd.product_id=pp.product_id 
			LEFT JOIN ".DB_PREFIX."product_to_category ptc ON ptc.product_id=pp.product_id 
			LEFT JOIN ".DB_PREFIX."product_to_supplier ptsr ON ptsr.product_id=pp.product_id 
			LEFT JOIN ".DB_PREFIX."manufacturer mr ON mr.manufacturer_id=p.manufacturer_id 
			LEFT JOIN ".DB_PREFIX."supplier sr ON sr.supplier_id=ptsr.supplier_id 
			LEFT JOIN ".DB_PREFIX."weight_class_description wcd ON wcd.weight_class_id=pp.weight_class_id 
			WHERE pp.procurement_product_id = '".(int)$procurement_product_id."' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND wcd.language_id = '" . (int)$this->config->get('config_language_id') . "'
		";

		$query = $this->db->query($sql);

		return $query->row;
	}

	public function editProduct($procurement_product_id, $data){
		// ---
			
			$sql = "SELECT * FROM `".DB_PREFIX."procurement_product` WHERE `procurement_id`='".$data['procurement_id']."' AND `product_id`='".$data['product_id']."' LIMIT 1;";

            $query = $this->db->query($sql);

            if(isset($query->num_rows) && $query->num_rows>0) {
                // ---
					$this->db->query("
						UPDATE `".DB_PREFIX."procurement_product` SET 
						purchase_price = '".$data['purchase_price']."',
						total_price = '".$data['total_price']."',
						quantity = '".$data['quantity']."',
						weight_class_id = '".$data['weight_class_id']."',
						purchased = '".$data['purchased']."',
						not_purchased = '".$data['not_purchased']."'
						WHERE procurement_product_id = '" . (int)$procurement_product_id . "'
					");
                // ---
            }
            else {
                // ---
                	$this->db->query("
						INSERT INTO `".DB_PREFIX."procurement_product` SET 
						procurement_id = '".$data['procurement_id']."',
						product_id = '".$data['product_id']."',
						purchase_price = '".$data['purchase_price']."',
						total_price = '".$data['total_price']."',
						quantity = '".$data['quantity']."',
						weight_class_id = '".$data['weight_class_id']."',
						purchased = '".$data['purchased']."',
						not_purchased = '".$data['not_purchased']."'
					");
                // ---
            }

            // Edit supplier and manufacturer
            	if( isset($data['product_id']) && isset($data['supplier_id']) ){
            		$this->db->query("
						UPDATE `".DB_PREFIX."product_to_supplier` SET 
						supplier_id = '".$data['supplier_id']."' 
						WHERE product_id = '" . (int)$data['product_id'] . "'
					");
            	}

            	if( isset($data['product_id']) && isset($data['manufacturer_id']) ){
            		$this->db->query("
						UPDATE `".DB_PREFIX."product` SET 
						manufacturer_id = '".$data['manufacturer_id']."' 
						WHERE product_id = '" . (int)$data['product_id'] . "'
					");
            	}
            // ---
		// ---
	}

	public function deleteProduct($procurement_product_id){
		$this->db->query("DELETE FROM `".DB_PREFIX."procurement_product` WHERE procurement_product_id = '" . (int)$procurement_product_id . "'");
	}


	public function getProductForAdd($product_id){
		$sql = "
			SELECT p.product_id, p.quantity, pd.name,  
			p.weight_class_id as weight_class_id, wcd.unit as weight_class,
			p.purchase_price, p.weight, p.image_preview as image, p.manufacturer_id as manufacturer_id,
			sr.supplier_id as supplier_id, sr.name as supplier, 
			mr.name as manufacturer
			FROM ".DB_PREFIX."product p 
			LEFT JOIN ".DB_PREFIX."product_description pd ON pd.product_id=p.product_id 
			LEFT JOIN ".DB_PREFIX."product_to_supplier ptsr ON ptsr.product_id=p.product_id 
			LEFT JOIN ".DB_PREFIX."manufacturer mr ON mr.manufacturer_id=p.manufacturer_id 
			LEFT JOIN ".DB_PREFIX."supplier sr ON sr.supplier_id=ptsr.supplier_id 
			LEFT JOIN ".DB_PREFIX."weight_class_description wcd ON wcd.weight_class_id=p.weight_class_id 
			WHERE p.product_id = '".(int)$product_id."' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND wcd.language_id = '" . (int)$this->config->get('config_language_id') . "'
		";

		$query = $this->db->query($sql);

		return $query->row;
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
						SELECT *
						FROM ".DB_PREFIX."product_to_category ptc 
						LEFT JOIN ".DB_PREFIX."category c ON ptc.category_id = c.category_id 
						WHERE ptc.product_id = '".$product_id."' AND c.parent_id = '".$query->row['category_id']."'
						LIMIT 1
					";

					$query = $this->db->query($sql);

					return $query->row;
				// ---
			}
			else {
				return array();
			}
		// ---
	}

	public function getSuppliers(){
		// ---
			$sql = "
				SELECT *
				FROM ".DB_PREFIX."supplier sr 
				ORDER BY sr.name
			";

			$query = $this->db->query($sql);

			return $query->rows;
		// ---
	}

	public function getManufacturers(){
		// ---
			$sql = "
				SELECT *
				FROM ".DB_PREFIX."manufacturer mr 
				ORDER BY mr.name
			";

			$query = $this->db->query($sql);

			return $query->rows;
		// ---
	}
}