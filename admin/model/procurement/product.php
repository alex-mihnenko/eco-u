<?php
class ModelProcurementProduct extends Model {
	public function getProducts($filter = array()) {
		
		$sql = "
			SELECT pp.procurement_product_id, pp.product_id, pp.quantity, pp.purchased, pp.not_purchased, pd.name,  
			pp.weight_class_id as weight_class_id, wcd.unit as weight_class,
			pp.purchase_price, p.minimum, p.weight, p.image_preview as image,
			sr.name as supplier 
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
				$sql .= " ORDER BY " .$filter['sort']." ".$filter['order'];
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
			SELECT pp.procurement_product_id, pp.product_id, pp.quantity, pp.purchased, pp.not_purchased, pd.name,  
			pp.weight_class_id as weight_class_id, wcd.unit as weight_class,
			pp.purchase_price, p.minimum, p.weight, p.image_preview as image,
			mr.name as manufacturer,
			sr.name as supplier 
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
						quantity = '".$data['quantity']."',
						weight_class_id = '".$data['weight_class_id']."',
						purchased = '".$data['purchased']."',
						not_purchased = '".$data['not_purchased']."'
					");
                // ---
            }

		// ---
	}

	public function deleteProduct($procurement_product_id){
		$this->db->query("DELETE FROM `".DB_PREFIX."procurement_product` WHERE procurement_product_id = '" . (int)$procurement_product_id . "'");
	}


	public function getProductForAdd($product_id){
		$sql = "
			SELECT p.product_id, p.quantity, pd.name,  
			p.weight_class_id as weight_class_id, wcd.unit as weight_class,
			p.purchase_price, p.weight, p.image_preview as image,
			mr.name as manufacturer,
			sr.name as supplier 
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
}