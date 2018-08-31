<?php
class ControllerExtensionModuleiBlog extends Controller {
	private $moduleName;
    private $modulePath;
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
        $this->moduleNameSmall      = $this->config->get('iblog_name_small');
        $this->callModel            = $this->config->get('iblog_model');
        $this->modulePath           = $this->config->get('iblog_path');
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
		$this->data['moduleNameSmall']   = $this->moduleNameSmall;
        $this->data['modulePath']   	 = $this->modulePath;
		$this->data['moduleData_module'] = $this->moduleData_module;
		$this->data['moduleModel'] 		 = $this->moduleModel;
		$this->data['moduleSearch'] 	 = $this->moduleSearch;
	   
        $this->data['limit']        = 15;
    }	
	
	public function index($setting) {

  	}

	private function getStore($store_id) {    
        if($store_id && $store_id != 0) {
            $store = $this->model_setting_store->getStore($store_id);
        } else {
            $store['store_id'] = 0;
            $store['name'] = $this->config->get('config_name');
            $store['url'] = $this->getCatalogURL();
        }
        return $store;
    }
	
	private function getCatalogURL(){
        if (isset($_SERVER['HTTPS']) && (($_SERVER['HTTPS'] == 'on') || ($_SERVER['HTTPS'] == '1'))) {
            $storeURL = HTTP_SERVER;
        } else {
            $storeURL = HTTPS_SERVER;
        } 
        return $storeURL;
    }
	
	public function post() {
		$this->load->model('setting/setting');

		$this->data['breadcrumbs'] = array();
		$this->data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home'),			
			'separator' => false
		);
		$this->data['breadcrumbs'][] = array(
			'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link($this->modulePath.'/listing'),
			'separator' => $this->language->get('text_separator')
		);
		
		
		if (isset($this->request->get['post_id'])) {
			$post_id = (int)$this->request->get['post_id'];
		} else {
			$post_id = 0;
		}
		
		$moduleSettings						= $this->model_setting_setting->getSetting($this->moduleName, $this->config->get('config_store_id'));
        $this->data['moduleData']					= isset($moduleSettings[$this->moduleName]) ? ($moduleSettings[$this->moduleName]) : array();
		$post_info							= $this->moduleModel->getPost($post_id);
		
		if ($post_info) {												
			$this->data['breadcrumbs'][] = array(
				'text'      => $post_info['title'],
				'href'      => $this->url->link($this->modulePath.'/post', 'post_id=' . $this->request->get['post_id']),
				'separator' => $this->language->get('text_separator')
			);			
			
			$this->document->setTitle($post_info['title']);
			$this->document->setDescription($post_info['meta_description']);
			$this->document->setKeywords($post_info['meta_keyword']);

			$this->document->addScript('catalog/view/javascript/jquery/magnific/jquery.magnific-popup.min.js');
			$this->document->addStyle('catalog/view/javascript/jquery/magnific/magnific-popup.css');
			
			if (!empty($this->data['moduleData']['PageTitle'][$language_id])) {
		        $this->data['heading_title'] = $this->data['moduleData']['PageTitle'][$language_id];
		    } else {
		        $this->data['heading_title'] = $this->language->get('heading_title');
		    }

			$this->data['title'] = $post_info['title'];
			
			$this->load->model('tool/image');

			if ($post_info['image']) {
				$this->data['popup'] = $this->model_tool_image->resize($post_info['image'], $this->getImageConfigs('config_image_popup_width'), $this->getImageConfigs('config_image_popup_height'));
			} else {
				$this->data['popup'] = '';
			}
			
			if ($post_info['image']) {
				$this->data['thumb'] = $this->model_tool_image->resize($post_info['image'], $this->data['moduleData']['MainImageWidth'], $this->data['moduleData']['MainImageHeight']);
			} else {
				$this->data['thumb'] = '';
			}

			if ($post_info['author_image']) {
				$this->data['thumb_author'] = $this->model_tool_image->resize($post_info['author_image'], 100, 100);
			} else {
				$this->data['thumb_author'] = '';
			}
			
			$this->data['iblog_keywords'] = $this->language->get('iblog_keywords');	
			
			if(isset($post_info['meta_keyword']) && $post_info['meta_keyword'] !== ',') {
				 $this->data['keywords'] = array();
				 
				 $keywords = explode(',',  trim($post_info['meta_keyword'], ","));
				 
				 foreach($keywords as $keyword) {
					
				 	$this->data['keywords'][] = array(
						'name' => $keyword,
						'href' => $this->url->link($this->modulePath.'/tag', 'tag=' .  $keyword),
					);
				 }
				
			} else {
				$this->data['keywords'] = $this->language->get('no_keywords');	
			}

			$categories = unserialize($post_info['categories']);
			$this->data['categories'] = array();
			
			if(!empty($categories)) {
				foreach($categories as $category) {
					$category_info = $this->moduleModel->getCategory($category, $this->config->get('config_store_id'));
			
					$this->data['categories'][] = array (
						'name' => $category_info['title'],
						'href'  => $this->url->link($this->modulePath.'/category', '&path=' . $category_info['category_id']),
					);
				}
			}
			
			$this->data['author'] = $post_info['author'];
			$this->data['show_author'] = $post_info['show_author'];

			$month = ['','янв.','фев.','мар.','апр.','май','июн.','июл.','авг.','сен.','окт.','ноя.','дек.'];
			$this->data['date_created'] = date('d', strtotime($post_info['created'])) . ' ' . $month[intval(date('n', strtotime($post_info['created'])))] . ' ' . date('Y', strtotime($post_info['created']));

			$this->data['excerpt'] = html_entity_decode($post_info['excerpt'], ENT_QUOTES, 'UTF-8');

			$this->data['body'] = html_entity_decode($post_info['body'], ENT_QUOTES, 'UTF-8');

			$this->data['text_author'] = $this->language->get('text_author');
			$this->data['text_date_created'] = $this->language->get('text_date_created');

			if (file_exists(DIR_TEMPLATE . $this->getConfigTemplate() . '/stylesheet/'.$this->moduleNameSmall.'.css')) {
				$this->document->addStyle('catalog/view/theme/' . $this->getConfigTemplate() . '/stylesheet/'.$this->moduleNameSmall.'.css');
			} else {
				$this->document->addStyle('catalog/view/theme/default/stylesheet/'.$this->moduleNameSmall.'.css');
			}

			$this->data['column_left']				= $this->load->controller('common/column_left');
			$this->data['column_right']				= $this->load->controller('common/column_right');
			$this->data['content_top']				= $this->load->controller('common/content_top');
			$this->data['content_bottom']				= $this->load->controller('common/content_bottom');
			$this->data['footer']						= $this->load->controller('common/footer');
			$this->data['header']						= $this->load->controller('common/header');
		
			$this->data['search_placeholder'] = $this->language->get('search_placeholder');

			$this->response->setOutput($this->getOutput($this->modulePath.'_post.tpl' , $this->data));
 
		} else {
			$this->data['breadcrumbs'][] = array(
        		'text'      => $this->language->get('text_error'),
				'href'      => $this->url->link('iblog/post', 'post_id=' . $post_id),
        		'separator' => $this->language->get('text_separator')
      		);			
		
      		$this->document->setTitle($this->language->get('text_error'));

      		$this->data['heading_title']				= $this->language->get('text_error');
      		$this->data['text_error']					= $this->language->get('text_error');
      		$this->data['button_continue']			= $this->language->get('button_continue');
      		$this->data['continue']					= $this->url->link('common/home');
			
			if (file_exists(DIR_TEMPLATE . $this->getConfigTemplate() . '/stylesheet/'.$this->moduleNameSmall.'.css')) {
				$this->document->addStyle('catalog/view/theme/' . $this->getConfigTemplate() . '/stylesheet/'.$this->moduleNameSmall.'.css');
			} else {
				$this->document->addStyle('catalog/view/theme/default/stylesheet/'.$this->moduleNameSmall.'.css');
			}
			
			$this->data['column_left']				= $this->load->controller('common/column_left');
			$this->data['column_right']				= $this->load->controller('common/column_right');
			$this->data['content_top']				= $this->load->controller('common/content_top');
			$this->data['content_bottom']				= $this->load->controller('common/content_bottom');
			$this->data['footer']						= $this->load->controller('common/footer');
			$this->data['header']						= $this->load->controller('common/header');
			
			$this->response->setOutput($this->getOutput('error/not_found.tpl' , $this->data));
    	}
	}

	public function postAjax() {
		// Init
          $response = new stdClass();
        // ---

		$this->load->model('setting/setting');

		$this->data['breadcrumbs'] = array();
		$this->data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home'),			
			'separator' => false
		);
		$this->data['breadcrumbs'][] = array(
			'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link($this->modulePath.'/listing'),
			'separator' => $this->language->get('text_separator')
		);
		
		
		if (isset($this->request->post['post_id'])) {
			$post_id = (int)$this->request->post['post_id'];
		} else {
			$post_id = 0;
		}
		
		$moduleSettings						= $this->model_setting_setting->getSetting($this->moduleName, $this->config->get('config_store_id'));
        $this->data['moduleData']					= isset($moduleSettings[$this->moduleName]) ? ($moduleSettings[$this->moduleName]) : array();
		$post_info							= $this->moduleModel->getPost($post_id);
		
		if ($post_info) {												
			$this->data['breadcrumbs'][] = array(
				'text'      => $post_info['title'],
				'href'      => $this->url->link($this->modulePath.'/post', 'post_id=' . $this->request->get['post_id']),
				'separator' => $this->language->get('text_separator')
			);			
			
			$this->document->setTitle($post_info['title']);
			$this->document->setDescription($post_info['meta_description']);
			$this->document->setKeywords($post_info['meta_keyword']);

			$this->document->addScript('catalog/view/javascript/jquery/magnific/jquery.magnific-popup.min.js');
			$this->document->addStyle('catalog/view/javascript/jquery/magnific/magnific-popup.css');
			
			if (!empty($this->data['moduleData']['PageTitle'][$language_id])) {
		        $this->data['heading_title'] = $this->data['moduleData']['PageTitle'][$language_id];
		    } else {
		        $this->data['heading_title'] = $this->language->get('heading_title');
		    }

			$this->data['title'] = $post_info['title'];
			
			$this->load->model('tool/image');

			if ($post_info['image']) {
				$this->data['popup'] = $this->model_tool_image->resize($post_info['image'], $this->getImageConfigs('config_image_popup_width'), $this->getImageConfigs('config_image_popup_height'));
			} else {
				$this->data['popup'] = '';
			}
			
			if ($post_info['image']) {
				$this->data['thumb'] = $this->model_tool_image->resize($post_info['image'], $this->data['moduleData']['MainImageWidth'], $this->data['moduleData']['MainImageHeight']);
			} else {
				$this->data['thumb'] = '';
			}

			if ($post_info['author_image']) {
				$this->data['thumb_author'] = $this->model_tool_image->resize($post_info['author_image'], 100, 100);
			} else {
				$this->data['thumb_author'] = '';
			}
			
			$this->data['iblog_keywords'] = $this->language->get('iblog_keywords');	
			
			if(isset($post_info['meta_keyword']) && $post_info['meta_keyword'] !== ',') {
				 $this->data['keywords'] = array();
				 
				 $keywords = explode(',',  trim($post_info['meta_keyword'], ","));
				 
				 foreach($keywords as $keyword) {
					
				 	$this->data['keywords'][] = array(
						'name' => $keyword,
						'href' => $this->url->link($this->modulePath.'/tag', 'tag=' .  $keyword),
					);
				 }
				
			} else {
				$this->data['keywords'] = $this->language->get('no_keywords');	
			}

			$categories = unserialize($post_info['categories']);
			$this->data['categories'] = array();
			
			if(!empty($categories)) {
				foreach($categories as $category) {
					$category_info = $this->moduleModel->getCategory($category, $this->config->get('config_store_id'));
			
					$this->data['categories'][] = array (
						'name' => $category_info['title'],
						'href'  => $this->url->link($this->modulePath.'/category', '&path=' . $category_info['category_id']),
					);
				}
			}
			
			$this->data['author'] = $post_info['author'];
			$this->data['show_author'] = $post_info['show_author'];

			$month = ['','янв.','фев.','мар.','апр.','май','июн.','июл.','авг.','сен.','окт.','ноя.','дек.'];
			$this->data['date_created'] = date('d', strtotime($post_info['created'])) . ' ' . $month[intval(date('n', strtotime($post_info['created'])))] . ' ' . date('Y', strtotime($post_info['created']));

			$this->data['excerpt'] = html_entity_decode($post_info['excerpt'], ENT_QUOTES, 'UTF-8');
			
			$this->data['body'] = html_entity_decode($post_info['body'], ENT_QUOTES, 'UTF-8');

			$this->data['text_author'] = $this->language->get('text_author');
			$this->data['text_date_created'] = $this->language->get('text_date_created');

			if (file_exists(DIR_TEMPLATE . $this->getConfigTemplate() . '/stylesheet/'.$this->moduleNameSmall.'.css')) {
				$this->document->addStyle('catalog/view/theme/' . $this->getConfigTemplate() . '/stylesheet/'.$this->moduleNameSmall.'.css');
			} else {
				$this->document->addStyle('catalog/view/theme/default/stylesheet/'.$this->moduleNameSmall.'.css');
			}

			$this->data['column_left']				= $this->load->controller('common/column_left');
			$this->data['column_right']				= $this->load->controller('common/column_right');
			$this->data['content_top']				= $this->load->controller('common/content_top');
			$this->data['content_bottom']				= $this->load->controller('common/content_bottom');
			$this->data['footer']						= $this->load->controller('common/footer');
			$this->data['header']						= $this->load->controller('common/header');
	
			//$this->response->setOutput($this->getOutput($this->modulePath.'_post_modal.tpl' , $this->data));

			$response->html = $this->load->view($this->modulePath.'_post_modal.tpl', $this->data);
 
		} else {
			$this->data['breadcrumbs'][] = array(
        		'text'      => $this->language->get('text_error'),
				'href'      => $this->url->link('iblog/post', 'post_id=' . $post_id),
        		'separator' => $this->language->get('text_separator')
      		);			
		
      		$this->document->setTitle($this->language->get('text_error'));

      		$this->data['heading_title']				= $this->language->get('text_error');
      		$this->data['text_error']					= $this->language->get('text_error');
      		$this->data['button_continue']			= $this->language->get('button_continue');
      		$this->data['continue']					= $this->url->link('common/home');
			
			if (file_exists(DIR_TEMPLATE . $this->getConfigTemplate() . '/stylesheet/'.$this->moduleNameSmall.'.css')) {
				$this->document->addStyle('catalog/view/theme/' . $this->getConfigTemplate() . '/stylesheet/'.$this->moduleNameSmall.'.css');
			} else {
				$this->document->addStyle('catalog/view/theme/default/stylesheet/'.$this->moduleNameSmall.'.css');
			}
			
			$this->data['column_left']				= $this->load->controller('common/column_left');
			$this->data['column_right']				= $this->load->controller('common/column_right');
			$this->data['content_top']				= $this->load->controller('common/content_top');
			$this->data['content_bottom']				= $this->load->controller('common/content_bottom');
			$this->data['footer']						= $this->load->controller('common/footer');
			$this->data['header']						= $this->load->controller('common/header');
			
			$this->response->setOutput($this->getOutput('error/not_found.tpl' , $this->data));

			$response->html = $this->load->view('error/not_found.tpl', $this->data);
    	}

    	$response->status = 'success';
        $response->message = 'Запрос успешно отправлен.<br>Спасибо!';

        echo json_encode($response);
        exit;
	}
	
	public function listing() {
		
		$this->load->model('setting/setting');
		$this->load->model('tool/image'); 
		
		$language_id = (int)$this->config->get('config_language_id');
				
		$moduleSettings						= $this->model_setting_setting->getSetting($this->moduleName, $this->config->get('config_store_id'));
       	   
	    $this->data['moduleData']					= isset($moduleSettings[$this->moduleName]) ? ($moduleSettings[$this->moduleName]) : array();
		
		if (!empty($this->data['moduleData']['PageTitle'][$language_id])) {
            $this->data['heading_title'] = $this->data['moduleData']['PageTitle'][$language_id];
        } else {
            $this->data['heading_title'] = $this->language->get('heading_title');
        }
    
        // Document Meta
        if (!empty($this->data['moduleData']['PageTitle'][$language_id])) {
            $this->document->setTitle($this->data['moduleData']['PageTitle'][$language_id]);
        } else {
            $this->document->setTitle($this->language->get('heading_title'));
        }
		
		$this->data['text_iblog_empty']			= $this->language->get('text_iblog_empty');
		$this->data['button_continue']			= $this->language->get('button_continue');
		$this->data['continue']					= $this->url->link('common/home');
		
		$this->data['text_display'] = $this->language->get('text_display');
		$this->data['text_list'] = $this->language->get('text_list');
		$this->data['text_grid'] = $this->language->get('text_grid');
		$this->data['text_sort'] = $this->language->get('text_sort');
		$this->data['text_limit'] = $this->language->get('text_limit');
		$this->data['iblog_button'] = $this->language->get('iblog_button');
		$this->data['search_string'] = $this->language->get('search_string');
		$this->data['search_button'] = $this->language->get('search_button');
		$this->data['search_placeholder'] = $this->language->get('search_placeholder');
	
		$this->data['search_link'] = $this->url->link($this->modulePath.'/search');

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'p.sort_order';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else { 
			$page = 1;
		}	
		
		if (isset($this->request->get['limit'])) {
			$limit = $this->request->get['limit'];
		} else {
			$limit = $this->getProductLimit();
		}

		$this->data['breadcrumbs'] = array();
		
		$this->data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home'),
			'separator' => false
		);
		
		$this->data['breadcrumbs'][] = array(
			'text'      => $this->data['heading_title'],
			'href'      => $this->url->link($this->modulePath.'/listing'),
			'separator' => $this->language->get('text_separator')
		);
		
		$url = '';

		if (isset($this->request->get['limit'])) {
			$url .= '&limit=' . $this->request->get['limit'];
		}
		
		$this->data['sorts'] = array();
		$this->data['sorts'][] = array(
			'text'  => $this->language->get('text_default'),
			'value' => 'p.sort_order-ASC',
			'href'  => $this->url->link($this->modulePath.'/listing', '&sort=p.sort_order&order=ASC' . $url)
		);
		$this->data['sorts'][] = array(
			'text'  => $this->language->get('text_name_asc'),
			'value' => 'pd.title-ASC',
			'href'  => $this->url->link($this->modulePath.'/listing', '&sort=pd.title&order=ASC' . $url)
		);
		$this->data['sorts'][] = array(
			'text'  => $this->language->get('text_name_desc'),
			'value' => 'pd.title-DESC',
			'href'  => $this->url->link($this->modulePath.'/listing', '&sort=pd.title&order=DESC' . $url)
		);
		
		$url = '';
		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}	

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}	

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
		
		// Document Meta
		if (!empty($this->data['moduleData']['PageTitle'][$language_id])) {
            $this->document->setTitle($this->data['moduleData']['PageTitle'][$language_id]);
        } else {
            $this->document->setTitle($this->language->get('heading_title'));
        }

		if (!empty($this->data['moduleData']['MetaDescription'][$language_id])) {
			$this->document->setDescription($this->data['moduleData']['MetaDescription'][$language_id]);
		} else {
			$this->document->setDescription($this->config->get('config_meta_description'));
		}
		
		if (!empty($this->data['moduleData']['MetaKeywords'][$language_id])) {
			$this->document->setKeywords($this->data['moduleData']['MetaKeywords'][$language_id]);
		} else {
			$this->document->setKeywords($this->config->get('config_meta_keywords'));
		}

		$limits = array_unique(array($this->getProductLimit(), 25, 50, 75, 100));
		sort($limits);
		foreach($limits as $value){
			$this->data['limits'][] = array(
				'text'  => $value,
				'value' => $value,
				'href'  => $this->url->link($this->modulePath.'/listing', $url . '&limit=' . $value)
			);
		}

		$postData = array(
			'sort'               => $sort,
			'order'              => $order,
			'start'              => ($page - 1) * $limit,
			'limit'              => $limit
		);

		$posts_total = $this->moduleModel->getPosts($postData); 
		$total_posts = $this->moduleModel->getTotalPosts($this->config->get('config_store_id'));

		$month = ['','янв.','фев.','мар.','апр.','май','июн.','июл.','авг.','сен.','окт.','ноя.','дек.'];

		foreach ($posts_total as $post) {

			$date_created = date('d', strtotime($post['created'])) . ' ' . $month[intval(date('n', strtotime($post['created'])))] . ' ' . date('Y', strtotime($post['created']));

			$category_id = unserialize($post['category_id']);
			$categories = array();
			
			if(!empty($category_id)) {
				foreach($category_id as $category) {
					$category_info = $this->moduleModel->getCategory($category, $this->config->get('config_store_id'));
			
					$categories[] = array (
						'name' => $category_info['title'],
						'href'  => $this->url->link($this->modulePath.'/category', '&path=' . $category_info['category_id']),
					);
				}
			}

			$this->data['posts'][] = array(
				'post_id'		=> $post['iblog_post_id'],
				'title'			=> $post['title'],
				'image'			=> (isset($post['image'])) ? $this->model_tool_image->resize($post['image'], $this->data['moduleData']['ListingImageWidth'], $this->data['moduleData']['ListingImageHeight']) : '',
				'author' 		=> $post['author'],
				'thumb_author' 	=> (isset($post['author_image'])) ? $this->model_tool_image->resize($post['author_image'], 100, 100) : '',
				'show_author' 	=> $post['show_author'],
				'excerpt'		=> $post['excerpt'],
				'date_created' 	=> $date_created,
				'categories' 	=> $categories,
				'href'			=> $this->url->link($this->modulePath.'/post', 'post_id=' . $post['iblog_post_id'])
			);	
		}
	
		$url = '';
		
		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}	

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}	

		if (isset($this->request->get['limit'])) {
			$url .= '&limit=' . $this->request->get['limit'];
		}
		
		$pagination 						= new Pagination();
		$pagination->total 					= $total_posts;
		$pagination->page 					= $page;
		$pagination->limit 					= $limit;
		$pagination->url 					= $this->url->link($this->modulePath.'/listing', $url.'&page={page}');
		$this->data['pagination']					= $pagination->render();
		$this->data['results']					= sprintf($this->language->get('text_pagination'), ($total_posts) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($total_posts - $limit)) ? $total_posts : ((($page - 1) * $limit) + $limit), $total_posts, ceil($total_posts / $limit));
		
		$this->data['sort'] = $sort;
		$this->data['order'] = $order;
		$this->data['limit'] = $limit;

		if (file_exists(DIR_TEMPLATE . $this->getConfigTemplate() . '/stylesheet/'.$this->moduleNameSmall.'.css')) {
			$this->document->addStyle('catalog/view/theme/' . $this->getConfigTemplate() . '/stylesheet/'.$this->moduleNameSmall.'.css');
		} else {
			$this->document->addStyle('catalog/view/theme/default/stylesheet/'.$this->moduleNameSmall.'.css');
		}

		$this->data['column_left']				= $this->load->controller('common/column_left');
		$this->data['column_right']				= $this->load->controller('common/column_right');
		$this->data['content_top']				= $this->load->controller('common/content_top');
		$this->data['content_bottom']				= $this->load->controller('common/content_bottom');
		$this->data['footer']						= $this->load->controller('common/footer');
		$this->data['header']						= $this->load->controller('common/header');


		
		$this->response->setOutput($this->getOutput($this->modulePath.'_listing.tpl' , $this->data));
	}

	private function getConfigTemplate(){
		if(version_compare(VERSION, '2.2.0.0', '<')) {
			return $this->config->get('config_template');
		} else {
			return  $this->config->get($this->config->get('config_theme') . '_directory');
		}
	}

	private function getImageConfigs($name) {
		if(version_compare(VERSION, '2.2.0.0', '<')) {
			return $this->config->get($name);
		} else {
			if(strpos($name, 'config_image') !== false){
				$name = str_replace('config', '', $name);
				return $this->config->get($this->config->get('config_theme') . $name);
			}
		}	
	}

	private function getOutput($TPL_name , $data){
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

	private function getProductLimit(){
		if(version_compare(VERSION, '2.2.0.0', '<')) {
			return $this->config->get('config_product_limit');
		} else {
			return  $this->config->get($this->config->get('config_theme') . '_product_limit');
		}
	}

	public function search() {
	
		$this->load->model('catalog/product');
		$this->load->model('tool/image'); 
		$this->load->model('setting/setting');
		
		$moduleSettings						= $this->model_setting_setting->getSetting($this->moduleName, $this->config->get('config_store_id'));
        $this->data['moduleData']					= isset($moduleSettings[$this->moduleName]) ? ($moduleSettings[$this->moduleName]) : array();

		if (isset($this->request->get['search'])) {
			$search = $this->request->get['search'];
		} else {
			$search = '';
		} 
		if (isset($this->request->get['description'])) {
			$description = $this->request->get['description'];
		} else {
			$description = '';
		} 
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'p.sort_order';
		} 
		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}
		if (isset($this->request->get['limit'])) {
			$limit = $this->request->get['limit'];
		} else {
			$limit = $this->getProductLimit();
		}
		
		$language_id = (int)$this->config->get('config_language_id');

		// Document Meta
		if (isset($this->request->get['search'])) {
			$this->document->setTitle($this->language->get('heading_title_search') .  ' - ' . $this->request->get['search']);
		} else {
			if (!empty($iBlogSetting[$this->moduleName]['LinkTitle'][$language_id])) {
				$this->document->setTitle($iBlogSetting[$this->moduleName]['LinkTitle'][$language_id]);
			} else {
				$this->document->setTitle($this->language->get('heading_title'));
			}
		}
		

		if (!empty($this->data['moduleData']['MetaDescription'][$language_id])) {
			$this->document->setDescription($this->data['moduleData']['MetaDescription'][$language_id]);
		} else {
			$this->document->setDescription($this->config->get('config_meta_description'));
		}
		
		if (!empty($this->data['moduleData']['MetaKeywords'][$language_id])) {
			$this->document->setKeywords($this->data['moduleData']['MetaKeywords'][$language_id]);
		} else {
			$this->document->setKeywords($this->config->get('config_meta_keywords'));
		}


		$this->data['breadcrumbs'] = array();
		$this->data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home'),
			'separator' => false
		);
		$this->data['breadcrumbs'][] = array(
			'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link($this->modulePath.'/listing'),
			'separator' => $this->language->get('text_separator')
		);


		$url = '';
		if (isset($this->request->get['search'])) {
			$url .= '&search=' . urlencode(html_entity_decode($this->request->get['search'], ENT_QUOTES, 'UTF-8'));
		}
		if (isset($this->request->get['description'])) {
			$url .= '&description=' . $this->request->get['description'];
		}
		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}	
		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}
		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}	
		if (isset($this->request->get['limit'])) {
			$url .= '&limit=' . $this->request->get['limit'];
		}

		$this->data['breadcrumbs'][] = array(
			'text'      => $this->language->get('heading_title_search'),
			'href'      => $this->url->link($this->modulePath.'/search', $url),
			'separator' => $this->language->get('text_separator')
		);

		if (isset($this->request->get['search'])) {
			$this->data['heading_title'] = $this->language->get('heading_title_search') .  ' - ' . $this->request->get['search'];
		} else {
			$this->data['heading_title'] = $this->language->get('heading_title_search');
		}

		$this->data['text_empty'] = $this->language->get('text_empty');
		$this->data['text_critea'] = $this->language->get('text_critea');
		$this->data['text_search'] = $this->language->get('text_search');
		$this->data['text_keyword'] = $this->language->get('text_keyword');
		$this->data['text_display'] = $this->language->get('text_display');
		$this->data['text_list'] = $this->language->get('text_list');
		$this->data['text_grid'] = $this->language->get('text_grid');		
		$this->data['text_sort'] = $this->language->get('text_sort');
		$this->data['text_limit'] = $this->language->get('text_limit');
		$this->data['entry_search'] = $this->language->get('entry_search');
		$this->data['entry_description'] = $this->language->get('entry_description');
		$this->data['search_button'] = $this->language->get('search_button');
		$this->data['iblog_button'] = $this->language->get('iblog_button');

		$this->data['products'] = array();

		if (isset($this->request->get['search'])) {
			
			if (isset($this->request->get['path'])) {
				$url = '';
	
				if (isset($this->request->get['sort'])) {
					$url .= '&sort=' . $this->request->get['sort'];
				}
	
				if (isset($this->request->get['order'])) {
					$url .= '&order=' . $this->request->get['order'];
				}
	
				if (isset($this->request->get['limit'])) {
					$url .= '&limit=' . $this->request->get['limit'];
				}
	
				$path = '';
	
				$parts = explode('_', (string)$this->request->get['path']);
	
				$current_category = (int)array_pop($parts);
	
				foreach ($parts as $path_id) { 
					if (!$path) {
						$path = (int)$path_id;
					} else {
						$path .= '_' . (int)$path_id;
					}
	
					$category_info = $this->moduleModel->getCategory($path_id,$this->config->get('config_store_id'));
	
					if ($category_info) {
						$this->data['breadcrumbs'][] = array(
							'text' => $category_info['name'],
							'href' => $this->url->link($this->modulePath.'/category', 'path=' . $path . $url)
						);
					}
				}
			} else {
				$current_category = 0;
			}
			$postData = array(
				'filter_name'         => $search, 
				'filter_description'  => $description,
				'sort'                => $sort,
				'order'               => $order,
				'start'               => ($page - 1) * $limit,
				'limit'               => $limit,
				'category_id' 		  => $current_category,
			);
			
			$total_posts = $this->moduleModel->getTotalPosts($this->config->get('config_store_id'), $postData);
			$results = $this->moduleModel->getPosts($postData); 

			$month = ['','янв.','фев.','мар.','апр.','май','июн.','июл.','авг.','сен.','окт.','ноя.','дек.'];
		
			foreach ($results as $post) {
				$date_created = date('d', strtotime($post['created'])) . ' ' . $month[intval(date('n', strtotime($post['created'])))] . ' ' . date('Y', strtotime($post['created']));

				$category_id = unserialize($post['category_id']);
				$categories = array();
				
				if(!empty($category_id)) {
					foreach($category_id as $category) {
						$category_info = $this->moduleModel->getCategory($category, $this->config->get('config_store_id'));
				
						$categories[] = array (
							'name' => $category_info['title'],
							'href'  => $this->url->link($this->modulePath.'/category', '&path=' . $category_info['category_id']),
						);
					}
				}

				$this->data['posts'][] = array(
					'post_id'	=> $post['iblog_post_id'],
					'title'		=> $post['title'],
					'image'		=> (isset($post['image'])) ? $this->model_tool_image->resize($post['image'], $this->data['moduleData']['ListingImageWidth'], $this->data['moduleData']['ListingImageHeight']) : '',
					'author' 		=> $post['author'],
					'thumb_author' 	=> (isset($post['author_image'])) ? $this->model_tool_image->resize($post['author_image'], 100, 100) : '',
					'show_author' 	=> $post['show_author'],
					'excerpt'	=> $post['excerpt'],
					'date_created' 	=> $date_created,
					'categories' 	=> $categories,
					'href'		=> $this->url->link($this->modulePath.'/post', 'post_id=' . $post['iblog_post_id'])
				);	
			}

			$url = '';

			if (isset($this->request->get['search'])) {
				$url .= '&search=' . urlencode(html_entity_decode($this->request->get['search'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['description'])) {
				$url .= '&description=' . $this->request->get['description'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$this->data['sorts'] = array();
			$this->data['sorts'][] = array(
				'text'  => $this->language->get('text_default'),
				'value' => 'p.sort_order-ASC',
				'href'  => $this->url->link($this->modulePath.'/search', '&sort=p.sort_order&order=ASC' . $url)
			);
			$this->data['sorts'][] = array(
				'text'  => $this->language->get('text_name_asc'),
				'value' => 'pd.title-ASC',
				'href'  => $this->url->link($this->modulePath.'/search', '&sort=pd.title&order=ASC' . $url)
			);
			$this->data['sorts'][] = array(
				'text'  => $this->language->get('text_name_desc'),
				'value' => 'pd.title-DESC',
				'href'  => $this->url->link($this->modulePath.'/search', '&sort=pd.title&order=DESC' . $url)
			);
			
			$url = '';

			if (isset($this->request->get['search'])) {
				$url .= '&search=' . urlencode(html_entity_decode($this->request->get['search'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['description'])) {
				$url .= '&description=' . $this->request->get['description'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}	

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			$this->data['limits'] = array();

			$limits = array_unique(array($this->getProductLimit(), 25, 50, 75, 100));

			sort($limits);

			foreach($limits as $value){
				$this->data['limits'][] = array(
					'text'  => $value,
					'value' => $value,
					'href'  => $this->url->link($this->modulePath.'/search', $url . '&limit=' . $value)
				);
			}

			$url = '';

			if (isset($this->request->get['search'])) {
				$url .= '&search=' . urlencode(html_entity_decode($this->request->get['search'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['description'])) {
				$url .= '&description=' . $this->request->get['description'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}	

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$pagination 						= new Pagination();
			$pagination->total 					= $total_posts;
			$pagination->page 					= $page;
			$pagination->limit 					= $limit;
			$pagination->url 					= $this->url->link($this->modulePath.'/search', $url.'&page={page}');
			$this->data['pagination']					= $pagination->render();
			$this->data['results']					= sprintf($this->language->get('text_pagination'), ($total_posts) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($total_posts - $limit)) ? $total_posts : ((($page - 1) * $limit) + $limit), $total_posts, ceil($total_posts / $limit));
		}	

		$this->data['search'] = $search;
		$this->data['description'] = $description;

		$this->data['sort'] = $sort;
		$this->data['order'] = $order;
		$this->data['limit'] = $limit;
		
		if (file_exists(DIR_TEMPLATE . $this->getConfigTemplate() . '/stylesheet/'.$this->moduleNameSmall.'.css')) {
			$this->document->addStyle('catalog/view/theme/' . $this->getConfigTemplate() . '/stylesheet/'.$this->moduleNameSmall.'.css');
		} else {
			$this->document->addStyle('catalog/view/theme/default/stylesheet/'.$this->moduleNameSmall.'.css');
		}

		$this->data['column_left']				= $this->load->controller('common/column_left');
		$this->data['column_right']				= $this->load->controller('common/column_right');
		$this->data['content_top']				= $this->load->controller('common/content_top');
		$this->data['content_bottom']				= $this->load->controller('common/content_bottom');
		$this->data['footer']						= $this->load->controller('common/footer');
		$this->data['header']						= $this->load->controller('common/header');

		$this->response->setOutput($this->getOutput($this->modulePath.'_search.tpl' , $this->data)); 
	}
	
	public function tag() {

		$this->load->model('catalog/product');
		$this->load->model('tool/image'); 
		$this->load->model('setting/setting');
		
		$moduleSettings						= $this->model_setting_setting->getSetting($this->moduleName, $this->config->get('config_store_id'));
        $this->data['moduleData']					= isset($moduleSettings[$this->moduleName]) ? ($moduleSettings[$this->moduleName]) : array();

		if (isset($this->request->get['search'])) {
			$search = $this->request->get['search'];
		} else {
			$search = '';
		} 
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'p.sort_order';
		} 
		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}
		if (isset($this->request->get['limit'])) {
			$limit = $this->request->get['limit'];
		} else {
			$limit = $this->getProductLimit();
		}
		
		$language_id = (int)$this->config->get('config_language_id');

		// Document Meta
		if (isset($this->request->get['tag'])) {
			$this->document->setTitle($this->language->get('heading_title_tag') .  ' - ' . $this->request->get['tag']);
		} else {
			if (!empty($iBlogSetting[$this->moduleName]['LinkTitle'][$language_id])) {
				$this->document->setTitle($iBlogSetting[$this->moduleName]['LinkTitle'][$language_id]);
			} else {
				$this->document->setTitle($this->language->get('heading_title'));
			}
		}
		

		if (!empty($this->data['moduleData']['MetaDescription'][$language_id])) {
			$this->document->setDescription($this->data['moduleData']['MetaDescription'][$language_id]);
		} else {
			$this->document->setDescription($this->config->get('config_meta_description'));
		}
		
		if (!empty($this->data['moduleData']['MetaKeywords'][$language_id])) {
			$this->document->setKeywords($this->data['moduleData']['MetaKeywords'][$language_id]);
		} else {
			$this->document->setKeywords($this->config->get('config_meta_keywords'));
		}


		$this->data['breadcrumbs'] = array();
		$this->data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home'),
			'separator' => false
		);
		$this->data['breadcrumbs'][] = array(
			'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link($this->modulePath.'/listing'),
			'separator' => $this->language->get('text_separator')
		);


		$url = '';
		if (isset($this->request->get['tag'])) {
			$url .= '&tag=' . urlencode(html_entity_decode($this->request->get['tag'], ENT_QUOTES, 'UTF-8'));
		}
		if (isset($this->request->get['description'])) {
			$url .= '&description=' . $this->request->get['description'];
		}
		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}	
		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}
		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}	
		if (isset($this->request->get['limit'])) {
			$url .= '&limit=' . $this->request->get['limit'];
		}

		$this->data['breadcrumbs'][] = array(
			'text'      => $this->language->get('heading_title_search'),
			'href'      => $this->url->link($this->modulePath.'/tag', $url),
			'separator' => $this->language->get('text_separator')
		);

		if (isset($this->request->get['tag'])) {
			$this->data['heading_title'] = $this->language->get('heading_title_tag') .  ' - ' . $this->request->get['tag'];
		} else {
			$this->data['heading_title'] = $this->language->get('heading_title_tag');
		}

		$this->data['text_empty'] = $this->language->get('text_empty');
		$this->data['text_critea'] = $this->language->get('text_critea');
		$this->data['text_search'] = $this->language->get('text_search');
		$this->data['text_keyword'] = $this->language->get('text_keyword');
		$this->data['text_display'] = $this->language->get('text_display');
		$this->data['text_list'] = $this->language->get('text_list');
		$this->data['text_grid'] = $this->language->get('text_grid');		
		$this->data['text_sort'] = $this->language->get('text_sort');
		$this->data['text_limit'] = $this->language->get('text_limit');
		$this->data['entry_search'] = $this->language->get('entry_search');
		$this->data['entry_description'] = $this->language->get('entry_description');
		$this->data['search_button'] = $this->language->get('search_button');
		$this->data['iblog_button'] = $this->language->get('iblog_button');

		$this->data['products'] = array();

		if (isset($this->request->get['tag'])) {
			
			if (isset($this->request->get['path'])) {
				$url = '';
	
				if (isset($this->request->get['sort'])) {
					$url .= '&sort=' . $this->request->get['sort'];
				}
	
				if (isset($this->request->get['order'])) {
					$url .= '&order=' . $this->request->get['order'];
				}
	
				if (isset($this->request->get['limit'])) {
					$url .= '&limit=' . $this->request->get['limit'];
				}
	
				$path = '';
	
				$parts = explode('_', (string)$this->request->get['path']);
		
				$current_category = (int)array_pop($parts);
	
				foreach ($parts as $path_id) { 
		
					if (!$path) {
						$path = (int)$path_id;
					} else {
						$path .= '_' . (int)$path_id;
					}
	
					$category_info = $this->moduleModel->getCategory($path_id,$this->config->get('config_store_id'));
	
					if ($category_info) {
						$this->data['breadcrumbs'][] = array(
							'text' => $category_info['name'],
							'href' => $this->url->link($this->modulePath.'/category', 'path=' . $path . $url)
						);
					}
				}
			} else {
				$current_category = 0;
			}
			
			$postData = array(
				'filter_tag'         =>  $this->request->get['tag'], 
				'sort'                => $sort,
				'order'               => $order,
				'start'               => ($page - 1) * $limit,
				'limit'               => $limit,
				'category_id' 		  => $current_category,
			);
			
			$total_posts = $this->moduleModel->getTotalPostsByTag($this->config->get('config_store_id'), $postData);
			$results = $this->moduleModel->getPostsByTag($postData); 
			
			foreach ($results as $post) {
				$this->data['posts'][] = array(
					'post_id'	=> $post['iblog_post_id'],
					'title'		=> $post['title'],
					'image'		=> (isset($post['image'])) ? $this->model_tool_image->resize($post['image'], $this->data['moduleData']['ListingImageWidth'], $this->data['moduleData']['ListingImageHeight']) : '',
					'excerpt'	=> $post['excerpt'],
					'href'		=> $this->url->link($this->modulePath.'/post', 'post_id=' . $post['iblog_post_id'])
				);	
			}

			$url = '';

			if (isset($this->request->get['tag'])) {
				$url .= '&tag=' . urlencode(html_entity_decode($this->request->get['tag'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$this->data['sorts'] = array();
			$this->data['sorts'][] = array(
				'text'  => $this->language->get('text_default'),
				'value' => 'p.sort_order-ASC',
				'href'  => $this->url->link($this->modulePath.'/tag', '&sort=p.sort_order&order=ASC' . $url)
			);
			$this->data['sorts'][] = array(
				'text'  => $this->language->get('text_name_asc'),
				'value' => 'pd.title-ASC',
				'href'  => $this->url->link($this->modulePath.'/tag', '&sort=pd.title&order=ASC' . $url)
			);
			$this->data['sorts'][] = array(
				'text'  => $this->language->get('text_name_desc'),
				'value' => 'pd.title-DESC',
				'href'  => $this->url->link($this->modulePath.'/tag', '&sort=pd.title&order=DESC' . $url)
			);
			
			$url = '';

			if (isset($this->request->get['tag'])) {
				$url .= '&tag=' . urlencode(html_entity_decode($this->request->get['tag'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}	

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			$this->data['limits'] = array();

			$limits = array_unique(array($this->getProductLimit(), 25, 50, 75, 100));

			sort($limits);

			foreach($limits as $value){
				$this->data['limits'][] = array(
					'text'  => $value,
					'value' => $value,
					'href'  => $this->url->link($this->modulePath.'/tag', $url . '&limit=' . $value)
				);
			}

			$url = '';

			if (isset($this->request->get['tag'])) {
				$url .= '&tag=' . urlencode(html_entity_decode($this->request->get['tag'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}	

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$pagination 						= new Pagination();
			$pagination->total 					= $total_posts;
			$pagination->page 					= $page;
			$pagination->limit 					= $limit;
			$pagination->url 					= $this->url->link($this->modulePath.'/tag', $url.'&page={page}');
			$this->data['pagination']					= $pagination->render();
			$this->data['results']					= sprintf($this->language->get('text_pagination'), ($total_posts) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($total_posts - $limit)) ? $total_posts : ((($page - 1) * $limit) + $limit), $total_posts, ceil($total_posts / $limit));
		}	

		$this->data['sort'] = $sort;
		$this->data['order'] = $order;
		$this->data['limit'] = $limit;
		
		if (file_exists(DIR_TEMPLATE . $this->getConfigTemplate() . '/stylesheet/'.$this->moduleNameSmall.'.css')) {
			$this->document->addStyle('catalog/view/theme/' . $this->getConfigTemplate() . '/stylesheet/'.$this->moduleNameSmall.'.css');
		} else {
			$this->document->addStyle('catalog/view/theme/default/stylesheet/'.$this->moduleNameSmall.'.css');
		}

		$this->data['column_left']				= $this->load->controller('common/column_left');
		$this->data['column_right']				= $this->load->controller('common/column_right');
		$this->data['content_top']				= $this->load->controller('common/content_top');
		$this->data['content_bottom']				= $this->load->controller('common/content_bottom');
		$this->data['footer']						= $this->load->controller('common/footer');
		$this->data['header']						= $this->load->controller('common/header');

		$this->response->setOutput($this->getOutput($this->modulePath.'_tag.tpl' , $this->data));
	}
	
	public function category() {
		
		$this->load->model('setting/setting');
		$this->load->model('tool/image'); 

		$language_id = (int)$this->config->get('config_language_id');
		
		$moduleSettings						= $this->model_setting_setting->getSetting($this->moduleName, $this->config->get('config_store_id'));
        $this->data['moduleData']					= isset($moduleSettings[$this->moduleName]) ? ($moduleSettings[$this->moduleName]) : array();

		$this->data['text_iblog_empty']			= $this->language->get('text_iblog_empty');
		$this->data['button_continue']			= $this->language->get('button_continue');
		$this->data['continue']					= $this->url->link('common/home');
		
		$this->data['text_display'] = $this->language->get('text_display');
		$this->data['text_list'] = $this->language->get('text_list');
		$this->data['text_grid'] = $this->language->get('text_grid');
		$this->data['text_sort'] = $this->language->get('text_sort');
		$this->data['text_limit'] = $this->language->get('text_limit');
		$this->data['iblog_button'] = $this->language->get('iblog_button');
		$this->data['search_string'] = $this->language->get('search_string');
		$this->data['search_button'] = $this->language->get('search_button');
		$this->data['search_placeholder'] = $this->language->get('search_placeholder');

		$this->data['search_link'] = $this->url->link($this->modulePath.'/search');
	
		$this->data['breadcrumbs'] = array();
		
		$this->data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home'),
			'separator' => false
		);
		
		$this->data['breadcrumbs'][] = array(
            'text'      => !empty($this->data['moduleData']['PageTitle'][$language_id]) ? $this->data['moduleData']['PageTitle'][$language_id] : $this->language->get('heading_title'),
            'href'      => $this->url->link($this->modulePath.'/listing'),
            'separator' => $this->language->get('text_separator')
        ); 
		
		if (isset($this->request->get['path'])) {
			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$path = '';

			$parts = explode('_', $this->request->get['path']);
	
			if(empty($parts)) {
				$path = (int)$this->request->get['path'];
				$current_category = (int)$this->request->get['path'];
				
				$category_info = $this->moduleModel->getCategory($current_category,$this->config->get('config_store_id'));

				if ($category_info) {
					$this->data['breadcrumbs'][] = array(
						'text' => $category_info['title'],
						'href' => $this->url->link($this->modulePath.'/category', 'path=' . $path . $url)
					);
				}
			} else {
				$current_category = (int)end($parts);

				foreach ($parts as $path_id) { 
					
					if (!$path) {
						$path = (int)$path_id;
					} else {
						$path .= '_' . (int)$path_id;
					}
	
					$category_info = $this->moduleModel->getCategory($path_id,$this->config->get('config_store_id'));
	
					if ($category_info) {
						$this->data['breadcrumbs'][] = array(
							'text' => $category_info['title'],
							'href' => $this->url->link($this->modulePath.'/category', 'path=' . $path . $url)
						);
					}
				}
			}
		} else {
			$current_category = 0;
		}
		
		$this->data['current_category'] = $current_category;
		$category_info = $this->moduleModel->getCategory($current_category, $this->config->get('config_store_id')); 

		$this->document->setTitle($category_info['meta_title']);
		$this->document->setDescription($category_info['meta_description']);
		$this->document->setKeywords($category_info['meta_keyword']);

		$this->data['heading_title']	= $category_info['title'];
		$this->data['description']	= html_entity_decode($category_info['description'], ENT_QUOTES, 'UTF-8');

		if(isset($category_info['image'])) { 
			$this->data['image'] = $this->model_tool_image->resize($category_info['image'], $this->data['moduleData']['ListingImageWidth'], $this->data['moduleData']['ListingImageHeight']);
		} else {
			$this->data['image'] = false;
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'p.sort_order';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}
		
		
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else { 
			$page = 1;
		}	
		
		if (isset($this->request->get['limit'])) {
			$limit = $this->request->get['limit'];
		} else {
			$limit = $this->getProductLimit();
		} 
		
		$url = '';

		if (isset($this->request->get['limit'])) {
			$url .= '&limit=' . $this->request->get['limit'];
		}
		

		$this->data['sorts'] = array();
		$this->data['sorts'][] = array(
			'text'  => $this->language->get('text_default'),
			'value' => 'p.sort_order-ASC',
			'href'  => $this->url->link($this->modulePath.'/category','&sort=p.sort_order&order=ASC&path='.$this->request->get['path'] . $url)
		);
		$this->data['sorts'][] = array(
			'text'  => $this->language->get('text_name_asc'),
			'value' => 'pd.title-ASC',
			'href'  => $this->url->link($this->modulePath.'/category', '&sort=pd.title&order=ASC&path='.$this->request->get['path'] . $url)
		);
		$this->data['sorts'][] = array(
			'text'  => $this->language->get('text_name_desc'),
			'value' => 'pd.title-DESC',
			'href'  => $this->url->link($this->modulePath.'/category', '&sort=pd.title&order=DESC&path='.$this->request->get['path'] . $url)
		);
		
		$url = '';
		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}	

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}	

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$limits = array_unique(array($this->getProductLimit(), 25, 50, 75, 100));
		sort($limits);
		foreach($limits as $value){
			$this->data['limits'][] = array(
				'text'  => $value,
				'value' => $value,
				'href'  => $this->url->link($this->modulePath.'/category', $url . '&path='.$this->request->get['path']. '&limit=' . $value)
			);
		}

		$postData = array(
			'sort'               => $sort,
			'order'              => $order,
			'start'              => ($page - 1) * $limit,
			'limit'              => $limit,
			'category_id'		 => $current_category,
		);
		

		$posts_total = $this->moduleModel->getPosts($postData); 

		$total_posts = $this->moduleModel->getTotalPosts($this->config->get('config_store_id'), $postData);

		foreach ($posts_total as $post) {
			$date_created = date('d', strtotime($post['created'])) . ' ' . $month[intval(date('n', strtotime($post['created'])))] . ' ' . date('Y', strtotime($post['created']));

			$category_id = unserialize($post['category_id']);
			$categories = array();
			
			if(!empty($category_id)) {
				foreach($category_id as $category) {
					$category_info = $this->moduleModel->getCategory($category, $this->config->get('config_store_id'));
			
					$categories[] = array (
						'name' => $category_info['title'],
						'href'  => $this->url->link($this->modulePath.'/category', '&path=' . $category_info['category_id']),
					);
				}
			}
				
			$this->data['posts'][] = array(
				'post_id'		=> $post['iblog_post_id'],
				'title'			=> $post['title'],
				'image'			=> (isset($post['image'])) ? $this->model_tool_image->resize($post['image'], $this->data['moduleData']['ListingImageWidth'], $this->data['moduleData']['ListingImageHeight']) : '',
				'author' 		=> $post['author'],
				'thumb_author' 	=> (isset($post['author_image'])) ? $this->model_tool_image->resize($post['author_image'], 100, 100) : '',
				'show_author' 	=> $post['show_author'],
				'excerpt'		=> $post['excerpt'],
				'date_created' 	=> $date_created,
				'categories' 	=> $categories,
				'href'			=> $this->url->link($this->modulePath.'/post', 'post_id=' . $post['iblog_post_id'])
			);	
		}
	
		$url = '';
		
		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}	

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}	

		if (isset($this->request->get['limit'])) {
			$url .= '&limit=' . $this->request->get['limit'];
		}
		
		
		$pagination 						= new Pagination();
		$pagination->total 					= $total_posts;
		$pagination->page 					= $page;
		$pagination->limit 					= $limit;
		$pagination->url 					= $this->url->link($this->modulePath.'/category','&page={page}&path='.$this->request->get['path'] . $url);
		$this->data['pagination']		    = $pagination->render();
		$this->data['results']				= sprintf($this->language->get('text_pagination'), ($total_posts) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($total_posts - $limit)) ? $total_posts : ((($page - 1) * $limit) + $limit), $total_posts, ceil($total_posts / $limit));
		
		$this->data['sort'] = $sort;
		$this->data['order'] = $order;
		$this->data['limit'] = $limit;

		if (file_exists(DIR_TEMPLATE . $this->getConfigTemplate() . '/stylesheet/'.$this->moduleNameSmall.'.css')) {
			$this->document->addStyle('catalog/view/theme/' . $this->getConfigTemplate() . '/stylesheet/'.$this->moduleNameSmall.'.css');
		} else {
			$this->document->addStyle('catalog/view/theme/default/stylesheet/'.$this->moduleNameSmall.'.css');
		}

		$this->data['column_left']				= $this->load->controller('common/column_left');
		$this->data['column_right']				= $this->load->controller('common/column_right');
		$this->data['content_top']				= $this->load->controller('common/content_top');
		$this->data['content_bottom']				= $this->load->controller('common/content_bottom');
		$this->data['footer']						= $this->load->controller('common/footer');
		$this->data['header']						= $this->load->controller('common/header');



		$this->response->setOutput($this->getOutput($this->modulePath.'_category.tpl' , $this->data));

	}

}
?>