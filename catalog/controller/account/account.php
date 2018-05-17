<?php
class ControllerAccountAccount extends Controller {
	public function index() {
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/account', '', true);

			$this->response->redirect($this->url->link('account/login', '', true));
		}
                
                $this->load->model('account/user');
                if (isset($this->session->data['user_id']) && $this->model_account_user->isAdmin($this->session->data['user_id']))
                {
                    $data['is_admin'] = true;
                }
                else
                {
                    $data['is_admin'] = false;
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
            // Get orders
                $orders = $this->model_checkout_order->getPersonalOrders($customer_id);
                
                if($orders !== false) {
                    foreach($orders as $order) {
                        $date = new DateTime($order['date_added']);

                        // Check status and payment
                            $payment_custom_field = 'undefined';

                            //if( $order['order_status_id'] == $this->config->get('config_payment_status_id') ){
                                if( isset($order['payment_custom_field']) && strpos($order['payment_custom_field'],$order['order_id'])>=0){
                                    // ---
                                        $payment_custom_field = $order['payment_custom_field'];
                                    // ---
                                }
                            //}
                        // ---

                        $data['orders'][] = Array(
                            'order_id' => $order['order_id'],
                            'date' => $date->format('d.m.Y'),
                            'status' => $order['status_text'],
                            'status_id' => $order['order_status_id'],
                            'total' => $order['order_total'],
                            'payment_custom_field' => $payment_custom_field
                        );
                    }
                }
                $data['customer_discount'] = -1 * $this->customer->getPersonalDiscount($customer_id, $orders);
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
        $data['pref_products'] = Array();
        foreach ($results as $result) {
                if(($result['quantity'] <= 0 && $result['stock_status_id'] == 5) || $result['status'] != 1) {
                    continue;
                }
                if ($result['image_preview']) {
                        $image = '/image/'.$result['image_preview'];
                        //$image = $this->model_tool_image->resize($result['image'], $this->config->get($this->config->get('config_theme') . '_image_product_width'), $this->config->get($this->config->get('config_theme') . '_image_product_height'));
                } else {
                        $this->load->model('tool/image');
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

                if (isset($this->request->get['limit'])) {
                        $url .= '&limit=' . $this->request->get['limit'];
                }
                $arProducts = array(
                        'product_id'  => $result['product_id'],
                        'available_in_time' => $result['available_in_time'],
                        'status'      => $result['status'],
                        'quantity'    => $result['quantity'],
                        'thumb'       => $image,
                        'name'        => $result['name'],
                        'description_short' => $result['description_short'],
                        'description' => utf8_substr(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8')), 0, $this->config->get($this->config->get('config_theme') . '_product_description_length')) . '...',
                        'price'       => $price,
                        'special'     => $special,
                        'tax'         => $tax,
                        'minimum'     => $result['minimum'] > 0 ? $result['minimum'] : 1,
                        'rating'      => $result['rating'],
                        'href'        => $this->url->link('product/product', 'product_id=' . $result['product_id'] . $url),
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
                if($data['is_admin']) {
                        $arProducts['edit_link'] = '/admin?route=catalog/product/edit&token='.$this->session->data['token'].'&product_id='.$result['product_id'];
                }
                if($result['composite_price'] !== false) {
                        $arProducts['composite_price'] = json_encode($result['composite_price']);
                }
                $data['pref_products'][] = $arProducts;
        }
        
        $this->response->setOutput($this->load->view('account/account', $data));
	}

	public function country() {
		$json = array();

		$this->load->model('localisation/country');

		$country_info = $this->model_localisation_country->getCountry($this->request->get['country_id']);

		if ($country_info) {
			$this->load->model('localisation/zone');

			$json = array(
				'country_id'        => $country_info['country_id'],
				'name'              => $country_info['name'],
				'iso_code_2'        => $country_info['iso_code_2'],
				'iso_code_3'        => $country_info['iso_code_3'],
				'address_format'    => $country_info['address_format'],
				'postcode_required' => $country_info['postcode_required'],
				'zone'              => $this->model_localisation_zone->getZonesByCountryId($this->request->get['country_id']),
				'status'            => $country_info['status']
			);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
