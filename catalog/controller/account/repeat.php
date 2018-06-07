<?php
class ControllerAccountRepeat extends Controller {

	public function index($options = array()) {
    	$data['order_id'] = $options['order_id'];


    	$this->load->model('account/order');
    	$this->load->model('catalog/product');
    	$this->load->model('tool/image');


    	$total = 0;
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
	            $product_total = $this->currency->format($product['total'] + ($this->config->get('config_tax') ? ($product['tax'] * $product['quantity']) : 0), $order_info['currency_code'], $order_info['currency_value']);
           		$total = $total + $product_total;
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
				'price'    => $this->currency->format($product['price'] + ($this->config->get('config_tax') ? $product['tax'] : 0), $order_info['currency_code'], $order_info['currency_value']),
				'total'    => $product_total,
				'return'   => $this->url->link('account/return/add', 'order_id=' . $order_info['order_id'] . '&product_id=' . $product['product_id'], true)
			);
		}

		$data['total'] = $total;

        return $this->load->view('account/repeat',$data);
    }

}