<?php
class ControllerErrorNotFound extends Controller {
	public function index() {

		$this->load->language('error/not_found');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);
		


		if (isset($this->request->get['route'])) {
			$url_data = $this->request->get;

			// Check 404
				$this->load->model('catalog/url_alias');

				$parts = explode('/', $url_data['_route_']);
				$last = $parts[count($parts)-1];

				if( $last == 'index.php' || $last == 'index.html' || $last == 'default.html' ) {
					$last = $parts[count($parts)-2];
				}
				
				$result = $this->model_catalog_url_alias->getUrlAlias($last,'product_id');
				
				if (count($result) > 0 ){ $redirect = "/eda/".$last; }
			// ...

			unset($url_data['_route_']);

			$route = $url_data['route'];

			unset($url_data['route']);

			$url = '';

			if ($url_data) {
				$url = '&' . urldecode(http_build_query($url_data, '', '&'));
			}

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link($route, $url, $this->request->server['HTTPS'])
			);
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_error'] = $this->language->get('text_error');

		$data['button_continue'] = $this->language->get('button_continue');

		$data['continue'] = $this->url->link('common/home');

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		//$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 404 Not Found');

		//$this->response->setOutput($this->load->view('error/not_found', $data));
		
		if( isset($redirect)) {
			header($this->request->server['SERVER_PROTOCOL'] . ' 301 Moved Permanently');
            $this->response->redirect($redirect);
		}
		else { $this->response->redirect($this->url->link('common/home')); }
	}
}