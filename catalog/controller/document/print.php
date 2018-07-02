<?php
class ControllerDocumentPrint extends Controller {
	private $error = array();

	public function index() {
		// Init
			$customer_id = $this->request->get['customer_id'];
			$order_id = $this->request->get['order_id'];
			$template = $this->request->get['template'];
		// ---


		// Get customer
			$this->load->model('checkout/order');
			
			$order_info = $this->model_checkout_order->getOrder($order_id);
			
			$data['order_info'] = $order_info;

			$data['customer_id'] = $order_info['customer_id'];
			$data['customer_name'] = $order_info['firstname'];
		// ---

		// Create coupon
			$data['coupon_code'] = '';
			$data['coupon_discount'] = '';
			$data['coupon_end'] = '';

			if( $template == 'clients_letter_one' ){
				// ---
					$this->load->model('document/print');

					$coupon_info = $this->model_document_print->createCoupon($order_id, $order_info['customer_id']);
					
					$data['coupon_code'] = $coupon_info['coupon_code'];
					$data['coupon_discount'] = intval($coupon_info['coupon_discount']);
					$data['coupon_end'] = $coupon_info['coupon_end'];
				// ---
			}
		// ---

		$this->response->setOutput($this->load->view('document/'.$template, $data));
	}

}
