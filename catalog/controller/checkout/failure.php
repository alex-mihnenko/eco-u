<?php
class ControllerCheckoutFailure extends Controller {
	public function index() {
		$this->load->language('checkout/failure');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_basket'),
			'href' => $this->url->link('checkout/cart')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_checkout'),
			'href' => $this->url->link('checkout/checkout', '', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_failure'),
			'href' => $this->url->link('checkout/failure')
		);

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_message'] = sprintf($this->language->get('text_message'), $this->url->link('information/contact'));

		$data['button_continue'] = $this->language->get('button_continue');

		$data['continue'] = $this->url->link('common/home');

                if (isset($this->session->data['user_id']) && $this->model_account_user->isAdmin($this->session->data['user_id']))
                {
                    $data['is_admin'] = true;
                }
                else
                {
                    $data['is_admin'] = false;
                }
                
                // Спеццена
                $this->load->model('catalog/product');
                $results = $this->model_catalog_product->getProductsSpecialPrice();
                $data['spec_products'] = Array();
                foreach ($results as $result) {
                        if($result['stock_status_id'] <> 7 && $result['quantity'] <= 0) continue;
                        if ($result['image_preview']) {
                                $image = '/image/'.$result['image_preview'];
                                //$image = $this->model_tool_image->resize($result['image'], $this->config->get($this->config->get('config_theme') . '_image_product_width'), $this->config->get($this->config->get('config_theme') . '_image_product_height'));
                        } else {
                                $image = $this->model_tool_image->resize('eco_logo.png', $this->config->get($this->config->get('config_theme') . '_image_product_width'), $this->config->get($this->config->get('config_theme') . '_image_product_height'));
                        }

                        if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
                                $price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
                        } else {
                                $price = false;
                        }

                        if ((float)$result['special']) {
                                $special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
                        } else {
                                $special = false;
                        }

                        if ($this->config->get('config_tax')) {
                                $tax = $this->currency->format((float)$result['special'] ? $result['special'] : $result['price'], $this->session->data['currency']);
                        } else {
                                $tax = false;
                        }

                        if ($this->config->get('config_review_status')) {
                                $rating = (int)$result['rating'];
                        } else {
                                $rating = false;
                        }

                        if($result['special_price']) {
                            $price = $result['special_price'];
                            $special = $price;
                        }

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

                        if (isset($this->request->get['limit'])) {
                                $url .= '&limit=' . $this->request->get['limit'];
                        }
                        $arProducts = array(
                                'product_id'  => $result['product_id'],
                                'available_in_time' => $result['available_in_time'],
                                'status'      => $result['status'],
                                'quantity'    => $result['quantity'],
                                'thumb'       => $image,
                                'name'        => $result['name'],
                                'description_short' => $result['description_short'],
                                'description' => utf8_substr(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8')), 0, $this->config->get($this->config->get('config_theme') . '_product_description_length')) . '...',
                                'price'       => $price,
                                'special'     => $special,
                                'tax'         => $tax,
                                'minimum'     => $result['minimum'] > 0 ? $result['minimum'] : 1,
                                'rating'      => $result['rating'],
                                'href'        => $this->url->link('product/product', 'product_id=' . $result['product_id'] . $url),
                                'stock_status'      => $result['stock_status'],
                                'stock_status_id'   => $result['stock_status_id'],
                                'weight_variants'   => $result['weight_variants'],
                                'weight_class' => $result['weight_class'],
                                'sticker_name' => $result['sticker']['name'],
                                'sticker_class' => $result['sticker']['class']
                        );
                        if(isset($discount_sticker)) {
                            $arProducts['discount_sticker'] = $discount_sticker;
                            unset($discount_sticker);
                        }
                        if($data['is_admin']) {
                                $arProducts['edit_link'] = '/admin?route=catalog/product/edit&token='.$this->session->data['token'].'&product_id='.$result['product_id'];
                        }
                        if($result['composite_price'] !== false) {
                                $arProducts['composite_price'] = json_encode($result['composite_price']);
                        }
                        $data['spec_products'][] = $arProducts;
                }
                        
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('checkout/failure', $data));
	}
}