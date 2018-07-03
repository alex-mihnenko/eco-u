<?php
class ControllerDocumentPrint extends Controller {
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

							$coupon_info = $this->model_document_print->createCoupon($order_id, $order_info['customer_id']);
							
							$data['coupon_code'] = $coupon_info['coupon_code'];
							$data['coupon_discount'] = intval($coupon_info['coupon_discount']);
							$data['coupon_end'] = $coupon_info['coupon_end'];
						// ---
					// ---
				break;

				case 'staff_document_packing':
					// ---
						$order_status_ids = array(13); // 13 - статус заказа "Собирается"

						$resultOrders = $this->model_document_print->getOrders($order_status_ids);


						$packing = array();

						foreach ($resultOrders as $key => $order) {
							// ---
								$resultProducts = $this->model_document_print->getOrderProducts($order['order_id']);
								
								foreach ($resultProducts as $key => $product) {
									$packing[$product['product_id']][] = $product;
								}
							// ---
						}


						$categories = array();

						foreach ($packing as $product_id => $product_packing) {
							$category = $this->model_document_print->getProductCategory($product_id);

							// 35 - Зелень | 42 - Овощи | 68 - Фркуты/Ягоды
							if( $category['category_id'] == 35 || $category['category_id'] == 42 || $category['category_id'] == 68 ) {
								$categories[$category['category_id']][] = $product_packing;
							}
						}


						$data['categories'] = $categories;
					// ---
				break;

				case 'staff_document_assembly':
					// ---
						$orders = array();
						$order_status_ids = array(13); // 13 - статус заказа "Собирается"

						$resultOrders = $this->model_document_print->getOrders($order_status_ids);

						foreach ($resultOrders as $key => $order) {
							// ---
								$resultProducts = $this->model_document_print->getOrderProducts($order['order_id']);
								$categories = array();

								foreach ($resultProducts as $key => $product) {
									$category = $this->model_document_print->getProductCategory($product['product_id']);

									// 35 - Зелень | 42 - Овощи | 68 - Фркуты/Ягоды
									if( $category['category_id'] == 35 || $category['category_id'] == 42 || $category['category_id'] == 68 ) {
										$categories[$category['category_id']][] = $product;
									}
								}

								$orders[] = array(
									'order_id' => $order['order_id'],
									'categories' => $categories
								);
							// ---
						}

						$data['orders'] = $orders;
					// ---
				break;
			}
		// ---

		$this->response->setOutput($this->load->view('document/'.$template, $data));
	}

}
