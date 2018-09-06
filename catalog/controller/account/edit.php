<?php
class ControllerAccountEdit extends Controller {
	private $error = array();

	public function index() {
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/account', '', true);

			$this->response->redirect($this->url->link('account/login', '', true));
		}

		$this->load->language('account/account');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_account'),
			'href' => $this->url->link('account/account', '', true)
		);

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		} 

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_my_account'] = $this->language->get('text_my_account');
		$data['text_my_orders'] = $this->language->get('text_my_orders');
		$data['text_my_newsletter'] = $this->language->get('text_my_newsletter');
		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_password'] = $this->language->get('text_password');
		$data['text_address'] = $this->language->get('text_address');
		$data['text_credit_card'] = $this->language->get('text_credit_card');
		$data['text_wishlist'] = $this->language->get('text_wishlist');
		$data['text_order'] = $this->language->get('text_order');
		$data['text_download'] = $this->language->get('text_download');
		$data['text_reward'] = $this->language->get('text_reward');
		$data['text_return'] = $this->language->get('text_return');
		$data['text_transaction'] = $this->language->get('text_transaction');
		$data['text_newsletter'] = $this->language->get('text_newsletter');
		$data['text_recurring'] = $this->language->get('text_recurring');

		$data['edit'] = $this->url->link('account/edit', '', true);
		$data['password'] = $this->url->link('account/password', '', true);
		$data['address'] = $this->url->link('account/address', '', true);
		
		$data['credit_cards'] = array();
		
		$files = glob(DIR_APPLICATION . 'controller/extension/credit_card/*.php');
		
		if(!empty($files)) foreach ($files as $file) {
			$code = basename($file, '.php');
			
			if ($this->config->get($code . '_status') && $this->config->get($code . '_card')) {
				$this->load->language('extension/credit_card/' . $code);

				$data['credit_cards'][] = array(
					'name' => $this->language->get('heading_title'),
					'href' => $this->url->link('extension/credit_card/' . $code, '', true)
				);
			}
		}
		
		$data['wishlist'] = $this->url->link('account/wishlist');
		$data['order'] = $this->url->link('account/order', '', true);
		$data['download'] = $this->url->link('account/download', '', true);
		
		if ($this->config->get('reward_status')) {
			$data['reward'] = $this->url->link('account/reward', '', true);
		} else {
			$data['reward'] = '';
		}		
		
		$data['return'] = $this->url->link('account/return', '', true);
		$data['transaction'] = $this->url->link('account/transaction', '', true);
		$data['newsletter'] = $this->url->link('account/newsletter', '', true);
		$data['recurring'] = $this->url->link('account/recurring', '', true);
		
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');
		
        $this->load->model('checkout/order');
        $customer_id = $this->session->data['customer_id'];
        $data['orders'] = Array();
        $data['customer'] = Array();

        if($this->customer->getNewsletter() == 1) {
            $data['newsletter'] = true;
        } else {
            $data['newsletter'] = false;
        }
        if(!empty($customer_id)) {
            // Check discount
                $this->session->data['personal_discount'] = 0;
                $this->session->data['personal_discount_percentage'] = 0;
                
                $this->session->data['cumulative_discount'] = 0;
                $this->session->data['cumulative_discount_percentage'] = 0;
                
                // Personal discount
                    $customer_discount = (int)$this->customer->getCustomerDiscount($customer_id);
                    
                    $basePrice = $this->cart->getTotal();
                    $order_discount = $customer_discount/100;

                    $this->session->data['personal_discount'] = floor($order_discount * $basePrice);
                    $this->session->data['personal_discount_percentage'] = $customer_discount;
                // ---

                // Cumulative discount
                    $totalCustomerOutcome = 0;

                    if($orders !== false) {
                        foreach($orders as $order) {
                            if($order['order_status_id'] == 5) {
                                $totalCustomerOutcome += $order['total'];
                            }
                        }
                    }

                    $cumulative_discount = intval(floor($totalCustomerOutcome/10000));
                    if( $cumulative_discount > intval($this->config->get('config_max_discount')) ) $cumulative_discount = intval($this->config->get('config_max_discount'));
                    
                    $basePrice = $this->cart->getTotal();
                    $order_discount = $cumulative_discount/100;


                    $this->session->data['cumulative_discount'] = floor($order_discount * $basePrice);
                    $this->session->data['cumulative_discount_percentage'] = $cumulative_discount;
                // ---


                $data['discount'] = 0;
                $data['discount_percentage'] = 0;

                if(!$this->customer->getCouponDiscount()) {
                    // ---
                        if( $this->session->data['personal_discount_percentage'] > $this->session->data['cumulative_discount_percentage'] ) {
                            $data['discount'] = $this->session->data['personal_discount'];
                            $data['discount_percentage'] = $this->session->data['personal_discount_percentage'];
                        }
                        else{
                            $data['discount'] = $this->session->data['cumulative_discount'];
                            $data['discount_percentage'] = $this->session->data['cumulative_discount_percentage'];
                        }
                    // ---
                } else {
                    // ---
                        $coupon = $this->customer->getCouponDiscount();

                        if($coupon['type'] == 'P') {
                            // ---
                                $couponDiscount = $coupon['discount'] / 100 * $this->cart->getTotal();
                                $couponPercentage = intval($coupon['discount']);

                                if( $couponPercentage > $personal_discount_percentage  && $couponPercentage > $cumulative_discount_percentage ) {
                                    $data['discount'] = $couponDiscount;
                                    $data['discount_percentage'] = $couponPercentage;

                                    $data['coupon'] = true;
                                }
                                else{
                                    if( $personal_discount_percentage > $cumulative_discount_percentage ) {
                                        $data['discount'] = $personal_discount;
                                        $data['discount_percentage'] = $personal_discount_percentage;
                                    }
                                    else{
                                        $data['discount'] = $cumulative_discount;
                                        $data['discount_percentage'] = $cumulative_discount_percentage;
                                    }
                                }
                            // ---
                        }
                    // ---
                }
            // ---
            
            $addresses = $this->customer->getAddresses();
            $arAddress = Array();
            if(!empty($addresses)) {
                foreach($addresses as $address) {
                    $arAddress[] = Array(
                        'address_id' => $address['address_id'],
                        'value' => $address['address_1']
                    );
                }
            } else {
                $arAddress[] = Array(
                    'address_id' => 0,
                    'value' => ''
                );
            }
            $data['customer'] = Array(
                'firstname' => $this->customer->getFirstName(),
                'telephone' => $this->customer->getTelephone(),
                'email' => $this->customer->getEmail(),
                'addresses' => $arAddress
            );
        }
        
        // Предпочитаемые товары
        $results = $this->model_catalog_product->getProductsPreferable();
        $data['pref_products'] = $results;
        
        $this->response->setOutput($this->load->view('account/edit', $data));
	}
	protected function validate() {
		if ((utf8_strlen(trim($this->request->post['firstname'])) < 1) || (utf8_strlen(trim($this->request->post['firstname'])) > 32)) {
			$this->error['firstname'] = $this->language->get('error_firstname');
		}

		if ((utf8_strlen(trim($this->request->post['lastname'])) < 1) || (utf8_strlen(trim($this->request->post['lastname'])) > 32)) {
			$this->error['lastname'] = $this->language->get('error_lastname');
		}

		if ((utf8_strlen($this->request->post['email']) > 96) || !filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)) {
			$this->error['email'] = $this->language->get('error_email');
		}

		if (($this->customer->getEmail() != $this->request->post['email']) && $this->model_account_customer->getTotalCustomersByEmail($this->request->post['email'])) {
			$this->error['warning'] = $this->language->get('error_exists');
		}

		if ((utf8_strlen($this->request->post['telephone']) < 3) || (utf8_strlen($this->request->post['telephone']) > 32)) {
			$this->error['telephone'] = $this->language->get('error_telephone');
		}

		// Custom field validation
		$this->load->model('account/custom_field');

		$custom_fields = $this->model_account_custom_field->getCustomFields($this->config->get('config_customer_group_id'));

		foreach ($custom_fields as $custom_field) {
			if (($custom_field['location'] == 'account') && $custom_field['required'] && empty($this->request->post['custom_field'][$custom_field['custom_field_id']])) {
				$this->error['custom_field'][$custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);
			} elseif (($custom_field['location'] == 'account') && ($custom_field['type'] == 'text') && !empty($custom_field['validation']) && !filter_var($this->request->post['custom_field'][$custom_field['custom_field_id']], FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => $custom_field['validation'])))) {
                $this->error['custom_field'][$custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);
            }
		}

		return !$this->error;
	}
}