<?php
class ControllerReportMsloss extends Controller {
	const MS_AUTH = 'admin@mail195:b41fd841edc5';
	private $error = array();

	public function index() {
		$this->load->language('report/ms_loss');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_total'] = $this->language->get('text_total');

		$data['text_default'] = $this->language->get('text_default');
		$data['text_newsletter'] = $this->language->get('text_newsletter');
		$data['text_customer_all'] = $this->language->get('text_customer_all');
		$data['text_customer'] = $this->language->get('text_customer');
		$data['text_customer_group'] = $this->language->get('text_customer_group');
		$data['text_affiliate_all'] = $this->language->get('text_affiliate_all');
		$data['text_affiliate'] = $this->language->get('text_affiliate');
		$data['text_product'] = $this->language->get('text_product');
		$data['text_loading'] = $this->language->get('text_loading');

		$data['entry_store'] = $this->language->get('entry_store');
		$data['entry_to'] = $this->language->get('entry_to');
		$data['entry_customer_group'] = $this->language->get('entry_customer_group');
		$data['entry_customer'] = $this->language->get('entry_customer');
		$data['entry_affiliate'] = $this->language->get('entry_affiliate');

		$data['entry_number'] = $this->language->get('entry_number');
		$data['entry_date'] = $this->language->get('entry_date');
		$data['entry_held'] = $this->language->get('entry_held');
		$data['entry_organisation'] = $this->language->get('entry_organisation');
		$data['entry_stock'] = $this->language->get('entry_stock');
		$data['entry_project'] = $this->language->get('entry_project');

		$data['entry_product'] = $this->language->get('entry_product');
		$data['entry_comment'] = $this->language->get('entry_comment');

		$data['help_customer'] = $this->language->get('help_customer');
		$data['help_affiliate'] = $this->language->get('help_affiliate');
		$data['help_product'] = $this->language->get('help_product');

		$data['button_send'] = $this->language->get('button_send');
		$data['button_cancel'] = $this->language->get('button_cancel');

		$data['token'] = $this->session->data['token'];

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('report/ms_loss', 'token=' . $this->session->data['token'], true)
		);

		$data['cancel'] = $this->url->link('report/ms_loss', 'token=' . $this->session->data['token'], true);

		$this->load->model('setting/store');

		$data['stores'] = $this->model_setting_store->getStores();

		$this->load->model('customer/customer_group');

		$data['customer_groups'] = $this->model_customer_customer_group->getCustomerGroups();

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('report/ms_loss', $data));
	}

	public function load() {
		// Init
         	//$product_id = $this->request->post['product_id'];
        	$response = new stdClass();
			
			$this->load->language('report/ms_loss');
        // ---

		// Get organisation
			$urlOrganisation = 'https://online.moysklad.ru/api/remap/1.1/entity/organization';
			$dataOrganisation = array();
			$resoponseOrganisation = $this->connectAPI($urlOrganisation, $dataOrganisation, 'GET', self::MS_AUTH);

			$response->organisation = array();
			$response->organisation[] = array('name' => $resoponseOrganisation->rows[0]->name, 'meta' => $resoponseOrganisation->rows[0]->meta);
		// ---

		// Get store
			$urlStore = 'https://online.moysklad.ru/api/remap/1.1/entity/store';
			$dataStore = array();
			$resoponseStore = $this->connectAPI($urlStore, $dataStore, 'GET', self::MS_AUTH);

			$response->store = array();
			$response->store[] = array('name' => $resoponseStore->rows[0]->name, 'meta' => $resoponseStore->rows[0]->meta);
		// ---


		// Output
			$response->status = 'success';
	        $response->message = 'Запрос успешно отправлен.<br>Спасибо!';

	        echo json_encode($response);
	        exit;
        // ---
	}

	public function product() {
		// Init
         	$ms_id = $this->request->post['ms_id'];
        	$response = new stdClass();
			
			$this->load->language('report/ms_loss');
        // ---

		// Get product
			$urlProduct = 'https://online.moysklad.ru/api/remap/1.1/entity/product/'.$ms_id;
			$dataProduct = array();
			$resoponseProduct = $this->connectAPI($urlProduct, $dataProduct, 'GET', self::MS_AUTH);

			$response->product = $resoponseProduct;
			//$response->product = array('name' => $resoponseOrganisation->rows[0]->name, 'meta' => $resoponseOrganisation->rows[0]->meta);
		// ---

		// Output
			$response->status = 'success';
	        $response->message = 'Запрос успешно отправлен.<br>Спасибо!';

	        echo json_encode($response);
	        exit;
        // ---
	}

	public function create() {
		// Init
         	$positions = $this->request->post['positions'];

        	$response = new stdClass();
			
			$this->load->language('report/ms_loss');
        // ---

		// Get organisation
			$urlOrganisation = 'https://online.moysklad.ru/api/remap/1.1/entity/organization';
			$dataOrganisation = array();
			$resoponseOrganisation = $this->connectAPI($urlOrganisation, $dataOrganisation, 'GET', self::MS_AUTH);

			$response->organization = $resoponseOrganisation->rows[0];
		// ---

		// Get store
			$urlStore = 'https://online.moysklad.ru/api/remap/1.1/entity/store';
			$dataStore = array();
			$resoponseStore = $this->connectAPI($urlStore, $dataStore, 'GET', self::MS_AUTH);

			$response->store = $resoponseStore->rows[0];
		// ---

		// Get positions
			$response->positions = array();

			foreach ($positions as $key => $position) {
				// ---
					$urlProduct = 'https://online.moysklad.ru/api/remap/1.1/entity/product/'.$position['ms_id'];
					$dataProduct = array();
					$resoponseProduct = $this->connectAPI($urlProduct, $dataProduct, 'GET', self::MS_AUTH);

					$response->positions[] = array("quantity" => (float)$position['quantity'], "assortment" => array("meta"=>$resoponseProduct->meta));
				// ---
			}
		// ---

		// Create loss
			$urlLoss = 'https://online.moysklad.ru/api/remap/1.1/entity/loss';
			$dataLoss = array(
				"store" => $response->store,
				"organization" => $response->organization,
				"positions" => $response->positions,
			);

			$response->loss = $this->connectAPI($urlLoss, json_encode($dataLoss), 'POST', self::MS_AUTH);
		// ---

		// Output
			$response->status = 'success';
	        $response->message = 'Запрос успешно отправлен.<br>Спасибо!';

	        echo json_encode($response);
	        exit;
        // ---
	}

	public function connectAPI($url, $qdata, $request, $auth='', $cookie='') {
		// ---
			$data = $qdata;

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,$url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $request);  
			curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			if( !empty($auth) ){
				curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
				curl_setopt($ch, CURLOPT_USERPWD, $auth);
			}
			curl_setopt($ch, CURLOPT_COOKIE, $cookie);
			$headers = ['Content-Type: application/json'];
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
		// ---
	}
}