<?php
class ControllerProcurementProducts extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('procurement/products');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->getList();
	}

	public function edit() {
		$this->load->language('procurement/products');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('procurement/product');


		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
			$this->model_procurement_product->editProduct($this->request->get['procurement_product_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('procurement/products', 'token=' . $this->session->data['token'] . $url, true));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('procurement/products');

		$this->load->model('procurement/product');

		if ($this->request->get['procurement_product_id']) {
			$this->model_procurement_product->deleteProduct($this->request->get['procurement_product_id']);

			$this->response->redirect($this->url->link('procurement/products', 'token=' . $this->session->data['token'] . $url, true));
		}

		$this->getList();
	}

	protected function getList() {
		if (isset($this->request->get['filter_date_added'])) {
			$filter_date_added = $this->request->get['filter_date_added'];
		} else {
			$filter_date_added = date("Y-m-d", time());
		}

		if (isset($this->request->get['filter_supplier'])) {
			$filter_supplier = $this->request->get['filter_supplier'];
		} else {
			$filter_supplier = 'Фуд-Сити';
		}

		if (isset($this->request->get['filter_name'])) {
			$filter_name = $this->request->get['filter_name'];
		} else {
			$filter_name = null;
		}

		if (isset($this->request->get['filter_category'])) {
			$filter_category = $this->request->get['filter_category'];
		} else {
			$filter_category = null;
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

		$url = '';

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . html_entity_decode($this->request->get['filter_date_added'], ENT_QUOTES, 'UTF-8');
		}

		if (isset($this->request->get['filter_supplier'])) {
			$url .= '&filter_supplier=' . urlencode(html_entity_decode($this->request->get['filter_supplier'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_category'])) {
			$url .= '&filter_category=' . $this->request->get['filter_category'];
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

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('procurement/products', 'token=' . $this->session->data['token'] . $url, true)
		);

		// Filter data
			$this->load->model('catalog/category');

			$filter_data_category = array(
				'sort'  => 'name',
				'order' => 'ACS',
				'start' => 0,
				'limit' => 1000
			);

			$categories = $this->model_catalog_category->getCategories($filter_data);
			$data['categories'] = $categories;
		// ---

		// Procurement
			$this->load->model('procurement/procurement');
			
			$data['procurement'] = array();


			$filter_data = array(
				'filter_date_added'  => $filter_date_added,
				'limit' => 1
			);

			$procurement = $this->model_procurement_procurement->getProcurement($filter_data);
			$data['procurement'] = $procurement;
		// ---


		// Product data
			$this->load->model('procurement/product');
			
			$data['products'] = array();

			$filter_data = array(
				'procurement_id'  => $procurement['procurement_id'],
				'filter_supplier'  => $filter_supplier,
				'filter_name'  => $filter_name,
				'filter_category'  => $filter_category,
				'sort'  => $sort,
				'order' => $order,
				'start' => ($page - 1) * 1000,
				'limit' => 1000
			);

			$products = $this->model_procurement_product->getProducts($filter_data);

			$total_weight = 0;
			$total_price = 0;

			foreach ($products as $key => $product) {
				// ---
					if( $product['minimum'] > $product['quantity'] ) {
						$products[$key]['quantity'] = floatval($product['minimum']);
					}
					else { $products[$key]['quantity'] = floatval($product['quantity']); }

					if( $product['weight_class_id'] == 1 || $product['weight_class_id'] == 2 ) {
						$total_weight = $total_weight + ((floatval($product['weight'])/1000) * $product['quantity']);
					}
					else{
						$total_weight = $total_weight + (floatval($product['weight']) * $product['quantity']);
					}
					
					$total_price = $total_price + floatval($product['purchase_price']);


					$products[$key]['view'] = $this->url->link('procurement/products/edit', 'token=' . $this->session->data['token'] . '&procurement_product_id=' . $product['procurement_product_id'] . '&procurement_id=' . $procurement['procurement_id'] . $url, true);
					$products[$key]['delete'] = $this->url->link('procurement/products/delete', 'token=' . $this->session->data['token'] . '&procurement_product_id=' . $product['procurement_product_id'] . '&procurement_id=' . $procurement['procurement_id'] . $url, true);
				// ---
			}

			$data['total_weight'] = $total_weight;
			$data['total_price'] = $total_price;
			
			$data['products'] = $products;
		// ---

		$data['add'] = $this->url->link('procurement/products/edit', 'token=' . $this->session->data['token'] . '&procurement_id=' . $procurement['procurement_id'] . $url, true);

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_list'] = $this->language->get('text_list');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');
		$data['text_default'] = $this->language->get('text_default');
		$data['text_document_number'] = $this->language->get('text_document_number');
		$data['text_document_date'] = $this->language->get('text_document_date');
		$data['text_total_weight'] = $this->language->get('text_total_weight');
		$data['text_total_price'] = $this->language->get('text_total_price');
		$data['text_weight'] = $this->language->get('text_weight');
		$data['text_price'] = $this->language->get('text_price');

		$data['column_name'] = $this->language->get('column_name');
		$data['column_min'] = $this->language->get('column_min');
		$data['column_price'] = $this->language->get('column_price');
		$data['column_action'] = $this->language->get('column_action');

		$data['entry_date_added'] = $this->language->get('entry_date_added');
		$data['entry_supplier'] = $this->language->get('entry_supplier');
		$data['entry_name'] = $this->language->get('entry_name');
		$data['entry_category'] = $this->language->get('entry_category');

		$data['button_add'] = $this->language->get('button_add');
		$data['button_edit'] = $this->language->get('button_edit');
		$data['button_delete'] = $this->language->get('button_delete');
		$data['button_filter'] = $this->language->get('button_filter');

		$data['token'] = $this->session->data['token'];

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$url = '';

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . html_entity_decode($this->request->get['filter_date_added']);
		}

		if (isset($this->request->get['filter_supplier'])) {
			$url .= '&filter_supplier=' . urlencode(html_entity_decode($this->request->get['filter_supplier'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_category'])) {
			$url .= '&filter_category=' . $this->request->get['filter_category'];
		}

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_name'] = $this->url->link('procurement/products', 'token=' . $this->session->data['token'] . '&sort=pd.name' . $url, true);
	

		$url = '';

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . html_entity_decode($this->request->get['filter_date_added']);
		}

		if (isset($this->request->get['filter_supplier'])) {
			$url .= '&filter_supplier=' . urlencode(html_entity_decode($this->request->get['filter_supplier'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_category'])) {
			$url .= '&filter_category=' .$this->request->get['filter_category'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $result_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('procurement/products', 'token=' . $this->session->data['token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($result_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($result_total - $this->config->get('config_limit_admin'))) ? $result_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $result_total, ceil($result_total / $this->config->get('config_limit_admin')));

		$data['filter_date_added'] = $filter_date_added;
		$data['filter_supplier'] = $filter_supplier;
		$data['filter_name'] = $filter_name;
		$data['filter_category'] = $filter_category;

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('procurement/products_list', $data));
	}

	protected function getForm() {
		$data['form'] = !isset($this->request->get['procurement_product_id']) ? 'add' : 'edit';
		
		if( isset($this->request->get['procurement_id']) ) { $data['procurement_id'] = $this->request->get['procurement_id']; }

		// Text
			$data['heading_title'] = $this->language->get('heading_title');

			$data['text_form'] = !isset($this->request->get['procurement_product_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
			$data['text_weight_title'] = $this->language->get('text_weight_title');
			$data['text_weight'] = $this->language->get('text_weight');
			$data['text_price'] = $this->language->get('text_price');
			$data['text_supplier'] = $this->language->get('text_supplier');
			$data['text_manufacturer'] = $this->language->get('text_manufacturer');
			$data['text_purchased'] = $this->language->get('text_purchased');
			$data['text_not_purchased'] = $this->language->get('text_not_purchased');

			$data['entry_name'] = $this->language->get('entry_name');
			$data['entry_quantity'] = $this->language->get('entry_quantity');
			$data['entry_purchase_price'] = $this->language->get('entry_purchase_price');
			$data['entry_total'] = $this->language->get('entry_total');

			$data['button_save'] = $this->language->get('button_save');
			$data['button_cancel'] = $this->language->get('button_cancel');

			if (isset($this->error['warning'])) {
				$data['error_warning'] = $this->error['warning'];
			} else {
				$data['error_warning'] = '';
			}
		// ---

		// Url
			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}
		// ---

		// Breadcrumbs
			$data['breadcrumbs'] = array();

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_home'),
				'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
			);

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('procurement/products', 'token=' . $this->session->data['token'] . $url, true)
			);
		// ---

		// Actions
			if (!isset($this->request->get['procurement_product_id'])) {
				$data['action'] = $this->url->link('procurement/products/edit', 'token=' . $this->session->data['token'] . '&procurement_product_id=0' . $url, true);
			} else {
				$data['action'] = $this->url->link('procurement/products/edit', 'token=' . $this->session->data['token'] . '&procurement_product_id=' . $this->request->get['procurement_product_id'] . $url, true);
			}

			$data['cancel'] = $this->url->link('procurement/products', 'token=' . $this->session->data['token'] . $url, true);
		// ---


		// Get product
			$data['product_info'] = array();

			if (isset($this->request->get['procurement_product_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
				// ---
					$data['product_info'] = $this->model_procurement_product->getProduct($this->request->get['procurement_product_id']);

					if( $data['product_info']['weight_class_id'] == 1 || $data['product_info']['weight_class_id'] == 2 ) {
						$data['product_info']['weight'] = $data['product_info']['weight']/1000;
					}

					// Image
						$this->load->model('tool/image');

						if (is_file(DIR_IMAGE.$data['product_info']['image'])) {
							$data['product_info']['image'] = $this->model_tool_image->resize($data['product_info']['image'], 100, 100);
						} else {
							$data['product_info']['image'] = $this->model_tool_image->resize('no_image.png', 100, 100);
						}
					// ---
				// ---
			}
		// ---

		$data['token'] = $this->session->data['token'];

		if (isset($this->request->post['name'])) {
			$data['name'] = $this->request->post['name'];
		} elseif (!empty($manufacturer_info)) {
			$data['name'] = $manufacturer_info['name'];
		} else {
			$data['name'] = '';
		}

		if (isset($this->request->post['sort_order'])) {
			$data['sort_order'] = $this->request->post['sort_order'];
		} elseif (!empty($manufacturer_info)) {
			$data['sort_order'] = $manufacturer_info['sort_order'];
		} else {
			$data['sort_order'] = '';
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('procurement/products_form', $data));
	}

	public function getProductForAdd() {
		// ---
			// Init
				if( !isset($this->request->post['product_id']) ){
					$response->status = 'error';
			        echo json_encode($response);
			        exit;
				}
		     	
		     	$product_id = $this->request->post['product_id'];
		     	$response = new stdClass();
		    // ---

		    // Get product
				$this->load->model('procurement/product');

				$product = $this->model_procurement_product->getProductForAdd($product_id);

				$product['quantity'] = 1;

				if( $product['weight_class_id'] == 1 || $product['weight_class_id'] == 2 ) {
					$product['weight'] = $product['weight']/1000;
				}
				$product['weight'] = floatval($product['weight']);

				// Image
					$this->load->model('tool/image');

					if (is_file(DIR_IMAGE.$product['image'])) {
						$product['image'] = $this->model_tool_image->resize($product['image'], 100, 100);
					} else {
						$product['image'] = $this->model_tool_image->resize('no_image.png', 100, 100);
					}
				// ---

				$response->product = $product;
			// ---


			$response->status = 'success';

	        echo json_encode($response);
	        exit;
		// ---
	}
}