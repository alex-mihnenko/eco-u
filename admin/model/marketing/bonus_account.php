<?php
class ModelMarketingBonusAccount extends Model {
	public function addSetting($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "bonus_account SET name = '" . $this->db->escape($data['name']) . "', code = '" . $this->db->escape($data['code']) . "', coin = '" . $this->db->escape($data['coin']) . "', rate = '" . $this->db->escape($data['rate']) . "', status = '" . (int)$data['status'] . "'");

		return $this->db->getLastId();
	}

	public function editSetting($bonus_account_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "bonus_account SET name = '" . $this->db->escape($data['name']) . "', code = '" . $this->db->escape($data['code']) . "', coin = '" . $this->db->escape($data['coin']) . "', rate = '" . $this->db->escape($data['rate']) . "', status = '" . (int)$data['status'] . "' WHERE bonus_account_id = '" . (int)$bonus_account_id . "'");
	}

	public function deletetSetting($bonus_account_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "bonus_account WHERE bonus_account_id = '" . (int)$bonus_account_id . "'");
	}

	public function getSetting($bonus_account_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "bonus_account WHERE bonus_account_id = '" . (int)$bonus_account_id . "'");

		return $query->row;
	}

	public function getSettingByCode($code) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "bonus_account WHERE code = '" . $this->db->escape($code) . "'");

		return $query->row;
	}

	public function getSettings($data = array()) {
		$implode = array();

		$order_statuses = $this->config->get('config_complete_status');

		foreach ($order_statuses as $order_status_id) {
			$implode[] = "o.order_status_id = '" . (int)$order_status_id . "'";
		}

		$sql = "SELECT * FROM " . DB_PREFIX . "bonus_account ba";

		$implode = array();

		if (!empty($data['filter_name'])) {
			$implode[] = "ba.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_code'])) {
			$implode[] = "ba.code = '" . $this->db->escape($data['filter_code']) . "'";
		}

		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}

		$sort_data = array(
			'ba.name',
			'ba.code'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY ba.name";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

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

	public function getTotalSettings($data = array()) {
		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "bonus_account";

		$implode = array();

		if (!empty($data['filter_name'])) {
			$implode[] = "name LIKE '" . $this->db->escape($data['filter_name']) . "'";
		}

		if (!empty($data['filter_code'])) {
			$implode[] = "code = '" . $this->db->escape($data['filter_code']) . "'";
		}

		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}
}