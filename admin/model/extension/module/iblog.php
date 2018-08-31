<?php 
class ModelExtensionModuleiBlog extends Model {
	
  	public function install() {
		$sql = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "iblog_post` (
					`id` int(11) NOT NULL auto_increment,
					`slug` varchar(255) character set utf8 NOT NULL,
					`author_id` int(11) NOT NULL,
					`image` varchar(255) character set utf8 NOT NULL,
					`category_id` varchar(255) character set utf8 NOT NULL,
					`is_published` tinyint(1) NOT NULL,
					`is_featured` tinyint(1) NOT NULL,
					`show_author` tinyint(1) NOT NULL DEFAULT 1,
					`sort_order_post` tinyint(11) NOT NULL DEFAULT 0,
					`modified` datetime NOT NULL,
					`created` datetime NOT NULL,
					`store_id` int(11) NOT NULL DEFAULT 0,
					PRIMARY KEY  (`id`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
		$this->db->query($sql);
		$sql = 	"CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "iblog_post_description` (
					`id` int(11) NOT NULL auto_increment,
					`iblog_post_id` int(11) NOT NULL,
					`language_id` int(11) NOT NULL,
					`title` varchar(255) character set utf8 NOT NULL,
					`excerpt` text character set utf8 NOT NULL,
					`body` longtext character set utf8 NOT NULL,
					`meta_description` text NOT NULL,
					`meta_keywords` text NOT NULL,
					PRIMARY KEY  (`id`),
					KEY `select` (`iblog_post_id`,`language_id`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8";
		$this->db->query($sql);
		
		$this->db->query('CREATE TABLE IF NOT EXISTS `'. DB_PREFIX . 'iblog_categories_id` (
			`category_id` int(11) NOT NULL auto_increment,
			`parent_id` int(11) NOT NULL DEFAULT 0,
			`store_id` int(11) NOT NULL DEFAULT 0,
			`slug` varchar(255) character set utf8 NOT NULL,
			`image` varchar(255) character set utf8 NOT NULL,
			PRIMARY KEY  (`category_id`)
		)');
		
		$this->db->query('CREATE TABLE IF NOT EXISTS `'. DB_PREFIX . 'iblog_categories` (
				`id` int(11) NOT NULL auto_increment,		
				`category_id` int(11) NOT NULL,
				`name` varchar(255) character set utf8 NOT NULL,
				`description` text character set utf8 NOT NULL,
				`meta_title` text NOT NULL,
				`meta_description` text NOT NULL,
				`meta_keywords` text NOT NULL,
				`language_id` int(11) NOT NULL,
				PRIMARY KEY  (`id`),
				KEY `select` (`category_id`,`language_id`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;');
				
		$this->db->query('CREATE TABLE IF NOT EXISTS `'. DB_PREFIX . 'iblog_category_path` (
			`category_id` int(11) NOT NULL,
			`path_id` int(11) NOT NULL,
			`level` int(11) NOT NULL,
			PRIMARY KEY  (`category_id`, `path_id`))');
		
		$this->db->query("UPDATE `" . DB_PREFIX . "modification` SET status=1 WHERE `name` LIKE'%iBlog by iSenseLabs%'");
		$modifications = $this->load->controller('extension/modification/refresh');
  	} 
  
  	public function uninstall() {
		$sql = "DROP TABLE IF EXISTS `" . DB_PREFIX . "iblog_post`;";
		$this->db->query($sql);
		$sql = "DROP TABLE IF EXISTS `" . DB_PREFIX . "iblog_post_description`;";
		$this->db->query($sql);
		
		$sql = "DROP TABLE IF EXISTS `" . DB_PREFIX . "iblog_categories`;";
		$this->db->query($sql);
		
		$this->db->query("UPDATE `" . DB_PREFIX . "modification` SET status=0 WHERE `name` LIKE'%iBlog by iSenseLabs%'");
		$modifications = $this->load->controller('extension/modification/refresh');
  	}
	
	public function getTotalPosts($store_id) {
		$sql = "SELECT COUNT(DISTINCT p.id) AS total FROM " . DB_PREFIX . "iblog_post p LEFT JOIN " . DB_PREFIX . "iblog_post_description pd ON (p.id = pd.iblog_post_id)";
		$sql .= " WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.store_id = '" . (int)$store_id . "'";
		$query = $this->db->query($sql);
		return $query->row['total'];
	}
	
	public function getTotalCategories($store_id) {
				$this->db->query('CREATE TABLE IF NOT EXISTS `'. DB_PREFIX . 'iblog_categories_id` (
			`category_id` int(11) NOT NULL auto_increment,
			`parent_id` int(11) NOT NULL DEFAULT 0,
			`store_id` int(11) NOT NULL DEFAULT 0,
			`slug` varchar(255) character set utf8 NOT NULL,
			`image` varchar(255) character set utf8 NOT NULL,
			PRIMARY KEY  (`category_id`)
		)');
		
		$this->db->query('CREATE TABLE IF NOT EXISTS `'. DB_PREFIX . 'iblog_categories` (
				`id` int(11) NOT NULL auto_increment,		
				`category_id` int(11) NOT NULL,
				`name` varchar(255) character set utf8 NOT NULL,
				`description` text character set utf8 NOT NULL,
				`meta_title` text NOT NULL,
				`meta_description` text NOT NULL,
				`meta_keywords` text NOT NULL,
				`language_id` int(11) NOT NULL,
				PRIMARY KEY  (`id`),
				KEY `select` (`category_id`,`language_id`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;');
				
		$this->db->query('CREATE TABLE IF NOT EXISTS `'. DB_PREFIX . 'iblog_category_path` (
			`category_id` int(11) NOT NULL ,
			`path_id` int(11) NOT NULL,
			`level` int(11) NOT NULL,
			PRIMARY KEY  (`category_id`, `path_id`))');
		
		$sql = "SELECT COUNT(DISTINCT cid.category_id) AS total FROM " . DB_PREFIX . "iblog_categories_id cid LEFT JOIN ". DB_PREFIX . "iblog_categories ic ON cid.category_id = ic.category_id";
		$sql .= " WHERE language_id = '" . (int)$this->config->get('config_language_id') . "' AND store_id = '" . (int)$store_id . "'";
		$query = $this->db->query($sql);
		return $query->row['total'];
	}
	
	public function viewPosts($page=1, $limit=8, $store_id=0,$sort="p.id", $order="DESC") {
		if ($page) {
				$start = ($page - 1) * $limit;
		}
		
		$modify_data_type_query= $this->db->query("ALTER TABLE  `" . DB_PREFIX . "iblog_post` MODIFY category_id varchar(255) character set utf8 NOT NULL");
		
		$sort_order_exists = $this->db->query("SHOW COLUMNS FROM  `" . DB_PREFIX . "iblog_post` LIKE 'sort_order_post'")->num_rows;
		$show_author_exists = $this->db->query("SHOW COLUMNS FROM  `" . DB_PREFIX . "iblog_post` LIKE 'show_author'")->num_rows;

		if($sort_order_exists < 1) {
			$this->db->query("ALTER TABLE " .DB_PREFIX . "iblog_post ADD sort_order_post  tinyint(11) NOT NULL DEFAULT 0 AFTER created");
		}
		
		if($show_author_exists < 1) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "iblog_post` ADD show_author tinyint(1)  NOT NULL DEFAULT 1 AFTER created");
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
		if (isset($start) || isset($limit)) {
			$sql .= " LIMIT ".$start.", ".$limit;
		}	
		$query = $this->db->query($sql);
		return $query->rows;
	}
	
	public function viewCategories($page=1, $limit=8, $store_id=0,$sort="category_id", $order="DESC", $filter_name='') {
	
		if ($page) {
				$start = ($page - 1) * $limit;
		}

		$sql = "SELECT icp.category_id AS category_id, GROUP_CONCAT(ic1.name ORDER BY icp.level SEPARATOR '&nbsp;&nbsp;&gt;&nbsp;&nbsp;') AS name, cid1.parent_id FROM " . DB_PREFIX . "iblog_category_path icp LEFT JOIN " . 
		DB_PREFIX . "iblog_categories_id cid1 ON (icp.category_id = cid1.category_id) LEFT JOIN " . DB_PREFIX . "iblog_categories_id cid2 
		ON (icp.path_id = cid2.category_id) LEFT JOIN " . DB_PREFIX . "iblog_categories ic1 ON (icp.path_id = ic1.category_id) 
		LEFT JOIN " . DB_PREFIX . "iblog_categories ic2 ON (icp.category_id = ic2.category_id) WHERE 
		ic1.language_id = '" . (int)$this->config->get('config_language_id') . "' AND ic2.language_id = 
		'" . (int)$this->config->get('config_language_id') . "'";

		$sql .= "AND cid1.store_id = '" . (int)$store_id . "'"; 

		if (!empty($filter_name)) {
			$sql .= " AND ic1.name LIKE '" . $this->db->escape($filter_name) . "%'";
		}
		
		$sql .= " GROUP BY icp.category_id";
				
		$sort_data = array(
			'name',
		);	
		if (isset($sort)) {
			$sql .= " ORDER BY ic1." . $sort;	
		}
		if (isset($order)) {
			$sql .= " " . $order;
		}
		if (isset($start) || isset($limit)) {
			$sql .= " LIMIT ".$start.", ".$limit;
		}	
	
		$query = $this->db->query($sql);
		return $query->rows;
	}
	
	public function addPost($data) {
		if(!isset($data['category_id'])) {
			$data['category_id'] = array();
		}
		$this->db->query("INSERT INTO " . DB_PREFIX . "iblog_post SET slug = '" . $this->db->escape($data['slug']) . "', author_id = '" . $this->db->escape($data['author_id']) . "',  show_author = '" . $this->db->escape($data['show_author']) . "', category_id = '" . $this->db->escape(serialize($data['category_id'])) . "', is_published = '" . $this->db->escape($data['status']) . "', sort_order_post='" . $this->db->escape($data['sort_order_post']) . "', is_featured='" . !empty($data['featured']) . "', modified = NOW(), created = '" . $data['date_published'] . "', store_id='" . $this->db->escape($data['store_id']) . "'");
		
		$post_id = $this->db->getLastId();
		
		if (isset($data['image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "iblog_post SET image = '" . $this->db->escape(html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8')) . "' WHERE id = '" . (int)$post_id . "'");
		}
		
		if (isset($data['small_image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "iblog_post SET small_image = '" . $this->db->escape(html_entity_decode($data['small_image'], ENT_QUOTES, 'UTF-8')) . "' WHERE id = '" . (int)$post_id . "'");
		}
		
		$flag_generate_slug = false;
		if( empty($data['slug']) ) { $flag_generate_slug = true; }

		foreach ($data['post_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "iblog_post_description SET iblog_post_id = '" . (int)$post_id . "', language_id = '" . (int)$language_id . "', title = '" . $this->db->escape($value['title']) . "', excerpt = '" . $this->db->escape($value['excerpt']) . "', body = '" . $this->db->escape($value['body']) . "', meta_keywords = '" . $this->db->escape(preg_replace('/,$/', '', $value['meta_keyword']).',') . "', meta_description = '" . $this->db->escape($value['meta_description']) . "'");
			
			// Set slug
				if( $flag_generate_slug ){
					// ---
						$slug = $this->str2url($value['title']);
						$checkSlug = $this->db->query("SELECT * FROM " . DB_PREFIX . "iblog_post WHERE slug = '" . $this->db->escape($slug) . "'");

						if( $checkSlug->num_rows > 0 ) {
							$count = 1;

							while ( $checkSlug->num_rows > 0 ) {
								$slug_tmp = $slug . '-' . $count;
								$checkSlug =  $this->db->query("SELECT * FROM " . DB_PREFIX . "iblog_post WHERE slug = '" . $this->db->escape($slug_tmp) . "'");
								$count ++;
							}

							$slug = $slug_tmp;
						}
						

						$this->db->query("UPDATE " . DB_PREFIX . "iblog_post SET slug = '" . $this->db->escape($slug) . "' WHERE id = '" . (int)$post_id . "'");		
						$flag_generate_slug = false;
					// ---
				}
			// ---
		}

		
	}
	
	public function addCategory($data) {

		$this->db->query('CREATE TABLE IF NOT EXISTS `'. DB_PREFIX . 'iblog_categories_id` (
			`category_id` int(11) NOT NULL auto_increment,
			`parent_id` int(11) NOT NULL DEFAULT 0,
			`store_id` int(11) NOT NULL DEFAULT 0,
			`slug` varchar(255) character set utf8 NOT NULL,
			`image` varchar(255) character set utf8 NOT NULL,
			PRIMARY KEY  (`category_id`)
		)');
		
		$this->db->query('CREATE TABLE IF NOT EXISTS `'. DB_PREFIX . 'iblog_categories` (
				`id` int(11) NOT NULL auto_increment,		
				`category_id` int(11) NOT NULL,
				`name` varchar(255) character set utf8 NOT NULL,
				`description` text character set utf8 NOT NULL,
				`meta_title` text NOT NULL,
				`meta_description` text NOT NULL,
				`meta_keywords` text NOT NULL,
				`language_id` int(11) NOT NULL,
				PRIMARY KEY  (`id`),
				KEY `select` (`category_id`,`language_id`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;');
				
		$this->db->query('CREATE TABLE IF NOT EXISTS `'. DB_PREFIX . 'iblog_category_path` (
			`category_id` int(11) NOT NULL,
			`path_id` int(11) NOT NULL,
			`level` int(11) NOT NULL,
			PRIMARY KEY  (`category_id`, `path_id`)	)');
			
		$this->db->query("INSERT INTO " . DB_PREFIX . "iblog_categories_id SET parent_id = '" . $this->db->escape($data['parent_id'])  . "' , slug = '" . $data['slug']  . "' , store_id = '" . (int)$data['store_id']  . "'");
		
		$category_id = $this->db->getLastId();

		if (isset($data['image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "iblog_categories_id SET image = '" . $this->db->escape(html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8')) . "' WHERE category_id = '" . (int)$category_id . "'");
		}
		
		$flag_generate_slug = false;
		if( empty($data['slug']) ) { $flag_generate_slug = true; }

		foreach ($data['category_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "iblog_categories SET category_id  = '" . $category_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "', meta_title = '" . $this->db->escape($value['meta_title']). "', meta_description = '" . $this->db->escape($value['meta_description']). "', meta_keywords = '" . $this->db->escape($value['meta_keyword']) . "',  language_id = '" . (int)$language_id . "'");
			
			// Set slug
				if( $flag_generate_slug ){
					// ---
						$slug = $this->str2url($value['title']);
						$checkSlug = $this->db->query("SELECT * FROM " . DB_PREFIX . "iblog_categories_id WHERE slug = '" . $this->db->escape($slug) . "'");

						if( $checkSlug->num_rows > 0 ) {
							$count = 1;

							while ( $checkSlug->num_rows > 0 ) {
								$slug_tmp = $slug . '-' . $count;
								$checkSlug =  $this->db->query("SELECT * FROM " . DB_PREFIX . "iblog_categories_id WHERE slug = '" . $this->db->escape($slug_tmp) . "'");
								$count ++;
							}

							$slug = $slug_tmp;
						}
						

						$this->db->query("UPDATE " . DB_PREFIX . "iblog_categories_id SET slug = '" . $this->db->escape($slug) . "' WHERE id = '" . (int)$post_id . "'");		
						$flag_generate_slug = false;
					// ---
				}
			// ---
		}
				
		// MySQL Hierarchical Data Closure Table Pattern
		$level = 0;

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "iblog_category_path` WHERE category_id = '" . (int)$data['parent_id'] . "' ORDER BY `level` ASC");

		foreach ($query->rows as $result) {
		
			$this->db->query("INSERT INTO `" . DB_PREFIX . "iblog_category_path` SET `category_id` = '" . (int)$category_id . "', `path_id` = '" . (int)$result['path_id'] . "', `level` = '" . (int)$level . "'");

			$level++;
		}
	
		$this->db->query("INSERT INTO `" . DB_PREFIX . "iblog_category_path` SET `category_id` = '" . (int)$category_id . "', `path_id` = '" . (int)$category_id . "', `level` = '" . (int)$level . "'");

		
	}	

	
	public function deletePost($post_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "iblog_post WHERE id = '" . (int)$post_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "iblog_post_description WHERE iblog_post_id = '" . (int)$post_id . "'");
	}
	
	public function deleteCategory($category_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "iblog_categories WHERE category_id = '" . (int)$category_id . "'");
	}
	
	public function getPost($post_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "iblog_post p LEFT JOIN " . DB_PREFIX . "iblog_post_description pd ON (p.id = pd.iblog_post_id) WHERE pd.iblog_post_id = '" . (int)$post_id . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'");
				
		return $query->row;
	}
	
	public function getPostDescriptions($post_id) {
		$post_description_data = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "iblog_post_description WHERE iblog_post_id = '" . (int)$post_id . "'");
		
		foreach ($query->rows as $result) {
			$post_description_data[$result['language_id']] = array(
				'title'             => $result['title'],
				'body'     			=> $result['body'],
				'meta_keyword'     	=> $result['meta_keywords'],
				'meta_description' 	=> $result['meta_description'],
				'excerpt'           => $result['excerpt']
			);
		}
		
		return $post_description_data;
	}
	
	public function getCategoryDescriptions($category_id) {
		$category_description_data = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "iblog_categories WHERE category_id = '" . (int)$category_id . "'");
		
		foreach ($query->rows as $result) {
			$category_description_data[$result['language_id']] = array(
				'name'             => $result['name'],
				'description'      => $result['description'],
				'meta_title'       => $result['meta_title'],	
				'meta_description' => $result['meta_description'],	
				'meta_keyword' 	   => $result['meta_keywords'],	
					
			);
		}
		return $category_description_data;
	}
	
	public function getCategory($category_id, $store_id=0) {
		$query = $this->db->query("SELECT DISTINCT *, (SELECT GROUP_CONCAT(ic1.name ORDER BY level SEPARATOR '&nbsp;&nbsp;&gt;&nbsp;&nbsp;') FROM " . DB_PREFIX . "iblog_category_path icp LEFT JOIN " . DB_PREFIX . "iblog_categories ic1 ON (icp.path_id = ic1.category_id AND icp.category_id != icp.path_id) WHERE icp.category_id = cid.category_id AND ic1.language_id = '" . (int)$this->config->get('config_language_id') . "'  AND cid.store_id = '" . (int)$store_id . "' GROUP BY icp.category_id) AS path FROM " . DB_PREFIX . "iblog_categories_id cid LEFT JOIN " . DB_PREFIX . "iblog_categories ic2 ON (cid.category_id = ic2.category_id) WHERE cid.category_id = '" . (int)$category_id . "' AND ic2.language_id = '" . (int)$this->config->get('config_language_id') . "' AND cid.store_id = '" . (int)$store_id . "'");
		
		return $query->row;
	}
	
	public function editPost($post_id, $data) {
		if(!isset($data['category_id'])) {
			$data['category_id'] = array();
		}
		$this->db->query("UPDATE " . DB_PREFIX . "iblog_post SET slug = '" . $this->db->escape($data['slug']) . "', author_id = '" . $this->db->escape($data['author_id']) . "', show_author = '" . $this->db->escape($data['show_author']) . "', category_id =  '" . $this->db->escape(serialize($data['category_id'])) . "', is_published = '" . $this->db->escape($data['status']) . "', sort_order_post='" . $this->db->escape($data['sort_order_post']) . "', is_featured='" . !empty($data['featured']) . "', modified = NOW(), created = '" . $data['date_published'] . "' WHERE id = '" . (int)$post_id . "'");

		if (isset($data['image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "iblog_post SET image = '" . $this->db->escape(html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8')) . "' WHERE id = '" . (int)$post_id . "'");
		}

		if (isset($data['small_image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "iblog_post SET small_image = '" . $this->db->escape(html_entity_decode($data['small_image'], ENT_QUOTES, 'UTF-8')) . "' WHERE id = '" . (int)$post_id . "'");
		}
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "iblog_post_description WHERE iblog_post_id = '" . (int)$post_id . "'");
		
		foreach ($data['post_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "iblog_post_description SET iblog_post_id = '" . (int)$post_id . "', language_id = '" . (int)$language_id . "', title = '" . $this->db->escape($value['title']) . "', excerpt = '" . $this->db->escape($value['excerpt']) . "', body = '" . $this->db->escape($value['body']) . "', meta_keywords = '" . $this->db->escape(preg_replace('/,$/', '', $value['meta_keyword']).',') . "', meta_description = '" . $this->db->escape($value['meta_description']) . "'");
		}
	}
	
	public function editCategory($category_id, $data) {
		
		$this->db->query("UPDATE " . DB_PREFIX . "iblog_categories_id SET parent_id = '" . $this->db->escape($data['parent_id']) . "', slug = '" . $data['slug']  . "' WHERE category_id = '" . (int)$category_id . "' AND store_id = '" . (int)$data['store_id'] . "'");
		
		if (isset($data['image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "iblog_categories_id SET image = '" . $this->db->escape(html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8')) . "' WHERE category_id = '" . (int)$category_id . "'");
		}
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "iblog_categories WHERE category_id = '" . (int)$category_id . "'");
		
		foreach ($data['category_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "iblog_categories SET category_id = '" . (int)$category_id  . "', description = '" . $this->db->escape($value['description']) . "', meta_title = '" . $this->db->escape($value['meta_title']). "', meta_description = '" . $this->db->escape($value['meta_description']). "', meta_keywords = '" . $this->db->escape($value['meta_keyword']) . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");
		}
		
		// MySQL Hierarchical Data Closure Table Pattern
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "iblog_category_path` WHERE path_id = '" . (int)$category_id . "' ORDER BY level ASC");

		if ($query->rows) {
			foreach ($query->rows as $category_path) {
				// Delete the path below the current one
				$this->db->query("DELETE FROM `" . DB_PREFIX . "iblog_category_path` WHERE category_id = '" . (int)$category_path['category_id'] . "' AND level < '" . (int)$category_path['level'] . "'");

				$path = array();

				// Get the nodes new parents
				$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "iblog_category_path` WHERE category_id = '" . (int)$data['parent_id'] . "' ORDER BY level ASC");

				foreach ($query->rows as $result) {
					$path[] = $result['path_id'];
				}

				// Get whats left of the nodes current path
				$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "iblog_category_path` WHERE category_id = '" . (int)$category_path['category_id'] . "' ORDER BY level ASC");

				foreach ($query->rows as $result) {
					$path[] = $result['path_id'];
				}

				// Combine the paths with a new level
				$level = 0;

				foreach ($path as $path_id) {
					$this->db->query("REPLACE INTO `" . DB_PREFIX . "iblog_category_path` SET category_id = '" . (int)$category_path['category_id'] . "', `path_id` = '" . (int)$path_id . "', level = '" . (int)$level . "'");
					
					$level++;
				}
			}
		} else {
			// Delete the path below the current one
			$this->db->query("DELETE FROM `" . DB_PREFIX . "iblog_category_path` WHERE category_id = '" . (int)$category_id . "'");

			// Fix for records with no paths
			$level = 0;

			$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "iblog_category_path` WHERE category_id = '" . (int)$data['parent_id'] . "' ORDER BY level ASC");

			foreach ($query->rows as $result) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "iblog_category_path` SET category_id = '" . (int)$category_id . "', `path_id` = '" . (int)$result['path_id'] . "', level = '" . (int)$level . "'");

				$level++;
			}

			$this->db->query("REPLACE INTO `" . DB_PREFIX . "iblog_category_path` SET category_id = '" . (int)$category_id . "', `path_id` = '" . (int)$category_id . "', level = '" . (int)$level . "'");
		}

	}
	
	public function getLastModuleByCode($code) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "module` WHERE `code` = '" . $this->db->escape($code) . "' ORDER BY `module_id` DESC");

		return $query->rows;
	}

	public function rus2translit($string) {
        $converter = array(
            'а' => 'a',   'б' => 'b',   'в' => 'v',
            'г' => 'g',   'д' => 'd',   'е' => 'e',
            'ё' => 'e',   'ж' => 'j',   'з' => 'z',
            'и' => 'i',   'й' => 'y',   'к' => 'k',
            'л' => 'l',   'м' => 'm',   'н' => 'n',
            'о' => 'o',   'п' => 'p',   'р' => 'r',
            'с' => 's',   'т' => 't',   'у' => 'u',
            'ф' => 'f',   'х' => 'h',   'ц' => 'ts',
            'ч' => 'ch',  'ш' => 'sh',  'щ' => 'shch',
            'ь' => '',  'ы' => 'y',   'ъ' => '',
            'э' => 'e',   'ю' => 'yu',  'я' => 'ya',
            
            'А' => 'A',   'Б' => 'B',   'В' => 'V',
            'Г' => 'G',   'Д' => 'D',   'Е' => 'E',
            'Ё' => 'E',   'Ж' => 'Zh',  'З' => 'Z',
            'И' => 'I',   'Й' => 'Y',   'К' => 'K',
            'Л' => 'L',   'М' => 'M',   'Н' => 'N',
            'О' => 'O',   'П' => 'P',   'Р' => 'R',
            'С' => 'S',   'Т' => 'T',   'У' => 'U',
            'Ф' => 'F',   'Х' => 'H',   'Ц' => 'C',
            'Ч' => 'Ch',  'Ш' => 'Sh',  'Щ' => 'Sch',
            'Ь' => '',  'Ы' => 'Y',   'Ъ' => '',
            'Э' => 'E',   'Ю' => 'Yu',  'Я' => 'Ya',
            '.' => '',   ' ' => '-',  '*' => '',
            '"' => '',   '"' => '', '-' => '',
        );
        return strtr($string, $converter);
    }

    public function str2url($str) {
        $str = $this->rus2translit($str);
        $str = strtolower($str);
        $url = trim($str, "-");

        return $url;
    }
	
  }
?>