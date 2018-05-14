<?php
class ModelCatalogUrlAlias extends Model {
	public function getUrlAlias($keyword,$type) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE `keyword` = '" . $this->db->escape($keyword) . "' AND `query` LIKE '%".$this->db->escape($type)."%'");

		return $query->row;
	}
}