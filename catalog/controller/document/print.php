<?php
class ControllerDocumentPrint extends Controller {
	const RETAILCRM_KEY = 'AuNf4IgJFHTmZQu7PwTKuPNQch5v03to';
  	const MS_AUTH = 'admin@mail195:134679';

	private $error = array();

	public function index() {
		// ---
			$this->load->model('document/print');

			// Init
				$template = $this->request->get['template'];
			// ---


			switch ($template) {
				case 'clients_letter_one':
					// ---
						// Custom init
							$customer_id = $this->request->get['customer_id'];
							$order_id = $this->request->get['order_id'];
							$document_code = $this->request->get['code'];
						// ---

						// Get customer
							$this->load->model('checkout/order');
							
							$order_info = $this->model_checkout_order->getOrder($order_id);
							
							$data['order_info'] = $order_info;

							$data['customer_id'] = $order_info['customer_id'];
							$data['customer_name'] = $order_info['firstname'];
						// ---

						// Get coupon
							$data['coupon_code'] = '';
							$data['coupon_discount'] = '';
							$data['coupon_end'] = '';

							$coupon_info = $this->model_document_print->createCoupon($order_id, $order_info['customer_id'], $document_code);
							
							$data['coupon_code'] = $coupon_info['coupon_code'];
							$data['coupon_discount'] = intval($coupon_info['coupon_discount']);
							$data['coupon_end'] = $coupon_info['coupon_end'];
						// ---
					// ---
				break;

				case 'staff_document_packing':
					// ---
						// retailCRM
							/*$orders = array();

							// Get CRM orders
					            $url = 'https://eco-u.retailcrm.ru/api/v5/orders';
					            $qdata = array(
					            	'apiKey' => self::RETAILCRM_KEY, 'limit' => 100, 'page' => 1,
					            	'filter' => array('extendedStatus' => 'assembling')
					            );

					            $result = $this->connectGetAPI($url,$qdata);
					            $resultOrders = $result->orders;
					        // ---


							$packing = array();

							foreach ($resultOrders as $key => $order) {
								// ---
									$resultProducts = $order->items;
									
									foreach ($resultProducts as $key => $product) {
										// Get CRM categories
								            $url = 'https://eco-u.retailcrm.ru/api/v5/store/products';
								            $qdata = array(
								            	'apiKey' => self::RETAILCRM_KEY, 'limit' => 100, 'page' => 1,
								            	'filter' => array('externalId' => $product->offer->externalId)
								            );

								            $result = $this->connectGetAPI($url,$qdata);
								            $groups = array();

								            foreach ($result->products[0]->groups as $key => $group) {
								            	$groups[] = $group->id;
								            }
								        // ---

								        // 55/57/58 - Зелень | 54 - Овощи | 47 - Фркуты/Ягоды
										if( in_array(55, $groups) || in_array(57, $groups) || in_array(58, $groups) || in_array(54, $groups) || in_array(47, $groups) ) {
											$packing[$product->offer->id][] = $product;
										}
									}
								// ---
							}


							$data['packing'] = $packing; */
						// ---

						// Opencart
							$orders = array();

							$this->load->model('document/print');
							
							
							// Get orders
								$resultOrders = $this->model_document_print->getOrders(array(13));
					        // ---

							$packing = array();

							foreach ($resultOrders as $key => $order) {
								// ---
									$resultProducts = $this->model_document_print->getOrderProducts($order['order_id']);
									
									foreach ($resultProducts as $key => $product) {
										// Get categories
											$resultCategories = $this->model_document_print->getProductCategory($product['product_id']);
											
											$groups = array();

								            foreach ($resultCategories as $key => $category) {
								            	$groups[] = $category['category_id'];
								            }
								        // ---

								        // 35/36 - Зелень | 42/44 - Овощи | 68/69 - Фркуты/Ягоды
										if( in_array(35, $groups) || in_array(36, $groups) || in_array(42, $groups) || in_array(44, $groups) || in_array(68, $groups) || in_array(69, $groups) ) {
											$packing[$product['product_id']][] = $product;
										}
									}
								// ---
							}


							$data['packing'] = $packing;
						// ---
					// ---
				break;

				case 'staff_document_assembly':
					// ---
						// retailCRM
							/*$orders = array();

							// Get CRM orders
					            $url = 'https://eco-u.retailcrm.ru/api/v5/orders';
					            $qdata = array(
					            	'apiKey' => self::RETAILCRM_KEY, 'limit' => 100, 'page' => 1,
					            	'filter' => array('extendedStatus' => 'assembling')
					            );

					            $result = $this->connectGetAPI($url,$qdata);
					            $resultOrders = $result->orders;
					        // ---

					        
							foreach ($resultOrders as $key => $order) {
								// ---
									$resultProducts = $order->items;
									$products = array();

									foreach ($resultProducts as $key => $product) {
										// Get CRM categories
								            $url = 'https://eco-u.retailcrm.ru/api/v5/store/products';
								            $qdata = array(
								            	'apiKey' => self::RETAILCRM_KEY, 'limit' => 100, 'page' => 1,
								            	'filter' => array('externalId' => $product->offer->externalId)
								            );

								            $result = $this->connectGetAPI($url,$qdata);
								            $groups = array();

								            foreach ($result->products[0]->groups as $key => $group) {
								            	$groups[] = $group->id;
								            }
								        // ---

										// 55/57/58 - Зелень | 54 - Овощи | 47 - Фркуты/Ягоды
										if( in_array(55, $groups) || in_array(57, $groups) || in_array(58, $groups) || in_array(54, $groups) || in_array(47, $groups) ) {
											$products[] = $product;
										}
									}

									
									$orders[] = array(
										'order_id' => $order->number,
										'products' => $products
									);
								// ---
							}

							
							$data['orders'] = $orders;*/
						// ---


						// Opencart
							$orders = array();

							$this->load->model('document/print');

							// Get orders
								$resultOrders = $this->model_document_print->getOrders(array(13));
					        // ---

					        
							foreach ($resultOrders as $key => $order) {
								// ---
									$resultProducts = $this->model_document_print->getOrderProducts($order['order_id']);

									$products = array();

									foreach ($resultProducts as $key => $product) {
										// Get categories
											$resultCategories = $this->model_document_print->getProductCategory($product['product_id']);
											
											$groups = array();

								            foreach ($resultCategories as $key => $category) {
								            	$groups[] = $category['category_id'];
								            }
								        // ---
								            
										// 35/36 - Зелень | 42/44 - Овощи | 68/69 - Фркуты/Ягоды
										if( in_array(35, $groups) || in_array(36, $groups) || in_array(42, $groups) || in_array(44, $groups) || in_array(68, $groups) || in_array(69, $groups) ) {
											$products[] = $product;
										}
									}

									
									if( !empty($products) ){
										$orders[] = array( 'order_id' => $order['order_id'], 'products' => $products );
									}
								// ---
							}

							
							$data['orders'] = $orders;
						// ---
					// ---
				break;
			}
		// ---

		$this->response->setOutput($this->load->view('document/'.$template, $data));
	}

	// Curl
	    public function connectPostAPI($url, $qdata, $auth='', $cookie='') {

	        $data = http_build_query($qdata);

	        $ch = curl_init();
	        curl_setopt($ch, CURLOPT_URL,$url);
	        curl_setopt($ch, CURLOPT_POST, 1);
	        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");  
	        curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
	        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	        if( !empty($auth) ){
	          curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
	          curl_setopt($ch, CURLOPT_USERPWD, $auth);
	        }
	        curl_setopt($ch, CURLOPT_COOKIE, $cookie);
	        $headers = ['Content-Type: application/x-www-form-urlencoded'];
	        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	        curl_setopt($ch, CURLOPT_HEADER, false);

	        // Output
	        $output = curl_exec($ch);
	        $result = json_decode($output);

	        // Result
	        if( $result != null ){
	          curl_close ($ch);
	          return $result;
	        }
	        else {
	          curl_close ($ch);
	          return false;
	        }

	    }

    	public function connectGetAPI($url, $qdata, $auth='') {

	        $data = http_build_query($qdata);

	        $ch = curl_init();
	        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
	        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	        if( !empty($auth) ){
	          curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
	          curl_setopt($ch, CURLOPT_USERPWD, $auth);
	        }
	        curl_setopt($ch, CURLOPT_URL,$url.'?'.$data);
	        curl_setopt($ch, CURLOPT_TIMEOUT, 80);

	        // Output
	        $output = curl_exec($ch);
	        $result = json_decode($output);

	        // Result
	        if( $result != null ){
	          curl_close ($ch);
	          return $result;
	        }
	        else {
	          curl_close ($ch);
	          return false;
	        }

    	}
    // ---

}
