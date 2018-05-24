<?php
class ControllerExtensionDashboardProcurement extends Controller {
	private $error = array();
	
	public function index() {
		$this->load->language('extension/dashboard/procurement');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('dashboard_procurement', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=dashboard', true));
		}

		$data['heading_title'] = $this->language->get('heading_title');
		
		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');

		$data['entry_width'] = $this->language->get('entry_width');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_sort_procurement'] = $this->language->get('entry_sort_procurement');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=dashboard', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/dashboard/procurement', 'token=' . $this->session->data['token'], true)
		);

		$data['action'] = $this->url->link('extension/dashboard/procurement', 'token=' . $this->session->data['token'], true);

		$data['cancel'] = $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=dashboard', true);

		if (isset($this->request->post['dashboard_procurement_width'])) {
			$data['dashboard_procurement_width'] = $this->request->post['dashboard_procurement_width'];
		} else {
			$data['dashboard_procurement_width'] = $this->config->get('dashboard_procurement_width');
		}
		
		$data['columns'] = array();
		
		for ($i = 3; $i <= 12; $i++) {
			$data['columns'][] = $i;
		}
				
		if (isset($this->request->post['dashboard_procurement_status'])) {
			$data['dashboard_procurement_status'] = $this->request->post['dashboard_procurement_status'];
		} else {
			$data['dashboard_procurement_status'] = $this->config->get('dashboard_procurement_status');
		}

		if (isset($this->request->post['dashboard_procurement_sort_procurement'])) {
			$data['dashboard_procurement_sort_procurement'] = $this->request->post['dashboard_procurement_sort_procurement'];
		} else {
			$data['dashboard_procurement_sort_procurement'] = $this->config->get('dashboard_procurement_sort_procurement');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/dashboard/procurement_form', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/analytics/google_analytics')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
	
	public function dashboard() {
		$this->load->language('extension/dashboard/procurement');

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_view'] = $this->language->get('text_view');

		$data['token'] = $this->session->data['token'];

		// Load API library
		include_once(DIR_APPLICATION . '/model/api/api.php');
		$apiKey = $this->config->get('config_apikey');
		$oc = new OpenCart\OpenCart(HTTP_CATALOG);

		if ($oc->login($apiKey)) {
		   //---
				$results = $oc->order->products(0,13,-1);
				$count = count($results['products']);


				if ($count > 1000000000000) {
					$data['total'] = round($count / 1000000000000, 1) . 'T';
				} elseif ($count > 1000000000) {
					$data['total'] = round($count / 1000000000, 1) . 'B';
				} elseif ($count > 1000000) {
					$data['total'] = round($count / 1000000, 1) . 'M';
				} elseif ($count > 1000) {
					$data['total'] = round($count / 1000, 1) . 'K';
				} else {
					$data['total'] = $count;
				}
		   //---
		} else {
		   $data['error'] = $oc->getLastError();
		}


		$data['procurement'] = $this->url->link('procurement/products', 'token=' . $this->session->data['token'], true);

		return $this->load->view('extension/dashboard/procurement_info', $data);
	}
}