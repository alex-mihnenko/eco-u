<?php
class ModelExtensionModuleiBlog extends Model {
	
	public function getFeaturedPost() {
		$query = $this->db->query("SELECT DISTINCT *, pd.title AS title, pd.body as body, (SELECT CONCAT_WS(' ', u.firstname, u.lastname) FROM " . DB_PREFIX . "user u WHERE p.author_id = u.user_id) as author FROM " . DB_PREFIX . "iblog_post p LEFT JOIN " . DB_PREFIX . "iblog_post_description pd ON (p.id = pd.iblog_post_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.is_published = '1' AND p.is_featured = '1' AND p.store_id = '" . (int)$this->config->get('config_store_id') . "' ORDER BY created DESC LIMIT 0,1");
		
		if ($query->num_rows) {
			return array(
				'post_id'       	=> $query->row['iblog_post_id'],
				'title'             => $query->row['title'],
				'body'      		=> $query->row['body'],
				'excerpt'			=> $query->row['excerpt'],
				'meta_description'	=> $query->row['meta_description'],
				'meta_keyword'		=> $query->row['meta_keywords'],
				'image'				=> $query->row['image'],
				'small_image'		=> $query->row['small_image'],
				'status'			=> $query->row['is_published'],
				'author'			=> $query->row['author'],
				'show_author'		=> $query->row['show_author'],
				'created'			=> $query->row['created'],
				'featured'			=> $query->row['is_featured'],
				'href'				=> $this->url->link('module/iblog/post', 'post_id=' . $query->row['iblog_post_id'])
			);
		} else {
			return false;
		}	
	}

	public function getFeaturedPosts() {
		$query = $this->db->query("SELECT DISTINCT *, pd.title AS title, pd.body as body, (SELECT CONCAT_WS(' ', u.firstname, u.lastname) FROM " . DB_PREFIX . "user u WHERE p.author_id = u.user_id) as author FROM " . DB_PREFIX . "iblog_post p LEFT JOIN " . DB_PREFIX . "iblog_post_description pd ON (p.id = pd.iblog_post_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.is_published = '1' AND p.is_featured = '1' AND p.store_id = '" . (int)$this->config->get('config_store_id') . "' ORDER BY created DESC");
		
		if ($query->num_rows) {
			return $query->rows;
		} else {
			return false;
		}	
	}
	
	public function getPosts($data = array()) {
		$sql = "
			SELECT *, 
				(SELECT CONCAT_WS(' ', u.firstname, u.lastname) FROM " . DB_PREFIX . "user u WHERE p.author_id = u.user_id) as author, 
				p.image as image, 
				p.small_image as small_image, 
				u.image as author_image 
			FROM " . DB_PREFIX . "iblog_post p 
			LEFT JOIN " . DB_PREFIX . "iblog_post_description pd ON (p.id = pd.iblog_post_id)
			LEFT JOIN " . DB_PREFIX . "user u ON (p.author_id = u.user_id) 
		";

		$sql .= " WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.is_published = '1' AND p.store_id = '" . (int)$this->config->get('config_store_id') . "'"; 

		if (!empty($data['filter_name']) || !empty($data['filter_tag'])) {
			$sql .= " AND (";

			if (!empty($data['filter_name'])) {
				$implode = array();

				$words = explode(' ', trim(preg_replace('/\s\s+/', ' ', $data['filter_name'])));

				foreach ($words as $word) {
					$implode[] = "pd.title LIKE '%" . $this->db->escape($word) . "%'";
				}

				if ($implode) {
					$sql .= " " . implode(" AND ", $implode) . "";
				}

				if (!empty($data['filter_description'])) {
					$sql .= " OR pd.body LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
				}
			}
			$sql .= ")";
		}		
		
		if (isset($data['category_id']) && !empty($data['category_id'])) {
			$sql .= " AND (p.category_id LIKE '%:\"" . $this->db->escape($data['category_id']) . "\"%' )";
		}
		
		$sql .= " GROUP BY p.id";
					
		$sort_data = array(
			'pd.title',
			'pd.excerpt',
			'author',
			'p.created',
			'p.is_published'
		);	
		
		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];	
			
			if (isset($data['order']) && ($data['order'] == 'DESC')) {
				$sql .= " DESC";
			} else {
				$sql .= " ASC";
			}
		} else {
			$sql .= " ORDER BY p.sort_order_post ASC";
		}
		
		if (isset($data['start']) || isset($data['limit'])) {
			if (!isset($data['start']) || (isset($data['start']) && $data['start'] < 0)) {
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
	
	public function getPost($post_id) {	
		$query = $this->db->query("
			SELECT DISTINCT *, 
				pd.title AS title, 
				pd.excerpt as excerpt, 
				pd.body as body, 
				(SELECT CONCAT_WS(' ', u.firstname, u.lastname) FROM " . DB_PREFIX . "user u WHERE p.author_id = u.user_id) as author,
				u.image as author_image 
			FROM " . DB_PREFIX . "iblog_post p 
			LEFT JOIN " . DB_PREFIX . "iblog_post_description pd ON (p.id = pd.iblog_post_id) 
			LEFT JOIN " . DB_PREFIX . "user u ON (p.author_id = u.user_id) 
			WHERE p.id = '" . (int)$post_id . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.is_published = '1' AND p.store_id='" . (int)$this->config->get('config_store_id') . "'");
		
		if ($query->num_rows) {
			return array(
				'post_id'       	=> $query->row['iblog_post_id'],
				'title'             => $query->row['title'],
				'excerpt'      		=> $query->row['excerpt'],
				'body'      		=> $query->row['body'],
				'meta_description'	=> $query->row['meta_description'],
				'meta_keyword'		=> $query->row['meta_keywords'],
				'image'				=> $query->row['image'],
				'small_image'		=> $query->row['small_image'],
				'status'			=> $query->row['is_published'],
				'author'			=> $query->row['author'],
				'author_image'		=> $query->row['author_image'],
				'show_author'		=> $query->row['show_author'],
				'created'			=> $query->row['created'],
				'featured'			=> $query->row['is_featured'],
				'categories' 		=> $query->row['category_id'],
			);
		} else {
			return false;
		}
	}
	
	public function getCategory($category_id, $store_id=0) {
		$query = $this->db->query("SELECT DISTINCT *, (SELECT GROUP_CONCAT(ic1.name ORDER BY level SEPARATOR '&nbsp;&nbsp;&gt;&nbsp;&nbsp;') FROM " . DB_PREFIX . "iblog_category_path icp LEFT JOIN " . DB_PREFIX . "iblog_categories ic1 ON (icp.path_id = ic1.category_id AND icp.category_id != icp.path_id) WHERE icp.category_id = cid.category_id AND ic1.language_id = '" . (int)$this->config->get('config_language_id') . "'  AND cid.store_id = '" . (int)$store_id . "' GROUP BY icp.category_id) AS path FROM " . DB_PREFIX . "iblog_categories_id cid LEFT JOIN " . DB_PREFIX . "iblog_categories ic2 ON (cid.category_id = ic2.category_id) WHERE cid.category_id = '" . (int)$category_id . "' AND ic2.language_id = '" . (int)$this->config->get('config_language_id') . "' AND cid.store_id = '" . (int)$store_id . "'");

		if ($query->num_rows) {
			return array(
				'category_id'       => $query->row['category_id'],
				'title'             => $query->row['name'],
				'description'       => $query->row['description'],
				'image'     		=> $query->row['image'],
				'meta_title'   		=> $query->row['meta_title'],
				'meta_description'	=> $query->row['meta_description'],
				'meta_keyword'     => $query->row['meta_keywords'],

			);
		} else {
			return false;
		}
	}
	
	public function getCategories($parent_id=0) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "iblog_categories_id cid LEFT JOIN " . DB_PREFIX . "iblog_categories ic ON (cid.category_id = ic.category_id) WHERE cid.parent_id = '".$parent_id."' AND ic.language_id = '" . (int)$this->config->get('config_language_id') . "' AND cid.store_id = '" . (int)$this->config->get('config_store_id') . "' ORDER BY ic.name");
 
		return $query->rows;
	}

	
	public function getTotalPosts($store_id, $data=array()) {
		$sql = "SELECT COUNT(DISTINCT p.id) AS total FROM " . DB_PREFIX . "iblog_post p LEFT JOIN " . DB_PREFIX . "iblog_post_description pd ON (p.id = pd.iblog_post_id)";
		$sql .= " WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.store_id = '" . (int)$store_id . "'";
		
		if (!empty($data['filter_name']) || !empty($data['filter_tag'])) {
			$sql .= " AND (";

			if (!empty($data['filter_name'])) {
				$implode = array();

				$words = explode(' ', trim(preg_replace('/\s\s+/', ' ', $data['filter_name'])));

				foreach ($words as $word) {
					$implode[] = "pd.title LIKE '%" . $this->db->escape($word) . "%'";
				}

				if ($implode) {
					$sql .= " " . implode(" AND ", $implode) . "";
				}

				if (!empty($data['filter_description'])) {
					$sql .= " OR pd.body LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
				}
			}
			$sql .= ")";
		}
		
		if (isset($data['category_id']) && !empty($data['category_id'])) {
			$sql .= " AND (p.category_id LIKE '%:\"" . $this->db->escape($data['category_id']) . "\"%' )";
		}
		
		$query = $this->db->query($sql);
		return $query->row['total'];
	}
	
	public function sgetPosts($page=1, $limit=8, $store_id=0,$sort="p.id", $order="DESC") {
		if ($page) {
				$start = ($page - 1) * $limit;
		}
			
		$sql = "SELECT *, (SELECT CONCAT_WS(' ', u.firstname, u.lastname) FROM " . DB_PREFIX . "user u WHERE p.author_id = u.user_id) as author FROM " . DB_PREFIX . "iblog_post p LEFT JOIN " . DB_PREFIX . "iblog_post_description pd ON (p.id = pd.iblog_post_id)";
		$sql .= " WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.store_id = '" . (int)$store_id . "'"; 
		$sql .= " GROUP BY p.id";
		$sort_data = array(
			'pd.title',
			'pd.excerpt',
			'author',
			'p.created',
			'p.is_published'
		);	
		if (isset($sort)) {
			$sql .= " ORDER BY " . $sort;	
		}
		if (isset($order)) {
			$sql .= " " . $order;
		}
		if (isset($data['start']) || isset($data['limit'])) {
			$sql .= " LIMIT ".$start.", ".$limit;
		}	
		$query = $this->db->query($sql);
		return $query->rows;
	}
	
	public function getTotalPostsByTag($store_id, $data=array()) {
		$sql = "SELECT COUNT(DISTINCT p.id) AS total FROM " . DB_PREFIX . "iblog_post p LEFT JOIN " . DB_PREFIX . "iblog_post_description pd ON (p.id = pd.iblog_post_id)";
		$sql .= " WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.store_id = '" . (int)$store_id . "'";

		if (!empty($data['filter_tag'])) {
			$sql .= " AND (";

			$tag = trim(preg_replace('/\s\s+/', ' ', $data['filter_tag']));
			
			$sql .= "pd.meta_keywords LIKE '%" . $this->db->escape($tag.',') . "%'";
				
			$sql .= ")";
		}
		
		$query = $this->db->query($sql);
		return $query->row['total'];
	}
	
	public function getPostsByTag($data = array()) {
		$sql = "SELECT *, (SELECT CONCAT_WS(' ', u.firstname, u.lastname) FROM " . DB_PREFIX . "user u WHERE p.author_id = u.user_id) as author FROM " . DB_PREFIX . "iblog_post p LEFT JOIN " . DB_PREFIX . "iblog_post_description pd ON (p.id = pd.iblog_post_id)";
		$sql .= " WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.is_published = '1' AND p.store_id = '" . (int)$this->config->get('config_store_id') . "'"; 

		if (!empty($data['filter_tag'])) {
			$sql .= " AND (";

			$tag = trim(preg_replace('/\s\s+/', ' ', $data['filter_tag']));

			$sql .= "pd.meta_keywords LIKE '%" . $this->db->escape($tag.',') . "%'";
				
			$sql .= ")";
		}	
				
		$sql .= " GROUP BY p.id";
					
		$sort_data = array(
			'pd.title',
			'pd.excerpt',
			'author',
			'p.created',
			'p.is_published'
		);	
		
		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];	
			
			if (isset($data['order']) && ($data['order'] == 'DESC')) {
				$sql .= " DESC";
			} else {
				$sql .= " ASC";
			}
		} else {
			$sql .= " ORDER BY p.is_featured DESC, p.created DESC";
		}
		
		if (isset($data['start']) || isset($data['limit'])) {
			if (!isset($data['start']) || (isset($data['start']) && $data['start'] < 0)) {
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
	
	public function getOgData() {
		// Exclude for non-iBlog URLs
		if (empty($this->request->get['route']) || (!empty($this->request->get['route']) && strpos($this->request->get['route'], 'iblog') === FALSE)) {
			return false;
		}

		$this->load->model('tool/image');

		$store_id = (int)$this->config->get('config_store_id');
		$language_id = (int)$this->config->get('config_language_id');

		if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
			$server = $this->config->get('config_ssl');
		} else {
			$server = $this->config->get('config_url');
		}
		$this->load->model('setting/setting');
		$iBlogSetting = $this->model_setting_setting->getSetting('iBlog', $this->config->get('config_store_id'));
		$iBlogSetting = $iBlogSetting['iBlog'];

		if ($this->config->get('config_logo') && file_exists(DIR_IMAGE . $this->config->get('config_logo'))) {
			$logo = str_replace(' ', '%20', $this->model_tool_image->resize($this->config->get('config_logo'), 200, 200));
		} else {
			$logo = '';
		}

		$get_parameters = '';

		foreach ($this->request->get as $get_param => $get_param_value) {

			if (strpos($get_param, 'post') !== FALSE || strpos($get_param, 'iblog') !== FALSE) {
				if (!empty($get_parameters)) {
					$get_parameters .= '&';
				}

				$get_parameters .= $get_param . '=' . $get_param_value;
			}
		}

		$og = array(
			'url' 			=> str_replace(' ', '%20', $this->url->link($this->request->get['route'], $get_parameters)),
			'title' 		=> !empty($iBlogSetting['PageTitle'][$language_id]) ? $iBlogSetting['PageTitle'][$language_id] : '',
			'image' 		=> str_replace(' ', '%20', $logo),
			'site_name' 	=> $this->config->get('config_title'),
			'description' 	=> !empty($iBlogSetting['MetaDescription'][$language_id]) ? $iBlogSetting['MetaDescription'][$language_id] : ''
		);

		// 
		// Blog Category
		// 

		if (isset($this->request->get['path'])) {
			
			if (isset($this->request->get['path'])) {
	
				$path = '';
	
				$parts = explode('_', (string)$this->request->get['path']);
	
				$current_category = (int)array_pop($parts);
	
				foreach ($parts as $path_id) { 
					if (!$path) {
						$path = (int)$path_id;
					} else {
						$path .= '_' . (int)$path_id;
					}
	
					$category_info = $this->getCategory($current_category, $this->config->get('config_store_id')); 
	
				}
			} else {
				$current_category = 0;
			}
			
			$category_info = $this->getCategory($current_category, $this->config->get('config_store_id'));

			if (!empty($category_info['title'])) {
				$og['title'] = $category_info['title'];
			}

			if (!empty($category_info['description'])) {
				$og['description'] = strip_tags(html_entity_decode($category_info['description']), '');
			}

			if (!empty($category_info['image']) && file_exists(DIR_IMAGE . $category_info['image'])) {
				$og['image'] = str_replace(' ', '%20', $this->model_tool_image->resize($category_info['image'], 200, 200));
			}
		}

		// 
		// Blog Post
		//
		
		if (isset($this->request->get['post_id'])) {
			$post_info = $this->getPost($this->request->get['post_id']);

			if (!empty($post_info['title'])) {
				$og['title'] = $post_info['title'];
			}

			if (!empty($post_info['body'])) {
				$og['description'] = strip_tags(html_entity_decode($post_info['body']), '');
			}

			if (!empty($post_info['image']) && file_exists(DIR_IMAGE . $post_info['image'])) {
				$og['image'] = str_replace(' ', '%20', $this->model_tool_image->resize($post_info['image'], 200, 200));
			}
		}

		return $og;
	}

 
}
?>