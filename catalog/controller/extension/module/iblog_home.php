<?php
class ControllerExtensionModuleiBlogHome extends Controller {
	
	private $moduleName;
	private $homeName;
    private $modulePath;
    private $homePath;
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
		$this->homeName   		= $this->config->get('iblog_home_name');
        $this->moduleNameSmall      = $this->config->get('iblog_name_small');
        $this->callModel            = $this->config->get('iblog_model');
        $this->modulePath           = $this->config->get('iblog_path');
	    $this->homePath   		= $this->config->get('iblog_home_path');
        $this->moduleVersion        = $this->config->get('iblog_version');   
		$this->moduleData_module    = $this->config->get('iblog_module_data');        

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
	   
        $this->data['limit']        = 15;
    }	
	
	public function index($setting) {

		$this->load->model('setting/setting');
    	$this->data['heading_title']				= $setting['name'];
		$this->data['no_featured']				= $this->language->get('no_featured');
		$this->data['no_posts']					= $this->language->get('no_posts');
		$this->data['iblog_button']				= $this->language->get('iblog_button');
		$this->data['text_all']				= $this->language->get('text_all');

		if(isset($setting['limit']))	{
			$limit = $setting['limit'];
		} else {
			$limit = $this->data['limit'];
		}
		
		$postData = array(
			'limit'              => $limit,
		);		
		
		$this->data['posts']						= array();
		$this->data['featured_posts']						= array();
		$this->data['post_id']					= !empty($this->request->get['post_id']) ? $this->request->get['post_id'] : 0;
		$posts								= $this->moduleModel->getPosts($postData);
		$featured_posts				= $this->moduleModel->getFeaturedPosts();
		$this->data['custom_css']					= $setting['custom_css'];
		$this->data['featured']					= $setting['featured'];
		
		foreach ($posts as $post) {
			$this->data['posts'][] = array(
				'post_id'	=> $post['iblog_post_id'],
				'title'		=> $post['title'],
				'image'		=> $post['image'],
				'small_image'	=> $post['small_image'],
				'excerpt'	=> $post['excerpt'],
				'href'		=> $this->url->link($this->modulePath.'/post', 'post_id=' . $post['iblog_post_id'])
			);	
		}

		if ($this->data['featured_post'] !== false) {
			$this->load->model('tool/image');

			foreach ($featured_posts as $post) {
				if (file_exists(DIR_IMAGE . $post['image'])) {
					$image = $this->model_tool_image->resize($post['image'], $setting['width'], $setting['height']);
				} else {
					$image = '';
				}

				if (file_exists(DIR_IMAGE . $post['small_image'])) {
					$small_image = $this->model_tool_image->resize($post['small_image'], 300, 300);
				} else {
					$small_image = '';
				}

				$this->data['featured_posts'][] = array(
					'post_id'       	=> $post['iblog_post_id'],
					'title'             => $post['title'],
					'body'      		=> $post['body'],
					'excerpt'			=> $post['excerpt'],
					'meta_description'	=> $post['meta_description'],
					'meta_keyword'		=> $post['meta_keywords'],
					'image'				=> $image,
					'small_image'		=> $small_image,
					'status'			=> $post['is_published'],
					'author'			=> $post['author'],
					'show_author'		=> $post['show_author'],
					'created'			=> $post['created'],
					'featured'			=> $post['is_featured'],
					'href'				=> $this->url->link($this->modulePath.'/post', 'post_id=' . $post['iblog_post_id'])
				);	
			}
		}
		
		
		return $this->getOutput($this->modulePath.'_home.tpl', $this->data);

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
?>