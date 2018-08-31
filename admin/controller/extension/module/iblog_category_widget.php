<?php
class ControllerExtensionModuleiBlogCategoryWidget extends Controller {
	private $moduleName;
	private $categoryWidgetName;
    private $modulePath;
    private $categoryWidgetPath;
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
		$this->categoryWidgetName   = $this->config->get('iblog_category_widget_name');
        $this->moduleNameSmall      = $this->config->get('iblog_name_small');
        $this->callModel            = $this->config->get('iblog_model');
        $this->modulePath           = $this->config->get('iblog_path');
	    $this->categoryWidgetPath   = $this->config->get('iblog_category_widget_path');
        $this->moduleVersion        = $this->config->get('iblog_version');   
		$this->moduleData_module    = $this->config->get('iblog_module_data');        
        $this->extensionsLink       = $this->url->link($this->config->get('iblog_link'), 'token=' . $this->session->data['token'].$this->config->get('iblog_link_params'), 'SSL');

        // Load Language
        $this->load->language($this->categoryWidgetPath);
        
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

		//$this->load->language('module/'.$this->categoryWidgetName);

		$this->document->setTitle($this->language->get('heading_title').' '.$this->version);
		
		$this->load->model('extension/module');
		//$this->load->model('module/iblog');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			if (!isset($this->request->get['module_id'])) {
				$this->model_extension_module->addModule($this->categoryWidgetName, $this->request->post);
				$this->session->data['success'] = $this->language->get('text_create');
				$lastModuleID = $this->moduleModel->getLastModuleByCode($this->categoryWidgetName);
				$this->response->redirect($this->url->link($this->categoryWidgetPath, 'token=' . $this->session->data['token'] . '&module_id=' . $lastModuleID[0]['module_id'], 'SSL'));
			} else {
				$this->model_extension_module->editModule($this->request->get['module_id'], $this->request->post);
				$this->session->data['success'] = $this->language->get('text_success');
				$this->response->redirect($this->url->link($this->categoryWidgetPath, 'token=' . $this->session->data['token'] . '&module_id=' . $this->request->get['module_id'], 'SSL'));
			}
		}

		$this->data['heading_title'] = $this->language->get('heading_title').' '.$this->version;
		
		$this->data['text_edit'] = $this->language->get('text_edit');
		$this->data['text_enabled'] = $this->language->get('text_enabled');
		$this->data['text_disabled'] = $this->language->get('text_disabled');

		$this->data['entry_name'] = $this->language->get('entry_name');
		$this->data['entry_status'] = $this->language->get('entry_status');
		$this->data['entry_width'] = $this->language->get('entry_width');
		$this->data['entry_height'] = $this->language->get('entry_height');

		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');

		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}
		
		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
		
		if (isset($this->error['name'])) {
			$this->data['error_name'] = $this->error['name'];
		} else {
			$this->data['error_name'] = '';
		}
		
		$this->data['breadcrumbs'] = array();

		$this->data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$this->data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_module'),
			'href' => $this->extensionsLink,
		);

		if (!isset($this->request->get['module_id'])) {
			$this->data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link($this->categoryWidgetPath, 'token=' . $this->session->data['token'], 'SSL')
			);
		} else {
			$this->data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link($this->categoryWidgetPath, 'token=' . $this->session->data['token'] . '&module_id=' . $this->request->get['module_id'], 'SSL')
			);			
		}
		
		if (!isset($this->request->get['module_id'])) {
			$this->data['action'] = $this->url->link($this->categoryWidgetPath, 'token=' . $this->session->data['token'], 'SSL');
		} else {
			$this->data['action'] = $this->url->link($this->categoryWidgetPath, 'token=' . $this->session->data['token'] . '&module_id=' . $this->request->get['module_id'], 'SSL');
		}
		
		$this->data['cancel'] = $this->extensionsLink;
		
		if (isset($this->request->get['module_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$module_info = $this->model_extension_module->getModule($this->request->get['module_id']);
		}
		
		if (isset($this->request->post['name'])) {
			$this->data['name'] = $this->request->post['name'];
		} elseif (!empty($module_info)) {
			$this->data['name'] = $module_info['name'];
		} else {
			$this->data['name'] = '';
		}
				
		if (isset($this->request->post['width'])) {
			$this->data['width'] = $this->request->post['width'];
		} elseif (!empty($module_info)) {
			$this->data['width'] = $module_info['width'];
		} else {
			$this->data['width'] = '100';
		}
		
		if (isset($this->request->post['height'])) {
			$this->data['height'] = $this->request->post['height'];
		} elseif (!empty($module_info)) {
			$this->data['height'] = $module_info['height'];
		} else {
			$this->data['height'] = '100';
		}
		
		if (isset($this->request->post['custom_css'])) {
			$this->data['custom_css'] = $this->request->post['custom_css'];
		} elseif (!empty($module_info)) {
			$this->data['custom_css'] = $module_info['custom_css'];
		} else {
			$this->data['custom_css'] = '';
		}
		
		if (isset($this->request->post['status'])) {
			$this->data['status'] = $this->request->post['status'];
		} elseif (!empty($module_info)) {
			$this->data['status'] = $module_info['status'];
		} else {
			$this->data['status'] = '';
		}
		
		$this->data['header'] = $this->load->controller('common/header');
		$this->data['column_left'] = $this->load->controller('common/column_left');
		$this->data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view($this->categoryWidgetPath.'.tpl', $this->data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', $this->categoryWidgetPath)) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 64)) {
			$this->error['name'] = $this->language->get('error_name');
		}

		return !$this->error;
	}
}