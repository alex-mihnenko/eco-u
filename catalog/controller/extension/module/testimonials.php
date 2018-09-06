<?php
class ControllerExtensionModuleTestimonials extends Controller {
	public function index() {
		$this->load->language('extension/module/testimonials');

		$this->document->setTitle($this->language->get('heading_title'));

		// Post
			if ($this->request->server['REQUEST_METHOD'] == 'POST') {
				$this->load->model('extension/module/testimonials');

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

		// Pagination
			if (isset($this->request->get['page'])) {
				$page = $this->request->get['page'];
			} else { 
				$page = 1;
			}	
			
			if (isset($this->request->get['limit'])) {
				$limit = $this->request->get['limit'];
			} else {
				$limit = 20;
			}

			$url = '';

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}
		// ---

		// Text and Lyouts
			$data['heading_title'] = $this->language->get('heading_title');
			$data['sub_heading_title'] = $this->language->get('sub_heading_title');

			$data['text_empty'] = $this->language->get('text_empty');
			$data['button_submit'] = $this->language->get('button_submit');
			$data['button_back'] = $this->language->get('button_back');

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');
		// ---

		// Get data
			$filter_data = array(
				'start'              => ($page - 1) * $limit,
				'limit'              => $limit
			);

			$this->load->model('extension/module/testimonials');
	        $this->load->model('tool/image');


			$testimonials_total = $this->model_extension_module_testimonials->getTotalItems();
			$testimonials_results = $this->model_extension_module_testimonials->getItems($filter_data);

			$data['testimonials'] = array();

			// Go-round results
				$months = ['','января','февраля','марта','апреля','мая','июня','июля','августа','сентября','октября','ноября','декабря'];

				foreach ($testimonials_results as $key => $item) {
					// ---
						// Get childs
							$childs_html = '';
							$childs = $this->model_extension_module_testimonials->getChilds($item['testimonials_id']);

							if( $childs != false ){
								foreach ($childs as $key_child => $child) {
				  				// ---
				  					$childs_html .= '
				  						<div class="child">
				              				<div class="author">
				              					<div class="image" style=" background: url('.$this->model_tool_image->resize($child['image'], 100, 100).') no-repeat center center scroll; -webkit-background-size: cover; -moz-background-size: cover; -o-background-size: cover; background-size: cover;"></div>
				              				</div>
				              				<div class="body">
					              				<h2 class="h3">'.$child['author'].'</h2>
					              				<div class="text">
					              					<p>'.$child['text'].'</p>
					              				</div>
				              				</div>
					              			
					              			<hr class="indent xs">
				              			</div>
				  					';
				  				// ---
								}
							}
						// ---

						// Author
							$author_preview = mb_substr($item['author'], 0, 1);
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

						$data['testimonials'][] = '
							<div class="item" data-id="'.$item['testimonials_id'].'" data-user-id="'.$item['user_id'].'">
				            <div class="author">
				            	<div class="image">'.$author_preview.'</div>
				            </div>
				            <div class="body">
				  				<h2 class="h3">'.$item['author'].'</h2>
				  				'.$rating.'
				  				<hr class="indent xs">
				  				<div class="text"><p>'.$item['text'].'</p></div>
				  				<hr class="indent xs">

				  				<div class="date">
				  					<i class="fa fa-calendar"></i> '.date('j', $item['date_added']).' '.$months[intval(date('m', $item['date_added']))].' '.date('Y', $item['date_added']).'
				  				</div>
				  				<hr class="indent sm">

				  				<div class="childs">'.$childs_html.'</div>
								</div>
							</div>
						';
					// ---
				}
			// ---

			// Pagination
				$pagination = new Pagination();
				$pagination->total = $testimonials_total;
				$pagination->page = $page;
				$pagination->limit = 20;
				$pagination->url = $this->url->link('extension/module/testimonials', 'page={page}', true);

				$data['results'] = sprintf($this->language->get('text_pagination'), ($testimonials_total) ? (($page - 1) * 20) + 1 : 0, ((($page - 1) * 20) > ($testimonials_total - 20)) ? $testimonials_total : ((($page - 1) * 20) + 20), $testimonials_total, ceil($testimonials_total / 20));

				$data['pagination'] = $pagination->render();
			// ---
		// ---

		$this->response->setOutput($this->load->view('extension/module/testimonials', $data));
	}
}