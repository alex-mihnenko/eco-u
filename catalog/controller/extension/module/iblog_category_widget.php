<?php
class ControllerExtensionModuleiBlogCategoryWidget extends Controller {
	private $moduleName;
	private $categoryWidgetName;
    private $modulePath;
    private $categoryWidgetPath;
	private $moduleModel;
	private $moduleVersion;
	
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
		$this->moduleSearch    		= $this->config->get('iblog_search_path');

        // Load Language
        $this->load->language($this->modulePath);
        
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
		$this->data['moduleSearch'] 	 = $this->moduleSearch;
	   
        $this->data['limit']        = 15;
    }	
	
	
	public function index($setting) {
	
    	$data['heading_title'] = !empty($setting['name']) ? $setting['name'] : $this->language->get('heading_title');

		if (isset($this->request->get['path'])) {
			$parts = explode('_', (string)$this->request->get['path']);
		} else {
			$parts = array();
		}

		if (isset($parts[0])) {
			$data['category_id'] = $parts[0];
		} else {
			$data['category_id'] = 0;
		}

		if (isset($parts[1])) {
			$data['child_id'] = $parts[1];
		} else {
			$data['child_id'] = 0;
		}

		$this->load->model($this->modulePath.'');

		$data['categories'] = array();

		$categories = $this->moduleModel->getCategories(0);
	
		foreach ($categories as $category) {
			$children_data = array();

			if ($category['category_id'] == $data['category_id']) {
				$children = $this->moduleModel->getCategories($category['category_id']);

				foreach($children as $child) {
					$filter_data = array('filter_category_id' => $child['category_id'], 'filter_sub_category' => true);

					$children_data[] = array(
						'category_id' => $child['category_id'],
						'name' => $child['name'],
						'href' => $this->url->link($this->modulePath.'/category', 'path=' . $category['category_id'] . '_' . $child['category_id'])
					);

				}
			}

			$filter_data = array(
				'filter_category_id'  => $category['category_id'],
				'filter_sub_category' => true
			);

			$data['categories'][] = array(
				'category_id' => $category['category_id'],
				'name'        => $category['name'],
				'children'    => $children_data,
				'href'        => $this->url->link($this->modulePath.'/category', 'path=' . $category['category_id'])
			);
		}
				

		return $this->getOutput($this->categoryWidgetPath.'.tpl', $data);
	}

	private function getConfigTemplate() {
		if(version_compare(VERSION, '2.2.0.0', '<')) {
			return $this->config->get('config_template');
		} else {
			return  $this->config->get($this->config->get('config_theme') . '_directory');
		}
	}

	private function getOutput($TPL_name , $data) {
		if(version_compare(VERSION, '2.2.0.0', "<")) {
		    if (file_exists(DIR_TEMPLATE . $this->getConfigTemplate() . '/template/'. $TPL_name)) {
				return $this->load->view($this->getConfigTemplate().'/template/'. $TPL_name, $data);
			} else {
				return $this->load->view('default/template/'. $TPL_name, $data);
			}  
		} else {
		      	return $this->load->view($TPL_name , $data);
		 }
	}

}