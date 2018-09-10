<?php
class ControllerAccountAccount extends Controller {
	public function index() {
		if (!$this->customer->isLogged()) {
            unset($this->session->data['redirect']);
            $this->session->data['redirect'] = $this->url->link('account/account', '', true);

			$this->response->redirect('/#modal');
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

                        // Check paid and surcharge
                            // Get order success paymants
                                $paid = false;
                                $surcharge = false;
                                $total_paid = 0;

                                $paymants = $this->model_checkout_order->getOrderPayments($order['order_id'], 20);

                                if($paymants !== false) {
                                    foreach($paymants as $paymant) {
                                        $total_paid = $total_paid + $paymant['total'];
                                    }
                                }

                                if( $total_paid == $order['order_total'] ) {
                                    // ---
                                        $paid = true;
                                    // ---
                                }
                                else {
                                    // ---
                                        if( $total_paid == 0 ) {
                                            $paid = false;
                                            $surcharge = false;
                                        }
                                        else {
                                            $paid = false;
                                            $surcharge = true;
                                        }
                                    // ---
                                }
                            // ---

                            // $payment_custom_field = 'undefined';

                            // if( isset($order['payment_custom_field']) && strpos($order['payment_custom_field'],$order['order_id'])>=0){
                            //     // ---
                            //         $payment_custom_field = $order['payment_custom_field'];
                            //     // ---
                            // }
                            
                            // if( $order['order_status_id'] != 5 && $order['order_status_id'] != 7 && $order['order_status_id'] != 20 ){
                            //     $online_pay = true;
                            // }
                            // else { $online_pay = false; }
                        // ---

                        $data['orders'][] = Array(
                            'order_id' => $order['order_id'],
                            'date' => $date->format('d.m.Y'),
                            'status' => $order['status_text'],
                            'status_id' => $order['order_status_id'],
                            'total' => $order['order_total'],
                            'payment_custom_field' => $payment_custom_field,
                            'paid' => $paid,
                            'surcharge' => $surcharge,
                            'total_paid' => $total_paid,
                            'paymants' => $paymants,
                        );
                    }
                }
            // ---
            

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
