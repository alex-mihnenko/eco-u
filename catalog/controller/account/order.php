<?php
class ControllerAccountOrder extends Controller {

	public function index($options = array()) {


    	$this->load->model('account/order');
    	$this->load->model('catalog/product');
    	$this->load->model('tool/image');


    	$data['order_id'] = $options['order_id'];
		$month = ['', 'января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'декабря'];

    	// Order info
			$order_info = $this->model_account_order->getOrder($options['order_id']);
			$data['shipping_method'] = $order_info['shipping_method'];
			$data['shipping_address_1'] = $order_info['shipping_address_1'];

			// Totals
				$order_totals = $this->model_account_order->getOrderTotals($options['order_id']);
				$data['order_totals'] = 0;

				foreach ($order_totals as $key => $total) {
					// ---
						if( $total['code'] == 'shipping' ) {
							$data['shipping_total'] = round($total['value']);
						}

						if( $total['code'] == 'total' ) {
							$data['total'] = round($total['value']);
						}
					// ---
				}
			// ---

			// Order date
				$date_added_arr = explode(' ', $order_info['date_added']);
				$date_added = explode('-', $date_added_arr[0]);

				$data['order_date'] = $date_added[2] . ' ' . $month[intval($date_added[1])] . ' ' . $date_added[0];
			// ---

			// Order status
				$data['order_status_id'] = $order_info['order_status_id'];
				$data['order_status_text'] = $this->model_account_order->getOrderStatus($order_info['order_status_id']);
			// ---

			// Delivery time
				$delivery_time_arr = explode(' ', $order_info['delivery_time']);
				$delivery_date_arr = explode('.', $delivery_time_arr[0]);

				$data['delivery_time'] = 'c '.str_replace('-',  ' до ', $delivery_time_arr[1]);
				$data['delivery_date'] = $delivery_date_arr[0] . ' ' . $month[intval($delivery_date_arr[1])];
			// ---
		// ---



		$products = $this->model_account_order->getOrderProducts($options['order_id']);

		foreach ($products as $product) {
			// Options
				$option_data = array();

				$options = $this->model_account_order->getOrderOptions($this->request->get['order_id'], $product['order_product_id']);

				foreach ($options as $option) {
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
						'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value)
					);
				}
			// ---

			$product_info = $this->model_catalog_product->getProduct($product['product_id']);

			// Image
				if ($product_info['image_preview']) {
	                $image = '/image/' . $product_info['image_preview'];
	            } else {
	                $image = $this->model_tool_image->resize('eco_logo.png', 257, 240);
	            }
            // ---

	        // Total
	            if( floatval($product_info['special_price']) > 0 ){
					$price = $product_info['special_price'];
				}
				else{
					$price = $product_info['price'];
				}

	            if($product_info['composite_price'] != false ) {
                    $cPrice = $this->config->get('config_composite_price');
                    $wVariants = explode(',', $product_info['weight_variants']);
                    $wKey = array_search($product['variant'], $wVariants);

                    if(isset($wVariants[$wKey]) && isset($cPrice[$wVariants[$wKey]])) {
                        $price = round(round($price * $cPrice[$wVariants[$wKey]]*$wVariants[$wKey])/$wVariants[$wKey]);
                    }
                }

				$product_price = (round($price));
				$product_total = ( round(($price) * $product['variant']) * $product['amount'] );
            // ---

			$data['products'][] = array(
				'image'    => $image,
				'name'     => $product_info['name'],
				'model'    => $product_info['model'],
				'quantity' => $product['quantity'],
				'quantity_stock' => $product_info['quantity'],
				'status' => $product_info['status'],
				'stock_status_id' => $product_info['stock_status_id'],
				'weight_class' => $product_info['weight_class'],
				'weight_variant' => $product_info['weight_variant'],
				'option'   => $option_data,
				'price'    => $product_price,
				'total'    => $product_total,
				'return'   => $this->url->link('account/return/add', 'order_id=' . $order_info['order_id'] . '&product_id=' . $product['product_id'], true)
			);
		}

        return $this->load->view('account/order_about',$data);
    }

}