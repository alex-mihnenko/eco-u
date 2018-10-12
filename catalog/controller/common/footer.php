<?php
class ControllerCommonFooter extends Controller {
	public function index() {
		$this->load->language('common/footer');

		// Scripts
			//$this->document->addScript('catalog/view/javascript/jquery-3.2.1.min.js');
			$this->document->addScript('catalog/view/javascript/jquery.liTabs.js');
			$this->document->addScript('catalog/view/javascript/jquery.selectric.js');
			$this->document->addScript('catalog/view/javascript/remodal.min.js');
			$this->document->addScript('catalog/view/javascript/jquery-ui.min.js');
			$this->document->addScript('catalog/view/javascript/placeholders.min.js');
			$this->document->addScript('catalog/view/javascript/dragend.min.js');
			$this->document->addScript('catalog/view/javascript/styling.js');
			$this->document->addScript('catalog/view/javascript/clamp.min.js');
			$this->document->addScript('catalog/view/javascript/jquery.suggestions.min.js');
			$this->document->addScript('catalog/view/javascript/jquery.jscrollpane.min.js');
			$this->document->addScript('catalog/view/javascript/jquery.mousewheel.js');
			$this->document->addScript('catalog/view/javascript/jquery.cookie.js');
			$this->document->addScript('catalog/view/javascript/ecomodal.js');
			$this->document->addScript('catalog/view/javascript/slick.min.js');
			$this->document->addScript('catalog/view/libs/cookie/js.cookie.min.js');
			$this->document->addScript('catalog/view/libs/enjoyhint-master/enjoyhint.js');
			$this->document->addScript('https://api-maps.yandex.ru/2.1/?lang=ru-RU');

			$this->document->addScript('catalog/view/javascript/input-mask/inputmask.min.js');
			$this->document->addScript('catalog/view/javascript/input-mask/jquery.inputmask.js');

			$this->document->addScript('catalog/view/javascript/bootstrap/b1760e6b3b.js');
			$this->document->addScript('catalog/view/javascript/bootstrap/popper.min.js');
			$this->document->addScript('catalog/view/javascript/bootstrap/bootstrap.min.js');
			$this->document->addScript('catalog/view/javascript/my_scripts.js');
			$this->document->addScript('catalog/view/javascript/app.js');
		// ---

		//$data['scripts'] = $this->document->getScripts('footer');
		$data['styles'] = $this->document->getStyles();
		$data['scripts'] = $this->document->getScripts();

		$data['text_information'] = $this->language->get('text_information');
		$data['text_service'] = $this->language->get('text_service');
		$data['text_extra'] = $this->language->get('text_extra');
		$data['text_contact'] = $this->language->get('text_contact');
		$data['text_return'] = $this->language->get('text_return');
		$data['text_sitemap'] = $this->language->get('text_sitemap');
		$data['text_manufacturer'] = $this->language->get('text_manufacturer');
		$data['text_voucher'] = $this->language->get('text_voucher');
		$data['text_affiliate'] = $this->language->get('text_affiliate');
		$data['text_special'] = $this->language->get('text_special');
		$data['text_account'] = $this->language->get('text_account');
		$data['text_order'] = $this->language->get('text_order');
		$data['text_wishlist'] = $this->language->get('text_wishlist');
		$data['text_newsletter'] = $this->language->get('text_newsletter');

		$this->load->model('catalog/information');

		$data['informations'] = array();

		foreach ($this->model_catalog_information->getInformations() as $result) {
			if ($result['bottom']) {
				$data['informations'][] = array(
					'title' => $result['title'],
					'href'  => $this->url->link('information/information', 'information_id=' . $result['information_id'])
				);
			}
		}

		// Contacts
			$data['telephone'] = $this->config->get('config_telephone');
			$data['telephone_href'] = preg_replace("/[^0-9,.]/", "",$this->config->get('config_telephone'));
			$data['email'] = $this->config->get('config_email');
		// ---

		$data['contact'] = $this->url->link('information/contact');
		$data['return'] = $this->url->link('account/return/add', '', true);
		$data['sitemap'] = $this->url->link('information/sitemap');
		$data['manufacturer'] = $this->url->link('product/manufacturer');
		$data['voucher'] = $this->url->link('account/voucher', '', true);
		$data['affiliate'] = $this->url->link('affiliate/account', '', true);
		$data['special'] = $this->url->link('product/special');
		$data['account'] = $this->url->link('account/account', '', true);
		$data['order'] = $this->url->link('account/order', '', true);
		$data['wishlist'] = $this->url->link('account/wishlist', '', true);
		$data['newsletter'] = $this->url->link('account/newsletter', '', true);

		$data['powered'] = sprintf($this->language->get('text_powered'), $this->config->get('config_name'), date('Y', time()));

		// Whos Online
		if ($this->config->get('config_customer_online')) {
			$this->load->model('tool/online');

			if (isset($this->request->server['REMOTE_ADDR'])) {
				$ip = $this->request->server['REMOTE_ADDR'];
			} else {
				$ip = '';
			}

			if (isset($this->request->server['HTTP_HOST']) && isset($this->request->server['REQUEST_URI'])) {
				$url = 'http://' . $this->request->server['HTTP_HOST'] . $this->request->server['REQUEST_URI'];
			} else {
				$url = '';
			}

			if (isset($this->request->server['HTTP_REFERER'])) {
				$referer = $this->request->server['HTTP_REFERER'];
			} else {
				$referer = '';
			}

			$this->model_tool_online->addOnline($ip, $this->customer->getId(), $url, $referer);
		}


		// Versions JS
			if( isset($this->session->data['controlversion']) ) {
				if( $this->session->data['controlversion'] < time()-1800 ) {
					unset($this->session->data['controlversion']);
					unset($this->session->data['jsversion']);
				}
			}

			if( !isset($this->session->data['jsversion']) ) {
				$this->session->data['jsversion'] = 'v'.rand(1, 1000);
				$this->session->data['controlversion'] = time();
			}

			$data['jsversion'] = $this->session->data['jsversion'];
		// ---

		if( isset( $this->session->data['ga_order_id'] ) ){
			$data['order_id'] = $this->session->data['ga_order_id'];
			unset($this->session->data['ga_order_id']);
		}

		return $this->load->view('common/footer', $data);
	}
}
