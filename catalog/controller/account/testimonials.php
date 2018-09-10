<?php
include(DIR_APPLICATION . "../_lib.php");

class ControllerAccountTestimonials extends Controller {
	public function index() {
		if (!$this->customer->isLogged()) {
            unset($this->session->data['redirect']);
            $this->session->data['redirect'] = $this->url->link('account/testimonials', '', true);

			$this->response->redirect('/#modal');
		}

		$this->load->language('account/testimonials');

		$this->document->setTitle($this->language->get('heading_title'));

		// Post
			if ($this->request->server['REQUEST_METHOD'] == 'POST') {
				$this->load->model('account/testimonials');

				$this->model_account_testimonials->addItem($this->request->post['testimonials']);

				$this->session->data['success'] = $this->language->get('text_success');

				$this->response->redirect($this->url->link('account/testimonials', '', true));
			}
		// ---

		// Breadcrumbs
			$data['breadcrumbs'] = array();

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_home'),
				'href' => $this->url->link('common/home')
			);

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_account'),
				'href' => $this->url->link('account/account', '', true)
			);

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_testimonials'),
				'href' => $this->url->link('account/testimonials', '', true)
			);
		// ---

		// Text and Lyouts
			$data['heading_title'] = $this->language->get('heading_title');

			$data['entry_testimonials'] = $this->language->get('entry_testimonials');
			$data['button_submit'] = $this->language->get('button_submit');
			$data['button_back'] = $this->language->get('button_back');

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');
		// ---

		$this->response->setOutput($this->load->view('account/testimonials', $data));
	}

	public function addItem() {
        // ---
        	// Init
	            $text = $this->request->post['text'];

	            if( isset($this->request->post['rating']) ) { $rating = intval($this->request->post['rating']); }
	            else { $rating = 0; }

	            if( isset($this->request->post['parent_id']) ) { $parent_id = $this->request->post['parent_id']; }
	            else { $parent_id = 0; }

	            $response = new stdClass();

	            $log = [];

	            // Get CRM managers
					$managers = [];

					$url = 'https://eco-u.retailcrm.ru/api/v5/users';
					$qdata = array('apiKey' => RCRM_KEY,'limit' => 100);

					$response = connectGetAPI($url,$qdata);

					foreach ($response->users as $key => $user) {
						$managers[] = $user->id;
					}
				// ---
        	// ---

        	// Get customer
	            $this->load->model('account/customer');
	            
	            $customer_id = $this->customer->isLogged();
	            $customer = $this->model_account_customer->getCustomer($customer_id);
        	// ---

        	// Set
	            $this->load->model('account/testimonials');
	            
	            $result = $this->model_account_testimonials->addItem($customer_id, $customer['firstname'], $text, $parent_id, $rating);

	            if( $result == false ){
	              $response->result = false;
	              $response->message = 'Отзыв не был сохранен';
	            }
	            else{
	              $response->result = true;
	              $response->message = 'Отзыв добавлен';
	            }
        	// ---

	        // Set task to managers
				$url = 'https://eco-u.retailcrm.ru/api/v5/tasks/create?apiKey='.RCRM_KEY;

				// Create commonID
					$commonId = uniqid();
					
					$taskText = 'Новый отзыв клиента '.$customer['firstname'].'';
				// ---

				foreach ($managers as $key => $manager_id) {
					// Set data
						$task['text'] = $taskText;
						$task['datetime'] = date('Y-m-d H:i', (time()+3600) );
						$task['performerId'] = $manager_id;
						$task['customer'] = array('id' => $customer['rcrm_id'], 'externalId' => $customer['customer_id']);
						$data['task'] = json_encode($task);
					// ---
					
					$response=connectPostAPI($url,$data);

					if( isset($response->success) && $response->success!= false && isset($response->id) ){
						// Save task
	            			$this->model_account_testimonials->addTask($commonId, $response->id, '', $customer['firstname'], $taskText);
						// ---
					}

				}
			// ---


        	// Response
        	$response->log = $log;
        	$response->status = 'success';
          
        	echo json_encode($response);
        	exit;
        // ---
    }

    public function getItems() {
       	// ---
        	// Init
	            $response = new stdClass();
        	// ---

        	// Get customer
	            $customer_id = $this->customer->isLogged();
        	// ---

        	// Get
	            $this->load->model('account/testimonials');
	            $this->load->model('tool/image');
	            
	            $results = $this->model_account_testimonials->getItems($customer_id);

	            if( $results == false ){
	              $response->result = false;
	              $response->message = 'Отзывы не найдены';
	            }
	            else{
	              $response->result = true;
	              $response->message = 'Отзывы успешно загружены';

	              $items = array();

	              $months = ['','января','февраля','марта','апреля','мая','июня','июля','августа','сентября','октября','ноября','декабря'];

	              foreach ($results as $key => $item) {
	              	// ---
	              		// Get childs
	              			$childs_html = '';
	              			$childs = $this->model_account_testimonials->getChilds($item['testimonials_id']);

	              			if( $childs != false ){
	              				foreach ($childs as $key_child => $child) {
		              				// ---
		              					$childs_html .= '
		              						<div class="child">
					              				<div class="author">
					              					<div class="image" style=" background: url('.$this->model_tool_image->resize($child['image'], 100, 100).') no-repeat center center scroll; -webkit-background-size: cover; -moz-background-size: cover; -o-background-size: cover; background-size: cover;"></div>
					              				</div>
					              				<div class="body">
						              				<div class="about">
						              					<span> <span class="firstname">'.$child['author'].'</span>, '.date('j', $child['date_added']).' '.$months[intval(date('m', $child['date_added']))].' '.date('Y', $child['date_added']).'</span>
						              				</div>
						              				<div class="text">
						              					<p>'.$child['text'].'</p>
						              				</div>
					              				</div>
					              			</div>
		              					';
		              				// ---
	              				}
		              			
		              			$childs_html .= '<button type="button" class="answer" data-action="testimonial-answer">Ответить</button>';
	              			}
	              		// ---

	              		// Rating
	              			$rating = '<div class="rating">';

		              		for ($i=1; $i <=5 ; $i++) { 
		              			// ---
		              				if( $i <= $item['rating'] ){
	              						$rating .= '<i class="fa fa-star red"></i>';
		              				}
		              				else {
	              						$rating .= '<i class="fa fa-star-o"></i>';
		              				}
		              			// ---
		              		}

	              			$rating .= '</div>';
	              		// ---

	              		$items[] = '
	              			<div class="item" data-id="'.$item['testimonials_id'].'" data-user-id="'.$item['user_id'].'">
					            <div class="body">
		              				<div class="about">
		              					'.$rating.'
		              					<span>'.date('j', $item['date_added']).' '.$months[intval(date('m', $item['date_added']))].' '.date('Y', $item['date_added']).'</span>
		              				</div>
		              				<div class="text">
		              					<p>'.$item['text'].'</p>
		              				</div>

		              				<div class="childs">
		              					'.$childs_html.'
		              				</div>
	              				</div>
	              			</div>
	              		';
	              	// ---
	              }

	              $response->items = $items;
	            }
        	// ---

        	// Response
        	$response->status = 'success';
          
        	echo json_encode($response);
        	exit;
        // ---
    }
}