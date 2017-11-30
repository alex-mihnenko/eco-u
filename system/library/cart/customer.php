<?php
namespace Cart;
class Customer {
	private $customer_id;
	private $firstname;
	private $lastname;
	private $customer_group_id;
	private $email;
	private $telephone;
	private $fax;
	private $newsletter;
	private $address_id;

	public function __construct($registry) {
		$this->config = $registry->get('config');
		$this->db = $registry->get('db');
		$this->request = $registry->get('request');
		$this->session = $registry->get('session');

		if (isset($this->session->data['customer_id'])) {
			$customer_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE customer_id = '" . (int)$this->session->data['customer_id'] . "' AND status = '1'");

			if ($customer_query->num_rows) {
				$this->customer_id = $customer_query->row['customer_id'];
				$this->firstname = $customer_query->row['firstname'];
				$this->lastname = $customer_query->row['lastname'];
				$this->customer_group_id = $customer_query->row['customer_group_id'];
				$this->email = $customer_query->row['email'];
				$this->telephone = $customer_query->row['telephone'];
				$this->fax = $customer_query->row['fax'];
				$this->newsletter = $customer_query->row['newsletter'];
				$this->address_id = $customer_query->row['address_id'];

				$this->db->query("UPDATE " . DB_PREFIX . "customer SET language_id = '" . (int)$this->config->get('config_language_id') . "', ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "' WHERE customer_id = '" . (int)$this->customer_id . "'");

				$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer_ip WHERE customer_id = '" . (int)$this->session->data['customer_id'] . "' AND ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "'");

				if (!$query->num_rows) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "customer_ip SET customer_id = '" . (int)$this->session->data['customer_id'] . "', ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "', date_added = NOW()");
				}
			} else {
				$this->logout();
			}
		}
	}

	public function login($email, $password, $override = false) {
		if ($override) {
			$customer_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE LOWER(email) = '" . $this->db->escape(utf8_strtolower($email)) . "' AND status = '1'");
		} else {
			$customer_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE LOWER(email) = '" . $this->db->escape(utf8_strtolower($email)) . "' AND (password = SHA1(CONCAT(salt, SHA1(CONCAT(salt, SHA1('" . $this->db->escape($password) . "'))))) OR password = '" . $this->db->escape(md5($password)) . "') AND status = '1' AND approved = '1'");
		}

		if ($customer_query->num_rows) {
			$this->session->data['customer_id'] = $customer_query->row['customer_id'];

			$this->customer_id = $customer_query->row['customer_id'];
			$this->firstname = $customer_query->row['firstname'];
			$this->lastname = $customer_query->row['lastname'];
			$this->customer_group_id = $customer_query->row['customer_group_id'];
			$this->email = $customer_query->row['email'];
			$this->telephone = $customer_query->row['telephone'];
			$this->fax = $customer_query->row['fax'];
			$this->newsletter = $customer_query->row['newsletter'];
			$this->address_id = $customer_query->row['address_id'];

			$this->db->query("UPDATE " . DB_PREFIX . "customer SET language_id = '" . (int)$this->config->get('config_language_id') . "', ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "' WHERE customer_id = '" . (int)$this->customer_id . "'");

			return true;
		} else {
			return false;
		}
	}
        
        public function loginByPhone($phone, $password) {

                $phone = str_replace(Array('(', ')', '+', ' ', '-'), '', $phone);
		$customer_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE `telephone` = '" . $this->db->escape($phone) . "' AND (password = SHA1(CONCAT(salt, SHA1(CONCAT(salt, SHA1('" . $this->db->escape($password) . "'))))) OR password = '" . $this->db->escape(md5($password)) . "') AND status = '1' AND approved = '1'");
                if ($customer_query->num_rows) {
			$this->session->data['customer_id'] = $customer_query->row['customer_id'];

			$this->customer_id = $customer_query->row['customer_id'];
			$this->firstname = $customer_query->row['firstname'];
			$this->lastname = $customer_query->row['lastname'];
			$this->customer_group_id = $customer_query->row['customer_group_id'];
			$this->email = $customer_query->row['email'];
			$this->telephone = $customer_query->row['telephone'];
			$this->fax = $customer_query->row['fax'];
			$this->newsletter = $customer_query->row['newsletter'];
			$this->address_id = $customer_query->row['address_id'];

			$this->db->query("UPDATE " . DB_PREFIX . "customer SET language_id = '" . (int)$this->config->get('config_language_id') . "', ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "' WHERE customer_id = '" . (int)$this->customer_id . "'");

			return true;
		} else {
			return false;
		}
	}

	public function logout() {
		unset($this->session->data['customer_id']);

		$this->customer_id = '';
		$this->firstname = '';
		$this->lastname = '';
		$this->customer_group_id = '';
		$this->email = '';
		$this->telephone = '';
		$this->fax = '';
		$this->newsletter = '';
		$this->address_id = '';
	}

	public function isLogged() {
		return $this->customer_id;
	}

	public function getId() {
		return $this->customer_id;
	}

	public function getFirstName() {
		return $this->firstname;
	}

	public function getLastName() {
		return $this->lastname;
	}

	public function getGroupId() {
		return $this->customer_group_id;
	}

	public function getEmail() {
		return $this->email;
	}

	public function getTelephone() {
		return $this->telephone;
	}

	public function getFax() {
		return $this->fax;
	}

	public function getNewsletter() {
		return $this->newsletter;
	}

	public function getAddressId() {
		return $this->address_id;
	}

	public function getBalance() {
		$query = $this->db->query("SELECT SUM(amount) AS total FROM " . DB_PREFIX . "customer_transaction WHERE customer_id = '" . (int)$this->customer_id . "'");

		return $query->row['total'];
	}

	public function getRewardPoints() {
		$query = $this->db->query("SELECT SUM(points) AS total FROM " . DB_PREFIX . "customer_reward WHERE customer_id = '" . (int)$this->customer_id . "'");

		return $query->row['total'];
	}
        
        public function setInfo($arRequest) {
            if(empty($arRequest)) return false;
            else {
                $data = Array();
                $sql = "UPDATE `" . DB_PREFIX . "customer` SET";
                
                if(isset($arRequest['firstname'])) {
                    $data['firstname'] = $this->db->escape($arRequest['firstname']);
                    $sql .= " `firstname` = '{$data['firstname']}'";
                }
                if(isset($arRequest['lastname'])) {
                    $data['lastname'] = $this->db->escape($arRequest['lastname']);
                    $sql .= " `lastname` = '{$data['lastname']}'";
                }
                if(isset($arRequest['email'])) {
                    $data['email'] = $this->db->escape($arRequest['email']);
                    $sql .= " `email` = '{$data['email']}'";
                }

                $sql .= " WHERE customer_id = '" . (int)$this->customer_id . "'";

                if(empty($data)) return false;
                elseif($this->db->query($sql)) return true;
                else return false;
            }
        }
        
        public function getByPhone($phoneFormat) {
            $query = $this->db->query("SELECT `customer_id` FROM " . DB_PREFIX . "customer WHERE `telephone` = '{$phoneFormat}'");
            return $query->row;
        }
        
        public function getSalt($cid = false) {
            if($this->isLogged()) {
                $query = $this->db->query("SELECT `salt` FROM " . DB_PREFIX . "customer WHERE `customer_id` = '{$this->customer_id}'");
                if(isset($query->row['salt'])) return $query->row['salt'];
                else return false;
            } elseif($cid) {
                $query = $this->db->query("SELECT `salt` FROM " . DB_PREFIX . "customer WHERE `customer_id` = '{$cid}'");
                if(isset($query->row['salt'])) return $query->row['salt'];
                else return false;
            } else {
                return false;
            }
        }
        
        public function setPassword($pass, $cid = false) {
            if($this->isLogged()) {
                $salt = $this->getSalt();
                $password = $this->db->escape(sha1($salt . sha1($salt . sha1($pass))));
                $query = $this->db->query("UPDATE `". DB_PREFIX . "customer` SET `password` = '{$password}' WHERE `customer_id` = {$this->customer_id}");
            } elseif($cid) {
                $salt = $this->getSalt($cid);
                $password = $this->db->escape(sha1($salt . sha1($salt . sha1($pass))));
                $query = $this->db->query("UPDATE `". DB_PREFIX . "customer` SET `password` = '{$password}' WHERE `customer_id` = {$cid}");
            } else {
                return false;
            }
        }
        
        public function getAddresses() {
            $customer_id = $this->customer_id;
            $sql = "SELECT * FROM ".DB_PREFIX."address WHERE `customer_id` = ".(int)$customer_id;
            $query = $this->db->query($sql);
            if($query->rows) {
                return $query->rows;
            } else {
                return false;
            }
        }
        
        public function setAddress($address_id, $address) {
            if(!empty($address)) { 
                if($address_id != 0) {
                    $this->db->query("UPDATE ".DB_PREFIX."address SET address_1 = '".$this->db->escape($address)."' WHERE customer_id = ".(int)$this->customer_id." AND address_id = ".(int)$address_id);
                } else {
                    $this->db->query("INSERT INTO ".DB_PREFIX."address (`customer_id`, `address_1`) VALUES(".(int)$this->customer_id.", '".$this->db->escape($address)."')");
                }
            }
        }
        public function setFirstName($firstname) {
            if(!empty($firstname)) {
                $this->db->query("UPDATE ".DB_PREFIX."customer SET firstname = '".$this->db->escape($firstname)."' WHERE customer_id = ".(int)$this->customer_id);
            }
        }
        public function setTelephone($telephone) {
            if(!empty($telephone)) {
                $this->db->query("UPDATE ".DB_PREFIX."customer SET telephone = '".$this->db->escape($telephone)."' WHERE customer_id = ".(int)$this->customer_id);
            }
        }
        public function setEmail($email) {
            if(!empty($email)) {
                $this->db->query("UPDATE ".DB_PREFIX."customer SET email = '".$this->db->escape($email)."' WHERE customer_id = ".(int)$this->customer_id);
            }
        }
        public function getPersonalDiscount($customer_id, $orders) {
            $totalCustomerOutcome = 0;
            if($orders !== false) {
                foreach($orders as $order) {
                    if($order['order_status_id'] == 5) {
                        $totalCustomerOutcome += $order['total'];
                    }
                }
            }
            $discount = intval(floor($totalCustomerOutcome/10000));
            if($discount > 10) $discount = 10;
            $this->session->data['personal_discount'] = $discount;
            return $discount;
        }
        public function getCouponDiscount() {
            if(isset($this->session->data['coupon_id'])) $coupon_id = $this->session->data['coupon_id'];
            else $coupon_id = 0;
            $sql = "SELECT * FROM " . DB_PREFIX . "coupon WHERE coupon_id = ".(int)$coupon_id." AND `status` = 1 AND `date_start` <= NOW() AND `date_end` >= NOW()";
            $query = $this->db->query($sql);
            
            return $query->row;
        }
}
