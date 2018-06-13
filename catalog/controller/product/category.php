<?php
class ControllerProductCategory extends Controller {
	public function index() {
        
        // Check struckture
        	$url_data = $this->request->get;
        	
        	if( $url_data['_route_'] != 'eda/' && $url_data['_route_'] != 'eda' ){
        		header($this->request->server['SERVER_PROTOCOL'] . ' 301 Moved Permanently');
            	$this->response->redirect($this->url->link('common/home'));
        	}
        // ---

        // Load models
			$this->load->language('product/category');

			$this->load->model('catalog/category');

			$this->load->model('catalog/product');

			$this->load->model('tool/image');
		// ---
        
        // Init
	        $data['alphabetCount'] = array();

			if (isset($this->request->get['filter'])) {
				$filter = $this->request->get['filter'];
			} else {
				$filter = '';
			}

			if (isset($this->request->get['sort'])) {
				$sort = $this->request->get['sort'];
			} else {
				$sort = 'pd.name';
			}

			if (isset($this->request->get['order'])) {
				$order = $this->request->get['order'];
			} else {
				$order = 'ASC';
			}

			if (isset($this->request->get['page'])) {
				$page = $this->request->get['page'];
			} else {
				$page = 1;
			}

			if (isset($this->request->get['limit'])) {
				$limit = (int)$this->request->get['limit'];
			} else {
				$limit = $this->config->get($this->config->get('config_theme') . '_product_limit');
			}


			$data['breadcrumbs'] = array();

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_home'),
				'href' => $this->url->link('common/home')
			);

			if (isset($this->request->get['path'])) {
				$url = '';

				if (isset($this->request->get['sort'])) {
					$url .= '&sort=' . $this->request->get['sort'];
				}

				if (isset($this->request->get['order'])) {
					$url .= '&order=' . $this->request->get['order'];
				}

				if (isset($this->request->get['limit'])) {
					$url .= '&limit=' . $this->request->get['limit'];
				}

				$path = '';

				$parts = explode('_', (string)$this->request->get['path']);

				$category_id = (int)array_pop($parts);
	                        
				foreach ($parts as $path_id) {
					if (!$path) {
						$path = (int)$path_id;
					} else {
						$path .= '_' . (int)$path_id;
					}

					$category_info = $this->model_catalog_category->getCategory($path_id);

					if ($category_info) {
						$data['breadcrumbs'][] = array(
							'text' => $category_info['name'],
							'href' => $this->url->link('product/category', 'path=' . $path . $url)
						);
					}
				}
			} else {
				$category_id = 0;
			}
			
			$data['category_id'] = $category_id;

			$category_info = $this->model_catalog_category->getCategory($category_id);
		// ---

		if ($category_info) {
			// Init template
				$this->document->setTitle($category_info['meta_title']);
				$this->document->setDescription($category_info['meta_description']);
				$this->document->setKeywords($category_info['meta_keyword']);

				$data['heading_title'] = $category_info['name'];

				$data['text_refine'] = $this->language->get('text_refine');
				$data['text_empty'] = $this->language->get('text_empty');
				$data['text_quantity'] = $this->language->get('text_quantity');
				$data['text_manufacturer'] = $this->language->get('text_manufacturer');
				$data['text_model'] = $this->language->get('text_model');
				$data['text_price'] = $this->language->get('text_price');
				$data['text_tax'] = $this->language->get('text_tax');
				$data['text_points'] = $this->language->get('text_points');
				$data['text_compare'] = sprintf($this->language->get('text_compare'), (isset($this->session->data['compare']) ? count($this->session->data['compare']) : 0));
				$data['text_sort'] = $this->language->get('text_sort');
				$data['text_limit'] = $this->language->get('text_limit');

				$data['button_cart'] = $this->language->get('button_cart');
				$data['button_wishlist'] = $this->language->get('button_wishlist');
				$data['button_compare'] = $this->language->get('button_compare');
				$data['button_continue'] = $this->language->get('button_continue');
				$data['button_list'] = $this->language->get('button_list');
				$data['button_grid'] = $this->language->get('button_grid');

				// Set the last category breadcrumb
				$data['breadcrumbs'][] = array(
					'text' => $category_info['name'],
					'href' => $this->url->link('product/category', 'path=' . $this->request->get['path'])
				);

				if ($category_info['image']) {
					$data['thumb'] = $this->model_tool_image->resize($category_info['image'], $this->config->get($this->config->get('config_theme') . '_image_category_width'), $this->config->get($this->config->get('config_theme') . '_image_category_height'));
				} else {
					$data['thumb'] = '';
				}

				$data['description'] = html_entity_decode($category_info['description'], ENT_QUOTES, 'UTF-8');
				$data['compare'] = $this->url->link('product/compare');

				$url = '';

				if (isset($this->request->get['filter'])) {
					$url .= '&filter=' . $this->request->get['filter'];
				}

				if (isset($this->request->get['sort'])) {
					$url .= '&sort=' . $this->request->get['sort'];
				}

				if (isset($this->request->get['order'])) {
					$url .= '&order=' . $this->request->get['order'];
				}

				if (isset($this->request->get['limit'])) {
					$url .= '&limit=' . $this->request->get['limit'];
				}
			// ---

			// Get category and subcategories
				$data['categories'] = array();

				$categories_level2 = $this->model_catalog_category->getCategories($category_id);
	                        
	            // Add pseudo categories
	            	$pseudo_subcategories = array();

					$pseudo_subcategories[] = array(
						'parent' => $category_id,
						'id' => 'new',
						'name' => 'Новинки',
						'href' => '',
						'image' => '/catalog/view/theme/default/img/svg/icon-new.svg',
	                    'total' => $this->model_catalog_product->getTotalNewProducts()-1
					);

	            	$data['categories'][] = array(
						'id' => 'new',
						'name' => 'Новинки',
						'href' => '',
						'image' => '/catalog/view/theme/default/img/svg/icon-new.svg',
	                    'sub' => $pseudo_subcategories
					);

	            	$pseudo_subcategories = array();

	            	$pseudo_subcategories[] = array(
	            		'parent' => $category_id,
						'id' => 'sale',
						'name' => 'Скидки',
						'href' => '',
						'image' => '/catalog/view/theme/default/img/svg/icon-sale.svg',
	                    'total' => $this->model_catalog_product->getTotalSaleProducts()-1
					);

					$data['categories'][] = array(
						'id' => 'sale',
						'name' => 'Скидки',
						'href' => '',
						'image' => '/catalog/view/theme/default/img/svg/icon-sale.svg',
	                    'sub' => $pseudo_subcategories
					);
	            // ---

				foreach ($categories_level2 as $result) {
	                            
	                $subcategories = array();
	                $categories_level3 = $this->model_catalog_category->getCategories($result['category_id']);

	                foreach ($categories_level3 as $result3) {
	                    $filter_data = array(
	                            'filter_category_id'  => $result3['category_id'],
	                            'filter_sub_category' => true
	                    );

	                    $subcategories[] = array(
								'parent' => $category_id,
	                            'id' => $result3['category_id'],
	                            'name' => $result3['name'] . ($this->config->get('config_product_count') ? ' (' . $this->model_catalog_product->getTotalProducts($filter_data) . ')' : ''),
	                            'href' => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '_' . $result3['category_id'] . $url),
	                            'image' => $result3['image'],
	                            'total' => $this->model_catalog_product->getTotalCategoryProducts($result3['category_id'])
	                    );
	                    
	                }

	                $filter_data = array(
						'filter_category_id'  => $result['category_id'],
						'filter_sub_category' => true
					);
	                                
					$data['categories'][] = array(
						'id' => $result['category_id'],
						'name' => $result['name'] . ($this->config->get('config_product_count') ? ' (' . $this->model_catalog_product->getTotalProducts($filter_data) . ')' : ''),
						'href' => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '_' . $result['category_id'] . $url),
						'image' => $result['image'],
	                    'sub' => $subcategories
					);
				}
			// ---
                        
			// Get products
				// Init
					$data['products'] = array();

		            $limit = 10000;

					$filter_data = array(
						'filter_category_id' => $category_id,
						'filter_filter'      => $filter,
						'sort'               => $sort,
						'order'              => $order,
						'start'              => ($page - 1) * $limit,
						'limit'              => $limit
					);


					$product_total = $this->model_catalog_product->getTotalProducts($filter_data);
		            $catSortTime = $this->cache->get('latest_category_sort');
		                        
		            $cacheRequired = $this->model_catalog_product->isCacheRequired();

		            if(!$catSortTime || !$cacheRequired) {
		                $catSortTime = 0;
		                $results = $this->model_catalog_product->getProducts($filter_data);
		                $data['products_asorted'] = array();
		                $data['products_catsorted'] = array();
		            } else {
		                $data['products_asorted'] = unserialize($this->cache->get('category_products_asorted'));
		                $data['products_catsorted'] = unserialize($this->cache->get('category_products_catsorted'));
		            }
		        // ---
                        
	            // Add
		            $iCount = 0;

		            if(!$cacheRequired) foreach($data['categories'] as $category_middle) {

		                $data['products_catsorted'][$category_middle['id']] = array(
		                    'id' => $category_middle['id'],
		                    'name' => $category_middle['name'],
		                    'image' => $category_middle['image'],
		                    'sub' => array()
		                );
		                
		                foreach($category_middle['sub'] as $category) {
		                    $subcat_filter_data = $filter_data;
		                    $subcat_filter_data['limit'] = 10;

		                    // Get category products
		                    	$subcat_results = array();

		                    	if( $category['id'] == 'new' ){
		                    		$subcat_filter_data['filter_category_id'] = $category_id;
		                    		$subcat_results = $this->model_catalog_product->getNewProducts($subcat_filter_data);
		                    	}
		                    	else if( $category['id'] == 'sale' ) {
		                    		$subcat_filter_data['filter_category_id'] =$category_id;
		                    		$subcat_results = $this->model_catalog_product->getSaleProducts($subcat_filter_data);
		                    	}
		                    	else {
		                    		$subcat_filter_data['filter_category_id'] = $category['id'];
		                    		$subcat_results = $this->model_catalog_product->getProducts($subcat_filter_data);
		                    	}
		                    // ---

		                    foreach ($subcat_results as $result) {
		                        if(($result['quantity'] <= 0 && $result['stock_status_id'] == 5) || $result['status'] != 1) {
		                            continue;
		                        }

		                        if ($result['image_preview']) {
		                                $image = '/image/'.$result['image_preview'];
		                                //$image = $this->model_tool_image->resize($result['image'], $this->config->get($this->config->get('config_theme') . '_image_product_width'), $this->config->get($this->config->get('config_theme') . '_image_product_height'));
		                        } else {
		                                $image = $this->model_tool_image->resize('eco_logo.png', $this->config->get($this->config->get('config_theme') . '_image_product_width'), $this->config->get($this->config->get('config_theme') . '_image_product_height'));
		                        }

		                        if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
		                                $price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
		                        } else {
		                                $price = false;
		                        }

		                        if ((float)$result['special']) {
		                                $special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
		                        } else {
		                                $special = false;
		                        }

		                        if ($this->config->get('config_tax')) {
		                                $tax = $this->currency->format((float)$result['special'] ? $result['special'] : $result['price'], $this->session->data['currency']);
		                        } else {
		                                $tax = false;
		                        }

		                        if ($this->config->get('config_review_status')) {
		                                $rating = (int)$result['rating'];
		                        } else {
		                                $rating = false;
		                        }

		                        if($special) {
		                            if($price != 0) $discount_sticker = ceil(((float)$price - (float)$special)/(float)$price*100);
		                            else $discount_sticker = 0;
		                            $price = $special;
		                        }

		                        $arProducts = array(
	                                'product_id'  => $result['product_id'],
	                                'available_in_time' => $result['available_in_time'],
	                                'quantity'    => $result['quantity'],
	                                'status'      => $result['status'],
	                                'thumb'       => $image,
	                                'name'        => $result['name'],
	                                'description_short' => $result['description_short'],
	                                'description' => utf8_substr(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8')), 0, $this->config->get($this->config->get('config_theme') . '_product_description_length')),
	                                'price'       => $price,
	                                'special'     => $special,
	                                'tax'         => $tax,
	                                'minimum'     => $result['minimum'] > 0 ? $result['minimum'] : 1,
	                                'rating'      => $result['rating'],
	                                'href'        => $this->url->link('product/product', 'path=' . $this->request->get['path'] . '&product_id=' . $result['product_id'] . $url),
	                                'stock_status'      => $result['stock_status'],
	                                'stock_status_id'   => $result['stock_status_id'],
	                                'weight_variants'   => $result['weight_variants'],
	                                'weight_class' => $result['weight_class'],
	                                'sticker_name' => $result['sticker']['name'],
	                                'sticker_class' => $result['sticker']['class']
		                        );

		                        if(isset($discount_sticker)) {
		                            $arProducts['discount_sticker'] = $discount_sticker;
		                            unset($discount_sticker);
		                        }
		                        
		                        if($result['composite_price'] !== false) {
		                                $arProducts['composite_price'] = json_encode($result['composite_price']);       
		                        }
		                        $data['products_catsorted'][$category_middle['id']]['sub'][$category['id']][] = $arProducts;
		                    }
		                }
		            }
	            // ---
	                        
	            // Set cache
		            if(!$cacheRequired) {
		                $this->cache->set('latest_category_sort', time());
		                $this->cache->set('category_products_asorted', serialize($data['products_asorted']));
		                $this->cache->set('category_products_catsorted', serialize($data['products_catsorted']));
		            }
	            // ---
		    // ---

            // URL      
				$url = '';

				if (isset($this->request->get['filter'])) {
					$url .= '&filter=' . $this->request->get['filter'];
				}

				if (isset($this->request->get['limit'])) {
					$url .= '&limit=' . $this->request->get['limit'];
				}

				$data['sorts'] = array();

				$data['sorts'][] = array(
					'text'  => $this->language->get('text_default'),
					'value' => 'p.sort_order-ASC',
					'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.sort_order&order=ASC' . $url)
				);

				$data['sorts'][] = array(
					'text'  => $this->language->get('text_name_asc'),
					'value' => 'pd.name-ASC',
					'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=pd.name&order=ASC' . $url)
				);

				$data['sorts'][] = array(
					'text'  => $this->language->get('text_name_desc'),
					'value' => 'pd.name-DESC',
					'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=pd.name&order=DESC' . $url)
				);

				$data['sorts'][] = array(
					'text'  => $this->language->get('text_price_asc'),
					'value' => 'p.price-ASC',
					'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.price&order=ASC' . $url)
				);

				$data['sorts'][] = array(
					'text'  => $this->language->get('text_price_desc'),
					'value' => 'p.price-DESC',
					'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.price&order=DESC' . $url)
				);

				if ($this->config->get('config_review_status')) {
					$data['sorts'][] = array(
						'text'  => $this->language->get('text_rating_desc'),
						'value' => 'rating-DESC',
						'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=rating&order=DESC' . $url)
					);

					$data['sorts'][] = array(
						'text'  => $this->language->get('text_rating_asc'),
						'value' => 'rating-ASC',
						'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=rating&order=ASC' . $url)
					);
				}

				$data['sorts'][] = array(
					'text'  => $this->language->get('text_model_asc'),
					'value' => 'p.model-ASC',
					'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.model&order=ASC' . $url)
				);

				$data['sorts'][] = array(
					'text'  => $this->language->get('text_model_desc'),
					'value' => 'p.model-DESC',
					'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.model&order=DESC' . $url)
				);

				$url = '';

				if (isset($this->request->get['filter'])) {
					$url .= '&filter=' . $this->request->get['filter'];
				}

				if (isset($this->request->get['sort'])) {
					$url .= '&sort=' . $this->request->get['sort'];
				}

				if (isset($this->request->get['order'])) {
					$url .= '&order=' . $this->request->get['order'];
				}

				$data['limits'] = array();

	                        
	                        
				$limits = array_unique(array($this->config->get($this->config->get('config_theme') . '_product_limit'), 25, 50, 75, 100));

				sort($limits);
	                        
				foreach($limits as $value) {
					$data['limits'][] = array(
						'text'  => $value,
						'value' => $value,
						'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . $url . '&limit=' . $value)
					);
				}

				$url = '';

				if (isset($this->request->get['filter'])) {
					$url .= '&filter=' . $this->request->get['filter'];
				}

				if (isset($this->request->get['sort'])) {
					$url .= '&sort=' . $this->request->get['sort'];
				}

				if (isset($this->request->get['order'])) {
					$url .= '&order=' . $this->request->get['order'];
				}

				if (isset($this->request->get['limit'])) {
					$url .= '&limit=' . $this->request->get['limit'];
				}
			// ---

			// Pagination
				$pagination = new Pagination();
				$pagination->total = $product_total;
				$pagination->page = $page;
				$pagination->limit = $limit;
				$pagination->url = $this->url->link('product/category', 'path=' . $this->request->get['path'] . $url . '&page={page}');

				$data['pagination'] = $pagination->render();

				$data['results'] = sprintf($this->language->get('text_pagination'), ($product_total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($product_total - $limit)) ? $product_total : ((($page - 1) * $limit) + $limit), $product_total, ceil($product_total / $limit));

				if ($page == 1) {
				    $this->document->addLink($this->url->link('product/category', 'path=' . $category_info['category_id'], true), 'canonical');
				} elseif ($page == 2) {
				    $this->document->addLink($this->url->link('product/category', 'path=' . $category_info['category_id'], true), 'prev');
				} else {
				    $this->document->addLink($this->url->link('product/category', 'path=' . $category_info['category_id'] . '&page='. ($page - 1), true), 'prev');
				}

				if ($limit && ceil($product_total / $limit) > $page) {
				    $this->document->addLink($this->url->link('product/category', 'path=' . $category_info['category_id'] . '&page='. ($page + 1), true), 'next');
				}
			// ---

			// Template data
				$data['sort'] = $sort;
				$data['order'] = $order;
				$data['limit'] = $limit;

				$data['continue'] = $this->url->link('common/home');

				$data['column_left'] = $this->load->controller('common/column_left');
				$data['column_right'] = $this->load->controller('common/column_right');
				$data['content_top'] = $this->load->controller('common/content_top');
				$data['content_bottom'] = $this->load->controller('common/content_bottom');
				$data['footer'] = $this->load->controller('common/footer');
				$data['header'] = $this->load->controller('common/header');
            	
            	$data['hide_advantage'] = !empty($this->request->cookie['hide_advantage']);
			// ---

			// Cart
	            $cartProducts = $this->cart->getProducts();
	            $data['cart_products'] = Array();
	            
	            foreach($cartProducts as $product) {
	                $data['cart_products'][(int)$product['product_id']] = $product['quantity'];
	            }
	        // ---
	            
            // Output
	            if($category_info['name'] == 'Еда') {
					$this->response->setOutput($this->load->view('product/category', $data));
	            } else {
	                $this->response->setOutput($this->load->view('product/categoryother', $data));
	            }
            // ---
		} else {
			// Init
				$url = '';

				if (isset($this->request->get['path'])) {
					$url .= '&path=' . $this->request->get['path'];
				}

				if (isset($this->request->get['filter'])) {
					$url .= '&filter=' . $this->request->get['filter'];
				}

				if (isset($this->request->get['sort'])) {
					$url .= '&sort=' . $this->request->get['sort'];
				}

				if (isset($this->request->get['order'])) {
					$url .= '&order=' . $this->request->get['order'];
				}

				if (isset($this->request->get['page'])) {
					$url .= '&page=' . $this->request->get['page'];
				}

				if (isset($this->request->get['limit'])) {
					$url .= '&limit=' . $this->request->get['limit'];
				}

				$data['breadcrumbs'][] = array(
					'text' => $this->language->get('text_error'),
					'href' => $this->url->link('product/category', $url)
				);
			// ---

			// Template & data
				$this->document->setTitle($this->language->get('text_error'));

				$data['heading_title'] = $this->language->get('text_error');

				$data['text_error'] = $this->language->get('text_error');

				$data['button_continue'] = $this->language->get('button_continue');

				$data['continue'] = $this->url->link('common/home');

				$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 404 Not Found');

				$data['column_left'] = $this->load->controller('common/column_left');
				$data['column_right'] = $this->load->controller('common/column_right');
				$data['content_top'] = $this->load->controller('common/content_top');
				$data['content_bottom'] = $this->load->controller('common/content_bottom');
				$data['footer'] = $this->load->controller('common/footer');
				$data['header'] = $this->load->controller('common/header');
			// ---

			// Output
				$this->response->setOutput($this->load->view('error/not_found', $data));
			// ---
		}
	}

	public function loadCategoryTemplate(){
		// ---

			// Init
	        	$category_id = $this->request->post['category_id'];
	        	$type = $this->request->post['type'];
	          
	        	$response = new stdClass();

		        // OC data
			        $data['alphabetCount'] = array();
			        
					if (isset($this->request->get['filter'])) {
						$filter = $this->request->get['filter'];
					} else {
						$filter = '';
					}

					if (isset($this->request->get['sort'])) {
						$sort = $this->request->get['sort'];
					} else {
						$sort = 'pd.name';
					}

					if (isset($this->request->get['order'])) {
						$order = $this->request->get['order'];
					} else {
						$order = 'ASC';
					}

					if (isset($this->request->get['page'])) {
						$page = $this->request->get['page'];
					} else {
						$page = 1;
					}

					if (isset($this->request->get['limit'])) {
						$limit = (int)$this->request->get['limit'];
					} else {
						$limit = $this->config->get($this->config->get('config_theme') . '_product_limit');
					}
				// ---
	        // ---


	        // Load models
				$this->load->language('product/category');

				$this->load->model('catalog/category');

				$this->load->model('catalog/product');

				$this->load->model('tool/image');
			// ---

			// Init products
				$products = array();

	            $limit = 10000;

				$filter_data = array(
					'filter_category_id' => $category_id,
					'filter_filter'      => $filter,
					'sort'               => $sort,
					'order'              => $order,
					'start'              => ($page - 1) * $limit,
					'limit'              => $limit
				);

				$product_total = $this->model_catalog_product->getTotalProducts($filter_data);

                $results = $this->model_catalog_product->getProducts($filter_data);
                $alphabet_list = array();
                $products_asorted = array();
                $alphabetCount = array();
	        // ---


	        // Get
				$category_info = $this->model_catalog_category->getCategory($category_id);

				if ($category_info) {
					switch ($type) {
						case 'alphabetic':
							// ---
					            foreach($results as $result) {
					            	// Init 
						                $alphabetSort = mb_strtoupper(mb_substr($result['name'], 0, 1));

						                if(!in_array($alphabetSort, $alphabet_list)) { $alphabet_list[] = $alphabetSort; }

						                if(!isset($alphabetCount[$alphabetSort])) {
						                    $alphabetCount[$alphabetSort] = $this->model_catalog_product->getTotalLiteralProducts($alphabetSort);
						                }
						            
						                if(($result['quantity'] <= 0 && $result['stock_status_id'] == 5) || $result['status'] != 1) {
						                    continue;
						                }

										if ($result['image_preview']) {
						                    $image = '/image/'.$result['image_preview'];
						                    //$image = $this->model_tool_image->resize($result['image'], $this->config->get($this->config->get('config_theme') . '_image_product_width'), $this->config->get($this->config->get('config_theme') . '_image_product_height'));
										} else {
											$image = $this->model_tool_image->resize('eco_logo.png', $this->config->get($this->config->get('config_theme') . '_image_product_width'), $this->config->get($this->config->get('config_theme') . '_image_product_height'));
										}

										if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
											$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
										} else {
											$price = false;
										}

										if ((float)$result['special']) {
											$special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
										} else {
											$special = false;
										}

										if ($this->config->get('config_tax')) {
											$tax = $this->currency->format((float)$result['special'] ? $result['special'] : $result['price'], $this->session->data['currency']);
										} else {
											$tax = false;
										}

										if ($this->config->get('config_review_status')) {
											$rating = (int)$result['rating'];
										} else {
											$rating = false;
										}
						                                
						                if($special) {
						                    if($price != 0) $discount_sticker = ceil(((float)$price - (float)$special)/(float)$price*100);
						                    else $discount_sticker = 0;
						                    $price = $special;
						                }
						            // ---

						            // Post init
						                if(isset($discount_sticker)) {
						                    $arProducts['discount_sticker'] = $discount_sticker;
						                    unset($discount_sticker);
						                }
						               
						                if($result['composite_price'] !== false) {
						                        $arProducts['composite_price'] = json_encode($result['composite_price']);
						                }
						            // ---

									$arProducts = array(
										'product_id'  => $result['product_id'],
					                    'available_in_time' => $result['available_in_time'],
					                    'status'      => $result['status'],
					                    'quantity'    => $result['quantity'],
										'thumb'       => $image,
										'name'        => $result['name'],
					                    'description_short' => $result['description_short'],
										'description' => utf8_substr(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8')), 0, $this->config->get($this->config->get('config_theme') . '_product_description_length')),
										'price'       => $price,
										'special'     => $special,
										'tax'         => $tax,
										'minimum'     => $result['minimum'] > 0 ? $result['minimum'] : 1,
										'rating'      => $result['rating'],
										'href'        => $this->url->link('product/product', 'path=' . $this->request->get['path'] . '&product_id=' . $result['product_id']),
					                    'stock_status'      => $result['stock_status'],
					                    'stock_status_id'   => $result['stock_status_id'],
					                    'weight_variants'   => $result['weight_variants'],
					                    'weight_class' => $result['weight_class'],
					                    'sticker_name' => $result['sticker']['name'],
					                    'sticker_class' => $result['sticker']['class']
									);
					                
					                $products_asorted[$alphabetSort][] = $arProducts;
					                $products[] = $arProducts;
								}
					            
					            natsort($alphabet_list);

					            ob_start();
					            include DIR_TEMPLATE.'default/template/product/category_alphabetic.tpl';
					            $response->template = ob_get_clean();
					            $response->alphabet_list = $alphabet_list;
							// ---
						break;

						case 'list':
							// ---
								$products_tagsorted = $this->model_catalog_product->getTags();
		            			natsort($products_tagsorted);
								
								ob_start();
					            include DIR_TEMPLATE.'default/template/product/category_list.tpl';
					            $response->template = ob_get_clean();
							// ---
						break;
					}
				}
	        // ---

	        // Response
			$response->response = $data;
	        $response->status = 'success';
	        
	        echo json_encode($response);
	        exit;

		// ---
	}
}
