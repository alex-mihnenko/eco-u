<?php
class ControllerExtensionModuleiBlog extends Controller {
	private $moduleName;
    private $modulePath;
    private $feedPath;
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
        $this->moduleNameSmall      = $this->config->get('iblog_name_small');
        $this->callModel            = $this->config->get('iblog_model');
        $this->modulePath           = $this->config->get('iblog_path');
		$this->feedPath           	= $this->config->get('iblog_feed_path');
        $this->moduleVersion        = $this->config->get('iblog_version');   
		$this->moduleData_module    = $this->config->get('iblog_module_data');        
        $this->extensionsLink       = $this->url->link($this->config->get('iblog_link'), 'token=' . $this->session->data['token'].$this->config->get('iblog_link_params'), 'SSL');

        // Load Language
        $this->load->language($this->modulePath);
        
        // Load Model
        $this->load->model($this->modulePath);
		
		        
        // Model Instance
        $this->moduleModel          = $this->{$this->callModel};

        // Global Variables      
        $this->data['moduleName']  		 = $this->moduleName;
		$this->data['moduleNameSmall']   = $this->moduleNameSmall;
        $this->data['modulePath']   	 = $this->modulePath;
 		$this->data['feedPath']   	 	 = $this->feedPath;
		$this->data['moduleData_module'] = $this->moduleData_module;
		$this->data['moduleModel'] 		 = $this->moduleModel;
	   
        $this->data['limit']        = 15;
    }	

    public function index() { 
	 
        $this->load->model('setting/store');
		$this->load->model('setting/setting');
        $this->load->model('localisation/language');
 
        $this->document->addStyle('view/stylesheet/'.$this->moduleNameSmall.'/'.$this->moduleNameSmall.'.css');
		$this->document->addScript('view/javascript/'.$this->moduleNameSmall.'/'.$this->moduleNameSmall.'.js');

        $this->document->setTitle($this->language->get('heading_title').' '.$this->moduleVersion);

        if(!isset($this->request->get['store_id'])) {
           $this->request->get['store_id'] = 0; 
        }
	
        $store = $this->getCurrentStore($this->request->get['store_id']);
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) { 	
            if (!empty($_POST['OaXRyb1BhY2sgLSBDb21'])) {
                $this->request->post[$this->moduleName]['LicensedOn'] = $_POST['OaXRyb1BhY2sgLSBDb21'];
            }
            if (!empty($_POST['cHRpbWl6YXRpb24ef4fe'])) {
                $this->request->post[$this->moduleName]['License'] = json_decode(base64_decode($_POST['cHRpbWl6YXRpb24ef4fe']), true);
            }

        	$this->model_setting_setting->editSetting($this->moduleName, $this->request->post, $this->request->post['store_id']);
            $this->session->data['success'] = $this->language->get('text_success');
            $this->response->redirect($this->url->link($this->modulePath, 'store_id='.$this->request->post['store_id'] . '&token=' . $this->session->data['token'], 'SSL'));
        }
		
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

        $this->data['breadcrumbs']   = array();
        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL'),
        );
        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_module'),
            'href' => $this->extensionsLink,
        );
        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link($this->modulePath, 'token=' . $this->session->data['token'], 'SSL'),
        );

        $languageVariables = array(
			'error_permission', 'text_success', 'text_enabled', 'text_disabled', 'button_cancel', 'save_changes', 'text_default', 'text_module', 'entry_code', 'entry_code_help', 'button_add_module', 'button_remove','entry_page_title','entry_meta_description','entry_meta_keywords','entry_seo_slug','entry_seo_slug_help','entry_image_dimension','entry_image_dimension_help','entry_main_image_dimension','entry_main_image_dimension_help','entry_width','entry_height','entry_css','entry_css_help','entry_px','entry_listing_url','entry_listing_url_help','entry_add_link_listing','entry_add_link_listing_help','entry_link_title','entry_link_title_help','entry_add_link_listing_help','entry_link_sort_order','entry_link_sort_order_help','entry_seo_options','entry_seo_options_help','entry_main_image_show','entry_main_image_show_help','entry_disqus_status','entry_disqus_status_help','entry_disqus_short','entry_disqus_short_help','entry_addthis','entry_addthis_help', 'entry_enable_disable', 'entry_status', 'entry_add','entry_edit','entry_add_new_category','entry_close','entry_remove_category_confirm','entry_fill_required','entry_add_category_success','entry_update_category_success','entry_category_details', 'entry_title_blog_help', 'entry_excerpt_help', 'entry_body_help', 'entry_meta_keyword_help', 'text_control_panel', 'text_blog_posts','text_blog_categories','text_blog_listing','text_post_view','text_settings', 'button_save', 'button_submit', 'button_feed', 'button_add_post',  'text_toggle_dropdown', 'entry_post_details', 'entry_close', 'entry_submit','text_support' 
		);
        foreach ($languageVariables as $languageVariable) {
            $this->data[$languageVariable] = $this->language->get($languageVariable);
        }
		
		$this->data['heading_title'] 			= $this->language->get('heading_title').' '.$this->moduleVersion;

        $this->data['stores']					= array_merge(array(0 => array('store_id' => '0', 'name' => $this->config->get('config_name') . ' (' . $this->data['text_default'].')', 'url' => HTTP_SERVER, 'ssl' => HTTPS_SERVER)), $this->model_setting_store->getStores());
        $this->data['languages']              = $this->model_localisation_language->getLanguages();
        $this->data['store']                  = $store;
        $this->data['token']                  = $this->session->data['token'];
        $this->data['action']                 = $this->url->link($this->modulePath, 'token=' . $this->session->data['token'], 'SSL');
        $this->data['cancel']                 = $this->extensionsLink;
        $this->data['moduleSettings']			= $this->model_setting_setting->getSetting($this->moduleName, $store['store_id']);
        $this->data['moduleData']				= (isset($this->data['moduleSettings'][$this->moduleName])) ? $this->data['moduleSettings'][$this->moduleName] : array();
		$this->data['catalog_url']			= $this->getCatalogURL();
		$this->data['url']					= $this->url;
		
		foreach ($this->data['languages'] as $key => $value) {
			$this->data['languages'][$key]['flag_url'] = 'language/'.$this->data['languages'][$key]['code'].'/'.$this->data['languages'][$key]['code'].'.png"';
		}

		$this->data['header']				= $this->load->controller('common/header');
		$this->data['column_left']			= $this->load->controller('common/column_left');
		$this->data['footer']				= $this->load->controller('common/footer');
			 
		$this->response->setOutput($this->load->view($this->modulePath.'.tpl', $this->data));
    }
	
	protected function validateForm($data=array()) {
		if (!$this->user->hasPermission('modify', $this->modulePath)) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if(isset($data['post_description'])) {
			foreach($data['post_description'] as $post_descr) {
				if(empty($post_descr['title']) || empty($post_descr['excerpt']) || empty($post_descr['body'])) {
					$this->error['warning']= "Please, fill in all of the required fields for all languages";
				} else {
					continue;
				}
			}
		}
		
		if(isset($data['category_description'])) {
			foreach($data['category_description'] as $cat_descr) {
				if(empty($cat_descr['name']) || empty($cat_descr['description']) || empty($cat_descr['meta_title'])) {
					$this->error['warning']= "Please, fill in all of the required fields for all languages";
				} else {
					continue;
				}
			}
		}		
		return !$this->error;
	}
	
    public function install() {
	    $this->moduleModel->install();
    }
    
    public function uninstall() {
        $this->load->model('setting/store');
		$this->load->model('setting/setting');
        $this->load->model('localisation/language');
        $this->load->model('design/layout');
		
		$this->model_setting_setting->deleteSetting($this->moduleData_module,0);
		$stores=$this->model_setting_store->getStores();
		foreach ($stores as $store) {
			$this->model_setting_setting->deleteSetting($this->moduleData_module, $store['store_id']);
		}
        $this->moduleModel->uninstall();
    }
	
	
    private function getCatalogURL() {
        if (isset($_SERVER['HTTPS']) && (($_SERVER['HTTPS'] == 'on') || ($_SERVER['HTTPS'] == '1'))) {
            $storeURL = HTTPS_CATALOG;
        } else {
            $storeURL = HTTP_CATALOG;
        } 
        return $storeURL;
    }

    private function getServerURL() {
        if (isset($_SERVER['HTTPS']) && (($_SERVER['HTTPS'] == 'on') || ($_SERVER['HTTPS'] == '1'))) {
            $storeURL = HTTPS_SERVER;
        } else {
            $storeURL = HTTP_SERVER;
        } 
        return $storeURL;
    }

    private function getCurrentStore($store_id) {    
        if($store_id && $store_id != 0) {
            $store = $this->model_setting_store->getStore($store_id);
        } else {
            $store['store_id'] = 0;
            $store['name'] = $this->config->get('config_name');
            $store['url'] = $this->getCatalogURL(); 
        }
        return $store;
    }
	
	public function getPosts() {

		$this->data['store_id']			= $this->request->get['store_id'];
		$this->data['token']				= $this->session->data['token'];

		if (!empty($this->request->get['page'])) {
            $page = (int) $this->request->get['page'];
        } else {
			$page = 1;	
		}
			
		if(!isset($this->request->get['store_id'])) {
           $this->request->get['store_id'] = 0;
        } 
		
		$this->data['sorts'] = array();
		
		$categories = $this->db->query( "SELECT * FROM " . DB_PREFIX . "iblog_categories_id cid LEFT JOIN ". DB_PREFIX . "iblog_categories ic ON cid.category_id = ic.category_id WHERE language_id = '" . (int)$this->config->get('config_language_id') . "' AND store_id = '" . (int)$this->data['store_id'] . "'")->rows;
	
		if(!empty($categories)) {
			foreach($categories as $category) {
				$this->data['sorts'][] = array(
					'text'  => $category['name'],
					'value' => $category['category_id'],
				);
			} 
		}
		
		if (isset($this->request->get['category'])) {
			$category_sort = $this->request->get['category'];
			$this->data['category_sort'] = $category_sort;
		} 
		
		$this->data['url_link'] = $this->url;

		$this->data['total']				= $this->moduleModel->getTotalPosts($this->request->get['store_id']);
		
		$sources			= $this->moduleModel->viewPosts($page, $this->data['limit'], $this->data['store_id']);
				
		if(!isset($category_sort) || $category_sort == 0) {
			$this->data['sources'] = $sources;
		} else {
			$this->data['sources'] = array();
			foreach($sources as $source) {
				if(isset($source['category_id']) && $source['category_id'] !== '0') {
					$category_id = unserialize($source['category_id']);
				} else {
					$category_id = array();
				}
			
				if((isset($category_sort) && isset($category_id) && in_array($category_sort,$category_id)) || $category_sort == 0) {
					$this->data['sources'][] = array(
						'iblog_post_id'	=> $source['iblog_post_id'],
						'title'		=> $source['title'],
						'excerpt'	=> $source['excerpt'],
						'author'	=> $source['author'],
						'created'	=> $source['created'],
						'modified'	=> $source['modified'],
						'is_published' => $source['is_published'],
					);	
				}
			}
		}

	    $pagination					= new Pagination();
        $pagination->total			= $this->data['total'];
        $pagination->page			= $page;
        $pagination->limit			= $this->data['limit']; 
        $pagination->url			= $this->url->link($this->modulePath.'/getPosts','token=' . $this->session->data['token'].'&page={page}&store_id='.$this->request->get['store_id'], 'SSL');
		
		$this->data['pagination']			= $pagination->render();
		
		$this->data['sort_category']		= $this->language->get('sort_category');
		$this->data['text_all'] 			= $this->language->get('text_all');
		
		$this->data['results'] = sprintf($this->language->get('text_pagination'), ($this->data['total']) ? (($page - 1) * $this->data['limit']) + 1 : 0, ((($page - 1) * $this->data['limit']) > ($this->data['total'] - $this->data['limit'])) ? $this->data['total'] : ((($page - 1) * $this->data['limit']) + $this->data['limit']), $this->data['total'], ceil($this->data['total'] / $this->data['limit']));	
			
		$this->response->setOutput($this->load->view($this->modulePath.'/view_blogs.tpl', $this->data));
	}
	
	public function getCategories() {

		if (!empty($this->request->get['page'])) {
            $page = (int) $this->request->get['page'];
        } else {
			$page = 1;	
		}
			
		if(!isset($this->request->get['store_id'])) {
           $this->request->get['store_id'] = 0;
        } 
		
		$this->data['url_link'] = $this->url;

		$this->data['store_id']			= $this->request->get['store_id'];
		$this->data['token']				= $this->session->data['token'];
		$this->data['total']				= $this->moduleModel->getTotalCategories($this->request->get['store_id']);
		
		$this->data['categories']			= $this->moduleModel->viewCategories($page, $this->data['limit'], $this->data['store_id']);
	    $pagination					= new Pagination();
        $pagination->total			= $this->data['total'];
        $pagination->page			= $page;
        $pagination->limit			= $this->data['limit']; 
        $pagination->url			= $this->url->link($this->modulePath.'/getCategories','token=' . $this->session->data['token'].'&page={page}&store_id='.$this->request->get['store_id'], 'SSL');
		
		$this->data['pagination']			= $pagination->render();

	    $languageVariables = array('entry_id','entry_name','entry_actions','entry_edit','entry_remove','entry_no_categories', 'entry_add','entry_edit','entry_add_new_category','entry_close','entry_remove_category_confirm','entry_fill_required','entry_add_category_success','entry_update_category_success','entry_category_details');
			
        foreach ($languageVariables as $languageVariable) {
            $this->data[$languageVariable] = $this->language->get($languageVariable);
        }
		
		$this->data['results'] = sprintf($this->language->get('text_pagination'), ($this->data['total']) ? (($page - 1) * $this->data['limit']) + 1 : 0, ((($page - 1) * $this->data['limit']) > ($this->data['total'] - $this->data['limit'])) ? $this->data['total'] : ((($page - 1) * $this->data['limit']) + $this->data['limit']), $this->data['total'], ceil($this->data['total'] / $this->data['limit']));	
			
		$this->response->setOutput($this->load->view($this->modulePath.'/view_categories.tpl', $this->data));
	}
	
	public function newBlogPost() {
		$this->load->model('tool/image');
		$this->load->model('localisation/language');
		$this->load->model('user/user');
		
		    $languageVariables = array(
			'text_published',
			'text_draft',
			'text_none',
			'text_image_manager',
			'text_browse',
			'text_clear',
			'entry_title',
			'entry_title_blog_help',
			'entry_meta_description',
			'entry_meta_keyword',
			'entry_meta_keyword_help',
			'entry_body',
			'entry_body_help',
			'entry_excerpt',
			'entry_excerpt_help',
			'entry_slug',
			'entry_slug_help',
			'entry_image',
			'entry_image_help',
			'entry_small_image',
			'entry_small_image_help',
			'entry_status',
			'entry_post_status',
			'entry_author',
			'entry_author_help',
			'entry_show_author',
			'entry_show_author_help',
			'entry_date_published',
			'entry_category',
			'entry_category_help',
			'entry_featured',
			'entry_featured_help',
			'text_enabled',
			'text_disabled',
			'entry_sort_order',
			'entry_sort_order_post_help'
        );
       
        foreach ($languageVariables as $languageVariable) {
            $this->data[$languageVariable] = $this->language->get($languageVariable);
        }
		
		$this->data['languages'] = $this->model_localisation_language->getLanguages();
		
		foreach ($this->data['languages'] as $key => $value) {
			$this->data['languages'][$key]['flag_url'] = 'language/'.$this->data['languages'][$key]['code'].'/'.$this->data['languages'][$key]['code'].'.png"';
		}
		
		$this->data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		
		if(!isset($this->request->get['store_id'])) {
           $this->request->get['store_id'] = 0; 
        }
		$this->data['store_id'] = $this->request->get['store_id'];
		
		if (isset($this->request->get['post_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
      		$post_info = $this->moduleModel->getPost($this->request->get['post_id']);
			$this->data['post_id'] = $this->request->get['post_id'];
    	}
		
		if (isset($this->request->post['post_description'])) {
			$this->data['post_description'] = $this->request->post['post_description'];
		} elseif (isset($this->request->get['post_id'])) {
			$this->data['post_description'] = $this->moduleModel->getPostDescriptions($this->request->get['post_id']);
		} else {
			$this->data['post_description'] = array();
		}
		
		if (isset($this->request->post['category_id'])) {
			$this->data['category_id'] = $this->request->post['category_id'];
		} elseif (!empty($post_info)) {
			$this->data['category_id'] = unserialize($post_info['category_id']);
		} else {
			$this->data['category_id'] = array(0);
		}
				
		if (isset($this->request->post['slug'])) {
      		$this->data['slug'] = $this->request->post['slug'];
    	} elseif (!empty($post_info)) {
			$this->data['slug'] = $post_info['slug'];
		} else {
      		$this->data['slug'] = '';
    	}
		
		if (isset($this->request->post['image'])) {
			$this->data['image'] = $this->request->post['image'];
		} elseif (!empty($post_info)) {
			$this->data['image'] = $post_info['image'];
		} else {
			$this->data['image'] = '';
		}

		if (isset($this->request->post['small_image'])) {
			$this->data['small_image'] = $this->request->post['small_image'];
		} elseif (!empty($post_info)) {
			$this->data['small_image'] = $post_info['small_image'];
		} else {
			$this->data['small_image'] = '';
		}
		
		$this->load->model('tool/image');

		if (isset($this->request->post['image']) && is_file(DIR_IMAGE . $this->request->post['image'])) {
			$this->data['thumb'] = $this->model_tool_image->resize($this->request->post['image'], 100, 100);
		} elseif (!empty($post_info) && is_file(DIR_IMAGE . $post_info['image'])) {
			$this->data['thumb'] = $this->model_tool_image->resize($post_info['image'], 100, 100);
		} else {
			$this->data['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		}

		if (isset($this->request->post['small_image']) && is_file(DIR_IMAGE . $this->request->post['small_image'])) {
			$this->data['thumb_small'] = $this->model_tool_image->resize($this->request->post['small_image'], 100, 100);
		} elseif (!empty($post_info) && is_file(DIR_IMAGE . $post_info['small_image'])) {
			$this->data['thumb_small'] = $this->model_tool_image->resize($post_info['small_image'], 100, 100);
		} else {
			$this->data['thumb_small'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		}
		
		if (isset($this->request->post['date_published'])) {
       		$this->data['date_published'] = $this->request->post['date_published'];
		} elseif (!empty($post_info)) {
			$this->data['date_published'] = date('Y-m-d H:i:s', strtotime($post_info['created']));
		} else {
			$this->data['date_published'] = date('Y-m-d H:i:s', time());
		}
				
    	if (isset($this->request->post['status'])) {
      		$this->data['status'] = $this->request->post['status'];
    	} elseif (!empty($post_info)) {
			$this->data['status'] = $post_info['is_published'];
		} else {
      		$this->data['status'] = 1;
    	}
		
		if (isset($this->request->post['featured'])) {
      		$this->data['featured'] = $this->request->post['featured'];
    	} elseif (!empty($post_info)) {
			$this->data['featured'] = !empty($post_info['is_featured']);
		} else {
      		$this->data['featured'] = 0;
    	}
		
		
		if (isset($this->request->post['show_author'])) {
      		$this->data['show_author'] = $this->request->post['show_author'];
    	} elseif (!empty($post_info['show_author'])) {
			$this->data['show_author'] = !empty($post_info['show_author']);
		} else {
      		$this->data['show_author'] = 0;
    	}

		$users = $this->model_user_user->getUsers();
		$this->data['authors'] = array();
		
		foreach ($users as $user) {
			$this->data['authors'][] = array(
				'author_id' => $user['user_id'],
				'name' => $user['firstname'] . ' ' . $user['lastname']
			);	
		}
		
		if (isset($this->request->post['category_id'])) {
      		$this->data['category_id'] = $this->request->post['category_id'];
    	} elseif (!empty($post_info)) {
			$this->data['category_id'] = $post_info['category_id'];
		} else {
      		$this->data['category_id'] = 0;
    	}
		
		if (isset($this->request->post['slug'])) {
      		$this->data['slug'] = $this->request->post['slug'];
    	} elseif (!empty($post_info)) {
			$this->data['slug'] = $post_info['slug'];
		} else {
      		$this->data['slug'] = '';
    	}
		
		if (isset($this->request->post['sort_order_post'])) {
      		$this->data['sort_order_post'] = $this->request->post['sort_order_post'];
    	} elseif (!empty($post_info['sort_order_post'])) {
			$this->data['sort_order_post'] = $post_info['sort_order_post'];
		} else {
      		$this->data['sort_order_post'] = '';
    	}
		
		$this->data['categories'] = $this->db->query("SELECT * FROM " . DB_PREFIX . "iblog_categories_id cid LEFT JOIN " . DB_PREFIX . "iblog_categories ic ON ic.category_id=cid.category_id WHERE language_id = '" . (int)$this->config->get('config_language_id') . "' AND store_id = '" . (int)$this->request->get['store_id'] . "'")->rows;	

		$this->data['token'] = $this->session->data['token'];
		
		$this->data['language_id'] = $this->config->get('config_language_id');
		
		$this->response->setOutput($this->load->view($this->modulePath.'/add_new.tpl', $this->data));
	}
	
	public function newCategory() {
		$this->load->model('localisation/language');
		$this->load->model('user/user');
		
		    $languageVariables = array(
			'entry_title',
			'entry_title_help',
			'entry_parent',
			'entry_parent_help',
			'entry_description',
			'entry_description_help',
			'text_none',
			'entry_meta_title',
			'entry_meta_description',
			'entry_meta_keyword',
			'entry_keyword',
			'entry_keyword_help',
			'entry_image',
        );
       
        foreach ($languageVariables as $languageVariable) {
            $this->data[$languageVariable] = $this->language->get($languageVariable);
        }

		$this->data['languages'] = $this->model_localisation_language->getLanguages();
		
		foreach ($this->data['languages'] as $key => $value) {
			$this->data['languages'][$key]['flag_url'] = 'language/'.$this->data['languages'][$key]['code'].'/'.$this->data['languages'][$key]['code'].'.png"';
		}
		
		if(!isset($this->request->get['store_id'])) {
           $this->request->get['store_id'] = 0; 
        }
		$this->data['store_id'] = $this->request->get['store_id'];
		
		if (isset($this->request->get['category_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
      		$category_info = $this->moduleModel->getCategory($this->request->get['category_id'], $this->data['store_id']);
			$this->data['category_id'] = $this->request->get['category_id'];
    	}
		
		if (isset($this->request->post['category_description'])) {
			$this->data['category_description'] = $this->request->post['category_description'];
		} elseif (isset($this->request->get['category_id'])) {
			$this->data['category_description'] = $this->moduleModel->getCategoryDescriptions($this->request->get['category_id']);
		} else {
			$this->data['category_description'] = array();
		}

		if (isset($this->request->post['path'])) {
			$this->data['path'] = $this->request->post['path'];
		} elseif (!empty($category_info)) {
			$this->data['path'] = $category_info['path'];
		} else {
			$this->data['path'] = '';
		}

	    if (isset($this->request->post['parent_id'])) {
			$this->data['parent_id'] = $this->request->post['parent_id'];
		} elseif (!empty($category_info)) {
			$this->data['parent_id'] = $category_info['parent_id'];
		} else {
			$this->data['parent_id'] = 0;
		}
		
		if (isset($this->request->post['slug'])) {
			$this->data['slug'] = $this->request->post['slug'];
		} elseif (!empty($category_info)) {
			$this->data['slug'] = $category_info['slug'];
		} else {
			$this->data['slug'] = '';
		}

		if (isset($this->request->post['image'])) {
			$this->data['image'] = $this->request->post['image'];
		} elseif (!empty($category_info)) {
			$this->data['image'] = $category_info['image'];
		} else {
			$this->data['image'] = '';
		}

		$this->load->model('tool/image');

		if (isset($this->request->post['image']) && is_file(DIR_IMAGE . $this->request->post['image'])) {
			$this->data['thumb'] = $this->model_tool_image->resize($this->request->post['image'], 100, 100);
		} elseif (!empty($category_info) && is_file(DIR_IMAGE . $category_info['image'])) {
			$this->data['thumb'] = $this->model_tool_image->resize($category_info['image'], 100, 100);
		} else {
			$this->data['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		}

		
		if (isset($this->error['meta_title'])) {
			$this->data['error_meta_title'] = $this->error['meta_title'];
		} else {
			$this->data['error_meta_title'] = array();
		}

		if (isset($this->error['slug'])) {
			$this->data['error_keyword'] = $this->error['keyword'];
		} else {
			$this->data['error_keyword'] = '';
		}
		
		$this->data['token'] = $this->session->data['token'];
		
		$this->data['language_id'] = $this->config->get('config_language_id');	
			
		$this->response->setOutput($this->load->view($this->modulePath.'/add_category.tpl', $this->data));
	}
	
	public function updatePost() {
		
		if($this->request->server['REQUEST_METHOD'] == 'POST') {
			$json = array();
			if($this->validateForm($this->request->post)) {
				if (!isset($this->request->post['post_id'])) {
					$this->moduleModel->addPost($this->request->post);
					$this->response->setOutput(json_encode($json));
				} else {
					$this->moduleModel->editPost($this->request->post['post_id'], $this->request->post);
					$this->response->setOutput(json_encode($json));
				} 
			} else {
				$json['error'] = $this->error['warning'];
				$this->response->setOutput(json_encode($json));
			}
		}
	}
	
	public function updateCategory() {
		
		if($this->request->server['REQUEST_METHOD'] == 'POST') {
			$json = array();
			if($this->validateForm($this->request->post)) {
				if (!isset($this->request->post['category_id'])) {
					$this->moduleModel->addCategory($this->request->post);
					$this->response->setOutput(json_encode($json));
				} else {
					$this->moduleModel->editCategory($this->request->post['category_id'], $this->request->post);
					$this->response->setOutput(json_encode($json));
				} 
				
			} else {
				$json['error'] = $this->error['warning'];
				$this->response->setOutput(json_encode($json));
			}
		}

	}
		
	public function removePost() {
		
		if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validateForm()) {
			if (isset($this->request->post['id'])) {
				$this->moduleModel->deletePost($this->request->post['id']);
			}
    	}	
	}
	
	public function removeCategories() {
		
		if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validateForm()) {
			if (isset($this->request->post['id'])) {
				$this->moduleModel->deleteCategory($this->request->post['id']);
			}
    	}	
	}
	
	public function autocompleteCategory() {
		$json = array();

		if (isset($this->request->get['filter_name'])) {
			
			if(isset($this->request->get['store_id'])) {
				$store_id = $this->request->get['store_id'];
			} else {
			   $store_id = 0; 
			}


			$results = $this->moduleModel->viewCategories(1, 5, $store_id ,'name', 'ASC', $this->request->get['filter_name']);

			foreach ($results as $result) {
				$json[] = array(
					'category_id' => $result['category_id'],
					'name'        => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
				);
			}
		}

		$sort_order = array();

		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['name'];
		}

		array_multisort($sort_order, SORT_ASC, $json);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
?>