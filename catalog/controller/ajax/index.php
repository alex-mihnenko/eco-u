<?php
// catalog/controller/ajax/index.php
class ControllerAjaxIndex extends Controller {
  const RETAILCRM_KEY = 'AuNf4IgJFHTmZQu7PwTKuPNQch5v03to';
  const MS_AUTH = 'admin@mail195:b41fd841edc5';

  // Customers
    // Registration
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
    
    // SMS Confirm
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
    
    // Check registration
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
    
    // Check new password
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
  
    // Set new password    
    public function ajaxSetPassword() {
        $arRequest = $this->request->get;
        $password = $arRequest['password'];
        $cid = 17;
        $this->customer->setPassword($password, $cid);
    }

    // Registration
    public function registrationCustomer() {
      // ---
        // Check
          if( !isset($this->request->post['firstname']) && !isset($this->request->post['telephone']) ){
            // ---
              $response->status = 'error';
              $response->message = 'Нет данных';
              

              echo json_encode($response);
              exit;
            // ---
          }
        // ---

        // Init
          $firstname = $this->request->post['firstname'];
          $telephone = preg_replace("/[^0-9,.]/", "", $this->request->post['telephone']);

          $response = new stdClass();
        // ---

        // Create customer
          $this->load->model('account/customer');
          $customer = $this->model_account_customer->getCustomerByTelephone($telephone);

          if( empty($customer) ) {
            // Create new customer
              $password = $this->model_account_customer->generatePassword();

              $customer_data = array(
                'firstname' => $firstname,
                'lastname' => '',
                'email' => '',
                'telephone' => $telephone,
                'fax' => '',
                'password' => $password,
                'address_1' => ''
              );

              $customer_id = $this->model_account_customer->addCustomer($customer_data);
            // ---

            // SMS alert
              $this->load->model('sms/confirmation');
              $message = str_replace('[REPLACE]', $password, $this->config->get('config_sms_password_new_text'));
              $this->model_sms_confirmation->sendSMS($telephone, $message);
            // ---
          }
          else{
            // ---
              $response->status = 'error';
              $response->message = 'Вы уже зарегистрированы';

              echo json_encode($response);
              exit;
            // ---
          }
        // ---

        // Response
        $response->status = 'success';
        $response->message = 'Успешно!<br>Мы отправили Вам SMS с паролем.';
        

        echo json_encode($response);
        exit;

      // ---
    }
    
    // Login
    public function ajaxLoginByPhone() {
        //$arUser = $this->request->post;
        //$phone = str_replace(Array('(', ')', '+', '-', ' '), '', $arUser['telephone']);
        //$password = $arUser['password'];

        $telephone = preg_replace("/[^0-9,.]/", "", $this->request->post['telephone']);
        $password =$this->request->post['password'];

        if(!empty($password)) {
            $response = Array('status' => 'success', 'message' => '');
            $locked = '';

            if($this->customer->loginByPhone($telephone, $password, false, $locked)) {
              echo json_encode($response);
            }
            else {
                if($locked) {
                    if($locked == 1) {
                        $m = 'минуту';
                    } elseif($locked < 5) {
                        $m = 'минуты';
                    } else {
                        $m = 'минут';
                    }
                    $message = 'Ваш аккаунт заблокирован на ' . $locked . ' ' . $m;// . date('H:i:s d.m.Y', strtotime($locked));
                } else {
                    $message = 'Не верный номер или пароль';
                }

                echo json_encode(Array('status' => 'error', 'message' => $message));
            }
        }
    }
    
    // Logout
    public function ajaxLogout() {
        $this->customer->logout();
        $this->response->setOutput(json_encode(Array('status' => 'success')));
    }

    // Recovery password
    public function recoveryPasswordByTelephone() {
      // Init
          $telephone = preg_replace("/[^0-9,.]/", "", $this->request->post['telephone']);

          $response = new stdClass();
        // ---

      // Check customer
        $this->load->model('account/customer');
        $customer = $this->model_account_customer->getCustomerByTelephone($telephone);

        if( empty($customer) ) {
          $response->status = 'error';
          $response->message = 'Не верный телефон';
          echo json_encode($response);
          exit;
        }
        else {
          // Create new password
            $password = $this->model_account_customer->generatePassword();
            $this->model_account_customer->editPassword($telephone,$password);
          // ---
          
          // SMS alert
            $this->load->model('sms/confirmation');
            $message = str_replace('[REPLACE]', $password, $this->config->get('config_sms_password_new_text'));
            $this->model_sms_confirmation->sendSMS($telephone, $message);
          // ---
        }

        // Response
        $response->status = 'success';
        $response->message = 'Мы отправили Вам SMS с паролем';
        

        echo json_encode($response);
        exit;
      // ---
    }

    // Get customer
    public function getCustomerByTelephone() {
      // ---
        // Init
          $telephone = preg_replace("/[^0-9,.]/", "", $this->request->post['telephone']);
          
          $response = new stdClass();
        // ---

        // Get
          $this->load->model('account/customer');
          
          $customer = $this->model_account_customer->getCustomerByTelephone($telephone);

          if( empty($customer) ){
            $response->result = false;
            $response->message = 'Пользователь не найден';
          }
          else{
            $response->result = true;
            $response->message = 'Пользователь найден';

            $response->customer = $customer;
            $response->addresses = $this->model_account_customer->getAddresses($customer['customer_id']);
          }
        // ---

        // Response
        $response->status = 'success';
        
        echo json_encode($response);
        exit;
      // ---
    } 

    // Apply coupon
    public function ajaxApplyCoupon() {
      // ---
        // Init
          $code = $this->request->post['code'];
          $response = new stdClass();
        // ---


        // Processing
        if(isset($code)) {
          $this->load->model('extension/total/coupon');
          $coupon = $this->model_extension_total_coupon->getCoupon($code);
          
          if(!$coupon) {
            $response->status = 'error';
            $response->message = 'Указан несуществующий купон';
            echo json_encode($response);
            exit;
          }

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
                  
                  if(isset($this->session->data['coupon_id'])) {
                      $customer_coupon = $this->customer->getCouponDiscount();
                  }
                  $html = '<div>'; // root
                  if($customer_id = $this->customer->isLogged()) {
                      $this->load->model('checkout/order');
                      $orders = $this->model_checkout_order->getPersonalOrders($customer_id);
                      $customer_discount = $this->customer->getPersonalDiscount($customer_id, $orders);
                      $html .= '<div class="personal-discount" style="position:relative;color:#666;font-size:18px;font-weight:700;height:50px;line-height:50px; margin-top: -32px;display: none;">';
                      $html .= 'Текущая скидка <span class="p-o_discount sticker_discount" style="position:relative;top:0;left:10px;display:inline-block;width:40px;height:40px;line-height:40px;font-size:16px;">' . -1 * (int)$customer_discount . '%</span>';
                      $html .= '<input type="hidden" id="customer_discount" data-type="P" value="' . (int)$customer_discount . '">';
                      $html .= '</div>';
                  }

                  $html .= '<div class="personal-coupon" style="height:50px;  margin-top: -32px; display: none;">';
                  if(isset($customer_coupon)) {
                      if($customer_coupon['type'] == 'P') {
                          $cDcnt = (int)$totals[0]['value']*((int)$customer_coupon['discount']/100);
                          $html .= 'Текущая скидка <span class="p-o_discount sticker_discount b-d_coupon_circle">' . -1*(int)$customer_coupon['discount'] . '%</span>';
                      } elseif($customer_coupon['type'] == 'F') {
                          $cDcnt = (int)$customer_coupon['discount'];
                          $html .= 'Ваша скидка <span class="c-d_amount">' . (int)$customer_coupon['discount'] . '</span> руб';
                      }
                      $html .= '<input type="hidden" id="customer_coupon" data-type="' . $customer_coupon['type'] . '" value="' . (int)$customer_coupon['discount'] . '">';
                  }
                  $html .= '</div>';
                  if(!isset($customer_coupon) && !isset($customer_discount)) {
                          $html .= '<div class="b-d_coupon" style="display: none;">';
                          $html .= 'Есть купон на скидку?';
                          $html .= '</div>';
                  } else {
                          $html .= '<div class="b-d_coupon_discount" style="display: none;">';
                          $html .= 'Увеличить скидку';
                          $html .= '</div>';
                  }
                  $html .= '</div>'; // root


                  $response->status = 'success';
                  $response->message = 'Купон успешно применен';
                  $response->coupon_id = $this->session->data['coupon_id'];

                  //$response->total = (int)$this->cart->getOrderPrice();
                  //$response->html = $html;
                  //$discountValue = (int)$this->cart->getTotal() - $response['total'];
                  //$response->discountValue = $discountValue;

                  echo json_encode($response);
                  exit;
              }
          }
        } else {
          $response->status = 'error';
          $response->message = 'Введите купон купон';
          echo json_encode($response);
          exit;
        }
        // ---
      // ---
    }

    // About order
       public function getAboutOrderModal() {
        // ---
          // Init
            $order_id = $this->request->post['order_id'];
            
            $response = new stdClass();
          // ---

          // Get
            $options['order_id'] = intval($order_id);
            
            $response->order_id = $order_id;
            $response->html = $this->load->controller('account/order',$options);
          // ---

          // Response
          $response->status = 'success';
          
          echo json_encode($response);
          exit;
        // ---
      }
    // ---

    // Reorder
      public function getReorderModal() {
        // ---
          // Init
            $order_id = $this->request->post['order_id'];
            
            $response = new stdClass();
          // ---

          // Get
            $options['order_id'] = $order_id;
            
            $response->order_id = $order_id;
            $response->html = $this->load->controller('account/repeat',$options);
          // ---

          // Response
          $response->status = 'success';
          
          echo json_encode($response);
          exit;
        // ---
      }

      public function repeatCustomerOrder() {
        // ---
          // Init
            $order_id = $this->request->post['order_id'];
            
            $response = new stdClass();
          // ---

          // Get
            $this->load->model('account/order');
            $this->load->model('catalog/product');

            $products = $this->model_account_order->getOrderProducts($order_id);

            $response->count = 0;

            foreach ($products as $product) {
              // ---
                $product_info = $this->model_catalog_product->getProduct($product['product_id']);

                $product_id = $product['product_id'];
                $quantity = $product['amount'];
                $packaging = $product['variant'];


                if ($product_info) {
                  // ---
                    // In stock calculate
                    if( $product_info['quantity'] > 0 && $product_info['status'] == 1 ) { $instock = true; }
                    else if ( $product_info['quantity'] <= 0 && $product_info['status'] == 1 && ($product_info['stock_status_id'] == 7 || $product_info['stock_status_id'] == 6) ) { $instock = true; }
                    else { $instock = false; }

                    if ( $instock ) {
                      // ---
                        if($product_info['weight_variants'] !== '') {
                          $weightVariants = explode(',', $product_info['weight_variants']);
                          $weight_variant = array_search($product['variant'], $weightVariants);
                        } else {
                          $weight_variant = 1;
                        }

                        $option = array();

                        $recurring_id = 0;
                                               
                        $this->cart->add($product_id, $quantity, $packaging, $option, $recurring_id, $weight_variant);

                        $response->report[$product_id] = array('quantity' => $quantity, 'packaging' => $packaging, 'option' => $option, 'recurring_id' => $recurring_id, 'weight_variant' => $weight_variant);
                        
                        $response->count++;
                      // ---
                    }
                  // ---
                }
              // ---
            }

            $response->order_id = $order_id;
          // ---

          // Response
          if( $response->count > 0 ) {
            $response->status = 'success';
            $response->message = 'Товары добавлены в корзину.<br>Вы можете продолжить<br>оформление заказа.';
          }
          else {
            $response->status = 'error';
            $response->message = 'Нет товаров для добавления в корзину';
          }

          
          echo json_encode($response);
          exit;
        // ---
      }
    // ---

    public function isCustomerLogged(){
      // Init
        $customer_id = $this->customer->isLogged();
        
        $response = new stdClass();
      // ---
      
      // Chech
        if( !$customer_id ) { $status = false; }
        else { $status = true; }
      // ---

      // Response
      $response->status = $status;
      
      echo json_encode($response);
      exit;

    }
  // ---

  // Orders
    // Add
    public function ajaxAddOrder($return = false) {
          $this->load->model('checkout/order');
          $this->cache->set('latest_category_sort', 0);
          
          // Основные данные заказа
          $data['products'] = $this->cart->getProducts();
          $total = 0;

          foreach($data['products'] as $i => $product) {
              if(empty($product['weight_variants'])) {
                  $data['products'][$i]['amount'] = round($product['quantity']*$product['packaging']);
                  $data['products'][$i]['variant'] = $product['packaging'];
              } else {
                  $arWeightVariants = explode(',', $product['weight_variants']);
                  $data['products'][$i]['amount'] = round(($product['quantity']*$product['packaging'])/$arWeightVariants[$product['weight_variant']]);
                  $data['products'][$i]['variant'] = $arWeightVariants[$product['weight_variant']];
                  
              }

              $data['products'][$i]['quantity'] = $product['quantity']*$product['packaging'];

              $total += ($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')) * $product['quantity']);
          }
          $data['total'] = $total;
              
          $data['invoice_prefix'] = $this->config->get('config_invoice_prefix');
          $data['store_id'] = $this->config->get('config_store_id');
          $data['store_name'] = $this->config->get('config_name');
          $data['store_url'] = $this->config->get('config_url');

          if(isset($this->session->data['customer_id'])) {
            $data['customer_id'] = $this->session->data['customer_id'];
          } else {
            $data['customer_id'] = $this->customer->getId();
          }

          $data['customer_group_id'] = $this->customer->getGroupId();
          $data['firstname'] =  isset($this->request->post['firstname']) ? $this->request->post['firstname'] :$this->customer->getFirstName();
          $data['lastname'] = $this->customer->getLastName();
          $data['email'] = $this->customer->getEmail();
          $data['telephone'] = isset($this->request->post['telephone']) ? $this->clearTelephone($this->request->post['telephone']) : $this->customer->getTelephone();
          $data['fax'] = $this->customer->getFax();
          
          // Оплата
          $data['payment_firstname'] = isset($this->request->post['firstname']) ? $this->request->post['firstname'] : $this->customer->getFirstName();
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
          $data['shipping_firstname'] = isset($this->request->post['firstname']) ? $this->request->post['firstname'] : $this->customer->getFirstName();
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
                // ---
                
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
          
          if($return) { return $order_id; }
          
          $this->response->setOutput(json_encode($json));
    }
    
    // Delivery
    public function ajaxGetDeliveryPrice() {
      // ---

        // Check
          if( !isset($this->request->post['firstname']) && !isset($this->request->post['telephone']) && 
                !isset($this->request->post['address']) ){
            // ---
              $response->status = 'error';
              $response->message = 'Нет данных';
              

              echo json_encode($response);
              exit;
            // ---
          }
        // ---

        // Init
          $firstname = $this->request->post['firstname'];
          $telephone = preg_replace("/[^0-9,.]/", "", $this->request->post['telephone']);

          $address = $this->request->post['address'];
          $deliverydistance = $this->request->post['deliverydistance'];

          $response = new stdClass();


          $this->load->model('checkout/order');
          $this->load->model('account/customer');
          $this->load->model('dadata/index');

          $customer = $this->model_account_customer->getCustomerByTelephone($telephone);

          if( !empty($customer) ) {
            $customer_id = $customer['customer_id'];
          }
        // ---

        // Get sipping area
          unset($this->session->data['shipping_custom_field']);

          $structure = array("ADDRESS");
          $record = array($address);
          $result = $this->model_dadata_index->cleanRecord($structure, $record);

          $response->address = null;
          $response->mkad = null;

          if( $deliverydistance == -1 ){
            if( isset($result['data'][0][0]['beltway_distance']) ) {
              $response->tobeltway = intval($result['data'][0][0]['beltway_distance']);
              $this->session->data['shipping_custom_field'] = 'Внимание! Расчет стоимости доставки произведен не точно. Необходима проверка.';
            }
          }
          else{
            $response->tobeltway = $deliverydistance;
          }

          $response->region = mb_strtolower($result['data'][0][0]['region']);
          $response->dadata = $result['data'];

          if( isset($result['data'][0][0]['source']) ) {
            $response->address = $result['data'][0][0]['source'];
          }

          if( isset($result['data'][0][0]['beltway_hit']) && $result['data'][0][0]['beltway_hit'] == 'IN_MKAD' ) {
              $response->mkad = 'IN_MKAD';
          } else {
              $response->mkad = 'OUT_MKAD';
          }

          // Check
            if( $response->address == null || $response->mkad == null ) {
              // ---
                $response->status = 'error';
                $response->message = 'Не удалось поулчить адрес доставки';
                echo json_encode($response);
                exit;
              // ---
            }
          // --
        // ---

        // Get shipping methods
          $method_data = array();

          $this->load->model('extension/extension');

          $results = $this->model_extension_extension->getExtensions('shipping');

          $methods = [];

          foreach ($results as $key => $method) {
            // ---
              if( $method['code'] == 'free' ) { $cost = (int)$this->config->get('free_total'); }
              else { $cost = (int)$this->config->get($method['code'].'_cost'); }

              if( $method['code'] == 'mkadout' ) { $milecost = (int)$this->config->get($method['code'].'_milecost'); }
              else { $milecost = 0; }

              $methods[$method['code']] = array(
                'extension_id' => $method['extension_id'],
                'cost' => $cost,
                'milecost' => $milecost,
                'title' => $method['title']
              );
            // ---
          }

          $response->methods = $methods;

          // Check
          if( empty($response->methods) ) {
            // ---
              $response->status = 'error';
              $response->message = 'No shipping methods';
              echo json_encode($response);
              exit;
            // ---
          }
        // ---

        // Check discount
            unset($this->session->data['discount']);
            unset($this->session->data['discount_percentage']);
            unset($this->session->data['coupon']);

            $data['discount'] = 0;
            $data['discount_percentage'] = 0;
            $data['coupon'] = false;
            
            $personal_discount = 0;
            $personal_discount_percentage = 0;

            $cumulative_discount = 0;
            $cumulative_discount_percentage = 0;

            if(isset($customer_id)) {
                $orders = $this->model_checkout_order->getPersonalOrders($customer_id);

                // Personal discount
                    $customer_discount = (int)$this->customer->getCustomerDiscount($customer_id);
                    
                    $basePrice = $this->cart->getTotal();
                    $order_discount = $customer_discount/100;

                    $personal_discount = $order_discount * $basePrice;
                    $personal_discount_percentage = $customer_discount;
                // ---

                // Cumulative discount
                    $totalCustomerOutcome = 0;

                    if($orders !== false) {
                        foreach($orders as $order) {
                            if($order['order_status_id'] == 5) {
                                $totalCustomerOutcome += $order['total'];
                            }
                        }
                    }

                    $customer_discount = floor($totalCustomerOutcome/10000);
                    if( $customer_discount > intval($this->config->get('config_max_discount')) ) $customer_discount = intval($this->config->get('config_max_discount'));
                    
                    $order_discount = $customer_discount/100;
                    $basePrice = $this->cart->getTotal();


                    $cumulative_discount = $order_discount * $basePrice;
                    $cumulative_discount_percentage = $customer_discount;
                // ---
            }

            if(!$this->customer->getCouponDiscount()) {
                // ---
                    if( $personal_discount_percentage > $cumulative_discount_percentage ) {
                        $data['discount'] = $personal_discount;
                        $data['discount_percentage'] = $personal_discount_percentage;
                    }
                    else{
                        $data['discount'] = $cumulative_discount;
                        $data['discount_percentage'] = $cumulative_discount_percentage;
                    }
                // ---
            } else {
                // ---
                    $coupon = $this->customer->getCouponDiscount();
                    $couponDiscount = $coupon['discount']/100*$this->cart->getTotal();
                    $couponPercentage = round($coupon['discount']);

                    if( $couponPercentage > $personal_discount_percentage  && $couponPercentage > $cumulative_discount_percentage ) {
                        $data['discount'] = $couponDiscount;
                        $data['discount_percentage'] = $couponPercentage;

                        $data['coupon'] = true;
                    }
                    else{
                        if( $personal_discount_percentage > $cumulative_discount_percentage ) {
                            $data['discount'] = $personal_discount;
                            $data['discount_percentage'] = $personal_discount_percentage;
                        }
                        else{
                            $data['discount'] = $cumulative_discount;
                            $data['discount_percentage'] = $cumulative_discount_percentage;
                        }
                    }
                // ---
            }

            $this->session->data['discount'] = $data['discount'];
            $this->session->data['discount_percentage'] = $data['discount_percentage'];
            $this->session->data['coupon'] = $data['coupon'];
        // ---

        // Get subtotal
          unset($this->session->data['subtotal']);
          unset($this->session->data['total']);

          $cart_products = $this->cart->getProducts();
          
          $totalproducts = 0;
          $totaldiscount = 0;

          // Create temp products array
            $tmp_products = array();

            foreach ($cart_products as $key => $product) {
              // ---
                if( !isset($tmp_products[$product['product_id']])){
                  // ---
                    $tmp_products[$product['product_id']] = array(
                      'name' => $product['name'],
                      'weight_class' => $product['weight_class'],
                      'weight_class_id' => $product['weight_class_id'],
                      'weight_variant' => $product['weight_variant'],
                      'weight_variants' => $product['weight_variants'],
                      'width' => $product['width'],
                      'packing' => array()
                    );

                    $tmp_products[$product['product_id']]['packing'][] = array(
                      'total' => $product['total'],
                      'price' => $product['price'],
                      'quantity' => $product['packaging'],
                      'amount' => $product['quantity']
                    );
                  // ---
                }
                else{
                  // ---
                    $tmp_products[$product['product_id']]['packing'][] = array(
                      'total' => $product['total'],
                      'price' => $product['price'],
                      'quantity' => $product['packaging'],
                      'amount' => $product['quantity']
                    );
                  // ---
                }
              // ---
            }
          // ---

          // Create fixed products array
            $fix_products = array();

            foreach ($tmp_products as $product_id => $product) {
              // ---
                // Calculates
                  $product_total = 0;
                  $product_price = 0;
                  $product_quantity = 0;
                  $product_amount = 0;
                  $product_discount_price = 0;
                  $product_discount_total = 0;
                  
                  foreach ($product['packing'] as $key_pack => $pack) {
                    $product_quantity = $product_quantity + $pack['quantity'];
                    $product_amount = $product_amount + $pack['amount'];
                    $product_total = $product_total + $pack['total'];
                    $product_price = $product_price + $pack['price'];

                  }
                  
                  // Set price
                    if( $product['weight_class_id'] == 1 ){ // Piece
                      $product_price = $product_total / $product_amount;
                    }
                    else{
                      $product_price = $product_total / $product_quantity;
                    }
                  // ---

                  // Set discount
                    if( isset($data['discount_percentage']) ){
                      if( $product['weight_class_id'] == 1 ){ // Piece
                        $product_discount_price = ($product_total / $product_amount) * ($data['discount_percentage']/100);
                        $product_discount_total = $product_discount_price * $product_amount;
                      }
                      else{
                        $product_discount_price = ($product_total / $product_quantity) * ($data['discount_percentage']/100);
                        $product_discount_total = $product_discount_price * $product_quantity;
                      }
                    }
                  // ---
                // ---

                // Total
                  $totalproducts = $totalproducts + $product_total;
                  $totaldiscount = $totaldiscount + $product_discount_total;
                // ---

                $fix_products[] = array(
                  'product_id' => $product_id,
                  'name' => $product['name'],
                  'price' => $product_price,
                  'total' => $product_total,
                  'quantity' => $product_quantity,
                  'amount' => $product_amount,
                  'discount_price' => $product_discount_price,
                  'discount_total' => $product_discount_total,
                  'packing' => $product['packing'],
                  'weight_class' => $product['weight_class'],
                  'weight_class_id' => $product['weight_class_id'],
                  'weight_variant' => $product['weight_variant'],
                  'weight_variants' => $product['weight_variants'],
                  'width' => $product['width'],
                );
              // ---
            }
          // ---

          $response->totaldiscount = round($totaldiscount,2);

          $this->session->data['subtotal'] = round($totalproducts,2);
          $response->subtotal = $this->session->data['subtotal'];
        // ---

        // Calculate delivery
          unset($this->session->data['shipping_price']);
          unset($this->session->data['shipping_code']);
          unset($this->session->data['shipping_address_1']);
          unset($this->session->data['shipping_method']);

          $response->deliveryprice = null;
          $response->method = null;

          // Inside
          if( $response->mkad == 'IN_MKAD' ){
            // ---
              $response->deliveryprice = $response->methods['flat']['cost'];
              $response->method = 'flat';

              if ( isset($response->methods['free']) ){
                // ---
                  if( $this->session->data['subtotal'] >= $response->methods['free']['cost'] ){
                    $response->deliveryprice = 0;
                  }
                // ---
              }
            // ---
          }
          // Outside
          else {
            // ---
              if ( isset($response->methods['free']) ){
                // ---
                  if( $this->session->data['subtotal'] >= $response->methods['free']['cost'] ){
                    // ---
                      if( $response->tobeltway != null ){
                        $response->deliveryprice = (int)$response->methods['mkadout']['milecost'] * (int)$response->tobeltway;
                      }
                      else $response->deliveryprice = (int)$response->methods['mkadout']['cost'];


                      $response->method = 'mkadout';
                    // ---
                  }
                  else{
                    // ---
                      if( $response->tobeltway != null ){
                        $response->deliveryprice = (int)$response->methods['mkadout']['cost'] + (int)$response->methods['mkadout']['milecost'] * (int)$response->tobeltway;
                      }
                      else $response->deliveryprice = (int)$response->methods['mkadout']['cost'];

                      // Fix for regions
                        if( $response->region != 'москва' && $response->region != 'московская' ){
                          $response->deliveryprice = 600;
                        }
                      // ---
                    
                      $response->method = 'mkadout';
                    // ---
                  }
                // ---
              }
              else{
                // ---
                  if( $response->tobeltway != null ){
                    $response->deliveryprice = (int)$response->methods['mkadout']['cost'] + (int)$response->methods['mkadout']['milecost'] * (int)$response->tobeltway;
                  }
                  else $response->deliveryprice = (int)$response->methods['mkadout']['cost'];


                  // Fix for regions
                    if( $response->region != 'москва' && $response->region != 'московская' ){
                      $response->deliveryprice = 600;
                    }
                  // ---

                  $response->method = 'mkadout';
                // ---
              }
            // ---
          }

          // Check
          if( $response->deliveryprice === null ) {
            // ---
              $response->status = 'error';
              $response->message = 'No delivery price';
              echo json_encode($response);
              exit;
            // ---
          }

          $this->load->model('extension/shipping/' . $response->method);
          $quote = $this->{'model_extension_shipping_' . $response->method}->getQuote(array('zone_id' => 0, 'country_id' => 0));

          $this->session->data['shipping_price'] = $response->deliveryprice;
          $this->session->data['shipping_code'] = $response->method;
          $this->session->data['shipping_address_1'] =  $response->address;
          $this->session->data['shipping_method'] = $quote['quote'][$response->method]['title'];

          // Get customer address
            if( isset($customer_id) && isset($response->dadata[0][0]['street']) ){
              $customer_address_by_street = $this->model_account_customer->getCustomerAddressByStreet($customer_id, $response->dadata[0][0]['street']);

              if( $customer_address_by_street != false ){
                $this->session->data['shipping_address_1'] = $customer_address_by_street['address_1'];
                $response->address = $customer_address_by_street['address_1'];
              }
            }
          // ---
        // ---

        // First purchase
          unset($this->session->data['first_purchase']);
          unset($this->session->data['first_purchase_discount']);

          $now = time();

          $first_purchase = intval($this->config->get('config_first_purchase'));
          $first_purchase_discount = intval($this->config->get('config_first_purchase_discount'));
          $first_purchase_discount_percent = intval($this->config->get('config_first_purchase_discount_percent'));
          $first_purchase_free_delivery = intval($this->config->get('config_first_purchase_free_delivery'));

          if ( $this->config->get('config_first_purchase_date_start') != '' && $this->config->get('config_first_purchase_date_end') != '' ) {
            // ---
              $config_full_date_start_arr = explode('T', $this->config->get('config_first_purchase_date_start'));
              $config_date_start_arr = explode('-', $config_full_date_start_arr[0]);
              $config_time_start_arr = explode(':', $config_full_date_start_arr[1]);

              $first_purchase_date_start = mktime(intval($config_time_start_arr[0]), intval($config_time_start_arr[1]), 0, intval($config_date_start_arr[1]), intval($config_date_start_arr[2]), intval($config_date_start_arr[0]));
              
              $config_full_date_end_arr = explode('T', $this->config->get('config_first_purchase_date_end'));
              $config_date_end_arr = explode('-', $config_full_date_end_arr[0]);
              $config_time_end_arr = explode(':', $config_full_date_end_arr[1]);
              $first_purchase_date_end = mktime(intval($config_time_end_arr[0]), intval($config_time_end_arr[1]), 0, intval($config_date_end_arr[1]), intval($config_date_end_arr[2]), intval($config_date_end_arr[0]));
            // ---
          }
          else {
            $first_purchase_date_start = $now-86400;
            $first_purchase_date_end = $now-86400;
          }
          
          if( $first_purchase == 1 && $first_purchase_date_start <= $now && $first_purchase_date_end >= $now ){
            // ---
              // Check first purchase
                $customer = $this->model_account_customer->getCustomerByTelephone($telephone);

                $customer_first_purchase = false;

                if( empty($customer) ) {
                  // ---
                    $customer_first_purchase = true;
                  // ---
                }
                else {
                  // Check orders
                    $customer_id = $customer['customer_id'];

                    $this->load->model('checkout/order');
                    $orders = $this->model_checkout_order->getPersonalOrders($customer_id);
                    
                    if( $orders == false ){ $customer_first_purchase = true; }
                  // ---
                }
              // ---

              if( $customer_first_purchase == true ) {
                $response->first_purchase = true;

                // Calculate purchase discount
                  $total = floatval($this->cart->getTotal());
                  $totalOne = floatval($first_purchase_discount);
                  $totalTwo = round($total * ($first_purchase_discount_percent/100));

                  if( $totalOne >= $totalTwo ){ $response->first_purchase_discount = $totalOne; }
                  else { $response->first_purchase_discount = $totalTwo; }
                // ---

                if( $first_purchase_free_delivery == 1 ){
                  $response->deliveryprice = 0;
                  $this->session->data['shipping_price'] = 0;
                }
              }
              else {
                $response->first_purchase = false;
              }
            // ---
          }


          $this->session->data['first_purchase'] = $first_purchase;
          $this->session->data['first_purchase_discount'] = $first_purchase_discount;
        // ---

        // Get total
          $this->session->data['total'] = round($totalproducts - $totaldiscount,2) + $this->session->data['shipping_price'];
          $response->total = $this->session->data['total'];
        // ---

        // Response
        $response->status = 'success';
        $response->message = 'Цена доставки получена';

        echo json_encode($response);
        exit;
      // ---
    }
    
    // Confirm and go to payment
    public function ajaxConfirmOrder() {
      // ---

        // Init
          $firstname = $this->request->post['firstname'];
          $telephone = preg_replace("/[^0-9,.]/", "", $this->request->post['telephone']);

          $payment_method = $this->request->post['payment_method'];

          $comment = $this->request->post['comment'];

          $strDateTime = 'Дата и время доставки: '.$this->request->post['date'].' '.$this->request->post['time'].PHP_EOL;
          $strDeliveryInterval = $this->request->post['date'].' '.$this->request->post['time'];

          $response = new stdClass();

          $this->load->model('checkout/order');
        // ---
        
        // Create order
          $order_id = (int)$this->ajaxAddOrder(true);
        // ---
        
        // Roistat
          $order_roistat_visit_id = array_key_exists('roistat_visit', $_COOKIE) ? $_COOKIE['roistat_visit'] : "неизвестно";
          $this->model_checkout_order->addRoistatVisitId($order_id, $order_roistat_visit_id);
        // ---

        // Check customer
          $this->load->model('account/customer');
          $customer = $this->model_account_customer->getCustomerByTelephone($telephone);

          if( empty($customer) ) {
            // Create new customer
              $password = $this->model_account_customer->generatePassword();

              $customer_data = array(
                'firstname' => $firstname,
                'lastname' => '',
                'email' => $telephone.'@eco-u.ru',
                'telephone' => $telephone,
                'fax' => '',
                'password' => $password,
                //'address_1' => $this->session->data['shipping_address_1']
              );

              $customer_id = $this->model_account_customer->addCustomer($customer_data);
            // ---

            // SMS alert
              $this->load->model('sms/confirmation');
              $message = str_replace('[REPLACE]', $password, $this->config->get('config_sms_password_new_text'));
              $this->model_sms_confirmation->sendSMS($telephone, $message);
            // ---
          }
          else{
            // ---
              $customer_id = $customer['customer_id'];

              // Add new address
                // $addresses = $this->model_account_customer->getAddresses($customer_id);
                // if( $addresses == false ) {
                //   $this->customer->setAddress(0,$this->session->data['shipping_address_1'],$customer_id);
                // }
              // ---

            // ---
          }
        // ---

        // Set order data
          $data = Array(
              'delivery_time' => $strDateTime,
              'delivery_interval' => $strDeliveryInterval,
              'payment_method' => $payment_method,
              'comment' => $comment,
              //'mkad' => $response->mkad
          );
        // ---

        // Checkout
          $this->model_checkout_order->setPayment($order_id, $this->request->post['payment_code']);

          $payment_method_online = $this->request->post['payment_code'] == 'cod' ? false : true;
          
          $response->subtotal = $this->session->data['subtotal'];
          $response->total = $this->session->data['total'];
          
          if($this->model_checkout_order->setDelivery($order_id, $customer_id, $data, 1)) {
            // ---

              // Set customer
              $this->model_checkout_order->setCustomer($order_id, $customer_id);

              if($payment_method_online) {
                // ---
                  $this->request->post['payment_total'] = $this->session->data['total'];
                  $rbsid = $this->model_checkout_order->generateUniqRbsId($order_id);

                  $results = $this->load->controller('extension/payment/rbs/payment', $rbsid);
                  
                  $this->model_checkout_order->addDetailPayment($order_id, $results['orderId'], $this->config->get('config_payment_status_id'), true, $this->session->data['total']);
                  $response->payment = $results;
                  
                  if( isset($results['redirect']) ) {
                    $response->redirect = $results['redirect'];
                  }
                  else {
                    // ---
                      $response->status = 'error';
                      $response->message = 'Не удалось сформировать ссылку для оплаты';
                      echo json_encode($response);
                      exit;
                    // ---
                  }

                  // Clear cart
                  $this->cart->clear();

                  // Send sms        
                  $this->load->model('sms/confirmation');
                  $message = str_replace('[REPLACE]', $order_id, $this->config->get('config_sms_order_new_text'));
                  $this->model_sms_confirmation->sendSMS($telephone, $message);
                  
                  // Response
                  $response->order_id = $order_id;
                  $response->status = 'success';
                  $response->message = 'Оплатите заказ онлайн';
                  

                  echo json_encode($response);
                  exit;

                // ---
              } else {
                // ---
                  // Add paymant detail
                  $this->model_checkout_order->addDetailPayment($order_id, $order_id.'-'.$this->request->post['payment_code'], $this->config->get('config_order_status_id'), true, $this->session->data['total']);

                  // Clear cart
                  $this->cart->clear();

                  // Send sms        
                  $this->load->model('sms/confirmation');
                  $message = str_replace('[REPLACE]', $order_id, $this->config->get('config_sms_order_new_text'));
                  $this->model_sms_confirmation->sendSMS($telephone, $message);

                  // Response
                  $response->order_id = $order_id;
                  $response->status = 'success';
                  $response->message = 'Заказ успешно создан';

                  echo json_encode($response);
                  exit;

                // ---
              }

            // ---
          } else {
              // ---

                // Clear cart
                $this->cart->clear();

                // Response
                $response->order_id = $order_id;
                $response->status = 'error';
                $response->message = 'Выбранный способ оплаты не доступен';

                echo json_encode($response);
                exit;

              // ---
          }
        // ---

      // ---
    }

    // Post paymnet
    public function rbsPostPayment() {
      // ---
        // Init
          $order_id = $this->request->post['order_id'];
          $action = $this->request->post['action'];

          $response = new stdClass();
        // ---

        // Create payment link
          $this->load->model('checkout/order');
          $rbsid = $this->model_checkout_order->generateUniqRbsId($order_id);

          switch ($action) {
            case 'payment':
              // ---
                // Get total
                  $total = 0;
                  $order = $this->model_checkout_order->getOrder($order_id);

                   $total = $order['total'];

                  $this->request->post['payment_total'] = $total;
                // ---

                $results = $this->load->controller('extension/payment/rbs/payment', $rbsid);

                $this->model_checkout_order->addDetailPayment($order_id, $results['orderId'], $this->config->get('config_payment_status_id'), false, $total);
                $response->payment = $results;
                
                if( isset($results['redirect']) ) {
                  $response->redirect = $results['redirect'];
                }
                else {
                  // ---
                    $response->status = 'error';
                    $response->message = 'Не удалось сформировать ссылку для оплаты';
                    echo json_encode($response);
                    exit;
                  // ---
                }
              // ---
            break;

            case 'surcharge':
              // ---
                // Get total
                  $total = 0;
                  $total_surchage = 0;

                  $order = $this->model_checkout_order->getOrder($order_id);
                  $paymants = $this->model_checkout_order->getOrderPayments($order_id, 20);

                  if($paymants !== false) {
                      foreach($paymants as $paymant) {
                          $total = $total + $paymant['total'];
                      }
                  }

                  if( $total < $order['total'] ){
                    $total_surchage = $order['total'] - $total;
                  }

                  $this->request->post['payment_total'] = $total_surchage;
                // ---

                $results = $this->load->controller('extension/payment/rbs/payment', $rbsid);

                $this->model_checkout_order->addDetailPayment($order_id, $results['orderId'], $this->config->get('config_payment_status_id'), false, $total_surchage);
                $response->payment = $results;
                
                if( isset($results['redirect']) ) {
                  $response->redirect = $results['redirect'];
                }
                else {
                  // ---
                    $response->status = 'error';
                    $response->message = 'Не удалось сформировать ссылку для оплаты';
                    echo json_encode($response);
                    exit;
                  // ---
                }
              // ---
            break;
          }
        // ---

        // Response
        $response->order_id = $order_id;
        $response->status = 'success';
        $response->message = 'Ссылка успешно сформирована';
        

        echo json_encode($response);
        exit;

      // ---
    }
  // ---

  // Catalog
    public function getViewProduct() {
        // Init
          $product_id = $this->request->post['product_id'];
          $response = new stdClass();
        // ---

        // Get product
          $this->load->language('product/product');

          $this->load->model('catalog/category');
          $this->load->model('catalog/product');
                      
          $data['breadcrumbs'] = array();

          $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home')
          );

          $product_info = $this->model_catalog_product->getProduct($product_id);

          if ($product_info) {
            $url = '';

            if (isset($this->request->get['path'])) {
              $url .= '&path=' . $this->request->get['path'];
            }

            if (isset($this->request->get['filter'])) {
              $url .= '&filter=' . $this->request->get['filter'];
            }

            if (isset($this->request->get['manufacturer_id'])) {
              $url .= '&manufacturer_id=' . $this->request->get['manufacturer_id'];
            }

            if (isset($this->request->get['search'])) {
              $url .= '&search=' . $this->request->get['search'];
            }

            if (isset($this->request->get['tag'])) {
              $url .= '&tag=' . $this->request->get['tag'];
            }

            if (isset($this->request->get['description'])) {
              $url .= '&description=' . $this->request->get['description'];
            }

            if (isset($this->request->get['category_id'])) {
              $url .= '&category_id=' . $this->request->get['category_id'];
            }

            if (isset($this->request->get['sub_category'])) {
              $url .= '&sub_category=' . $this->request->get['sub_category'];
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

            $data['breadcrumbs'][] = array(
              'text' => $product_info['name'],
              'href' => $this->url->link('product/product', $url . '&product_id=' . $product_id)
            );

            $this->document->setTitle($product_info['meta_title']);
            $this->document->setDescription($product_info['meta_description']);
            $this->document->setKeywords($product_info['meta_keyword']);
            $this->document->addLink($this->url->link('product/product', 'product_id=' . $product_id), 'canonical');
            $this->document->addScript('catalog/view/javascript/jquery/magnific/jquery.magnific-popup.min.js');
            $this->document->addStyle('catalog/view/javascript/jquery/magnific/magnific-popup.css');
            $this->document->addScript('catalog/view/javascript/jquery/datetimepicker/moment.js');
            $this->document->addScript('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js');
            $this->document->addStyle('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css');

            $data['heading_title'] = $product_info['name'];
                              
            $data['text_select'] = $this->language->get('text_select');
            $data['text_manufacturer'] = $this->language->get('text_manufacturer');
            $data['text_model'] = $this->language->get('text_model');
            $data['text_reward'] = $this->language->get('text_reward');
            $data['text_points'] = $this->language->get('text_points');
            $data['text_stock'] = $this->language->get('text_stock');
            $data['text_discount'] = $this->language->get('text_discount');
            $data['text_tax'] = $this->language->get('text_tax');
            $data['text_option'] = $this->language->get('text_option');
            $data['text_minimum'] = sprintf($this->language->get('text_minimum'), $product_info['minimum']);
            $data['text_write'] = $this->language->get('text_write');
            $data['text_login'] = sprintf($this->language->get('text_login'), $this->url->link('account/login', '', true), $this->url->link('account/register', '', true));
            $data['text_note'] = $this->language->get('text_note');
            $data['text_tags'] = $this->language->get('text_tags');
            $data['text_related'] = $this->language->get('text_related');
            $data['text_payment_recurring'] = $this->language->get('text_payment_recurring');
            $data['text_loading'] = $this->language->get('text_loading');

            $data['entry_qty'] = $this->language->get('entry_qty');
            $data['entry_name'] = $this->language->get('entry_name');
            $data['entry_review'] = $this->language->get('entry_review');
            $data['entry_rating'] = $this->language->get('entry_rating');
            $data['entry_good'] = $this->language->get('entry_good');
            $data['entry_bad'] = $this->language->get('entry_bad');

            $data['button_cart'] = $this->language->get('button_cart');
            $data['button_wishlist'] = $this->language->get('button_wishlist');
            $data['button_compare'] = $this->language->get('button_compare');
            $data['button_upload'] = $this->language->get('button_upload');
            $data['button_continue'] = $this->language->get('button_continue');

            $this->load->model('catalog/review');

            $data['tab_description'] = $this->language->get('tab_description');
            $data['tab_attribute'] = $this->language->get('tab_attribute');
            $data['tab_review'] = sprintf($this->language->get('tab_review'), $product_info['reviews']);

            $data['product_id'] = (int)$product_id;
            $data['manufacturer'] = $product_info['manufacturer'];
            $data['manufacturers'] = $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $product_info['manufacturer_id']);
            $data['model'] = $product_info['model'];
            $data['reward'] = $product_info['reward'];
            $data['points'] = $product_info['points'];

            $descriptionTmp = html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8');
            $descriptionTmp = preg_replace("/[^A-Za-z0-9 ]/", '', strip_tags($descriptionTmp));

            $data['description'] = html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8');
            if( $descriptionTmp == '' ) { $data['description'] = '<noindex><p>'.html_entity_decode($this->config->get('config_description_default')).'</p><noindex>'; }
            
            $data['description_short'] = html_entity_decode($product_info['description_short'], ENT_QUOTES, 'UTF-8');
            $data['props3'] = explode(PHP_EOL, $product_info['customer_props3']);
            $data['weight_variants'] = $product_info['weight_variants'];
            $data['weight_class'] = $product_info['weight_class'];
            $data['composite_price'] = json_encode($product_info['composite_price']);
            $data['quantity'] = $product_info['quantity'];
            $data['stock_status_id'] = $product_info['stock_status_id'];
            $data['available_in_time'] = $product_info['available_in_time'];
            
            if($product_info['special']) {
                if($product_info['price'] != 0) $discount_sticker = ceil(((float)$product_info['price'] - (float)$product_info['special'])/(float)$product_info['price']*100);
                else $discount_sticker = 0;
                $data['discount_sticker'] = $discount_sticker;
                unset($discount_sticker);
            }
            
            $data['sticker_name'] = $product_info['sticker']['name'];
            $data['sticker_class'] = $product_info['sticker']['class'];
            $data['location'] = $product_info['location'];
            $data['shelf_life'] = $product_info['shelf_life'];
                              
            if ($product_info['quantity'] <= 0) {
              $data['stock'] = $product_info['stock_status'];
            } elseif ($this->config->get('config_stock_display')) {
              $data['stock'] = $product_info['quantity'];
            } else {
              $data['stock'] = $this->language->get('text_instock');
            }

            $this->load->model('tool/image');

            if ($product_info['image']) {
              $data['popup'] = '/image/'.$product_info['image'];
                                      //$data['popup'] = $this->model_tool_image->resize($product_info['image'], $this->config->get($this->config->get('config_theme') . '_image_popup_width'), $this->config->get($this->config->get('config_theme') . '_image_popup_height'));
            } else {
              $data['popup'] = '';
            }

            if ($product_info['image']) {
              $data['thumb'] = $this->model_tool_image->resize($product_info['image'], $this->config->get($this->config->get('config_theme') . '_image_thumb_width'), $this->config->get($this->config->get('config_theme') . '_image_thumb_height'));
            } else {
              $data['thumb'] = '';
            }

            $data['images'] = array();

            $results = $this->model_catalog_product->getProductImages($product_id);

            foreach ($results as $result) {
              $data['images'][] = array(
                'popup' => $this->model_tool_image->resize($result['image'], $this->config->get($this->config->get('config_theme') . '_image_popup_width'), $this->config->get($this->config->get('config_theme') . '_image_popup_height')),
                'thumb' => $this->model_tool_image->resize($result['image'], $this->config->get($this->config->get('config_theme') . '_image_additional_width'), $this->config->get($this->config->get('config_theme') . '_image_additional_height'))
              );
            }

            if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
              $data['price'] = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
            } else {
              $data['price'] = false;
            }

            if ($product_info['discount'] > 0) {
              $data['discount'] = $product_info['discount'];
              $data['special'] = $this->currency->format($this->tax->calculate($product_info['special_price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
            } else {
              $data['discount'] = false;
              $data['special'] = false;
            }

            if ($this->config->get('config_tax')) {
              $data['tax'] = $this->currency->format((float)$product_info['special'] ? $product_info['special'] : $product_info['price'], $this->session->data['currency']);
            } else {
              $data['tax'] = false;
            }

            $discounts = $this->model_catalog_product->getProductDiscounts($product_id);

            $data['discounts'] = array();

            foreach ($discounts as $discount) {
              $data['discounts'][] = array(
                'quantity' => $discount['quantity'],
                'price'    => $this->currency->format($this->tax->calculate($discount['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency'])
              );
            }

            $data['options'] = array();

            foreach ($this->model_catalog_product->getProductOptions($product_id) as $option) {
              $product_option_value_data = array();

              foreach ($option['product_option_value'] as $option_value) {
                if (!$option_value['subtract'] || ($option_value['quantity'] > 0)) {
                  if ((($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) && (float)$option_value['price']) {
                    $price = $this->currency->format($this->tax->calculate($option_value['price'], $product_info['tax_class_id'], $this->config->get('config_tax') ? 'P' : false), $this->session->data['currency']);
                  } else {
                    $price = false;
                  }

                  $product_option_value_data[] = array(
                    'product_option_value_id' => $option_value['product_option_value_id'],
                    'option_value_id'         => $option_value['option_value_id'],
                    'name'                    => $option_value['name'],
                    'image'                   => $this->model_tool_image->resize($option_value['image'], 50, 50),
                    'price'                   => $price,
                    'price_prefix'            => $option_value['price_prefix']
                  );
                }
              }

              $data['options'][] = array(
                'product_option_id'    => $option['product_option_id'],
                'product_option_value' => $product_option_value_data,
                'option_id'            => $option['option_id'],
                'name'                 => $option['name'],
                'type'                 => $option['type'],
                'value'                => $option['value'],
                'required'             => $option['required']
              );
            }

            if ($product_info['minimum']) {
              $data['minimum'] = $product_info['minimum'];
            } else {
              $data['minimum'] = 1;
            }

            $data['review_status'] = $this->config->get('config_review_status');

            if ($this->config->get('config_review_guest') || $this->customer->isLogged()) {
              $data['review_guest'] = true;
            } else {
              $data['review_guest'] = false;
            }

            if ($this->customer->isLogged()) {
              $data['customer_name'] = $this->customer->getFirstName() . '&nbsp;' . $this->customer->getLastName();
            } else {
              $data['customer_name'] = '';
            }

            $data['reviews'] = sprintf($this->language->get('text_reviews'), (int)$product_info['reviews']);
            $data['rating'] = (int)$product_info['rating'];

            // Captcha
            if ($this->config->get($this->config->get('config_captcha') . '_status') && in_array('review', (array)$this->config->get('config_captcha_page'))) {
              $data['captcha'] = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha'));
            } else {
              $data['captcha'] = '';
            }

            $data['share'] = $this->url->link('product/product', 'product_id=' . (int)$product_id);

            $data['attribute_groups'] = $this->model_catalog_product->getProductAttributes($product_id);

            $data['products'] = array();

            $results = $this->model_catalog_product->getProductRelated($product_id);

            foreach ($results as $result) {
              if($data['product_id'] == $result['product_id'] || ($result['quantity'] <= 0 && $result['stock_status_id'] == 5)) continue;
              
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
                                     
              if($special) {
                  if($price != 0) $discount_sticker = ceil(((float)$price - (float)$special)/(float)$price*100);
                  else $discount_sticker = 0;
                  $price = $special;
              }
              if(isset($this->request->get['path'])) $category_path = 'path=' . $this->request->get['path'];
              else $category_path = '';

              $arProducts = array(
                'product_id'  => $result['product_id'],
                'status'      => $result['status'],
                'available_in_time' => $result['available_in_time'],
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
                'href'        => $this->url->link('product/product', $category_path . '&product_id=' . $result['product_id'] . $url),
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

              if($result['composite_price'] !== false) {
                      $arProducts['composite_price'] = json_encode($result['composite_price']);
              }

              $data['products'][] = $arProducts;
            }

            $data['tags'] = array();

            if ($product_info['tag']) {
              $tags = explode(',', $product_info['tag']);

              foreach ($tags as $tag) {
                $data['tags'][] = array(
                  'tag'  => trim($tag),
                  'href' => $this->url->link('product/search', 'tag=' . trim($tag))
                );
              }
            }

            $data['recurrings'] = $this->model_catalog_product->getProfiles($product_id);

            $this->model_catalog_product->updateViewed($product_id);
            
            $response->html = $this->load->view('product/product_modal', $data);
          } else {
            $this->response->addHeader("Location: http://{$_SERVER['SERVER_NAME']}");
            $data = Array();
            $this->response->setOutput($this->load->view('error/not_found', $data));
          }
        // ---

        $response->status = 'success';
        $response->message = 'Запрос успешно отправлен.<br>Спасибо!';

        echo json_encode($response);
        exit;
    }
  // ---

  // Widgets
      // Chrome
        public function chormeSetNote() {
          header("Access-Control-Allow-Origin: *");

          // Init
            $customerId = intval($this->request->post['customerId']);
            $noteText = $this->request->post['noteText'];
            $response = new stdClass();
          // ---

          // Send note
            $url = 'https://eco-u.retailcrm.ru/api/v5/customers/notes/create';

            $note = array();
            $note["text"] = $noteText;
            $customer["id"] = $customerId;
            $note["customer"] = $customer;

            $qdata = array('apiKey' => self::RETAILCRM_KEY, 'note' => json_encode($note));

            $res = $this->connectPostAPI($url,$qdata);

            $response->res = $res;
            $response->data = $qdata;
          // ---

          $response->status = 'success';
          $response->message = 'Успешно';

          echo json_encode($response);
          exit;
        }

        public function chormeGetNotes() {
          header("Access-Control-Allow-Origin: *");

          // Init
            $orderid = $this->request->post['id'];
            $response = new stdClass();
          // ---

          // Get CRM order
            $url = 'https://eco-u.retailcrm.ru/api/v5/orders/'.$orderid;
            $qdata = array('apiKey' => self::RETAILCRM_KEY, 'by' => 'id');

            $res = $this->connectGetAPI($url,$qdata);
            $order = $res->order;

            $response->order = $res->order;
            $response->customer = $res->order->customer;

            // Fix primary address
              $customer_address_array = array();
              $customer_address_text = '';

              // ---
                if( isset($response->customer->address->region) ){
                  $customer_address_array['region'] = $response->customer->address->region;
                  $customer_address_text .= $response->customer->address->region . ', '; // Область
                }
                if( isset($response->customer->address->regionId) ){
                  $customer_address_array['regionId'] = $response->customer->address->regionId;
                  //$customer_address_text .= $response->customer->address->regionId; // Идентификатор области в geohelper
                }
                
                if( isset($response->customer->address->city) && isset($response->customer->address->cityType) ){
                  $customer_address_array['city'] = $response->customer->address->city;
                  $customer_address_text .= $response->customer->address->cityType . ' ' . $response->customer->address->city . ', ' ; // Город
                }
                else if( isset($response->customer->address->city) && !isset($response->customer->address->cityType) ){
                  $customer_address_array['city'] = $response->customer->address->city;
                  $customer_address_text .= $response->customer->address->city . ', '; // Город
                }

                if( isset($response->customer->address->cityId) ){
                  $customer_address_array['cityId'] = $response->customer->address->cityId;
                  //$customer_address_text .= $response->customer->address->cityId . ''; // Идентификатор города в geohelper
                }
                if( isset($response->customer->address->street) && isset($response->customer->address->streetType) ){
                  $customer_address_array['street'] = $response->customer->address->street;
                  $customer_address_text .= $response->customer->address->streetType . ' ' . $response->customer->address->street . ', '; // Улица
                }
                if( isset($response->customer->address->streetId) ){
                  $customer_address_array['streetId'] = $response->customer->address->streetId;
                  //$customer_address_text .= '' . $response->customer->address->streetId . ''; // Идентификатор улицы в geohelper
                }
                if( isset($response->customer->address->building) ){
                  $customer_address_array['building'] = $response->customer->address->building;
                  $customer_address_text .= 'д. ' . $response->customer->address->building . ', '; // Номер дома
                }
                if( isset($response->customer->address->flat) ){
                  $customer_address_array['flat'] = $response->customer->address->flat;
                  $customer_address_text .= 'кв./офис ' . $response->customer->address->flat . ', '; // Номер квартиры или офиса
                }
                if( isset($response->customer->address->intercomCode) ){
                  $customer_address_array['intercomCode'] = $response->customer->address->intercomCode;
                  $customer_address_text .= 'код домофона ' . $response->customer->address->intercomCode . ', '; // Код домофона
                }
                if( isset($response->customer->address->floor) ){
                  $customer_address_array['floor'] = $response->customer->address->floor;
                  $customer_address_text .= 'эт. ' . $response->customer->address->floor . ', '; // Этаж
                }
                if( isset($response->customer->address->block) ){
                  $customer_address_array['block'] = $response->customer->address->block;
                  $customer_address_text .= 'под. ' . $response->customer->address->block . ', '; // Подъезд
                }
                if( isset($response->customer->address->house) ){
                  $customer_address_array['house'] = $response->customer->address->house;
                  $customer_address_text .= 'стр./корпус ' . $response->customer->address->house . ', '; // Строение/корпус
                }
                if( isset($response->customer->address->metro) ){
                  $customer_address_array['metro'] = $response->customer->address->metro;
                  $customer_address_text .= 'метро ' . $response->customer->address->metro . ', '; // Метро
                }

                // Fix
                $customer_address_text = mb_substr($customer_address_text,0,mb_strlen($customer_address_text)-2);


                if( isset($response->customer->customFields->customer_delivery_address_type) && $response->customer->customFields->customer_delivery_address_type != false ){
                  $customer_address_array['address_type'] = $response->customer->customFields->customer_delivery_address_type;
                }
              // ---


              if( $customer_address_text != '' ){
                $response->customer->address->text = $customer_address_text;
              }

              if( isset($response->customer->customFields->customer_delivery_address_type) && $response->customer->customFields->customer_delivery_address_type != false ){
                $response->customer->address->text .= '(Доставка в офис)';
              }
            // ---
          // ---

          // Get customer notes
            $customer_id = $response->customer->id;

            $url = 'https://eco-u.retailcrm.ru/api/v5/customers/notes';

            $filter = array();
            $filter['customerIds'] = array();
            $filter['customerIds'][] = $customer_id;
            $qdata = array('apiKey' => self::RETAILCRM_KEY, 'limit' => 100, 'page' => 1, 'filter' => $filter);

            $res_notes = $this->connectGetAPI($url,$qdata);

            $response->notes = $res_notes->notes;
          // ---

          $response->status = 'success';
          $response->message = 'Успешно';

          echo json_encode($response);
          exit;
        }

        public function chormeGetStocks() {
          header("Access-Control-Allow-Origin: *");

          // Init
            $orderid = $this->request->post['id'];
            $orderExternalId = $this->request->post['externalId'];
            $response = new stdClass();
          // ---

          // Get OC order products
            $this->load->model('tool/addon');
            
            $order_products = $this->model_tool_addon->getOrderProducts($orderExternalId);

            $products = array();

            foreach ($order_products as $key => $product) {
              // ---
                if( $product['stock'] <= $product['quantity'] ){
                  $products[] = $product;
                }
              // ---
            }
            $response->products = $products;
          // ---

          $response->status = 'success';
          $response->message = 'Успешно';

          echo json_encode($response);
          exit;
        }

        public function chormeGetDocuments() {
          header("Access-Control-Allow-Origin: *");

          // Init
            $response = new stdClass();
          // ---

          // Get documents
            $documentsConfig = json_decode(file_get_contents(DIR_APPLICATION.'/addons/retailcrm/documents/config.json'));
            

            $response->documents = $documentsConfig;
          // ---

          $response->status = 'success';
          $response->message = 'Успешно';

          echo json_encode($response);
          exit;
        }

        public function chormeSetAddress() {
          header("Access-Control-Allow-Origin: *");

          // Init
            $customerId = intval($this->request->post['customerId']);
            $code = $this->request->post['code'];
            $response = new stdClass();

            $this->load->model('tool/addon');
          // ---

          // Get CRM customer
            $url = 'https://eco-u.retailcrm.ru/api/v5/customers/'.$customerId;
            $qdata = array('apiKey' => self::RETAILCRM_KEY, 'by' => 'id');

            $res = $this->connectGetAPI($url,$qdata);


            if( isset($res->customer) ) {
              // ---
                $customer = $res->customer;

                $customer_info = $this->model_tool_addon->getCustomer($customer->externalId);

                if( $customer_info ){
                  // ---
                    // Get addresses
                      $primary_address = $this->model_tool_addon->getCustomerAddress($customer_info['customer_id'], 'primary');
                      $additional_address = $this->model_tool_addon->getCustomerAddress($customer_info['customer_id'], $code);

                      if( $primary_address != false && $additional_address != false ){
                        // ---
                          $customerData = array();

                          // Set primary address
                            $primary_address_text = '';
                            $primary_address_array = array();

                            $primary_address_text = $primary_address['address_1'];
                            
                            $customerCustomFields[$code] = $primary_address_text;
                            $customerData['customFields'] = $customerCustomFields;
                          // ---

                          // Set additional address
                            $additional_address_text = $additional_address['address_1'];
                            $additional_address_array = array();

                            if( !empty(json_decode($additional_address['address_2'])) && count(json_decode($additional_address['address_2'])) > 1 ){
                              // ---
                                $additional_address_array = (array)json_decode($additional_address['address_2']);
                                $customerData['address'] = $additional_address_array;

                                if( isset($additional_address_array['address_type']) && $additional_address_array['address_type'] == true ){
                                  $customerCustomFields['customer_delivery_address_type'] = true;
                                }
                                else{
                                  $customerCustomFields['customer_delivery_address_type'] = false;
                                }
                              // ---
                            }
                            else{
                              // ---
                                $customerData['address'] = array('text' => $additional_address_text);
                                
                                if( !empty($additional_address['address_2']) && !empty(json_decode($additional_address['address_2'])) ){
                                  $additional_address_array = (array)json_decode($additional_address['address_2']);

                                  if( isset($additional_address_array['address_type']) && $additional_address_array['address_type'] == true ){
                                    $customerCustomFields['customer_delivery_address_type'] = true;
                                  }
                                  else{
                                    $customerCustomFields['customer_delivery_address_type'] = false;
                                  }
                                }
                              // ---
                            }
                          // ---

                          // Edit OC addresses
                            $this->model_tool_addon->editCustomerAddress($primary_address['address_id'], $code);
                            $this->model_tool_addon->editCustomerAddress($additional_address['address_id'], 'primary');
                          // ---

                          // Clear main address
                              $url = 'https://eco-u.retailcrm.ru/api/v5/customers/'.$customerId.'/edit';

                              $qdata = array(
                                'apiKey' => self::RETAILCRM_KEY,
                                'by' => 'id',
                                'customer' => json_encode(array('address' => array()))
                              );

                              $res = $this->connectPostAPI($url, $qdata);
                          // ---
                          
                          // Set addresses
                            $url = 'https://eco-u.retailcrm.ru/api/v5/customers/'.$customerId.'/edit';

                            $qdata = array(
                              'apiKey' => self::RETAILCRM_KEY,
                              'by' => 'id',
                              'customer' => json_encode($customerData)
                            );

                            $res = $this->connectPostAPI($url, $qdata);

                            $response->res = $res;
                            $response->data = $customerData;
                          // ----
                        // ---
                      }
                    // ---
                  // ---
                }
              // ---
            }

          // ---
     
          // ---

          $response->status = 'success';
          $response->message = 'Успешно';

          echo json_encode($response);
          exit;
        }

        public function chormeGetCouriers() {
          // ---
            header("Access-Control-Allow-Origin: *");

            // Init
              $response = new stdClass();
            // ---

            // Get CRM order
              $url = 'https://eco-u.retailcrm.ru/api/v5/orders';
              $qdata = array(
                'apiKey' => self::RETAILCRM_KEY,
                'by' => 'id',
                'limit' => '100',
                'filter' => array(
                  'deliveryDateFrom' => date('Y-m-d', time()),
                  'deliveryDateTo' => date('Y-m-d', time()),
                  'extendedStatus' => array('send-to-delivery')
                )
              );

              $res = $this->connectGetAPI($url,$qdata);

              $response->res = $res;
              $response->orders = $res->orders;
            // ---

            // Create crouriers
              $couriers = array();

              foreach ($response->orders as $key => $order) {
                // ---
                  if( isset($order->delivery->data->courierId) ){
                    // ---

                      if( !isset($couriers[$order->delivery->data->courierId]) ){
                        // ---
                          $couriers[$order->delivery->data->courierId] = array(
                            'firstName' => $order->delivery->data->firstName,
                            'phone' => $order->delivery->data->phone->number,
                            'orders' => array()
                          );
                        // ---
                      }

                      // Add order data
                        $total = 0;

                        // Check payment status
                          if( isset($order->payments) ) {
                            // ---
                              foreach ($order->payments as $key => $payment) {
                                // ---
                                  if( $payment->type == 'cash' && $payment->type == 'paid' ) {
                                    $total = $total + floatval($payment->amount);
                                  }
                                // ---
                              }
                            // ---
                          }
                        // ---

                        $courier_order = array(
                          'number' => $order->number,
                          'total' => round($total, 2),
                          'deliveryNetCost' => round($order->delivery->netCost,2),
                          'payment_type' => $payment_type
                        );

                        $couriers[$order->delivery->data->courierId]['orders'][] = $courier_order;
                      // ---

                    // ---
                  }
                // ---
              }
              
              $response->couriers = $couriers;
            // ---

            $response->status = 'success';
            $response->message = 'Успешно';

            echo json_encode($response);
            exit;
          // ---
        }
      // ---
  // ---

  // Other
    // Call request
    public function sendCallRequest() {
        // Init
          $phone = preg_replace("/[^0-9,.]/", "", $this->request->post['phone']);
          $roistat_visit = $this->request->post['roistat_visit'];
          $response = new stdClass();
        // ---


        // Send to Telphin
          include_once(DIR_APPLICATION . '/model/tool/teleo.php');

          $response->call = call_proccessing('+'.$phone);
        // --
            
        // Save callback
            $this->load->model('tool/addon');
            $coupon = $this->model_tool_addon->callbackAdd($phone, $roistat_visit, $response->call);
        // ---


        $response->status = 'success';
        $response->message = 'Запрос успешно отправлен.<br>Спасибо!';

        echo json_encode($response);
        exit;
    }

    // Curl
      public function connectPostAPI($url, $qdata, $auth='', $cookie='') {

        $data = http_build_query($qdata);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");  
        curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if( !empty($auth) ){
          curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
          curl_setopt($ch, CURLOPT_USERPWD, $auth);
        }
        curl_setopt($ch, CURLOPT_COOKIE, $cookie);
        $headers = ['Content-Type: application/x-www-form-urlencoded'];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HEADER, false);

        // Output
        $output = curl_exec($ch);
        $result = json_decode($output);

        // Result
        if( $result != null ){
          curl_close ($ch);
          return $result;
        }
        else {
          curl_close ($ch);
          return false;
        }

      }

      public function connectGetAPI($url, $qdata, $auth='') {

        $data = http_build_query($qdata);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        if( !empty($auth) ){
          curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
          curl_setopt($ch, CURLOPT_USERPWD, $auth);
        }
        curl_setopt($ch, CURLOPT_URL,$url.'?'.$data);
        curl_setopt($ch, CURLOPT_TIMEOUT, 80);

        // Output
        $output = curl_exec($ch);
        $result = json_decode($output);

        // Result
        if( $result != null ){
          curl_close ($ch);
          return $result;
        }
        else {
          curl_close ($ch);
          return false;
        }

      }
    // ---
  // ---


  // Not proccessed
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
    
    public function ajaxShowMore() {
          $mode = $this->request->post['mode'];
          $target = $this->request->post['target'];
          $parent = $this->request->post['parent'];
          $nInclude = $this->request->post['not_include'];

          $this->load->model('catalog/product');
          $this->load->model('account/user');
          
          if($mode == 'asort') {
              $data['products'] = $this->model_catalog_product->getAsortProducts($target, $nInclude);
          } elseif($mode == 'catsort') {
              $data['products'] = $this->model_catalog_product->getCatsortProducts($target, $nInclude, $parent);
          }

          $this->response->setOutput($this->load->view('product/dynamic', $data));
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
        
          $data['success'] = true;
          $data['html'] = $this->load->controller('common/cart');
        
          $data['products'] = Array();
          $total = 0;
          $totalCount = 0;
          if ($this->cart->hasProducts() || !empty($this->session->data['vouchers'])) {
              $products = $this->cart->getProducts();
              $totalCount = count($products);
              foreach($products as $product) {
                $total += round($product['total']);
              }
          }
          $data['total'] = floor($total);
          $data['count'] = $totalCount;
          
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

    // Оформление доставки
    public function ajaxSetDelivery() {
        $address = $this->request->post['address'];
        $address_new = $this->request->post['address_new'];
        $comment = $this->request->post['comment'];
        $order_id = $this->request->post['order_id'];
        $payment_method = $this->request->post['payment_method'];
        $payment_code = $this->request->post['payment_code'];
        $payment_method_online = $this->request->post['payment_method_online'];
        $strDateTime = 'Дата и время доставки: '.$this->request->post['date'].' '.$this->request->post['time'].PHP_EOL;
        $strDeliveryInterval = $this->request->post['date'].' '.$this->request->post['time'];
        $customer_id = (int)$this->customer->getId();
        $telephone = str_replace(Array('(', ')', '+', ' ', '-'), '', $this->request->post['telephone']);
        
        //    TODO 2018-03-27 Отключил принудительную авторизацию по причине смены корзины
        //    Как результат $this->cart->getTotal() $this->cart->getProducts() и т.д.
        //    возвращали чужую, в основном пустую корзину. В результате итоги (totals)
        //    заказа были нулевые
        $is_guest = false;
        //      if($customer_id == 0) {
        //          $is_guest = true;
        //          $this->customer->loginByPhone($telephone, false, true);
        //          $customer_id = (int)$this->customer->getId();
        //      }
        
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
        if(isset($this->session->data['personal_discount'])) {
          $personalPercentage = (int)$this->session->data['personal_discount'];
          $data['discount_percentage'] = $personalPercentage;
        }
          } else {
              if(isset($this->session->data['personal_discount'])) {
                  $personalDiscount = floor($this->session->data['personal_discount']/100*$this->cart->getTotal());
                  if($this->session->data['personal_discount'] <= 10) $personalPercentage = (int)$this->session->data['personal_discount'];
              } else $personalDiscount = 0;
              $coupon = $this->customer->getCouponDiscount();
              $couponDiscount = floor($coupon['discount']/100*$this->cart->getTotal());
              if($coupon['discount'] <= 100) $couponPercentage = $coupon['discount'];
              if($couponDiscount > $personalDiscount) {
                  $data['coupon_discount'] = $couponDiscount;
                  $data['discount_percentage'] = $couponPercentage;
              } else {
                  $data['discount'] = $personalDiscount;
                  $data['discount_percentage'] = $personalPercentage;
              }
          }
        
        $this->load->model('checkout/order');
        
        $this->model_checkout_order->setPayment($order_id, $payment_code);
        
        if($this->model_checkout_order->setDelivery($order_id, $customer_id, $data, ($payment_method_online ? 16 : 1))) {
            // Добавление адреса доставки в список адресов клиента
            if($address_new == 'true') $this->customer->setAddress(0, $address);
            // Очистка корзины
            // $this->cart->clear();
            if($is_guest) $this->customer->logout();
            // $this->cart->clear();
            if($payment_method_online) {
                $results = $this->load->controller('extension/payment/rbs/payment', $order_id);
                echo json_encode($results);
            } else {
            // Отправка sms        
              $this->load->model('sms/confirmation');
              $message = str_replace('[REPLACE]', $order_id, $this->config->get('config_sms_order_new_text'));
              $this->model_sms_confirmation->sendSMS($telephone, $message);
              echo json_encode(Array('status' => 'success'));
            }
        } else {
            if($is_guest) $this->customer->logout();
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
    
    // Заказ без авторизации
    public function ajaxAddNoAuthOrder() {
        $arRequest = $this->request->post;
        $this->customer->loginByPhone($arRequest['telephone'], false, true);
        $this->ajaxAddOrder();
        $this->customer->logout();
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
    
    public function ajaxGetTotals() {
        echo $this->cart->getTotal();
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
        if(isset($arRequest['newsletter'])) $this->customer->setNewsletter($arRequest['newsletter']);
        $this->response->setOutput(json_encode(Array('status' => 'success', 'dadata' => $toReplace)));
    }
    
    private function clearTelephone($telephone) {
        return str_replace(Array('(', ')', '+', '-', ' '), '', $telephone);
    }
    
    
    public function ajaxSearchProducts() {
      $search = $this->request->get['search'];

      $this->load->model('catalog/product');
      $this->load->model('account/user');

      $data['products'] = $this->model_catalog_product->searchProducts($search);
      
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
    
    public function ajaxRemoveAddress() {
        $address_id = $this->request->get['address_id'];
        $this->load->model('account/customer');
        $this->model_account_customer->deleteAddress($address_id);
        $response = Array(
              'status' => 'success'
        );
        $this->response->setOutput(json_encode($response));
    }
  // ---
}
