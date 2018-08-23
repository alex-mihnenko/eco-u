<?php
class ModelCatalogProduct extends Model {
	public function updateViewed($product_id) {
		$this->db->query("UPDATE " . DB_PREFIX . "product SET viewed = (viewed + 1) WHERE product_id = '" . (int)$product_id . "'");
	}

    public function isCacheRequired() {
        $sql = "SELECT UNIX_TIMESTAMP(CURRENT_TIMESTAMP) AS time, UNIX_TIMESTAMP(MAX(date_modified)) AS modified FROM `oc_product` WHERE 1";
        $query = $this->db->query($sql);
        if(isset($query->row)) {
            $currentTime = $query->row['time'];
            $productTime = $query->row['modified'];
            $cachedTime  = $this->cache->get('latest_category_sort');
            
            if($cachedTime < $currentTime - 21600) return false;
            elseif($productTime >= $cachedTime) return false;
            else return true;
        }
        else {
            return false;
        }
    }
        
    public function getProductsAttributes($str_product_id) {
            
            $sql = "SELECT pa.product_id AS product_id, a.attribute_id AS attribute_id, a.sort_order AS sort, ad.name AS name FROM oc_attribute a JOIN oc_attribute_description ad ON a.attribute_id = ad.attribute_id JOIN oc_product_attribute pa ON pa.attribute_id = a.attribute_id WHERE a.attribute_group_id = 8 ORDER BY pa.product_id";
            $query = $this->db->query($sql);
            
            $arStickers = Array(
                26, // Наше
                25, // Vegan
                24, // RAW
                23, // Конец сезона
                22, // Сезон
                21, // Скидка
                20, // Новинка
                19, // Редкость
                18,  // Без ГМО
                17,  // Organic
                16  // Выгодно
            );
            $stickers = Array();
            
            foreach($query->rows as $row) {
                if((!isset($stickers[$row['product_id']]) 
                        || $row['sort'] < $stickers[$row['product_id']]['sort']
                    ) && in_array($row['attribute_id'], $arStickers)) {
                    $stickers[$row['product_id']] = Array(
                        'class' => $row['attribute_id'],
                        'name' => $row['name'],
                        'sort' => $row['sort']
                    );
                }
            }
            return $stickers;
	}
        
    public function getProductsByID($str_product_id) {
        $sql = "SELECT DISTINCT *, pd.customer_props3 AS customer_props3, pd.name AS name, p.image, m.name AS manufacturer, (SELECT price FROM " . DB_PREFIX . "product_special ps WHERE ps.product_id = p.product_id AND ps.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) ORDER BY ps.priority ASC, ps.price ASC LIMIT 1) AS special, (SELECT points FROM " . DB_PREFIX . "product_reward pr WHERE pr.product_id = p.product_id AND pr.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "') AS reward, (SELECT ss.name FROM " . DB_PREFIX . "stock_status ss WHERE ss.stock_status_id = p.stock_status_id AND ss.language_id = '" . (int)$this->config->get('config_language_id') . "') AS stock_status, (SELECT wcd.unit FROM " . DB_PREFIX . "weight_class_description wcd WHERE p.weight_class_id = wcd.weight_class_id AND wcd.language_id = '" . (int)$this->config->get('config_language_id') . "') AS weight_class, (SELECT lcd.unit FROM " . DB_PREFIX . "length_class_description lcd WHERE p.length_class_id = lcd.length_class_id AND lcd.language_id = '" . (int)$this->config->get('config_language_id') . "') AS length_class, (SELECT AVG(rating) AS total FROM " . DB_PREFIX . "review r1 WHERE r1.product_id = p.product_id AND r1.status = '1' GROUP BY r1.product_id) AS rating, (SELECT COUNT(*) AS total FROM " . DB_PREFIX . "review r2 WHERE r2.product_id = p.product_id AND r2.status = '1' GROUP BY r2.product_id) AS reviews, p.sort_order FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) LEFT JOIN " . DB_PREFIX . "manufacturer m ON (p.manufacturer_id = m.manufacturer_id) WHERE p.product_id IN (" . $str_product_id . ") AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";
        
        $query = $this->db->query($sql);
        $arProdAttributes = $this->getProductsAttributes($str_product_id);
        $products = Array();
        if($query->num_rows > 0) foreach($query->rows as $row) {
            $product_id = $row['product_id'];
            if(isset($arProdAttributes[$row['product_id']])) $sticker = $arProdAttributes[$row['product_id']];
            else $sticker = false;

            $compPrice = false;
            if($row['composite_price'] == 1) {
                $arCompositePrice = $this->config->get('config_composite_price');
                if($row['weight_class'] == 'кг' || $row['weight_class'] == 'л') {
                    $compPrice = $arCompositePrice;
                } elseif($row['weight_class'] == 'г' || $row['weight_class'] == 'мл') {
                    $arNewCompositePrice = Array();
                    foreach($arCompositePrice as $key => $val) {
                        $key = $key * 1000;
                        $arNewCompositePrice[$key] = $val;
                    }
                    $compPrice = $arNewCompositePrice;
                }
            }

            // Автоматическое присвоение/снятие стикера "Новинка"
            if($row['new'] == 1) {
                $sticker = Array('class' => 20, 'name' => "Новинка");
            }
            else {
                $sticker = false;
            }

            $products[$row['product_id']] = array(
                    'product_id'       => $row['product_id'],
                    'available_in_time' => $row['available_in_time'],
                    'name'             => $row['name'],
                    'description'      => $row['description'],
                    'available'        => $row['available'],
                    'description_short'      => $row['description_short'],
                    'meta_title'       => $row['meta_title'],
                    'meta_description' => $row['meta_description'],
                    'meta_keyword'     => $row['meta_keyword'],
                    'tag'              => $row['tag'],
                    'model'            => $row['model'],
                    'sku'              => $row['sku'],
                    'upc'              => $row['upc'],
                    'ean'              => $row['ean'],
                    'jan'              => $row['jan'],
                    'isbn'             => $row['isbn'],
                    'mpn'              => $row['mpn'],
                    'location'         => $row['manufacturer'],
                    'quantity'         => $row['quantity'],
                    'stock_status'     => $row['stock_status'],
                    'stock_status_id'  => $row['stock_status_id'],
                    'image'            => $row['image'],
                    'image_preview'    => $row['image_preview'],
                    'manufacturer_id'  => $row['manufacturer_id'],
                    'manufacturer'     => $row['manufacturer'],
                    'price'            => $row['price'],
                    'special_price'    => $row['special_price'],
                    'discount'          => $row['discount'],
                    'reward'           => $row['reward'],
                    'points'           => $row['points'],
                    'tax_class_id'     => $row['tax_class_id'],
                    'date_available'   => $row['date_available'],
                    'weight'           => $row['weight'],
                    'weight_class_id'  => $row['weight_class_id'],
                    'weight_variants'  => $row['weight_variants'],
                    'weight_class'     => $row['weight_class'],
                    'length'           => $row['length'],
                    'width'            => $row['width'],
                    'height'           => $row['height'],
                    'length_class_id'  => $row['length_class_id'],
                    'subtract'         => $row['subtract'],
                    'rating'           => round($row['rating']),
                    'reviews'          => $row['reviews'] ? $row['reviews'] : 0,
                    'minimum'          => $row['minimum'],
                    'sort_order'       => $row['sort_order'],
                    'status'           => $row['status'],
                    'date_added'       => $row['date_added'],
                    'date_modified'    => $row['date_modified'],
                    'viewed'           => $row['viewed'],
                    'sticker'          => $sticker,
                    'customer_props3'  => $row['customer_props3'],
                    'composite_price'  => $compPrice,
                    'shelf_life'       => $row['shelf_life']
            );
        }
        return $products;
    }
        
	public function getProduct($product_id) {
		$query = $this->db->query("SELECT DISTINCT *, pd.customer_props3 AS customer_props3, pd.name AS name, p.image, m.name AS manufacturer, (SELECT price FROM " . DB_PREFIX . "product_special ps WHERE ps.product_id = p.product_id AND ps.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) ORDER BY ps.priority ASC, ps.price ASC LIMIT 1) AS special, (SELECT points FROM " . DB_PREFIX . "product_reward pr WHERE pr.product_id = p.product_id AND pr.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "') AS reward, (SELECT ss.name FROM " . DB_PREFIX . "stock_status ss WHERE ss.stock_status_id = p.stock_status_id AND ss.language_id = '" . (int)$this->config->get('config_language_id') . "') AS stock_status, (SELECT wcd.unit FROM " . DB_PREFIX . "weight_class_description wcd WHERE p.weight_class_id = wcd.weight_class_id AND wcd.language_id = '" . (int)$this->config->get('config_language_id') . "') AS weight_class, (SELECT lcd.unit FROM " . DB_PREFIX . "length_class_description lcd WHERE p.length_class_id = lcd.length_class_id AND lcd.language_id = '" . (int)$this->config->get('config_language_id') . "') AS length_class, (SELECT AVG(rating) AS total FROM " . DB_PREFIX . "review r1 WHERE r1.product_id = p.product_id AND r1.status = '1' GROUP BY r1.product_id) AS rating, (SELECT COUNT(*) AS total FROM " . DB_PREFIX . "review r2 WHERE r2.product_id = p.product_id AND r2.status = '1' GROUP BY r2.product_id) AS reviews, p.sort_order FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) LEFT JOIN " . DB_PREFIX . "manufacturer m ON (p.manufacturer_id = m.manufacturer_id) WHERE p.product_id = '" . (int)$product_id . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = '1' AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'");
                
        $sticker = false;
        $sort = false;
        $attribute_groups = $this->getProductAttributes($product_id);

        $sql = "SELECT DISTINCT *, pd.customer_props3 AS customer_props3, pd.name AS name, p.image, m.name AS manufacturer, (SELECT price FROM " . DB_PREFIX . "product_special ps WHERE ps.product_id = p.product_id AND ps.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) ORDER BY ps.priority ASC, ps.price ASC LIMIT 1) AS special, (SELECT points FROM " . DB_PREFIX . "product_reward pr WHERE pr.product_id = p.product_id AND pr.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "') AS reward, (SELECT ss.name FROM " . DB_PREFIX . "stock_status ss WHERE ss.stock_status_id = p.stock_status_id AND ss.language_id = '" . (int)$this->config->get('config_language_id') . "') AS stock_status, (SELECT wcd.unit FROM " . DB_PREFIX . "weight_class_description wcd WHERE p.weight_class_id = wcd.weight_class_id AND wcd.language_id = '" . (int)$this->config->get('config_language_id') . "') AS weight_class, (SELECT lcd.unit FROM " . DB_PREFIX . "length_class_description lcd WHERE p.length_class_id = lcd.length_class_id AND lcd.language_id = '" . (int)$this->config->get('config_language_id') . "') AS length_class, (SELECT AVG(rating) AS total FROM " . DB_PREFIX . "review r1 WHERE r1.product_id = p.product_id AND r1.status = '1' GROUP BY r1.product_id) AS rating, (SELECT COUNT(*) AS total FROM " . DB_PREFIX . "review r2 WHERE r2.product_id = p.product_id AND r2.status = '1' GROUP BY r2.product_id) AS reviews, p.sort_order FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) LEFT JOIN " . DB_PREFIX . "manufacturer m ON (p.manufacturer_id = m.manufacturer_id) WHERE p.product_id = '" . (int)$product_id . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = '1' AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";

        foreach($attribute_groups as $group) {
            if($group['attribute_group_id'] == '8') {
                foreach($group['attribute'] as $attribute) {
                    $arStickers = Array(
                        26, // Наше
                        25, // Vegan
                        24, // RAW
                        23, // Конец сезона
                        22, // Сезон
                        21, // Скидка
                        20, // Новинка
                        19, // Редкость
                        18,  // Без ГМО
                        17,  // Organic
                        16  // Выгодно
                    );
                    if((!$sort || $attribute['sort'] < $sort) && in_array($attribute['attribute_id'], $arStickers)) {
                        $sticker = Array(
                            'class' => $attribute['attribute_id'],
                            'name' => $attribute['name']
                        );
                        $sort = $attribute['sort'];
                    } 
                }
            }
        }
                    
        $compPrice = false;

        if($query->num_rows && $query->row['composite_price'] == 1) {
            $arCompositePrice = $this->config->get('config_composite_price');
            if($query->row['weight_class'] == 'кг' || $query->row['weight_class'] == 'л') {
                $compPrice = $arCompositePrice;
            } elseif($query->row['weight_class'] == 'г' || $query->row['weight_class'] == 'мл') {
                $arNewCompositePrice = Array();
                foreach($arCompositePrice as $key => $val) {
                    $key = $key * 1000;
                    $arNewCompositePrice[$key] = $val;
                }
                $compPrice = $arNewCompositePrice;
            }
        }
                    
        if ($query->num_rows) {   
            // Автоматическое присвоение/снятие стикера "Новинка"
            if($query->row['new'] == 1) {
                $sticker = Array('class' => 20,'name' => "Новинка");
            }
            else {
                $sticker = false;
            }
                    
			return array(
				'product_id'       => $query->row['product_id'],
                'available_in_time' => $query->row['available_in_time'],
				'name'             => $query->row['name'],
				'description'      => $query->row['description'],
                'available'        => $query->row['available'],
                'description_short'      => $query->row['description_short'],
				'meta_title'       => $query->row['meta_title'],
				'meta_description' => $query->row['meta_description'],
				'meta_keyword'     => $query->row['meta_keyword'],
				'tag'              => $query->row['tag'],
				'model'            => $query->row['model'],
				'sku'              => $query->row['sku'],
				'upc'              => $query->row['upc'],
				'ean'              => $query->row['ean'],
				'jan'              => $query->row['jan'],
				'isbn'             => $query->row['isbn'],
				'mpn'              => $query->row['mpn'],
				'location'         => $query->row['manufacturer'],
				'quantity'         => $query->row['quantity'],
				'stock_status'     => $query->row['stock_status'],
                'stock_status_id'  => $query->row['stock_status_id'],
				'image'            => $query->row['image'],
                'image_preview'    => $query->row['image_preview'],
				'manufacturer_id'  => $query->row['manufacturer_id'],
				'manufacturer'     => $query->row['manufacturer'],
				'price'            => round($query->row['price']),
                'special_price'          => $query->row['special_price'],
				'discount'          => $query->row['discount'],
				'reward'           => $query->row['reward'],
				'points'           => $query->row['points'],
				'tax_class_id'     => $query->row['tax_class_id'],
				'date_available'   => $query->row['date_available'],
				'weight'           => $query->row['weight'],
				'weight_class_id'  => $query->row['weight_class_id'],
                'weight_variants'  => $query->row['weight_variants'],
                'weight_class'     => $query->row['weight_class'],
				'length'           => $query->row['length'],
				'width'            => $query->row['width'],
				'height'           => $query->row['height'],
				'length_class_id'  => $query->row['length_class_id'],
				'subtract'         => $query->row['subtract'],
				'rating'           => round($query->row['rating']),
				'reviews'          => $query->row['reviews'] ? $query->row['reviews'] : 0,
				'minimum'          => $query->row['minimum'],
				'sort_order'       => $query->row['sort_order'],
				'status'           => $query->row['status'],
				'date_added'       => $query->row['date_added'],
				'date_modified'    => $query->row['date_modified'],
				'viewed'           => $query->row['viewed'],
                'sticker'          => $sticker,
                'customer_props3'  => $query->row['customer_props3'],
                'composite_price'  => $compPrice,
                'shelf_life'       => $query->row['shelf_life']
			);
		} else {
			return false;
		}
	}

    public function getTags() {
        $query = $this->db->query("SELECT DISTINCT pd.tag AS tags FROM ".DB_PREFIX."product p, ".DB_PREFIX."product_description pd WHERE pd.tag <> '' AND (p.quantity > 0 OR p.stock_status_id <> 5) AND p.product_id = pd.product_id");
        $result = array();
        if($query->num_rows) {
            foreach($query->rows as $row) {
                $arTags = explode(',', $row['tags']);
                foreach($arTags as $tag)
                {
                    $tag = trim($tag);
                    if(empty($tag)) {
                        continue;
                    } else {
                        $tagLetter = mb_strtoupper(mb_substr($tag, 0, 1));
                        $tagFormat = $tagLetter.mb_substr($tag, 1);
                        $result[$tagFormat] = $tagFormat;
                    }
                }
            }
        }
        return $result;
    }
        
	public function getProducts($data = array()) {
            
		$sql = "SELECT p.product_id, p.stock_status_id, (SELECT AVG(rating) AS total FROM " . DB_PREFIX . "review r1 WHERE r1.product_id = p.product_id AND r1.status = '1' GROUP BY r1.product_id) AS rating, (SELECT price FROM " . DB_PREFIX . "product_discount pd2 WHERE pd2.product_id = p.product_id AND pd2.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND pd2.quantity = '1' AND ((pd2.date_start = '0000-00-00' OR pd2.date_start < NOW()) AND (pd2.date_end = '0000-00-00' OR pd2.date_end > NOW())) ORDER BY pd2.priority ASC, pd2.price ASC LIMIT 1) AS discount, (SELECT price FROM " . DB_PREFIX . "product_special ps WHERE ps.product_id = p.product_id AND ps.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) ORDER BY ps.priority ASC, ps.price ASC LIMIT 1) AS special, (SELECT name FROM " . DB_PREFIX . "stock_status pt WHERE pt.stock_status_id = p.stock_status_id) AS stock_status";
                
		if (!empty($data['filter_category_id'])) {
			if (!empty($data['filter_sub_category'])) {
				$sql .= " FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (cp.category_id = p2c.category_id)";
			} else {
				$sql .= " FROM " . DB_PREFIX . "product_to_category p2c";
			}

			if (!empty($data['filter_filter'])) {
				$sql .= " LEFT JOIN " . DB_PREFIX . "product_filter pf ON (p2c.product_id = pf.product_id) LEFT JOIN " . DB_PREFIX . "product p ON (pf.product_id = p.product_id)";
			} else {
				$sql .= " LEFT JOIN " . DB_PREFIX . "product p ON (p2c.product_id = p.product_id)";
			}
		} else {
			$sql .= " FROM " . DB_PREFIX . "product p";
		}

		$sql .= " LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";
                
		if (!empty($data['filter_category_id'])) {
			if (!empty($data['filter_sub_category'])) {
				$sql .= " AND cp.path_id = '" . (int)$data['filter_category_id'] . "'";
			} else {
				$sql .= " AND p2c.category_id = '" . (int)$data['filter_category_id'] . "'";
			}

			if (!empty($data['filter_filter'])) {
				$implode = array();

				$filters = explode(',', $data['filter_filter']);

				foreach ($filters as $filter_id) {
					$implode[] = (int)$filter_id;
				}

				$sql .= " AND pf.filter_id IN (" . implode(',', $implode) . ")";
			}
		}

		if (!empty($data['filter_name']) || !empty($data['filter_tag'])) {
			$sql .= " AND (";

			if (!empty($data['filter_name'])) {
				$implode = array();

				$words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_name'])));

				foreach ($words as $word) {
					$implode[] = "pd.name LIKE '%" . $this->db->escape($word) . "%'";
				}

				if ($implode) {
					$sql .= " " . implode(" AND ", $implode) . "";
				}

				if (!empty($data['filter_description'])) {
					$sql .= " OR pd.description LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
				}
			}

			if (!empty($data['filter_name']) && !empty($data['filter_tag'])) {
				$sql .= " OR ";
			}

			if (!empty($data['filter_tag'])) {
				$implode = array();

				$words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_tag'])));

				foreach ($words as $word) {
					$implode[] = "pd.tag LIKE '%" . $this->db->escape($word) . "%'";
				}

				if ($implode) {
					$sql .= " " . implode(" AND ", $implode) . "";
				}
			}

			if (!empty($data['filter_name'])) {
				$sql .= " OR LCASE(p.model) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
				$sql .= " OR LCASE(p.sku) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
				$sql .= " OR LCASE(p.upc) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
				$sql .= " OR LCASE(p.ean) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
				$sql .= " OR LCASE(p.jan) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
				$sql .= " OR LCASE(p.isbn) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
				$sql .= " OR LCASE(p.mpn) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
			}

			$sql .= ")";
		}

		if (!empty($data['filter_manufacturer_id'])) {
			$sql .= " AND p.manufacturer_id = '" . (int)$data['filter_manufacturer_id'] . "'";
		}

		$sql .= " GROUP BY p.product_id";

		$sort_data = array(
			'pd.name',
			'p.model',
			'p.quantity',
			'p.price',
			'rating',
			'p.sort_order',
			'p.date_added'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			if ($data['sort'] == 'pd.name' || $data['sort'] == 'p.model') {
				$sql .= " ORDER BY LCASE(" . $data['sort'] . ")";
			} elseif ($data['sort'] == 'p.price') {
				$sql .= " ORDER BY (CASE WHEN special IS NOT NULL THEN special WHEN discount IS NOT NULL THEN discount ELSE p.price END)";
			} else {
				$sql .= " ORDER BY " . $data['sort'];
			}
		} else {
			$sql .= " ORDER BY p.sort_order";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC, LCASE(pd.name) DESC";
		} else {
			$sql .= " ASC, LCASE(pd.name) ASC";
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

		$product_data = array();

		$query = $this->db->query($sql);
        
        $arProductsID = array();
        foreach($query->rows as $result) {
            $arProductsID[] = $result['product_id'];
        }


        if ( !empty($arProductsID) ) {
            $strProductsID = implode(',', $arProductsID);
            $arProductsAll = $this->getProductsByID($strProductsID);
                    
            foreach ($query->rows as $result) {
                $product_data[$result['product_id']] = $arProductsAll[$result['product_id']];
                $product_data[$result['product_id']]['stock_status_id'] = $result['stock_status_id'];
            }

            if( $product_data[$result['product_id']]['available_in_time'] == ''){
                $product_data[$result['product_id']]['available_in_time'] = $this->config->get('config_available_in_time');
            }

            /*foreach ($query->rows as $result) {
                $product_data[$result['product_id']] = $this->getProduct($result['product_id']);
                            $product_data[$result['product_id']]['stock_status_id'] = $result['stock_status_id'];
            }*/
        }

		return $product_data;
	}

    public function searchProducts($search) {
        if(!empty($search)) {
            $sql = "SELECT pd.product_id, pd.name FROM ".DB_PREFIX."product_description pd WHERE pd.name LIKE '%".$this->db->escape($search)."%' OR pd.description_short LIKE '%".$this->db->escape($search)."%' OR pd.tag LIKE '%".$this->db->escape($search)."%' GROUP BY pd.product_id ORDER BY pd.name LIMIT 0, 100";
            
            $query = $this->db->query($sql);

            $products = array();


            foreach($query->rows as $i => $row) {
                // ---
                $product = $this->getProduct($row['product_id']);

                if ( $product !== false ) {
                    // ---
                        $product['attribute_groups'] = $this->getProductAttributes($row['product_id']);

                        $product_href = $this->url->link('product/product', '&product_id=' . $row['product_id']);
                        if($_SERVER['HTTPS']) { $product['href'] = str_replace(HTTPS_SERVER, HTTPS_SERVER.'eda/', $product_href); }
                        else { $product['href'] = str_replace(HTTP_SERVER, HTTP_SERVER.'eda/', $product_href); }

                        $product['props3'] = explode(PHP_EOL, $product['customer_props3']);
                        $product['sticker_name'] = $product['sticker']['name'];
                        $product['sticker_class'] = $product['sticker']['class'];
                        

                        if ($product['image_preview']) {
                                $product['thumb'] = '/image/'.$product['image_preview'];
                        } else {
                                $product['thumb'] = '';
                        }

                        if (!$this->config->get('config_customer_price')) {
                                $product['price'] = $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
                        } else {
                                $product['price'] = false;
                        }

                        if ($product['discount'] > 0) {
                                $product['discount'] = $product['discount'];
                                $product['special'] = $this->currency->format($this->tax->calculate($product['special_price'], $product['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
                        } else {
                                $product['discount'] = false;
                                $product['special'] = false;
                        }

                        if ($this->config->get('config_tax')) {
                                $product['tax'] = $this->currency->format((float)$product['special'] ? $product['special'] : $product['price'], $this->session->data['currency']);
                        } else {
                                $product['tax'] = false;
                        }

                        if ($this->config->get('config_review_status')) {
                                $product['rating'] = (int)$product['rating'];
                        } else {
                                $product['rating'] = false;
                        }

                        if($product['special']) {
                            $product['discount_sticker'] = ceil(((float)$product['price'] - (float)$product['special'])/(float)$product['price']*100);
                        }

                        if(!$product['composite_price']) {
                            unset($product['composite_price']);
                        }
                        else{
                            $product['composite_price'] = json_encode($product['composite_price']);

                        }

                        $products[] = $product;
                    // ---
                }
                // ---
            }

            return $products;
        } else {
            return false;
        }
    }

    public function getProductsByTag($tag = '') {
        if(!empty($tag)) {
            $tag = strtolower($tag);
            $sql = "SELECT `product_id`, LOWER(`tag`) AS `tag` FROM `" . DB_PREFIX . "product_description` WHERE `tag` LIKE '%{$tag}%'";
            $query = $this->db->query($sql);
            foreach($query->rows as $i => $row) {
                $tags = explode(',', $row['tag']);
                if(!in_array($tag, $tags)) {
                    unset($query->rows[$i]);
                }
                $product = $this->getProduct($row['product_id']);
                $product['attribute_groups'] = $this->getProductAttributes($row['product_id']);

                $product_href = $this->url->link('product/product', '&product_id=' . $row['product_id']);
                if($_SERVER['HTTPS']) { $product['href'] = str_replace(HTTPS_SERVER, HTTPS_SERVER.'eda/', $product_href); }
                else { $product['href'] = str_replace(HTTP_SERVER, HTTP_SERVER.'eda/', $product_href); }

                $product['props3'] = explode(PHP_EOL, $product['customer_props3']);
                $product['sticker_name'] = $product['sticker']['name'];
                $product['sticker_class'] = $product['sticker']['class'];
                if($product['special']) {
                    $product['discount_sticker'] = ceil(((float)$product['price'] - (float)$product['special'])/(float)$product['price']*100);
                }
                $products[] = $product;
            }
            return $products;
        } else {
            return false;
        }
    }
    
    public function getAsortProducts($letter, $not_include) {
        if(!empty($letter)) {
            $nInclude = implode(', ', $not_include);
            $sql = "SELECT `product_id` FROM " . DB_PREFIX . "product_description pc WHERE `name` LIKE '{$letter}%' AND `product_id` NOT IN (".$nInclude.") AND product_id = (SELECT product_id FROM ".DB_PREFIX."product pd WHERE pc.product_id = pd.product_id AND status != 0 AND (stock_status_id != 5 || quantity > 0)) ORDER BY pc.name LIMIT 0, 10000";
            $query = $this->db->query($sql);
            foreach($query->rows as $i => $row) {
                $product = $this->getProduct($row['product_id']);
                $product['attribute_groups'] = $this->getProductAttributes($row['product_id']);
                
                $product_href = $this->url->link('product/product', '&product_id=' . $row['product_id']);
                if($_SERVER['HTTPS']) { $product['href'] = str_replace(HTTPS_SERVER, HTTPS_SERVER.'eda/', $product_href); }
                else { $product['href'] = str_replace(HTTP_SERVER, HTTP_SERVER.'eda/', $product_href); }

                $product['props3'] = explode(PHP_EOL, $product['customer_props3']);
                $product['sticker_name'] = $product['sticker']['name'];
                $product['sticker_class'] = $product['sticker']['class'];
                $product['edit_link'] = '/admin?route=catalog/product/edit&token='.$this->session->data['token'].'&product_id='.$product['product_id'];
                if ($product['image_preview']) {
                    $product['thumb'] = '/image/'.$product['image_preview'];
                } else {
                    $this->load->model('tool/image');
                    $product['thumb'] = $this->model_tool_image->resize('eco_logo.png', $this->config->get($this->config->get('config_theme') . '_image_product_width'), $this->config->get($this->config->get('config_theme') . '_image_product_height'));
                }
                if($product['special']) {
                    $product['discount_sticker'] = ceil(((float)$product['price'] - (float)$product['special'])/(float)$product['price']*100);
                }
                if(!$product['composite_price']) {
                    unset($product['composite_price']);
                }
                $products[] = $product;
            }
            return $products;
        } else {
            return false;
        }
    }
    
    public function getCatsortProducts($category_id, $not_include, $parent=0) {
        if(!empty($category_id)) {
            $nInclude = implode(', ', $not_include);

            if( $category_id == 'new' ){
                $sql = "SELECT `product_id`, (SELECT `name` FROM ".DB_PREFIX."product_description pn WHERE pn.product_id = pc.product_id) AS `name` FROM " . DB_PREFIX . "product_to_category pc WHERE `category_id` = ".(int)$parent." AND `product_id` NOT IN (".$nInclude.") AND product_id = (SELECT product_id FROM ".DB_PREFIX."product pd WHERE pc.product_id = pd.product_id AND new = 1 AND status != 0 AND (stock_status_id != 5 || quantity > 0)) ORDER BY name LIMIT 0, 10000";
            }
            else if( $category_id == 'sale' ){
                $sql = "SELECT `product_id`, (SELECT `name` FROM ".DB_PREFIX."product_description pn WHERE pn.product_id = pc.product_id) AS `name` FROM " . DB_PREFIX . "product_to_category pc WHERE `category_id` = ".(int)$parent." AND `product_id` NOT IN (".$nInclude.") AND product_id = (SELECT product_id FROM ".DB_PREFIX."product pd WHERE pc.product_id = pd.product_id AND special_price<> 0 AND status != 0 AND (stock_status_id != 5 || quantity > 0)) ORDER BY name LIMIT 0, 10000";
            }
            else {
                $sql = "SELECT `product_id`, (SELECT `name` FROM ".DB_PREFIX."product_description pn WHERE pn.product_id = pc.product_id) AS `name` FROM " . DB_PREFIX . "product_to_category pc WHERE `category_id` = ".(int)$category_id." AND `product_id` NOT IN (".$nInclude.") AND product_id = (SELECT product_id FROM ".DB_PREFIX."product pd WHERE pc.product_id = pd.product_id AND status != 0 AND (stock_status_id != 5 || quantity > 0)) ORDER BY name LIMIT 0, 10000";
            }

            $query = $this->db->query($sql);

            $products = array();
            
            foreach($query->rows as $i => $row) {
                // ---
                    $product = $this->getProduct($row['product_id']);

                    if( $product != false ){
                        // ---
                            $product['attribute_groups'] = $this->getProductAttributes($row['product_id']);

                            $product_href = $this->url->link('product/product', '&product_id=' . $row['product_id']);
                            if($_SERVER['HTTPS']) { $product['href'] = str_replace(HTTPS_SERVER, HTTPS_SERVER.'eda/', $product_href); }
                            else { $product['href'] = str_replace(HTTP_SERVER, HTTP_SERVER.'eda/', $product_href); }

                            $product['props3'] = explode(PHP_EOL, $product['customer_props3']);
                            $product['sticker_name'] = $product['sticker']['name'];
                            $product['sticker_class'] = $product['sticker']['class'];
                            

                            if ($product['image_preview']) {
                                    $product['thumb'] = '/image/'.$product['image_preview'];
                            } else {
                                    $product['thumb'] = '';
                            }

                            if (!$this->config->get('config_customer_price')) {
                                    $product['price'] = $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
                            } else {
                                    $product['price'] = false;
                            }

                            if ($product['discount'] > 0) {
                                    $product['discount'] = $product['discount'];
                                    $product['special'] = $this->currency->format($this->tax->calculate($product['special_price'], $product['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
                            } else {
                                    $product['discount'] = false;
                                    $product['special'] = false;
                            }

                            if ($this->config->get('config_tax')) {
                                    $product['tax'] = $this->currency->format((float)$product['special'] ? $product['special'] : $product['price'], $this->session->data['currency']);
                            } else {
                                    $product['tax'] = false;
                            }

                            if ($this->config->get('config_review_status')) {
                                    $product['rating'] = (int)$product['rating'];
                            } else {
                                    $product['rating'] = false;
                            }

                            if($product['special']) {
                                $product['discount_sticker'] = ceil(((float)$product['price'] - (float)$product['special'])/(float)$product['price']*100);
                            }

                            if(!$product['composite_price']) {
                                unset($product['composite_price']);
                            }
                            else{
                                $product['composite_price'] = json_encode($product['composite_price']);

                            }

                            $products[] = $product;
                        // ---
                    }
                // ---
            }

            return $products;
        } else {
            return false;
        }
    }
        
	public function getProductSpecials($data = array()) {
		$sql = "SELECT DISTINCT ps.product_id, (SELECT AVG(rating) FROM " . DB_PREFIX . "review r1 WHERE r1.product_id = ps.product_id AND r1.status = '1' GROUP BY r1.product_id) AS rating FROM " . DB_PREFIX . "product_special ps LEFT JOIN " . DB_PREFIX . "product p ON (ps.product_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND ps.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) GROUP BY ps.product_id";

		$sort_data = array(
			'pd.name',
			'p.model',
			'ps.price',
			'rating',
			'p.sort_order'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			if ($data['sort'] == 'pd.name' || $data['sort'] == 'p.model') {
				$sql .= " ORDER BY LCASE(" . $data['sort'] . ")";
			} else {
				$sql .= " ORDER BY " . $data['sort'];
			}
		} else {
			$sql .= " ORDER BY p.sort_order";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC, LCASE(pd.name) DESC";
		} else {
			$sql .= " ASC, LCASE(pd.name) ASC";
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

		$product_data = array();

		$query = $this->db->query($sql);

		foreach ($query->rows as $result) {
			$product_data[$result['product_id']] = $this->getProduct($result['product_id']);
		}

		return $product_data;
	}

	public function getLatestProducts($limit) {
		$product_data = $this->cache->get('product.latest.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $this->config->get('config_customer_group_id') . '.' . (int)$limit);

		if (!$product_data) {
			$query = $this->db->query("SELECT p.product_id FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' ORDER BY p.date_added DESC LIMIT " . (int)$limit);

			foreach ($query->rows as $result) {
				$product_data[$result['product_id']] = $this->getProduct($result['product_id']);
			}

			$this->cache->set('product.latest.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $this->config->get('config_customer_group_id') . '.' . (int)$limit, $product_data);
		}

		return $product_data;
	}

	public function getPopularProducts($limit) {
		$product_data = $this->cache->get('product.popular.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $this->config->get('config_customer_group_id') . '.' . (int)$limit);
	
		if (!$product_data) {
			$query = $this->db->query("SELECT p.product_id FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' ORDER BY p.viewed DESC, p.date_added DESC LIMIT " . (int)$limit);
	
			foreach ($query->rows as $result) {
				$product_data[$result['product_id']] = $this->getProduct($result['product_id']);
			}
			
			$this->cache->set('product.popular.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $this->config->get('config_customer_group_id') . '.' . (int)$limit, $product_data);
		}
		
		return $product_data;
	}

	public function getBestSellerProducts($limit) {
		$product_data = $this->cache->get('product.bestseller.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $this->config->get('config_customer_group_id') . '.' . (int)$limit);

		if (!$product_data) {
			$product_data = array();

			$query = $this->db->query("SELECT op.product_id, SUM(op.quantity) AS total FROM " . DB_PREFIX . "order_product op LEFT JOIN `" . DB_PREFIX . "order` o ON (op.order_id = o.order_id) LEFT JOIN `" . DB_PREFIX . "product` p ON (op.product_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE o.order_status_id > '0' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' GROUP BY op.product_id ORDER BY total DESC LIMIT " . (int)$limit);

			foreach ($query->rows as $result) {
				$product_data[$result['product_id']] = $this->getProduct($result['product_id']);
			}

			$this->cache->set('product.bestseller.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $this->config->get('config_customer_group_id') . '.' . (int)$limit, $product_data);
		}

		return $product_data;
	}

	public function getProductAttributes($product_id) {
		$product_attribute_group_data = array();

		$product_attribute_group_query = $this->db->query("SELECT ag.attribute_group_id, agd.name FROM " . DB_PREFIX . "product_attribute pa LEFT JOIN " . DB_PREFIX . "attribute a ON (pa.attribute_id = a.attribute_id) LEFT JOIN " . DB_PREFIX . "attribute_group ag ON (a.attribute_group_id = ag.attribute_group_id) LEFT JOIN " . DB_PREFIX . "attribute_group_description agd ON (ag.attribute_group_id = agd.attribute_group_id) WHERE pa.product_id = '" . (int)$product_id . "' AND agd.language_id = '" . (int)$this->config->get('config_language_id') . "' GROUP BY ag.attribute_group_id ORDER BY ag.sort_order, agd.name");

		foreach ($product_attribute_group_query->rows as $product_attribute_group) {
			$product_attribute_data = array();

			$product_attribute_query = $this->db->query("SELECT a.attribute_id, ad.name, pa.text, a.sort_order FROM " . DB_PREFIX . "product_attribute pa LEFT JOIN " . DB_PREFIX . "attribute a ON (pa.attribute_id = a.attribute_id) LEFT JOIN " . DB_PREFIX . "attribute_description ad ON (a.attribute_id = ad.attribute_id) WHERE pa.product_id = '" . (int)$product_id . "' AND a.attribute_group_id = '" . (int)$product_attribute_group['attribute_group_id'] . "' AND ad.language_id = '" . (int)$this->config->get('config_language_id') . "' AND pa.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY a.sort_order, ad.name");

			foreach ($product_attribute_query->rows as $product_attribute) {
				$product_attribute_data[] = array(
					'attribute_id' => $product_attribute['attribute_id'],
					'name'         => $product_attribute['name'],
					'text'         => $product_attribute['text'],
                                        'sort'         => $product_attribute['sort_order']
				);
			}

			$product_attribute_group_data[] = array(
				'attribute_group_id' => $product_attribute_group['attribute_group_id'],
				'name'               => $product_attribute_group['name'],
				'attribute'          => $product_attribute_data
			);
		}

		return $product_attribute_group_data;
	}

	public function getProductOptions($product_id) {
		$product_option_data = array();

		$product_option_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option po LEFT JOIN `" . DB_PREFIX . "option` o ON (po.option_id = o.option_id) LEFT JOIN " . DB_PREFIX . "option_description od ON (o.option_id = od.option_id) WHERE po.product_id = '" . (int)$product_id . "' AND od.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY o.sort_order");

		foreach ($product_option_query->rows as $product_option) {
			$product_option_value_data = array();

			$product_option_value_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_id = '" . (int)$product_id . "' AND pov.product_option_id = '" . (int)$product_option['product_option_id'] . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY ov.sort_order");

			foreach ($product_option_value_query->rows as $product_option_value) {
				$product_option_value_data[] = array(
					'product_option_value_id' => $product_option_value['product_option_value_id'],
					'option_value_id'         => $product_option_value['option_value_id'],
					'name'                    => $product_option_value['name'],
					'image'                   => $product_option_value['image'],
					'quantity'                => $product_option_value['quantity'],
					'subtract'                => $product_option_value['subtract'],
					'price'                   => $product_option_value['price'],
					'price_prefix'            => $product_option_value['price_prefix'],
					'weight'                  => $product_option_value['weight'],
					'weight_prefix'           => $product_option_value['weight_prefix']
				);
			}

			$product_option_data[] = array(
				'product_option_id'    => $product_option['product_option_id'],
				'product_option_value' => $product_option_value_data,
				'option_id'            => $product_option['option_id'],
				'name'                 => $product_option['name'],
				'type'                 => $product_option['type'],
				'value'                => $product_option['value'],
				'required'             => $product_option['required']
			);
		}

		return $product_option_data;
	}

	public function getProductDiscounts($product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$product_id . "' AND customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND quantity > 1 AND ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW())) ORDER BY quantity ASC, priority ASC, price ASC");

		return $query->rows;
	}

	public function getProductImages($product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_image WHERE product_id = '" . (int)$product_id . "' ORDER BY sort_order ASC");

		return $query->rows;
	}

	public function getProductRelated($product_id) {	
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_related pr LEFT JOIN " . DB_PREFIX . "product p ON (pr.related_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE pr.product_id = '" . (int)$product_id . "' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'");

        if( empty($query->rows) ){
            $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product p WHERE p.status='1' ORDER BY RAND() LIMIT 8;");
        }
        

        $products = array();

        foreach($query->rows as $i => $row) {
            $product = $this->getProduct($row['product_id']);

            if( $product != false ){
                // ---
                    $product['attribute_groups'] = $this->getProductAttributes($row['product_id']);

                    $product_href = $this->url->link('product/product', '&product_id=' . $row['product_id']);
                    if($_SERVER['HTTPS']) { $product['href'] = str_replace(HTTPS_SERVER, HTTPS_SERVER.'eda/', $product_href); }
                    else { $product['href'] = str_replace(HTTP_SERVER, HTTP_SERVER.'eda/', $product_href); }

                    $product['props3'] = explode(PHP_EOL, $product['customer_props3']);
                    $product['sticker_name'] = $product['sticker']['name'];
                    $product['sticker_class'] = $product['sticker']['class'];
                    

                    if ($product['image_preview']) {
                            $product['thumb'] = '/image/'.$product['image_preview'];
                    } else {
                            $product['thumb'] = '';
                    }

                    if (!$this->config->get('config_customer_price')) {
                            $product['price'] = $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
                    } else {
                            $product['price'] = false;
                    }

                    if ($product['discount'] > 0) {
                            $product['discount'] = $product['discount'];
                            $product['special'] = $this->currency->format($this->tax->calculate($product['special_price'], $product['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
                    } else {
                            $product['discount'] = false;
                            $product['special'] = false;
                    }

                    if ($this->config->get('config_tax')) {
                            $product['tax'] = $this->currency->format((float)$product['special'] ? $product['special'] : $product['price'], $this->session->data['currency']);
                    } else {
                            $product['tax'] = false;
                    }

                    if ($this->config->get('config_review_status')) {
                            $product['rating'] = (int)$product['rating'];
                    } else {
                            $product['rating'] = false;
                    }

                    if($product['special']) {
                        $product['discount_sticker'] = ceil(((float)$product['price'] - (float)$product['special'])/(float)$product['price']*100);
                    }

                    if(!$product['composite_price']) {
                        unset($product['composite_price']);
                    }
                    else{
                        $product['composite_price'] = json_encode($product['composite_price']);

                    }

                    $products[] = $product;
                // ---
            }
        }
        
        return $products;

	}

	public function getProductLayoutId($product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_layout WHERE product_id = '" . (int)$product_id . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "'");

		if ($query->num_rows) {
			return $query->row['layout_id'];
		} else {
			return 0;
		}
	}

	public function getCategories($product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "'");

		return $query->rows;
	}

    public function getTotalCategoryProducts($category_id) {
        $sql = "SELECT DISTINCT COUNT(p.product_id) AS total FROM ".DB_PREFIX."product p WHERE p.status = 1 AND (p.quantity > 0 OR (p.quantity <= 0 AND p.stock_status_id <> 5)) AND p.product_id IN (SELECT pc.product_id FROM ".DB_PREFIX."product_to_category pc WHERE pc.category_id = ".(int)$category_id.")";
        $query = $this->db->query($sql);
        if(isset($query->row['total'])) {
            return (int)$query->row['total'];
        } else {
            return 0;
        }
    }
    
    public function getTotalLiteralProducts($literal) {
        $sql = "SELECT DISTINCT COUNT(p.product_id) AS total FROM ".DB_PREFIX."product p WHERE p.status = 1 AND (p.quantity > 0 OR (p.quantity <= 0 AND p.stock_status_id <> 5)) AND p.product_id IN (SELECT pc.product_id FROM ".DB_PREFIX."product_description pc WHERE pc.name LIKE '".$this->db->escape($literal)."%')";
        $query = $this->db->query($sql);
        if(isset($query->row['total'])) {
            return (int)$query->row['total'];
        } else {
            return 0;
        }
    }
        
	public function getTotalProducts($data = array()) {
		$sql = "SELECT COUNT(DISTINCT p.product_id) AS total";

		if (!empty($data['filter_category_id'])) {
			if (!empty($data['filter_sub_category'])) {
				$sql .= " FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (cp.category_id = p2c.category_id)";
			} else {
				$sql .= " FROM " . DB_PREFIX . "product_to_category p2c";
			}

			if (!empty($data['filter_filter'])) {
				$sql .= " LEFT JOIN " . DB_PREFIX . "product_filter pf ON (p2c.product_id = pf.product_id) LEFT JOIN " . DB_PREFIX . "product p ON (pf.product_id = p.product_id)";
			} else {
				$sql .= " LEFT JOIN " . DB_PREFIX . "product p ON (p2c.product_id = p.product_id)";
			}
		} else {
			$sql .= " FROM " . DB_PREFIX . "product p";
		}

		$sql .= " LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";

		if (!empty($data['filter_category_id'])) {
			if (!empty($data['filter_sub_category'])) {
				$sql .= " AND cp.path_id = '" . (int)$data['filter_category_id'] . "'";
			} else {
				$sql .= " AND p2c.category_id = '" . (int)$data['filter_category_id'] . "'";
			}

			if (!empty($data['filter_filter'])) {
				$implode = array();

				$filters = explode(',', $data['filter_filter']);

				foreach ($filters as $filter_id) {
					$implode[] = (int)$filter_id;
				}

				$sql .= " AND pf.filter_id IN (" . implode(',', $implode) . ")";
			}
		}

		if (!empty($data['filter_name']) || !empty($data['filter_tag'])) {
			$sql .= " AND (";

			if (!empty($data['filter_name'])) {
				$implode = array();

				$words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_name'])));

				foreach ($words as $word) {
					$implode[] = "pd.name LIKE '%" . $this->db->escape($word) . "%'";
				}

				if ($implode) {
					$sql .= " " . implode(" AND ", $implode) . "";
				}

				if (!empty($data['filter_description'])) {
					$sql .= " OR pd.description LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
				}
			}

			if (!empty($data['filter_name']) && !empty($data['filter_tag'])) {
				$sql .= " OR ";
			}

			if (!empty($data['filter_tag'])) {
				$implode = array();

				$words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_tag'])));

				foreach ($words as $word) {
					$implode[] = "pd.tag LIKE '%" . $this->db->escape($word) . "%'";
				}

				if ($implode) {
					$sql .= " " . implode(" AND ", $implode) . "";
				}
			}

			if (!empty($data['filter_name'])) {
				$sql .= " OR LCASE(p.model) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
				$sql .= " OR LCASE(p.sku) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
				$sql .= " OR LCASE(p.upc) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
				$sql .= " OR LCASE(p.ean) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
				$sql .= " OR LCASE(p.jan) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
				$sql .= " OR LCASE(p.isbn) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
				$sql .= " OR LCASE(p.mpn) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
			}

			$sql .= ")";
		}

		if (!empty($data['filter_manufacturer_id'])) {
			$sql .= " AND p.manufacturer_id = '" . (int)$data['filter_manufacturer_id'] . "'";
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getProfile($product_id, $recurring_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "recurring r JOIN " . DB_PREFIX . "product_recurring pr ON (pr.recurring_id = r.recurring_id AND pr.product_id = '" . (int)$product_id . "') WHERE pr.recurring_id = '" . (int)$recurring_id . "' AND status = '1' AND pr.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "'");

		return $query->row;
	}

	public function getProfiles($product_id) {
		$query = $this->db->query("SELECT rd.* FROM " . DB_PREFIX . "product_recurring pr JOIN " . DB_PREFIX . "recurring_description rd ON (rd.language_id = " . (int)$this->config->get('config_language_id') . " AND rd.recurring_id = pr.recurring_id) JOIN " . DB_PREFIX . "recurring r ON r.recurring_id = rd.recurring_id WHERE pr.product_id = " . (int)$product_id . " AND status = '1' AND pr.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' ORDER BY sort_order ASC");

		return $query->rows;
	}

	public function getTotalProductSpecials() {
		$query = $this->db->query("SELECT COUNT(DISTINCT ps.product_id) AS total FROM " . DB_PREFIX . "product_special ps LEFT JOIN " . DB_PREFIX . "product p ON (ps.product_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND ps.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW()))");

		if (isset($query->row['total'])) {
			return $query->row['total'];
		} else {
			return 0;
		}
	}
        
    // Получить товары с заполненным полем "спеццена"
    public function getProductsSpecialPrice() {
        $sql = "SELECT * FROM `" . DB_PREFIX . "product` WHERE `special_price` <> 0 AND `status` <> 0";
        $query = $this->db->query($sql);
        
        $products = Array();
        foreach($query->rows as $product) {
            $arProduct = $this->getProduct($product['product_id']);
            $arProduct['special_price'] = $product['special_price'];
            $products[] = $arProduct;
        }
        
        return $products;
    }
   
    // Получить любимые товары
    public function getProductsPreferable() {
        
        $customer_id = $this->customer->getId();

        $sql = "SELECT order_id FROM `".DB_PREFIX."order` WHERE customer_id = '{$customer_id}'";
        $query = $this->db->query($sql);
        
        $products = array();

        if( !empty($query->rows)){
            // ---
                $ordersStr = 'order_id IN (';
                for($i = 0; $i<count($query->rows); $i++) {
                    $ordersStr .= $query->rows[$i]['order_id'];
                    if($i < count($query->rows)-1) $ordersStr .= ', '; 
                }
                $ordersStr .= ')';
            
                $sql = "SELECT product_id FROM ".DB_PREFIX."order_product WHERE {$ordersStr} GROUP BY product_id;";
                $query = $this->db->query($sql);

                foreach($query->rows as $i => $row) {
                    $product = $this->getProduct($row['product_id']);
                    $product['attribute_groups'] = $this->getProductAttributes($row['product_id']);

                    $product_href = $this->url->link('product/product', '&product_id=' . $row['product_id']);
                    if($_SERVER['HTTPS']) { $product['href'] = str_replace(HTTPS_SERVER, HTTPS_SERVER.'eda/', $product_href); }
                    else { $product['href'] = str_replace(HTTP_SERVER, HTTP_SERVER.'eda/', $product_href); }

                    $product['props3'] = explode(PHP_EOL, $product['customer_props3']);
                    $product['sticker_name'] = $product['sticker']['name'];
                    $product['sticker_class'] = $product['sticker']['class'];
                    

                    if ($product['image_preview']) {
                            $product['thumb'] = '/image/'.$product['image_preview'];
                    } else {
                            $product['thumb'] = '';
                    }

                    if (!$this->config->get('config_customer_price')) {
                            $product['price'] = $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
                    } else {
                            $product['price'] = false;
                    }

                    if ($product['discount'] > 0) {
                            $product['discount'] = $product['discount'];
                            $product['special'] = $this->currency->format($this->tax->calculate($product['special_price'], $product['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
                    } else {
                            $product['discount'] = false;
                            $product['special'] = false;
                    }

                    if ($this->config->get('config_tax')) {
                            $product['tax'] = $this->currency->format((float)$product['special'] ? $product['special'] : $product['price'], $this->session->data['currency']);
                    } else {
                            $product['tax'] = false;
                    }

                    if ($this->config->get('config_review_status')) {
                            $product['rating'] = (int)$product['rating'];
                    } else {
                            $product['rating'] = false;
                    }

                    if($product['special']) {
                        $product['discount_sticker'] = ceil(((float)$product['price'] - (float)$product['special'])/(float)$product['price']*100);
                    }

                    if(!$product['composite_price']) {
                        unset($product['composite_price']);
                    }
                    else{
                        $product['composite_price'] = json_encode($product['composite_price']);

                    }

                    $products[] = $product;
                } 
            // ---
        }
        
        return $products;
    }



    // --- //



    public function getProductsOrigin($data = array()) {
        $sql = "SELECT p.product_id, (SELECT AVG(rating) AS total FROM " . DB_PREFIX . "review r1 WHERE r1.product_id = p.product_id AND r1.status = '1' GROUP BY r1.product_id) AS rating, (SELECT price FROM " . DB_PREFIX . "product_discount pd2 WHERE pd2.product_id = p.product_id AND pd2.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND pd2.quantity = '1' AND ((pd2.date_start = '0000-00-00' OR pd2.date_start < NOW()) AND (pd2.date_end = '0000-00-00' OR pd2.date_end > NOW())) ORDER BY pd2.priority ASC, pd2.price ASC LIMIT 1) AS discount, (SELECT price FROM " . DB_PREFIX . "product_special ps WHERE ps.product_id = p.product_id AND ps.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) ORDER BY ps.priority ASC, ps.price ASC LIMIT 1) AS special";

        if (!empty($data['filter_category_id'])) {
            if (!empty($data['filter_sub_category'])) {
                $sql .= " FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (cp.category_id = p2c.category_id)";
            } else {
                $sql .= " FROM " . DB_PREFIX . "product_to_category p2c";
            }

            if (!empty($data['filter_filter'])) {
                $sql .= " LEFT JOIN " . DB_PREFIX . "product_filter pf ON (p2c.product_id = pf.product_id) LEFT JOIN " . DB_PREFIX . "product p ON (pf.product_id = p.product_id)";
            } else {
                $sql .= " LEFT JOIN " . DB_PREFIX . "product p ON (p2c.product_id = p.product_id)";
            }
        } else {
            $sql .= " FROM " . DB_PREFIX . "product p";
        }

        $sql .= " LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";

        if (!empty($data['categories'])) {
            $categories = explode(',', $data['categories']);

            $sql .= " AND (";
            $count = 0;
            $operand = "";

            foreach ($categories as $key => $val) {
                if( $count > 0 ) { $operand = " OR "; }
                $sql .= $operand."p2c.category_id = ".$val;
                $count++;
            }
            
            $sql .= ")";
        }


        if (!empty($data['filter_category_id'])) {
            if (!empty($data['filter_sub_category'])) {
                $sql .= " AND cp.path_id = '" . (int)$data['filter_category_id'] . "'";
            } else {
                $sql .= " AND p2c.category_id = '" . (int)$data['filter_category_id'] . "'";
            }

            if (!empty($data['filter_filter'])) {
                $implode = array();

                $filters = explode(',', $data['filter_filter']);

                foreach ($filters as $filter_id) {
                    $implode[] = (int)$filter_id;
                }

                $sql .= " AND pf.filter_id IN (" . implode(',', $implode) . ")";
            }
        }

        if (!empty($data['filter_name']) || !empty($data['filter_tag'])) {
            $sql .= " AND (";

            if (!empty($data['filter_name'])) {
                $implode = array();

                $words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_name'])));

                foreach ($words as $word) {
                    $implode[] = "pd.name LIKE '%" . $this->db->escape($word) . "%'";
                }

                if ($implode) {
                    $sql .= " " . implode(" AND ", $implode) . "";
                }

                if (!empty($data['filter_description'])) {
                    $sql .= " OR pd.description LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
                }
            }

            if (!empty($data['filter_name']) && !empty($data['filter_tag'])) {
                $sql .= " OR ";
            }

            if (!empty($data['filter_tag'])) {
                $implode = array();

                $words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_tag'])));

                foreach ($words as $word) {
                    $implode[] = "pd.tag LIKE '%" . $this->db->escape($word) . "%'";
                }

                if ($implode) {
                    $sql .= " " . implode(" AND ", $implode) . "";
                }
            }

            if (!empty($data['filter_name'])) {
                $sql .= " OR LCASE(p.model) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
                $sql .= " OR LCASE(p.sku) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
                $sql .= " OR LCASE(p.upc) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
                $sql .= " OR LCASE(p.ean) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
                $sql .= " OR LCASE(p.jan) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
                $sql .= " OR LCASE(p.isbn) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
                $sql .= " OR LCASE(p.mpn) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
            }

            $sql .= ")";
        }

        if (!empty($data['filter_manufacturer_id'])) {
            $sql .= " AND p.manufacturer_id = '" . (int)$data['filter_manufacturer_id'] . "'";
        }

        $sql .= " GROUP BY p.product_id";

        $sort_data = array(
            'pd.name',
            'p.model',
            'p.quantity',
            'p.price',
            'rating',
            'p.sort_order',
            'p.date_added'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            if ($data['sort'] == 'pd.name' || $data['sort'] == 'p.model') {
                $sql .= " ORDER BY LCASE(" . $data['sort'] . ")";
            } elseif ($data['sort'] == 'p.price') {
                $sql .= " ORDER BY (CASE WHEN special IS NOT NULL THEN special WHEN discount IS NOT NULL THEN discount ELSE p.price END)";
            } else {
                $sql .= " ORDER BY " . $data['sort'];
            }
        } else {
            $sql .= " ORDER BY p.sort_order";
        }

        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= " DESC, LCASE(pd.name) DESC";
        } else {
            $sql .= " ASC, LCASE(pd.name) ASC";
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

        $product_data = array();

        $query = $this->db->query($sql);

        foreach ($query->rows as $result) {
            $product_data[$result['product_id']] = $this->getProduct($result['product_id']);
        }

        return $product_data;
    }

    public function clearDublicatesAlias() {
            
        $query = $this->db->query("SELECT * FROM ".DB_PREFIX."url_alias WHERE `query` LIKE '%product_id=%';");
        
        $result = $query->num_rows;
        $count = array();

        if($query->num_rows > 0) {
            foreach($query->rows as $row) {
                $subquery = $this->db->query("SELECT * FROM ".DB_PREFIX."product WHERE ".$row['query'].";");

                if($subquery->num_rows == 0) {
                    $this->db->query("DELETE FROM ".DB_PREFIX."url_alias WHERE `query` = '".$row['query']."';");
                    $count[] = $row['query'];
                }
            }
        }

        return $count;      

    }

    // For category grid
        public function getNewProducts($data = array()) {
            
            $sql = "SELECT p.product_id, p.stock_status_id, (SELECT AVG(rating) AS total FROM " . DB_PREFIX . "review r1 WHERE r1.product_id = p.product_id AND r1.status = '1' GROUP BY r1.product_id) AS rating, (SELECT price FROM " . DB_PREFIX . "product_discount pd2 WHERE pd2.product_id = p.product_id AND pd2.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND pd2.quantity = '1' AND ((pd2.date_start = '0000-00-00' OR pd2.date_start < NOW()) AND (pd2.date_end = '0000-00-00' OR pd2.date_end > NOW())) ORDER BY pd2.priority ASC, pd2.price ASC LIMIT 1) AS discount, (SELECT price FROM " . DB_PREFIX . "product_special ps WHERE ps.product_id = p.product_id AND ps.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) ORDER BY ps.priority ASC, ps.price ASC LIMIT 1) AS special, (SELECT name FROM " . DB_PREFIX . "stock_status pt WHERE pt.stock_status_id = p.stock_status_id) AS stock_status";
                    
            if (!empty($data['filter_category_id'])) {
                if (!empty($data['filter_sub_category'])) {
                    $sql .= " FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (cp.category_id = p2c.category_id)";
                } else {
                    $sql .= " FROM " . DB_PREFIX . "product_to_category p2c";
                }

                if (!empty($data['filter_filter'])) {
                    $sql .= " LEFT JOIN " . DB_PREFIX . "product_filter pf ON (p2c.product_id = pf.product_id) LEFT JOIN " . DB_PREFIX . "product p ON (pf.product_id = p.product_id)";
                } else {
                    $sql .= " LEFT JOIN " . DB_PREFIX . "product p ON (p2c.product_id = p.product_id)";
                }
            } else {
                $sql .= " FROM " . DB_PREFIX . "product p";
            }

            $sql .= " LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = '1' AND p.new = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";
                    
            if (!empty($data['filter_category_id'])) {
                if (!empty($data['filter_sub_category'])) {
                    $sql .= " AND cp.path_id = '" . (int)$data['filter_category_id'] . "'";
                } else {
                    $sql .= " AND p2c.category_id = '" . (int)$data['filter_category_id'] . "'";
                }

                if (!empty($data['filter_filter'])) {
                    $implode = array();

                    $filters = explode(',', $data['filter_filter']);

                    foreach ($filters as $filter_id) {
                        $implode[] = (int)$filter_id;
                    }

                    $sql .= " AND pf.filter_id IN (" . implode(',', $implode) . ")";
                }
            }

            if (!empty($data['filter_name']) || !empty($data['filter_tag'])) {
                $sql .= " AND (";

                if (!empty($data['filter_name'])) {
                    $implode = array();

                    $words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_name'])));

                    foreach ($words as $word) {
                        $implode[] = "pd.name LIKE '%" . $this->db->escape($word) . "%'";
                    }

                    if ($implode) {
                        $sql .= " " . implode(" AND ", $implode) . "";
                    }

                    if (!empty($data['filter_description'])) {
                        $sql .= " OR pd.description LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
                    }
                }

                if (!empty($data['filter_name']) && !empty($data['filter_tag'])) {
                    $sql .= " OR ";
                }

                if (!empty($data['filter_tag'])) {
                    $implode = array();

                    $words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_tag'])));

                    foreach ($words as $word) {
                        $implode[] = "pd.tag LIKE '%" . $this->db->escape($word) . "%'";
                    }

                    if ($implode) {
                        $sql .= " " . implode(" AND ", $implode) . "";
                    }
                }

                if (!empty($data['filter_name'])) {
                    $sql .= " OR LCASE(p.model) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
                    $sql .= " OR LCASE(p.sku) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
                    $sql .= " OR LCASE(p.upc) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
                    $sql .= " OR LCASE(p.ean) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
                    $sql .= " OR LCASE(p.jan) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
                    $sql .= " OR LCASE(p.isbn) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
                    $sql .= " OR LCASE(p.mpn) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
                }

                $sql .= ")";
            }

            if (!empty($data['filter_manufacturer_id'])) {
                $sql .= " AND p.manufacturer_id = '" . (int)$data['filter_manufacturer_id'] . "'";
            }

            $sql .= " GROUP BY p.product_id";

            $sort_data = array(
                'pd.name',
                'p.model',
                'p.quantity',
                'p.price',
                'rating',
                'p.sort_order',
                'p.date_added'
            );

            if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
                if ($data['sort'] == 'pd.name' || $data['sort'] == 'p.model') {
                    $sql .= " ORDER BY LCASE(" . $data['sort'] . ")";
                } elseif ($data['sort'] == 'p.price') {
                    $sql .= " ORDER BY (CASE WHEN special IS NOT NULL THEN special WHEN discount IS NOT NULL THEN discount ELSE p.price END)";
                } else {
                    $sql .= " ORDER BY " . $data['sort'];
                }
            } else {
                $sql .= " ORDER BY p.sort_order";
            }

            if (isset($data['order']) && ($data['order'] == 'DESC')) {
                $sql .= " DESC, LCASE(pd.name) DESC";
            } else {
                $sql .= " ASC, LCASE(pd.name) ASC";
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

            $product_data = array();

            $query = $this->db->query($sql);
        
            $arProductsID = array();
            foreach($query->rows as $result) {
                $arProductsID[] = $result['product_id'];
            }

            if ( !empty($arProductsID) ) {
                $strProductsID = implode(',', $arProductsID);
                $arProductsAll = $this->getProductsByID($strProductsID);
                        
                foreach ($query->rows as $result) {
                    $product_data[$result['product_id']] = $arProductsAll[$result['product_id']];
                    $product_data[$result['product_id']]['stock_status_id'] = $result['stock_status_id'];
                }

                if( $product_data[$result['product_id']]['available_in_time'] == ''){
                    $product_data[$result['product_id']]['available_in_time'] = $this->config->get('config_available_in_time');
                }
            }
            
            return $product_data;
        }

        public function getTotalNewProducts() {
            $sql = "SELECT DISTINCT COUNT(p.product_id) AS total FROM ".DB_PREFIX."product p WHERE p.status = 1 AND (p.quantity > 0 OR (p.quantity <= 0 AND p.stock_status_id <> 5)) AND p.new = 1";
            $query = $this->db->query($sql);
            if(isset($query->row['total'])) {
                return (int)$query->row['total'];
            } else {
                return 0;
            }
        }

        public function getSaleProducts($data = array()) {
            
            $sql = "SELECT p.product_id, p.stock_status_id, (SELECT AVG(rating) AS total FROM " . DB_PREFIX . "review r1 WHERE r1.product_id = p.product_id AND r1.status = '1' GROUP BY r1.product_id) AS rating, (SELECT price FROM " . DB_PREFIX . "product_discount pd2 WHERE pd2.product_id = p.product_id AND pd2.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND pd2.quantity = '1' AND ((pd2.date_start = '0000-00-00' OR pd2.date_start < NOW()) AND (pd2.date_end = '0000-00-00' OR pd2.date_end > NOW())) ORDER BY pd2.priority ASC, pd2.price ASC LIMIT 1) AS discount, (SELECT price FROM " . DB_PREFIX . "product_special ps WHERE ps.product_id = p.product_id AND ps.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) ORDER BY ps.priority ASC, ps.price ASC LIMIT 1) AS special, (SELECT name FROM " . DB_PREFIX . "stock_status pt WHERE pt.stock_status_id = p.stock_status_id) AS stock_status";
                    
            if (!empty($data['filter_category_id'])) {
                if (!empty($data['filter_sub_category'])) {
                    $sql .= " FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (cp.category_id = p2c.category_id)";
                } else {
                    $sql .= " FROM " . DB_PREFIX . "product_to_category p2c";
                }

                if (!empty($data['filter_filter'])) {
                    $sql .= " LEFT JOIN " . DB_PREFIX . "product_filter pf ON (p2c.product_id = pf.product_id) LEFT JOIN " . DB_PREFIX . "product p ON (pf.product_id = p.product_id)";
                } else {
                    $sql .= " LEFT JOIN " . DB_PREFIX . "product p ON (p2c.product_id = p.product_id)";
                }
            } else {
                $sql .= " FROM " . DB_PREFIX . "product p";
            }

            $sql .= " LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = '1' AND p.date_available <= NOW() AND p.special_price <> 0 AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";
                    
            if (!empty($data['filter_category_id'])) {
                if (!empty($data['filter_sub_category'])) {
                    $sql .= " AND cp.path_id = '" . (int)$data['filter_category_id'] . "'";
                } else {
                    $sql .= " AND p2c.category_id = '" . (int)$data['filter_category_id'] . "'";
                }

                if (!empty($data['filter_filter'])) {
                    $implode = array();

                    $filters = explode(',', $data['filter_filter']);

                    foreach ($filters as $filter_id) {
                        $implode[] = (int)$filter_id;
                    }

                    $sql .= " AND pf.filter_id IN (" . implode(',', $implode) . ")";
                }
            }

            if (!empty($data['filter_name']) || !empty($data['filter_tag'])) {
                $sql .= " AND (";

                if (!empty($data['filter_name'])) {
                    $implode = array();

                    $words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_name'])));

                    foreach ($words as $word) {
                        $implode[] = "pd.name LIKE '%" . $this->db->escape($word) . "%'";
                    }

                    if ($implode) {
                        $sql .= " " . implode(" AND ", $implode) . "";
                    }

                    if (!empty($data['filter_description'])) {
                        $sql .= " OR pd.description LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
                    }
                }

                if (!empty($data['filter_name']) && !empty($data['filter_tag'])) {
                    $sql .= " OR ";
                }

                if (!empty($data['filter_tag'])) {
                    $implode = array();

                    $words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_tag'])));

                    foreach ($words as $word) {
                        $implode[] = "pd.tag LIKE '%" . $this->db->escape($word) . "%'";
                    }

                    if ($implode) {
                        $sql .= " " . implode(" AND ", $implode) . "";
                    }
                }

                if (!empty($data['filter_name'])) {
                    $sql .= " OR LCASE(p.model) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
                    $sql .= " OR LCASE(p.sku) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
                    $sql .= " OR LCASE(p.upc) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
                    $sql .= " OR LCASE(p.ean) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
                    $sql .= " OR LCASE(p.jan) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
                    $sql .= " OR LCASE(p.isbn) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
                    $sql .= " OR LCASE(p.mpn) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
                }

                $sql .= ")";
            }

            if (!empty($data['filter_manufacturer_id'])) {
                $sql .= " AND p.manufacturer_id = '" . (int)$data['filter_manufacturer_id'] . "'";
            }

            $sql .= " GROUP BY p.product_id";

            $sort_data = array(
                'pd.name',
                'p.model',
                'p.quantity',
                'p.price',
                'rating',
                'p.sort_order',
                'p.date_added'
            );

            if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
                if ($data['sort'] == 'pd.name' || $data['sort'] == 'p.model') {
                    $sql .= " ORDER BY LCASE(" . $data['sort'] . ")";
                } elseif ($data['sort'] == 'p.price') {
                    $sql .= " ORDER BY (CASE WHEN special IS NOT NULL THEN special WHEN discount IS NOT NULL THEN discount ELSE p.price END)";
                } else {
                    $sql .= " ORDER BY " . $data['sort'];
                }
            } else {
                $sql .= " ORDER BY p.sort_order";
            }

            if (isset($data['order']) && ($data['order'] == 'DESC')) {
                $sql .= " DESC, LCASE(pd.name) DESC";
            } else {
                $sql .= " ASC, LCASE(pd.name) ASC";
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

            $product_data = array();

            $query = $this->db->query($sql);
                    
            $arProductsID = array();
            foreach($query->rows as $result) {
                $arProductsID[] = $result['product_id'];
            }

            if ( !empty($arProductsID) ) {
                $strProductsID = implode(',', $arProductsID);
                $arProductsAll = $this->getProductsByID($strProductsID);
                        
                foreach ($query->rows as $result) {
                    $product_data[$result['product_id']] = $arProductsAll[$result['product_id']];
                    $product_data[$result['product_id']]['stock_status_id'] = $result['stock_status_id'];
                }

                if( $product_data[$result['product_id']]['available_in_time'] == ''){
                    $product_data[$result['product_id']]['available_in_time'] = $this->config->get('config_available_in_time');
                }
            }
            
            return $product_data;
        }

        public function getTotalSaleProducts() {
            $sql = "SELECT DISTINCT COUNT(p.product_id) AS total FROM ".DB_PREFIX."product p WHERE p.status = 1 AND (p.quantity > 0 OR (p.quantity <= 0 AND p.stock_status_id <> 5)) AND p.special_price <> 0";
            $query = $this->db->query($sql);
            if(isset($query->row['total'])) {
                return (int)$query->row['total'];
            } else {
                return 0;
            }
        }
    // ---
}
