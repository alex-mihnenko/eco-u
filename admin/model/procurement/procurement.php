<?php
class ModelProcurementProcurement extends Model {
	public function getProcurement($filter = array()) {
		
		$sql = "
			SELECT pt.procurement_id, pt.date_added 
			FROM ".DB_PREFIX."procurement pt 
			WHERE pt.procurement_id > 0
		";

		// Filter
			if (!empty($filter['filter_date_added'])) {
				$sql .= " AND pt.date_added LIKE '%" . $filter['filter_date_added'] . "%'";
			}
		// ---

		if (isset($data['limit'])) {
			$sql .= " LIMIT " . (int)$data['start'];
		}

		$query = $this->db->query($sql);

		return $query->row;
	}
}