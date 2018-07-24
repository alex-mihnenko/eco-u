<?php
class ControllerExtensionPaymentRbs extends Controller {
    /**
     * Инициализация языкового пакета
     * @param $registry
     */
    public function __construct($registry) {
        parent::__construct($registry);
        $this->load->language('extension/payment/rbs');
        $this->have_template = true;
    }

    /**
     * Рендеринг кнопки-ссылки для перехода в метод payment()
     * @return mixed Шаблон кнопки
     */
    public function index() {
        $data['action'] = $this->url->link('extension/payment/rbs/payment');
        $data['button_confirm'] = $this->language->get('button_confirm');
        return $this->get_template('extension/payment/rbs', $data);
    }

    /**
     * Регистрация заказа.
     * Переадресация покупателя при успешной регистрации.
     * Вывод ошибки при неуспешной регистрации.
     */
    public function payment($order_id = 0) {
        if (!$order_id && $this->request->server['REQUEST_METHOD'] != 'POST') {
            return;
        }

        $order_number = $order_id ? $order_id : $this->session->data['order_id'];
        
        if( strpos('-', $order_number) !== false ){
            $order_id_tmp = explode('-', $order_number);
            $order_id = intval($order_id_tmp[0]);
        }

        $this->load->model('checkout/order');
        $order_info = $this->model_checkout_order->getOrder($order_id ? $order_id : $this->session->data['order_id']);
        $amount = $order_info['total'] * 100;
        $return_url = $this->url->link('extension/payment/rbs/callback');

        $this->initializeRbs();
        $response = $this->rbs->register_order($order_number, $amount, $return_url);
        if($order_id) {
            $this->session->data['payment_order_id'] = $order_id;
            if (isset($response['errorCode'])) {
                return array('error' => $response['errorMessage']);
            } else {
                return array('redirect' => $response['formUrl']);
            }
        }
        if (isset($response['errorCode'])) {
            $this->document->setTitle($this->language->get('error_title'));

            $data['header'] = $this->load->controller('common/header');
            $data['column_left'] = $this->load->controller('common/column_left');
            $data['column_right'] = $this->load->controller('common/column_right');
            $data['content_top'] = $this->load->controller('common/content_top');
            $data['button_continue'] = $this->language->get('error_continue');

            $data['heading_title'] = $this->language->get('error_title') . ' #' . $response['errorCode'];
            $data['text_error'] = $response['errorMessage'];
            $data['continue'] = $this->url->link('checkout/cart');

            $data['content_bottom'] = $this->load->controller('common/content_bottom');
            $data['footer'] = $this->load->controller('common/footer');

            $this->response->setOutput($this->get_template('error/rbs', $data));
        } else {
            $this->response->redirect($response['formUrl']);
        }
    }

    /**
     * Колбек для возвращения покупателя из ПШ в магазин.
     * Пример http://eco-u.ru/index.php?route=extension/payment/rbs/callback&orderId=31be534e-bbec-7e5e-31be-534e0014f8d6&lang=ru
     */
    public function callback() {
        if (isset($this->request->get['orderId'])) {
            $orderId= $this->request->get['orderId'];
        } else {
            die('Illegal Access');
        }

        $order_number = 0;
        
        if(isset($this->session->data['payment_order_id'])) {
            //$order_number = $this->session->data['payment_order_id'];
            $this->load->model('checkout/order');
            $order_number = $this->model_checkout_order->getOrderIdByUniqRbsId($this->session->data['payment_order_id']);
        }

        $order_info = $this->model_checkout_order->getOrder($order_number);
        if ($order_info) {
            $this->initializeRbs();
            
            $response = $this->rbs->get_order_status($orderId);
            if(($response['errorCode'] == 0) && (($response['orderStatus'] == 1) || ($response['orderStatus'] == 2))) {
                $this->model_checkout_order->addOrderHistory($order_number, $this->config->get('config_paid_status_id'));
                $this->model_checkout_order->addDetailPayment($order_number, $this->config->get('config_paid_status_id'));

                $this->session->data['success_order_id'] = $order_number;
                $this->response->redirect($this->url->link('common/home', 'payment=rbs-success&order_id='.$order_number, true));
            } else {
                $this->model_checkout_order->addDetailPayment($order_number, $this->config->get('config_nopaid_status_id'));
                $this->response->redirect($this->url->link('checkout/failure', '', true));
            }
        }
    }

    /**
     * Инициализация библиотеки RBS
     */
    private function initializeRbs() {
        $this->library('rbs');
        $this->rbs = new RBS();
        $this->rbs->login = $this->config->get('rbs_merchantLogin');
        $this->rbs->password = $this->config->get('rbs_merchantPassword');
        $this->rbs->stage = $this->config->get('rbs_stage');
        $this->rbs->mode = $this->config->get('rbs_mode');
        $this->rbs->logging = $this->config->get('rbs_logging');
        $this->rbs->currency = $this->config->get('rbs_currency');
    }

    /**
     * В версии 2.1 нет метода Loader::library()
     * Своя реализация
     * @param $library
     */
    private function library($library) {
        $file = DIR_SYSTEM . 'library/' . str_replace('../', '', (string)$library) . '.php';

        if (file_exists($file)) {
            include_once($file);
        } else {
            trigger_error('Error: Could not load library ' . $file . '!');
            exit();
        }
    }

    /**
     * Отрисовка шаблона
     * @param $template     Шаблон вместе с корневой папкой
     * @param $data         Данные
     * @return mixed        Отрисованный шаблон
     */
    private function get_template($template, $data) {
        
        return $this->load->view($template, $data);
        
    }
}