<?php
class ModelAccountUser extends Model {
	public function isAdmin($user_id) {
                $query = $this->db->query("SELECT user_group_id FROM `" . DB_PREFIX . "user` WHERE `user_id` = '" . $user_id . "' AND `status` = '1'");
                if($query->row['user_group_id'] == '1')
                {
                    return true;
                }
                else
                {
                    return false;
                }
	}
}
