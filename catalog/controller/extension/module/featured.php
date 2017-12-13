<?php
class ControllerExtensionModuleFeatured extends Controller {
	public function index($setting) {
		$this->load->language('extension/module/featured');

                if (isset($this->session->data['user_id']) && $this->model_account_user->isAdmin($this->session->data['user_id']))
                {
                    $data['is_admin'] = true;
                }
                else
                {
                    $data['is_admin'] = false;
                }
                
		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_tax'] = $this->language->get('text_tax');

		$data['button_cart'] = $this->language->get('button_cart');
		$data['button_wishlist'] = $this->language->get('button_wishlist');
		$data['button_compare'] = $this->language->get('button_compare');

		$this->load->model('catalog/product');

		$this->load->model('tool/image');

		$data['products'] = array();

		if (!$setting['limit']) {
			$setting['limit'] = 4;
		}

		if (!empty($setting['product'])) {
			$products = array_slice($setting['product'], 0, (int)$setting['limit']);

			foreach ($products as $product_id) {
				$product_info = $this->model_catalog_product->getProduct($product_id);

				if ($product_info) {
                                        if($product_info['stock_status_id'] <> 7 && $product_info['quantity'] <= 0) continue;
					if ($product_info['image']) {
						$image = $this->model_tool_image->resize($product_info['image'], $setting['width'], $setting['height']);
					} else {
						$image = $this->model_tool_image->resize('placeholder.png', $setting['width'], $setting['height']);
					}

					if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
						$price = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
					} else {
						$price = false;
					}

					if ((float)$product_info['special']) {
						$special = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
					} else {
						$special = false;
					}

					if ($this->config->get('config_tax')) {
						$tax = $this->currency->format((float)$product_info['special'] ? $product_info['special'] : $product_info['price'], $this->session->data['currency']);
					} else {
						$tax = false;
					}

					if ($this->config->get('config_review_status')) {
						$rating = $product_info['rating'];
					} else {
						$rating = false;
					}

                                        if($special) {
                                            $discount_sticker = ceil(((float)$price - (float)$special)/(float)$price*100);
                                            $price = $special;
                                        }
                                        $arProducts = array(
                                                'product_id'  => $product_info['product_id'],
                                                'available_in_time' => $product_info['available_in_time'],
                                                'quantity'    => $product_info['quantity'],
                                                'thumb'       => $image,
                                                'name'        => $product_info['name'],
                                                'description_short' => $product_info['description_short'],
                                                'description' => utf8_substr(strip_tags(html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8')), 0, $this->config->get($this->config->get('config_theme') . '_product_description_length')) . '...',
                                                'price'       => $price,
                                                'special'     => $special,
                                                'tax'         => $tax,
                                                'minimum'     => $product_info['minimum'] > 0 ? $product_info['minimum'] : 1,
                                                'rating'      => $product_info['rating'],
                                                'href'        => $this->url->link('product/product', 'path=' . $this->request->get['path'] . '&product_id=' . $product_info['product_id']),
                                                'stock_status'      => $product_info['stock_status'],
                                                'stock_status_id'   => $product_info['stock_status_id'],
                                                'weight_variants'   => $product_info['weight_variants'],
                                                'weight_class' => $product_info['weight_class'],
                                                'sticker_name' => $product_info['sticker']['name'],
                                                'sticker_class' => $product_info['sticker']['class']
                                        );
                                        
                                        if(isset($discount_sticker)) {
                                            $arProducts['discount_sticker'] = $discount_sticker;
                                            unset($discount_sticker);
                                        }
                                        if($data['is_admin']) {
                                                $arProducts['edit_link'] = '/admin/?route=catalog/product/edit&token='.$this->session->data['token'].'&product_id='.$product_info['product_id'];
                                        }
                                        if($product_info['composite_price'] !== false) {
                                                $arProducts['composite_price'] = json_encode($product_info['composite_price']);       
                                        }
                                        $data['products'][] = $arProducts;
				}
			}
		}

		if ($data['products']) {
			return $this->load->view('extension/module/featured', $data);
		}
	}
}