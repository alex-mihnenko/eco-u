<?php
// catalog/controller/ajax/index.php
class ControllerAjaxIndex extends Controller {
  public function ajaxGetProducts() {
    $this->load->language('ajax/index');
    $this->load->model('catalog/product');

    // загружаем все товары
    $products = $this->model_catalog_product->getProducts();
    $data['products'] = $products;

    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($data['products']));
  }

  // Получить товар
  public function ajaxGetProduct() {
    if (isset($this->request->get['product_id'])) {
        $product_id = (int) $this->request->get['product_id'];

        if ($product_id > 0) {
            $this->load->model('catalog/product');
            $product = $this->model_catalog_product->getProduct($product_id);
            $data['product'] = $product;
            
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($data['product']));
        }
    }
  }
  
  // Получить товары по тэгу
  public function ajaxGetProductsByTag() {
      $arRequest = $this->request->get;
      if(!empty($arRequest['tag'])) {
          $this->load->model('catalog/product');
          $data['products'] = $this->model_catalog_product->getProductsByTag($arRequest['tag']);
          if(!empty($data['products'])) {
                $this->response->setOutput($this->load->view('product/modal', $data));
          } else {
                $this->response->setOutput(json_encode(false));    
          }
      }
  }
  
  // Получить корзину
  public function ajaxGetCart() {
        $this->load->language('checkout/cart');
        
        $data['products'] = Array();
        if ($this->cart->hasProducts() || !empty($this->session->data['vouchers'])) {
                $products = $this->cart->getProducts();
                foreach($products as $i => $product)
                {
                    $products[$i]['link_remove'] = '/?route=ajax/index/ajaxRemoveCartProduct&cart_id='.$product['cart_id'];
                }
                $data['products'] = $products;
        }
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($data));
  }
  
  // Удалить товар из корзины
  public function ajaxRemoveCartProduct() {
        if($cart_id = $this->request->get['cart_id'])
        {
            $this->cart->remove(intval($cart_id));
            
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode(Array('success' => true)));
        }
  }
  
  // Изменить количество товара в корзине
  public function ajaxChangeCartQuantity() {
        $cart_id = $this->request->post['cart_id'];
        $quantity = $this->request->post['quantity'];
        if(!empty($cart_id) && !empty($quantity)) {
            $this->cart->update($cart_id, $quantity);
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode(Array('status' => 'success')));
        }
  }
  
  // Зарегистрировать покупателя
  public function ajaxRegisterCustomer() {
        $arRequest = $this->request->post;
        $arUser['firstname'] = '';
        $arUser['lastname'] = '';
        $arUser['fax'] = '';
        $arUser['company'] = '';
        $arUser['address_1'] = '';
        $arUser['address_2'] = '';
        $arUser['city'] = '';
        $arUser['postcode'] = '';
        $arUser['country_id'] = 0;
        $arUser['zone_id'] = 0;
        $arUser['telephone'] = str_replace(Array('(', ')', '+', '-', ' '), '', $arRequest['telephone']);
        $arUser['password'] = $arRequest['pass'];
        $arUser['email'] = $arUser['telephone'].'@eco-u.ru';
        
        $this->load->model('account/customer');
        $customer_id = $this->model_account_customer->addCustomer($arUser);
        return $customer_id;
  }
  
  // Отправка sms с подтверждением
  public function ajaxSendConfirmationSms() {
        $this->load->model('sms/confirmation');
        $arRequest = $this->request->post;
        $phone = $arRequest['telephone'];
        $phoneFormat = str_replace(Array('(', ')', '+', '-', ' '), '', $phone);
        if(!empty($phoneFormat))
        {
            $code = substr(str_replace('.', '', hexdec(md5(time()+$phone))), 0, 6);
            $message = str_replace('[REPLACE]', $code, $this->config->get('config_sms_confirmation_text'));
            $this->model_sms_confirmation->addCode($code, time()+300);
            $this->model_sms_confirmation->clearOldCodes();
            $result = json_decode($this->model_sms_confirmation->sendSMS($phoneFormat, $message));
            if($result->status == 'success')
            {
                echo json_encode(Array('status' => 'success'));
            }
            else
            {
                echo json_encode(Array('status' => 'error'));
            }
        }
  }
  
  // Проверка sms кода и регистрация покупателя
  public function ajaxValidateRegistration() {
      
        $this->load->model('sms/confirmation');
        $arRequest = $this->request->post;
        $phone = $arRequest['telephone'];
        $password = $arRequest['pass'];
        $code = $arRequest['smscode'];
        
        //$this->model_sms_confirmation->clearOldCodes();
        $valid = $this->model_sms_confirmation->validateCode($code);
        if($valid === 0)
        {
            echo json_encode(Array('status' => 'error'));
        }
        else
        {
            $customer_id = $this->ajaxRegisterCustomer();
            if($customer_id) {
                $this->customer->loginByPhone($phone, $password);
                echo json_encode(Array('status' => 'success', 'customer_id' => $customer_id));
            } else {
                echo json_encode(Array('status' => 'error'));
            }
        }
  }
  
  // Проверка sms кода при перегенерации забытого пароля
  public function ajaxValidateNewPassword() {
      
        $this->load->model('sms/confirmation');
        $arRequest = $this->request->get;
        $phoneFormat = str_replace(Array('(', ')', '+', '-', ' '), '', $arRequest['telephone']);
        $password = $arRequest['password'];
        
        $valid = $this->model_sms_confirmation->validateCode($password);
        if($valid === 0)
        {
            echo json_encode(Array('status' => 'error'));
        }
        else
        {
            $result = $this->customer->getByPhone($phoneFormat);
            if(isset($result['customer_id'])) {
                $this->customer->setPassword($password, $result['customer_id']);
                $this->customer->loginByPhone($phoneFormat, $password);
                echo json_encode(Array('status' => 'success'));
            } else {
                echo json_encode(Array('status' => 'error'));
            }
        }
  }
  
  /*public function ajaxSetPassword() {
      $arRequest = $this->request->get;
      $password = $arRequest['password'];
      $cid = 17;
      $this->customer->setPassword($password, $cid);
  }*/
  
  // Авторизация по номеру телефона и паролю
  public function ajaxLoginByPhone() {
      $arUser = $this->request->post;
      $phone = str_replace(Array('(', ')', '+', '-', ' '), '', $arUser['telephone']);
      $password = $arUser['password'];
      if(!empty($password)) {
          $response = Array('status' => 'success');
          if($this->customer->loginByPhone($phone, $password)) {
              echo json_encode($response);
          }
          else
          {
              echo json_encode(Array('status' => 'error'));
          }
      }
  }
  
  public function ajaxLogout() {
      $this->customer->logout();
      $this->response->setOutput(json_encode(Array('status' => 'success')));
  }
  
  // Создать заказ
  public function ajaxAddOrder() {
        $this->load->model('checkout/order');
        
        // Основные данные заказа
        $data['products'] = $this->cart->getProducts();
        foreach($data['products'] as $i => $product) {
            if(empty($product['weight_variants'])) {
                $data['products'][$i]['amount'] = round($product['quantity']);
                $data['products'][$i]['variant'] = 1;
            } else {
                $arWeightVariants = explode(',', $product['weight_variants']);
                $data['products'][$i]['amount'] = round($product['quantity']/$arWeightVariants[$product['weight_variant']]);
                $data['products'][$i]['variant'] = $arWeightVariants[$product['weight_variant']];
            }
        }
        $data['total'] = $this->cart->getTotal();
        $data['invoice_prefix'] = $this->config->get('config_invoice_prefix');
        $data['store_id'] = $this->config->get('config_store_id');
        $data['store_name'] = $this->config->get('config_name');
        $data['store_url'] = $this->config->get('config_url');
        $data['customer_id'] = $this->session->data['customer_id'];
        $data['customer_group_id'] = $this->customer->getGroupId();
        $data['firstname'] = $this->customer->getFirstName();
        $data['lastname'] = $this->customer->getLastName();
        $data['email'] = $this->customer->getEmail();
        $data['telephone'] = $this->customer->getTelephone();
        $data['fax'] = $this->customer->getFax();
        
        // Оплата
        $data['payment_firstname'] = $this->customer->getFirstName();
        $data['payment_lastname'] = $this->customer->getLastName();
        $data['payment_company'] = '';
        $data['payment_address_1'] = '';
        $data['payment_address_2'] = '';
        $data['payment_city'] = '';
        $data['payment_postcode'] = '';
        $data['payment_country'] = '';
        $data['payment_country_id'] = '';
        $data['payment_zone'] = '';
        $data['payment_zone_id'] = '';
        $data['payment_address_format'] = ''; 
        $data['payment_method'] = '';
        $data['payment_code'] = '';
        
        // Доставка
        $data['shipping_firstname'] = $this->customer->getFirstName();
        $data['shipping_lastname'] = $this->customer->getLastName();
        $data['shipping_company'] = '';
        $data['shipping_address_1'] = '';
        $data['shipping_address_2'] = '';
        $data['shipping_city'] = '';
        $data['shipping_postcode'] = '';
        $data['shipping_country'] = '';
        $data['shipping_country_id'] = '';
        $data['shipping_zone'] = '';
        $data['shipping_zone_id'] = '';
        $data['shipping_address_format'] = '';
        $data['shipping_method'] = '';
        $data['shipping_code'] = '';
        $data['custom_field'] = '';
        $data['payment_custom_field'] = '';
        $data['shipping_custom_field'] = '';
        
        // Прочее
        $data['comment'] = '';
        $data['affiliate_id'] = '';
        $data['commission'] = '';
        $data['marketing_id'] = '';
        $data['tracking'] = '';
        $data['currency_id'] = $this->currency->getId($this->session->data['currency']);
        $data['currency_code'] = $this->session->data['currency'];
        $data['currency_value'] = $this->currency->getValue($this->session->data['currency']);
        $data['ip'] = $this->request->server['REMOTE_ADDR'];
        $data['user_agent'] = '';
        $data['accept_language'] = '';
        $data['language_id'] = $this->config->get('config_language_id');

        if (!empty($this->request->server['HTTP_X_FORWARDED_FOR'])) {
                $data['forwarded_ip'] = $this->request->server['HTTP_X_FORWARDED_FOR'];
        } elseif (!empty($this->request->server['HTTP_CLIENT_IP'])) {
                $data['forwarded_ip'] = $this->request->server['HTTP_CLIENT_IP'];
        } else {
                $data['forwarded_ip'] = '';
        }

        if (isset($this->request->server['HTTP_USER_AGENT'])) {
                $data['user_agent'] = $this->request->server['HTTP_USER_AGENT'];
        } else {
                $data['user_agent'] = '';
        }

        if (isset($this->request->server['HTTP_ACCEPT_LANGUAGE'])) {
                $data['accept_language'] = $this->request->server['HTTP_ACCEPT_LANGUAGE'];
        } else {
                $data['accept_language'] = '';
        }
        
        $order_id = $this->model_checkout_order->addOrder($data);
        
        $this->response->addHeader('Content-Type: application/json');
	if($order_id) {
            // Подтверждение купона
            if(isset($this->session->data['coupon'])) {
                $this->load->model('extension/total/coupon');

                $order_info = Array(
                    'order_id' => $order_id,
                    'customer_id' => $this->customer->getId()
                );
                $coupon = $this->model_extension_total_coupon->getCoupon($this->session->data['coupon']);

                // Totals
                  $this->load->model('extension/extension');

                  $totals = array();
                  $taxes = $this->cart->getTaxes();
                  $total = 0;

                  // Because __call can not keep var references so we put them into an array. 
                  $total_data = array(
                          'totals' => &$totals,
                          'taxes'  => &$taxes,
                          'total'  => &$total
                  );

                  $sort_order = array();

                  $results = $this->model_extension_extension->getExtensions('total');

                  foreach ($results as $key => $value) {
                          $sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
                  }

                  array_multisort($sort_order, SORT_ASC, $results);

                  foreach ($results as $result) {
                          if ($this->config->get($result['code'] . '_status')) {
                                  $this->load->model('extension/total/' . $result['code']);
                                  $this->{'model_extension_total_' . $result['code']}->getTotal($total_data);
                          }
                  }

                  $sort_order = array();

                  foreach ($totals as $key => $value) {
                          $sort_order[$key] = $value['sort_order'];
                  }

                  array_multisort($sort_order, SORT_ASC, $totals);

                  foreach($totals as $total) {
                      if($total['code'] == 'total') {
                          $total_price = ceil($total["value"]);
                          break;
                      }
                  }

                $order_total = Array(
                    'value' => $total_price,
                    'title' => "#{$order_id} ({$coupon['code']})"
                );
                $data['total'] = $total_price;
                
                if(!$this->customer->getCouponDiscount()) {
                    $data['discount'] = $this->cart->getOrderDiscount();
                } else {
                    if(isset($this->session->data['personal_discount'])) $personalDiscount = floor($this->session->data['personal_discount']/100*$this->cart->getTotal());
                    else $personalDiscount = 0;
                    $coupon = $this->customer->getCouponDiscount();
                    $couponDiscount = floor($coupon['discount']/100*$this->cart->getTotal());
                    if($couponDiscount > $personalDiscount) {
                        $data['coupon_discount'] = $couponDiscount;
                    } else {
                        $data['discount'] = $personalDiscount;
                    }
                }
                
                $this->model_extension_total_coupon->confirm($order_info, $order_total);
                $this->model_checkout_order->editOrder($order_id, $data);
                $this->model_checkout_order->addOrderHistory($order_id, 1);
              }
            $json = Array('status' => 'success', 'orderId' => $order_id);
        } else {
            $json = Array('status' => 'error');
        }
        $this->response->setOutput(json_encode($json));
  }
  
  public function ajaxGetDeliveryPrice() {
      $address = $this->request->post['address'];
      
      $this->load->model('dadata/index');
      
      $structure = array("ADDRESS");
      $record = array($address);
      $result = $this->model_dadata_index->cleanRecord($structure, $record);
      if(!isset($result['data'][0][0]['beltway_hit'])) {
          $bwhit = 'NOT_IN_MKAD';
      } else {
          $bwhit = 'IN_MKAD';
      }
      echo json_encode(Array('status' => 'success', 'result' => $result, 'mkad' => $bwhit));
  }
  
  // Оформление доставки
  public function ajaxSetDelivery() {
      $address = $this->request->post['address'];
      $address_new = $this->request->post['address_new'];
      $comment = $this->request->post['comment'];
      $order_id = $this->request->post['order_id'];
      $payment_method = $this->request->post['payment_method'];
      $strDateTime = 'Дата и время доставки: '.$this->request->post['date'].' '.$this->request->post['time'].PHP_EOL;
      $strDeliveryInterval = $this->request->post['date'].' '.$this->request->post['time'];
      $customer_id = $this->customer->getId();
      $telephone = str_replace(Array('(', ')', '+', ' ', '-'), '', $this->request->post['telephone']);
      
      $this->load->model('dadata/index');
      
      $structure = array("ADDRESS");
      $record = array($address);
      $result = $this->model_dadata_index->cleanRecord($structure, $record);
      
      if(isset($result['data'][0][0]['beltway_hit'])) {
          $bwhit = $result['data'][0][0]['beltway_hit'];
          if($result['data'][0][0]['beltway_hit'] == 'IN_MKAD') {
              $delivery_price = 250;
          } else {
              $delivery_price = 600;
          }
      } else {
          $bwhit = 'NOT_IN_MKAD';
          $delivery_price = 600;
      }
      
      
      $data = Array(
          'address' => $address,
          'comment' => $comment,
          'delivery_price' => $delivery_price,
          'delivery_time' => $strDateTime,
          'delivery_interval' => $strDeliveryInterval,
          'payment_method' => $payment_method,
          'mkad' => $bwhit
      );
      if(!$this->customer->getCouponDiscount()) {
            $data['discount'] = $this->cart->getOrderDiscount();
        } else {
            if(isset($this->session->data['personal_discount'])) $personalDiscount = floor($this->session->data['personal_discount']/100*$this->cart->getTotal());
            else $personalDiscount = 0;
            $coupon = $this->customer->getCouponDiscount();
            $couponDiscount = floor($coupon['discount']/100*$this->cart->getTotal());
            if($couponDiscount > $personalDiscount) {
                $data['coupon_discount'] = $couponDiscount;
            } else {
                $data['discount'] = $personalDiscount;
            }
        }
      
      $this->load->model('checkout/order');
      if($this->model_checkout_order->setDelivery($order_id, $customer_id, $data)) {
          // Добавление адреса доставки в список адресов клиента
          if($address_new == 'true') $this->customer->setAddress(0, $address);
          // Очистка корзины
          $this->cart->clear();
          // Отправка sms        
            $this->load->model('sms/confirmation');
            $message = str_replace('[REPLACE]', $order_id, $this->config->get('config_sms_order_new_text'));
            $this->model_sms_confirmation->sendSMS($telephone, $message);
          echo json_encode(Array('status' => 'success'));
      } else {
          echo json_encode(Array('status' => 'error'));
      }
  }
  
  // Изменить имя покупателя
  public function ajaxChangeCustomerInfo() {
      $arRequest = $this->request->post;
      if($this->customer->isLogged()) {
          if($this->customer->setInfo($arRequest)) {
              $this->response->setOutput(json_encode(Array('status' => 'success')));
          } else {
              $this->response->setOutput(json_encode(Array('status' => 'error')));
          }
      } elseif(!empty($arRequest['firstname']) && !empty($arRequest['telephone'])) {
            $arUser['firstname'] = $arRequest['firstname'];
            $arUser['lastname'] = '';
            $arUser['fax'] = '';
            $arUser['company'] = '';
            $arUser['address_1'] = '';
            $arUser['address_2'] = '';
            $arUser['city'] = '';
            $arUser['postcode'] = '';
            $arUser['country_id'] = 0;
            $arUser['zone_id'] = 0;
            $arUser['telephone'] = str_replace(Array('(', ')', '+', '-', ' '), '', $arRequest['telephone']);
            $arUser['password'] = md5($arUser['telephone'].time());
            $arUser['email'] = $arUser['telephone'].'@eco-u.ru';

            $this->load->model('account/customer');
            $customer_id = $this->model_account_customer->addCustomer($arUser);
            
            if($customer_id) { 
                $this->customer->loginByPhone($arUser['telephone'], $arUser['password']);
                $this->response->setOutput(json_encode(Array('status' => 'success')));
            } else {
                $this->response->setOutput(json_encode(Array('status' => 'error')));
            }
      } else {
          $this->response->setOutput(json_encode(Array('status' => 'error')));
      }
  }
  
  // Получить новый пароль по sms
  public function ajaxForgotPassword() {
      $this->load->model('sms/confirmation');
      
      $arRequest = $this->request->get;
      $phoneFormat = str_replace(Array('(', ')', '+', '-', ' '), '', $arRequest['telephone']);
      
      $result = $this->customer->getByPhone($phoneFormat);
      
      if(isset($result['customer_id'])) {
          $code = substr(str_replace('.', '', hexdec(md5(time()+$phoneFormat))), 0, 6);
          $message = str_replace('[REPLACE]', $code, $this->config->get('config_sms_password_new_text'));
          $this->model_sms_confirmation->addCode($code, time()+300);
          $this->model_sms_confirmation->clearOldCodes();
          $this->model_sms_confirmation->sendSMS($phoneFormat, $message);
          $this->response->setOutput(json_encode(Array('status' => 'success')));
      } else {
          $this->response->setOutput(json_encode(Array('status' => 'error')));
      }
  }
  
  public function ajaxApplyCoupon() {
      $this->load->model('extension/total/coupon');
      $arRequest = $this->request->post;
      if(isset($arRequest['code'])) {
          $coupon = $this->model_extension_total_coupon->getCoupon($arRequest['code']);
          if($coupon) {
              $this->session->data['coupon'] = $coupon['code'];
              $this->session->data['coupon_id'] = $coupon['coupon_id'];
          } else {
              unset($this->session->data['coupon']);
              unset($this->session->data['coupon_id']);
          }
          
          // Totals
        $this->load->model('extension/extension');

        $totals = array();
        $taxes = $this->cart->getTaxes();
        $total = 0;

        // Because __call can not keep var references so we put them into an array. 
        $total_data = array(
                'totals' => &$totals,
                'taxes'  => &$taxes,
                'total'  => &$total
        );

        $sort_order = array();

        $results = $this->model_extension_extension->getExtensions('total');

        foreach ($results as $key => $value) {
                $sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
        }

        array_multisort($sort_order, SORT_ASC, $results);

        foreach ($results as $result) {
                if ($this->config->get($result['code'] . '_status')) {
                        $this->load->model('extension/total/' . $result['code']);
                        $this->{'model_extension_total_' . $result['code']}->getTotal($total_data);
                }
        }

        $sort_order = array();

        foreach ($totals as $key => $value) {
                $sort_order[$key] = $value['sort_order'];
        }

        array_multisort($sort_order, SORT_ASC, $totals);  
          
          
        foreach($totals as $total) {
            if($total['code'] == 'total') {
                $total_price = ceil($total["value"]);
                $this->response->setOutput(json_encode(Array('status' => 'success', 'total' => $total_price)));
                break;
            }
        }
      } else {
          $this->response->setOutput(json_encode(Array('status' => 'error')));
      }
  }
  
  public function ajaxGetPersonalOrders() {
      $customer_id = $this->session->data['customer_id'];
      
      $this->load->model('checkout/order');
      $orders = $this->model_checkout_order->getPersonalOrders($customer_id);
      $arOrders = Array();
      foreach($orders as $order) {
          $arOrders[] = Array(
              'order_id' => $order['order_id'],
              'date_added' => $order['date_added'],
              'order_status' => $order['status_text'],
              'order_status_id' => $order['order_status_id'],
              'order_total' => $order['total']
          );
      }
      
      $this->response->setOutput(json_encode(Array('status' => 'success', 'orders' => $arOrders)));
  }
  
  public function ajaxSetCustomerData() {
      $arRequest = $this->request->post;
      $arRequest['telephone'] = str_replace(Array('(', ')', '+', '-', ' '), '', $arRequest['telephone']);
      
      $this->load->model('dadata/index');
      $structure = Array();
      $record = Array();
      foreach($arRequest['addresses'] as $address) {
          $structure[] = "ADDRESS";
          $record[] = $address['value'];
      }
      $result = $this->model_dadata_index->cleanRecord($structure, $record);
      $toReplace = Array();
      foreach($arRequest['addresses'] as $i => $address) {
            if(in_array($result['data'][0][$i]['qc'], Array(0,3))) {
                $this->customer->setAddress($address['address_id'], $result['data'][0][$i]['result']);
                $toReplace[] = Array(
                    'value' => $result['data'][0][$i]['result'],
                    'id' => $address['address_id']
                );
            } else {
                $this->customer->setAddress($address['address_id'], $address['value']);
            }
      }
      
      $this->customer->setFirstName($arRequest['firstname']);
      $this->customer->setTelephone($arRequest['telephone']);
      $this->customer->setEmail($arRequest['email']);
      
      $this->response->setOutput(json_encode(Array('status' => 'success', 'dadata' => $toReplace)));
  }
  
  public function ajaxSearchProducts() {
      $search = $this->request->get['search'];
      
      $this->load->model('catalog/product');
      $this->load->model('tool/image');
      
      $data['products'] = $this->model_catalog_product->searchProducts($search);
      foreach($data['products'] as $i => $result) {
            if(isset($result['composite_price'])) {
                $data['products'][$i]['composite_price'] = json_encode($result['composite_price']);
            }
            if ($result['image_preview']) {
                    $image = '/image/'.$result['image_preview'];
                    //$image = $this->model_tool_image->resize($result['image'], $this->config->get($this->config->get('config_theme') . '_image_product_width'), $this->config->get($this->config->get('config_theme') . '_image_product_height'));
            } else {
                    $image = $this->model_tool_image->resize('eco_logo.png', $this->config->get($this->config->get('config_theme') . '_image_product_width'), $this->config->get($this->config->get('config_theme') . '_image_product_height'));
            }
            if(!empty($result['sticker']['class'])) {
                $data['products'][$i]['sticker_class'] = $result['sticker']['class'];
                $data['products'][$i]['sticker_name'] = $result['sticker']['name'];
            }
            $data['products'][$i]['thumb'] = $image;
      }
      $this->response->setOutput($this->load->view('product/search', $data));
  }
  
  public function ajaxGetOrderPrice() {
      $response = Array(
            'status' => 'success',
            'price' => (int)$this->cart->getOrderPrice()
      );
      $response['discount'] = (int)$this->cart->getTotal() - $response['price'];
      $this->response->setOutput(json_encode($response));
  }
}