<?php
class ControllerExtensionModuleiBlogHome extends Controller {
	private $moduleName;
	private $homeName;
    private $modulePath;
    private $homePath;
	private $moduleModel;
	private $moduleVersion;
    private $extensionsLink;
	
    private $callModel;
    private $error = array(); 
    private $data = array();

    public function __construct($registry) {
        parent::__construct($registry);
        
        // Config Loader
        $this->config->load('isenselabs/iblog');
        
        // Module Constants
        $this->moduleName           = $this->config->get('iblog_name');
		$this->homeName   		= $this->config->get('iblog_home_name');
        $this->moduleNameSmall      = $this->config->get('iblog_name_small');
        $this->callModel            = $this->config->get('iblog_model');
        $this->modulePath           = $this->config->get('iblog_path');
	    $this->homePath   		= $this->config->get('iblog_home_path');
        $this->moduleVersion        = $this->config->get('iblog_version');   
		$this->moduleData_module    = $this->config->get('iblog_module_data');        
        $this->extensionsLink       = $this->url->link($this->config->get('iblog_link'), 'token=' . $this->session->data['token'].$this->config->get('iblog_link_params'), 'SSL');

        // Load Language
        $this->load->language($this->homePath);
        
        // Load Model
        $this->load->model($this->modulePath);
		        
        // Model Instance
        $this->moduleModel          = $this->{$this->callModel};

        // Global Variables      
        $this->data['moduleName']  		 = $this->moduleName;
		$this->data['moduleNameSmall']     = $this->moduleNameSmall;
        $this->data['modulePath']   	 	 = $this->modulePath;
		$this->data['moduleData_module']   = $this->moduleData_module;
		$this->data['moduleModel'] 		 = $this->moduleModel;
	   
        $this->data['limit']        = 15;
    }	
	
	
	public function index() {

		$this->document->setTitle($this->language->get('heading_title').' '.$this->version);
		$this->load->model('extension/module');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			if (!isset($this->request->get['module_id'])) {
				$this->model_extension_module->addModule($this->homeName, $this->request->post);
				$this->session->data['success'] = $this->language->get('text_create');
				$lastModuleID = $this->moduleModel->getLastModuleByCode($this->homeName);
				$this->response->redirect($this->url->link($this->homePath, 'token=' . $this->session->data['token'] . '&module_id=' . $lastModuleID[0]['module_id'], 'SSL'));
			} else {
				$this->model_extension_module->editModule($this->request->get['module_id'], $this->request->post);
				$this->session->data['success'] = $this->language->get('text_success');
				$this->response->redirect($this->url->link($this->homePath, 'token=' . $this->session->data['token'] . '&module_id=' . $this->request->get['module_id'], 'SSL'));
			}
		}

		$data['heading_title'] = $this->language->get('heading_title').' '.$this->version;
		
		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');

		$data['entry_name'] = $this->language->get('entry_name');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_width'] = $this->language->get('entry_width');
		$data['entry_height'] = $this->language->get('entry_height');
		$data['entry_limit'] = $this->language->get('entry_limit');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}
		
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		
		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = '';
		}
		
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_module'),
			'href' => $this->extensionsLink,
		);

		if (!isset($this->request->get['module_id'])) {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link($this->homePath, 'token=' . $this->session->data['token'], 'SSL')
			);
		} else {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link($this->homePath, 'token=' . $this->session->data['token'] . '&module_id=' . $this->request->get['module_id'], 'SSL')
			);			
		}
		
		if (!isset($this->request->get['module_id'])) {
			$data['action'] = $this->url->link($this->homePath, 'token=' . $this->session->data['token'], 'SSL');
		} else {
			$data['action'] = $this->url->link($this->homePath, 'token=' . $this->session->data['token'] . '&module_id=' . $this->request->get['module_id'], 'SSL');
		}
		
		$data['cancel'] = $this->extensionsLink;
		
		if (isset($this->request->get['module_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$module_info = $this->model_extension_module->getModule($this->request->get['module_id']);
		}
		
		if (isset($this->request->post['name'])) {
			$data['name'] = $this->request->post['name'];
		} elseif (!empty($module_info)) {
			$data['name'] = $module_info['name'];
		} else {
			$data['name'] = '';
		}
		
		if (isset($this->request->post['limit'])) {
			$data['limit'] = $this->request->post['limit'];
		} elseif (!empty($module_info)) {
			$data['limit'] = $module_info['limit'];
		} else {
			$data['limit'] = '';
		}
				
		if (isset($this->request->post['width'])) {
			$data['width'] = $this->request->post['width'];
		} elseif (!empty($module_info)) {
			$data['width'] = $module_info['width'];
		} else {
			$data['width'] = '100';
		}
		
		if (isset($this->request->post['height'])) {
			$data['height'] = $this->request->post['height'];
		} elseif (!empty($module_info)) {
			$data['height'] = $module_info['height'];
		} else {
			$data['height'] = '100';
		}
		
		if (isset($this->request->post['featured'])) {
			$data['featured'] = $this->request->post['featured'];
		} elseif (!empty($module_info)) {
			$data['featured'] = $module_info['featured'];
		} else {
			$data['featured'] = 'no';
		}
		
		if (isset($this->request->post['custom_css'])) {
			$data['custom_css'] = $this->request->post['custom_css'];
		} elseif (!empty($module_info)) {
			$data['custom_css'] = $module_info['custom_css'];
		} else {
			$data['custom_css'] = '';
		}
		
		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($module_info)) {
			$data['status'] = $module_info['status'];
		} else {
			$data['status'] = '';
		}
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view($this->homePath.'.tpl', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', $this->homePath)) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 64)) {
			$this->error['name'] = $this->language->get('error_name');
		}

		return !$this->error;
	}
}