<?php
class ControllerCommonCart extends Controller {
    
        public function index($options = array()) {
                $this->load->language('checkout/cart');
            
                if(empty($options['empty'])) {
                    $data['page_cart'] = $this->cart();
                    $data['page_customer'] = $this->customer();
                    $data['page_payment'] = $this->payment();
                    $data['page_success'] = $this->success();
                } else {
                    $data['page_cart'] = '';
                    $data['page_customer'] = '';
                    $data['page_payment'] = '';
                    $data['page_success'] = '';
                }
                
                return $this->load->view('common/cart', $data);
        }
        
        private function customer() {
                $this->load->model('checkout/order');
            
                // Totals
                $this->load->model('extension/extension');

                $totals = array();
                $taxes = $this->cart->getTaxes();
                $total = 0;

                // Because __call can not keep var references so we put them into an array. 			
                $total_data = array(
                        'totals' => &$totals,
                        'taxes'  => &$taxes,
                        'total'  => &$total
                );

                // Display prices
                if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
                        $sort_order = array();

                        $results = $this->model_extension_extension->getExtensions('total');

                        foreach ($results as $key => $value) {
                                $sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
                        }

                        array_multisort($sort_order, SORT_ASC, $results);

                        foreach ($results as $result) {
                                if ($this->config->get($result['code'] . '_status')) {
                                        $this->load->model('extension/total/' . $result['code']);

                                        // We have to put the totals in an array so that they pass by reference.
                                        $this->{'model_extension_total_' . $result['code']}->getTotal($total_data);
                                }
                        }

                        $sort_order = array();

                        foreach ($totals as $key => $value) {
                                $sort_order[$key] = $value['sort_order'];
                        }

                        array_multisort($sort_order, SORT_ASC, $totals);
                }

                $data['totals'] = array();

                foreach ($totals as $total) {
                        $data['totals'][] = array(
                                'title' => $total['title'],
                                'text'  => $this->currency->format($total['value'], $this->session->data['currency'])
                        );
                }


                $data['customer'] = Array(
                    'phone' => $this->customer->getTelephone(),
                    'first_name' => $this->customer->getFirstName()
                );
                
                if($customer_id = $this->customer->isLogged()) {
                        $orders = $this->model_checkout_order->getPersonalOrders($customer_id);
                        $data['customer_discount'] = $this->customer->getPersonalDiscount($customer_id, $orders);
                }
                if(isset($this->session->data['coupon_id'])) {
                    $data['customer_coupon'] = $this->customer->getCouponDiscount();
                }
                        
                $data['order_price'] = $this->cart->getOrderPrice();
                
                
                // Shipping
                $date = new DateTime();
                $mRus = Array(
                    '01' => 'января',
                    '02' => 'февраля',
                    '03' => 'марта',
                    '04' => 'апреля',
                    '05' => 'мая',
                    '06' => 'июня',
                    '07' => 'июля',
                    '08' => 'августа',
                    '09' => 'сентября',
                    '10' => 'октября',
                    '11' => 'ноября',
                    '12' => 'декабря'
                );
                
                $order_time = explode(':',$this->config->get('config_order_time'));
                
                $data['delivery_date'] = Array();
                if(((int)$date->format('H') < $order_time[0]) || ((int)$date->format('H') == $order_time[0] && (int)$date->format('i') < $order_time[1])) {                
                    $date->add(new DateInterval('P1D'));
                    $data['delivery_date'][] = Array(
                        'format' => $date->format('d.m.Y'),
                        'text' => 'Завтра '.$date->format('d')
                    );
                    $date->add(new DateInterval('P1D'));
                } else {
                    $date->add(new DateInterval('P2D'));
                }
                $data['delivery_date'][] = Array(
                    'format' => $date->format('d.m.Y'),
                    'text' => 'Послезавтра '.$date->format('d')
                );
                for($i=0;$i<5;$i++) {
                    $date->add(new DateInterval('P1D'));
                    $data['delivery_date'][] = Array(
                        'format' => $date->format('d.m.Y'),
                        'text' => $date->format('d').' '.$mRus[$date->format('m')]
                    );
                }
                
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
                    $arAddress = Array();
                }
                $data['delivery_address'] = $arAddress;
			
                $intervals = $this->config->get('config_delivery_intervals');
                if(!empty($intervals)) {
                    $data['delivery_intervals'] = explode(',',$intervals);
                } else {
                    $data['delivery_intervals'] = array();
                }
                
                
                return $this->load->view('common/cart_page_customer', $data);
        }
    
        private function payment() {
            // Payment Methods
            $method_data = array();

            $this->load->model('extension/extension');

            $results = $this->model_extension_extension->getExtensions('payment');

            $recurring = $this->cart->hasRecurringProducts();

            $total = $this->cart->getTotal();
            foreach ($results as $result) {
                    if ($this->config->get($result['code'] . '_status')) {
                            $this->load->model('extension/payment/' . $result['code']);

                            $method = $this->{'model_extension_payment_' . $result['code']}->getMethod(array('country_id' => 0, 'zone_id' => 0), $total);

                            if ($method) {
                                    $method['image'] = '/new_design/img/payment/' . $result['code'] . '.png';
                                    if ($recurring) {
                                            if (property_exists($this->{'model_extension_payment_' . $result['code']}, 'recurringPayments') && $this->{'model_extension_payment_' . $result['code']}->recurringPayments()) {
                                                    $method_data[$result['code']] = $method;
                                            }
                                    } else {
                                            $method_data[$result['code']] = $method;
                                    }
                            }
                    }
            }

            $sort_order = array();

            foreach ($method_data as $key => $value) {
                    $sort_order[$key] = $value['sort_order'];
            }

            array_multisort($sort_order, SORT_ASC, $method_data);

            $data['payment_methods'] = $method_data;
            
            return $this->load->view('common/cart_page_payment', $data);
        }
    
        private function success() {
            //$this->session->data['success_order_id'] = 12345;
            if(!empty($this->session->data['success_order_id'])) {
                    $success_order_id = $this->session->data['success_order_id'];
                    unset($this->session->data['success_order_id']);
            } else {
                    $success_order_id = 0;
            }

            $data['order_id'] = $success_order_id;

            return $this->load->view('common/cart_page_success', $data);
        }
        
        private function cart() {
                $totalPrice = 0;
                $totalPositions = 0;
                
                $data['products'] = Array();
                if ($this->cart->hasProducts() || !empty($this->session->data['vouchers'])) {
                        $this->load->model('tool/image');
                        $products = $this->cart->getProducts();
                        foreach($products as $i => $product) {
                            
                            if($product['weight_variants'] !== '') {
                                $weightVariants = explode(',', $product['weight_variants']);
                                $weightVariant = $weightVariants[$product['weight_variant']];
                                $wwLabel = '(' . $weightVariants[$product['weight_variant']] . ' ' . $product['weight_class'] . ')';
                                $product['name'] = $product['name'] . ' ' . $wwLabel;
                            } else {
                                $weightVariant = 1;
                            }
                            $totalPrice += $product['total'];
                            
                            $product['total'] = floor($product['total']);
                            $product['weightVariant'] = $weightVariant;
                            
                            $product['quantity'] = round($product['quantity']/$weightVariant);
                            
                            
                            if ($product['image_preview']) {
                                    $image = '/image/' . $product['image_preview'];
                            } else {
                                    $image = $this->model_tool_image->resize('eco_logo.png', 257, 240);
                            }

                            $product['image'] = $image;
                            
                            $product['link_remove'] = '/?route=ajax/index/ajaxRemoveCartProduct&cart_id='.$product['cart_id'];
                            $data['products'][] = $product;
                        }
                        
                        $data['error_total'] = floor($totalPrice) < 1000;
                        
                        $data['islogged'] = $this->customer->isLogged();
                        $data['total'] = number_format(floor($totalPrice), 0, '.', ' ');
//                        $data['total'] = floor($this->cart->getOrderPrice());
                        $data['discount'] = number_format(floor($totalPrice) - floor($this->cart->getOrderPrice()), 0, '.', ' ');
                        
                } else {
                        return false;
                }
            
                return $this->load->view('common/cart_page_cart', $data);
        }
        
	public function index_bak() {
		$this->load->language('common/cart');

		// Totals
		$this->load->model('extension/extension');

		$totals = array();
		$taxes = $this->cart->getTaxes();
		$total = 0;

		// Because __call can not keep var references so we put them into an array.
		$total_data = array(
			'totals' => &$totals,
			'taxes'  => &$taxes,
			'total'  => &$total
		);
		
		// Display prices
		if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
			$sort_order = array();

			$results = $this->model_extension_extension->getExtensions('total');

			foreach ($results as $key => $value) {
				$sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
			}

			array_multisort($sort_order, SORT_ASC, $results);

			foreach ($results as $result) {
				if ($this->config->get($result['code'] . '_status')) {
					$this->load->model('extension/total/' . $result['code']);

					// We have to put the totals in an array so that they pass by reference.
					$this->{'model_extension_total_' . $result['code']}->getTotal($total_data);
				}
			}

			$sort_order = array();

			foreach ($totals as $key => $value) {
				$sort_order[$key] = $value['sort_order'];
			}

			array_multisort($sort_order, SORT_ASC, $totals);
		}

		$data['text_empty'] = $this->language->get('text_empty');
		$data['text_cart'] = $this->language->get('text_cart');
		$data['text_checkout'] = $this->language->get('text_checkout');
		$data['text_recurring'] = $this->language->get('text_recurring');
		$data['text_items'] = sprintf($this->language->get('text_items'), $this->cart->countProducts() + (isset($this->session->data['vouchers']) ? count($this->session->data['vouchers']) : 0), $this->currency->format($total, $this->session->data['currency']));
		$data['text_loading'] = $this->language->get('text_loading');

		$data['button_remove'] = $this->language->get('button_remove');

		$this->load->model('tool/image');
		$this->load->model('tool/upload');

		$data['products'] = array();

		foreach ($this->cart->getProducts() as $product) {
			if ($product['image']) {
				$image = $this->model_tool_image->resize($product['image'], $this->config->get($this->config->get('config_theme') . '_image_cart_width'), $this->config->get($this->config->get('config_theme') . '_image_cart_height'));
			} else {
				$image = '';
			}

			$option_data = array();

			foreach ($product['option'] as $option) {
				if ($option['type'] != 'file') {
					$value = $option['value'];
				} else {
					$upload_info = $this->model_tool_upload->getUploadByCode($option['value']);

					if ($upload_info) {
						$value = $upload_info['name'];
					} else {
						$value = '';
					}
				}

				$option_data[] = array(
					'name'  => $option['name'],
					'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value),
					'type'  => $option['type']
				);
			}

			// Display prices
			if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
				$unit_price = $this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax'));
				
				$price = $this->currency->format($unit_price, $this->session->data['currency']);
				$total = $this->currency->format($unit_price * $product['quantity'], $this->session->data['currency']);
			} else {
				$price = false;
				$total = false;
			}

			$data['products'][] = array(
				'cart_id'   => $product['cart_id'],
				'thumb'     => $image,
				'name'      => $product['name'],
				'model'     => $product['model'],
				'option'    => $option_data,
				'recurring' => ($product['recurring'] ? $product['recurring']['name'] : ''),
				'quantity'  => $product['quantity'],
				'price'     => $price,
				'total'     => $total,
				'href'      => $this->url->link('product/product', 'product_id=' . $product['product_id'])
			);
		}

		// Gift Voucher
		$data['vouchers'] = array();

		if (!empty($this->session->data['vouchers'])) {
			foreach ($this->session->data['vouchers'] as $key => $voucher) {
				$data['vouchers'][] = array(
					'key'         => $key,
					'description' => $voucher['description'],
					'amount'      => $this->currency->format($voucher['amount'], $this->session->data['currency'])
				);
			}
		}

		$data['totals'] = array();

		foreach ($totals as $total) {
			$data['totals'][] = array(
				'title' => $total['title'],
				'text'  => $this->currency->format($total['value'], $this->session->data['currency']),
			);
		}

		$data['cart'] = $this->url->link('checkout/cart');
		$data['checkout'] = $this->url->link('checkout/checkout', '', true);
                
		return $this->load->view('common/cart', $data);
	}

	public function info() {
		$this->response->setOutput($this->index());
	}
}
