<?php
class ControllerExtensionModuleExchange1c extends Controller {
	private $error = array();
	private $module_name = 'Exchange 1C 8.x';


	/**
	 * Пишет информацию в файл журнала
	 *
	 * @param	int				Уровень сообщения
	 * @param	string,object	Сообщение или объект
	 */
	private function log($message, $level=1) {
		if ($this->config->get('exchange1c_log_level') >= $level) {

			if ($level == 1) {
				$this->log->write(print_r($message,true));

			} elseif ($level == 2) {
				$memory_usage = sprintf("%.3f", memory_get_usage() / 1024 / 1024);
				list ($di) = debug_backtrace();
				$line = sprintf("%04s",$di["line"]);

				if (is_array($message) || is_object($message)) {
					$this->log->write($memory_usage . " Mb | " . $line);
					$this->log->write(print_r($message, true));
				} else {
					$this->log->write($memory_usage . " Mb | " . $line . " | " . $message);
				}
			}
		}
	} // log()


	/**
	 * Выводит сообщение
	 */
	private function echo_message($ok, $message="") {
		if ($ok) {
			echo "success\n";
			$this->log("[ECHO] success",2);
			if ($message) {
				echo $message;
				$this->log("[ECHO] " . $message,2);
			}
		} else {
			echo "failure\n";
			$this->log("[ECHO] failure",2);
			if ($message) {
				echo $message;
				$this->log("[ECHO] " . $message,2);
			}
		};
	} // echo_message()


	/**
	 * Сохраняет настройки сразу в базу данных
	 */
	private function configSet($key, $value, $store_id=0) {
		if (!$this->config->has('exchange1c_'.$key)) {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "setting` SET `value` = '" . $value . "', `store_id` = " . $store_id . ", `code` = 'exchange1c', `key` = '" . $key . "'");
		}
	}

	/**
	 * Определяет значение переменной ошибки
	 */
	private function setParamError(&$data, $param) {
		if (isset($this->request->post[$param])) {
			$data['error_'.$param] = $this->request->post[$param];
		} else {
			$data['error_'.$param] = '';
		}
	} // setParamsError()


	/**
	 * Определяет значение переменной
	 */
	private function getParam($param, $default='') {
		if (isset($this->request->post['exchange1c_'.$param])) {
			return $this->request->post['exchange1c_'.$param];
		} else {
			if ($this->config->get('exchange1c_'.$param)) {
				return $this->config->get('exchange1c_'.$param);
			} else {
				return $default;
			}
		}
	} // getParam()


	/**
	 * Выводит форму текстового многострочного поля
	 */
	private function htmlTextarea($name, $param) {
		$value = $this->getParam($name);
		if (!$value && isset($param['default'])) $value = $param['default'];
		$tmpl = '<textarea class="form-control" id="exchange1c_'.$name.'" name="exchange1c_'.$name.'" rows="6">'.$value.'</textarea>';
		return $tmpl;
	} // htmlTextarea()


	/**
	 * Выводит форму выбора значений
	 */
	private function htmlSelect($name, $param) {
		$value = $this->getParam($name);
        if (!$value && isset($param['default'])) $value = $param['default'];
		$disabled = isset($param['disabled']) ? ' disabled="true"' : '';
		$tmpl = '<select name="exchange1c_'.$name.'" id="exchange1c_'.$name.'" class="form-control"'.$disabled.'>';
		foreach ($param['options'] as $option => $text) {
			$selected = ($option == $value ? ' selected="selected"' : '');
			$tmpl .= '<option value="'.$option.'"'.$selected.'>'.$text.'</option>';
		}
		$tmpl .= '</select>';
		return $tmpl;
	} // htmlSelect()


	/**
	 * ver 1
	 * update 2017-06-09
	 * Выводит форму выбора несколько значений checkbox
	 */
	private function htmlCheckbox($name, $param) {
		if (!isset($this->request->post['exchange1c_'.$name])) {
			$value = '';
		} else {
			$value = $this->getParam($name);
		}
		$tmpl = '<div name="exchange1c_'.$name.'" id="exchange1c_'.$name.'" class="well well-sm" style="height:150px; overflow:auto;">';
		foreach ($param['options'] as $option => $text) {
			$checked = '';
			if (is_array($value)) {
				$checked = (array_search($option, $value) !== false ? ' checked="checked"' : '');
			}
			$tmpl .= '<div class="checkbox"><label><input type="checkbox" name="exchange1c_'.$name.'[]" value="'.$option.'"'.$checked.'>'.$text.'</label></div>';
		}
		$tmpl .= '</div>';
		return $tmpl;
	} // htmlCheckbox()


	/**
	 * Выводит форму переключателя "Да"+"Нет" или "Вкл"+"Откл"
	 */
	private function htmlRadio($name, $param) {
		$value = $this->getParam($name);
		//$this->log($name . ' = ' . $value);
		if (!$value && isset($param['default'])) $value = $param['default'];
		$disabled = isset($param['disabled']) ? ' disabled="true"' : '';
		if (isset($param['text'])) {
			if ($param['text'] == 'on_off') {
				$text1 = 'text_on';
				$text0 = 'text_off';
			} else {
				$text1 = 'text_yes';
				$text0 = 'text_no';
			}
		} else {
			$text1 = 'text_yes';
			$text0 = 'text_no';
		}
		$id = isset($param['id']) ? ' id="'.$param['id'].'"' : '';
		$tmpl = '<label class="radio-inline">';
		$tmpl .= '<input type="radio" name="exchange1c_'.$name.'" value="1"'.($value == 1 ? ' checked = "checked"' : '').$disabled.'>';
		$tmpl .= '&nbsp;'.$this->language->get($text1);
		$tmpl .= '</label>';
		$tmpl .= '<label class="radio-inline">';
		$tmpl .= '<input type="radio" name="exchange1c_'.$name.'" value="-1"'.($value == -1 ? ' checked = "checked"' : '').$disabled.'>';
		$tmpl .= '&nbsp;'.$this->language->get($text0);
		$tmpl .= '</label>';
		return $tmpl;
	} // htmlRadio()


	/**
	 * Формирует форму кнопки
	 */
	private function htmlButton($name) {
		$tmpl = '<button id="exchange1c-button-'.$name.'" class="btn btn-primary" type="button" data-loading-text="' . $this->language->get('entry_button_'.$name). '">';
		$tmpl .= '<i class="fa fa-trash-o fa-lg"></i> ' . $this->language->get('text_button_'.$name) . '</button>';
		return $tmpl;
	} // htmlButton()


	/**
	 * Формирует форму картинки
	 */
	private function htmlImage($name, $param) {
		$tmpl = '<a title="" class="img_thumbnail" id="thumb-image0" aria-describedby="popover" href="" data-original-title="" data-toggle="image">';
		$tmpl .= '<img src="' . $param['thumb'] . '" data-placeholder="' . $param['ph'] . '" alt="" />';
		$tmpl .= '<input name="exchange1c_' . $name . '" id="input_image0" value="' . $param['value'] . '" type="hidden" /></a>';
		return $tmpl;
	} // htmlImage()


	/**
	 * Формирует форму поля ввода
	 */
	private function htmlInput($name, $param, $type='text') {
		$value = $this->getParam($name);
		if (empty($value) && !empty($param['default'])) $value = $param['default'];
		$disabled = isset($param['disabled']) ? ' disabled="true"' : '';
		if ($this->language->get('ph_'.$name) != 'ph_'.$name) {
			$placeholder = ' placeholder="' . $this->language->get('ph_'.$name) . '"';
		} else {
			$placeholder = '';
		}
		$tmpl = '<input class="form-control"' . $placeholder . ' type="'.$type.'" id="exchange1c_'.$name.'" name="exchange1c_'.$name.'" value="'.$value.'"'.$disabled.'>';
		return $tmpl;
	} // htmlInput()


	/**
	 * Формирует форму ...
	 */
	private function htmlParam($name, $text, $param, $head=false) {
		$tmpl = '';
		if ($head) {
			$tmpl .= '<div class="panel-heading"><h3 class="panel-title"><i class="fa fa-pencil"></i>' . $this->language->get('legend_'.$name) . '</h3></div>';
		}
		//var_dump('<PRE>');var_dump($name);var_dump($param);var_dump('</PRE>');
		$label_width = isset($param['width'][0]) ? $param['width'][0] : 2;
		$entry_width = isset($param['width'][1]) ? $param['width'][1] : 2;
		$desc_width = isset($param['width'][2]) ? $param['width'][2] : 8;
		if ($label_width) {
			$tmpl .= '<label class="col-sm-'.$label_width.' control-label">'. $this->language->get('entry_'.$name) . '</label>';
		}
		$tmpl .= '<div class="col-sm-'.$entry_width.'">' . $text . '</div>';
		if ($desc_width) {
			$tmpl .= '<div class="col-sm-'.$desc_width.'"><div class="alert alert-info"><i class="fa fa-info-circle"></i>&nbsp;'. nl2br(htmlspecialchars($this->language->get('desc_'.$name))) . '</div></div>';
		}

		return $tmpl;
	} // HtmlParam()


	/**
	 * ver 2
	 * update 2017-05-02
	 * Проверка разрешения на изменение
	 */
	private function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/exchange1c'))
			$this->error['warning'] = $this->language->get('error_permission');
		if (!$this->error) {
			return true;
		}
		return false;

	} // validate()


	/**
	 * ver 2
	 * update 2017-05-04
	 * Основная функция
	 */
	public function refresh() {
		$this->index(true);
	}


	/**
	 * ver 4
	 * update 2017-05-27
	 * Основная функция
	 */
	public function index($refresh = false) {

		$data['lang'] = $this->load->language('extension/module/exchange1c');
		//var_dump("<pre>"); var_dump($data['lang']); var_dump("</pre>");

		$this->load->model('tool/image');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		$data['text_info'] = "";
		$this->load->model('extension/exchange1c');
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			//var_dump($this->request->post['exchange1c_product_property_type']);
			// При нажатии кнопки сохранить
			$settings = $this->request->post;
			$settings['exchange1c_version'] = $this->config->get('exchange1c_version');
			$settings['exchange1c_order_date'] = $this->config->get('exchange1c_order_date');
			$settings['exchange1c_table_fields'] = $this->config->get('exchange1c_table_fields');
			$settings['exchange1c_CMS_version'] = VERSION;

			$this->model_setting_setting->editSetting('exchange1c', $settings);
			$this->session->data['success'] = $this->language->get('text_success');
			if (!$refresh) {
				$this->response->redirect($this->url->link('extension/extension', 'token=' . $this->session->data['token'], 'SSL'));
			}
			$data['text_info'] = "Настройки сохранены";
		} else {
			$settings = $this->model_setting_setting->getSetting('exchange1c', 0);

			if (!isset($settings['exchange1c_version'])) {
				// Чистая установка
				$this->install();
				$this->load->model('extension/extension');
				$this->model_extension_extension->install('module', 'exchange1c');
				$data['text_info'] = "Модуль установлен";
			}
			$result = $this->model_extension_exchange1c->checkUpdates($settings);
			$data['text_info'] = $result['success'];
			$data['error_update'] = $result['error'];
		}

		$settings = $this->model_setting_setting->getSetting('exchange1c', 0);
		$data['version'] = $settings['exchange1c_version'];

		$data['exchange1c_config_icon'] = $this->getParam('config_icon');

		// Формирование $data['error_warning']
		$this->setParamError($data, 'warning');

		// Проверка базы данных
		$data['error_warning'] .= $this->model_extension_exchange1c->checkDB();

		// Выводим сообьщение при ошибке обновления
		if (isset($data['error_update'])) {
			$data['error_warning'] .= $data['error_update'];
		}

		$this->setParamError($data, 'image');
		$this->setParamError($data, 'exchange1c_username');
		$this->setParamError($data, 'exchange1c_password');

		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
			'text'		=> $this->language->get('text_home'),
			'href'		=> $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
			'separator'	=> false
		);
		$data['breadcrumbs'][] = array(
			'text'		=> $this->language->get('text_module'),
			'href'		=> $this->url->link('extension/extension', 'token=' . $this->session->data['token'], 'SSL'),
			'separator'	=> ' :: '
		);
		$data['breadcrumbs'][] = array(
			'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('extension/module/exchange1c', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => ' :: '
		);
		$data['token'] = $this->session->data['token'];
		$data['refresh'] = $this->url->link('extension/module/exchange1c/refresh', 'token=' . $this->session->data['token'], 'SSL');
		$data['action'] = $this->url->link('extension/module/exchange1c', 'token=' . $this->session->data['token'], 'SSL');
		$data['cancel'] = $this->url->link('extension/extension', 'token=' . $this->session->data['token'], 'SSL');

		/**
		 * ГЕНЕРАЦИЯ ШАБЛОНА
		 */

		// Магазины
		if (isset($this->request->post['exchange1c_stores'])) {
			$data['exchange1c_stores'] = $this->request->post['exchange1c_stores'];
		}
		else {
			$data['exchange1c_stores'] = $this->config->get('exchange1c_stores');
			if(empty($data['exchange1c_stores'])) {
				$data['exchange1c_stores'][] = array(
					'store_id'	=> 0,
					'name'		=> ''
				);
			}
		}

		// Таблица настроек базы данных

		// свойства из торговой системы
		$data['exchange1c_properties'] 		= $this->getParam('properties', array());
		// таблица настройки загрузки заказов
		$data['exchange1c_order_import'] 	= $this->getParam('order_import', array());
		// таблица настройки выгрузки заказов
		$data['exchange1c_order_export'] 	= $this->getParam('order_export', array());
		// таблица настройки видов доставки
		$data['exchange1c_order_delivery'] 	= $this->getParam('order_delivery', array());
		// Виды доставки
		$data['order_types_of_delivery'] = array(
			0 => 'в разработке0',
			1 => 'в разработке1',
			2 => 'в разработке2'
		);

		//$data['config_stores'] = $this->getParam('stores', array());
		//var_dump('<PRE>');var_dump($data['config_stores']);var_dump('</PRE>');
		$stores = $this->db->query("SELECT * FROM `" . DB_PREFIX . "store`")->rows;
		$data['stores'] = array();
		$data['stores'][0] = $this->config->get('config_name');
		foreach ($stores as $store) {
			$data['stores'][$store['store_id']] = $store['name'];
		}

		// Поля товара для записи
		$data['product_fields'] = array(
			''			=> $this->language->get('text_not_import')
			,'sku'		=> $this->language->get('text_sku')
			,'ean'		=> $this->language->get('text_ean')
			,'mpn'		=> $this->language->get('text_mpn')
			,'model'	=> $this->language->get('text_model')
			,'weight'	=> $this->language->get('text_weight')
			,'width'	=> $this->language->get('text_width')
			,'height'	=> $this->language->get('text_height')
			,'length'	=> $this->language->get('text_length')
		);

		// Картинки
		$images = array(
			'watermark'	=> array(
				'ph'			=> $this->model_tool_image->resize('no_image.png', 100, 100),
				'value'			=> $this->getParam('watermark'),
				'thumb'			=> $this->getParam('watermark') ? $this->model_tool_image->resize($this->getParam('watermark'), 100, 100) : $this->model_tool_image->resize('no_image.png', 100, 100)
			)
		);

		// Уровень записи в журнал
		$log_level_list = array(
			0	=> $this->language->get('text_log_level_0'),
			1	=> $this->language->get('text_log_level_1'),
			2	=> $this->language->get('text_log_level_2'),
			3	=> $this->language->get('text_log_level_3')
		);

		$file_exchange_list = array(
			'zip'	=> $this->language->get('text_file_exchange_zip'),
			'files'	=> $this->language->get('text_file_exchange_files')
		);

		// SEO товары
		if (isset($this->request->post['exchange1c_seo_product_tags'])) {
			$data['exchange1c_seo_product_tags'] = $this->request->post['exchange1c_seo_product_tags'];
		} else {
			$data['exchange1c_seo_product_tags'] = '{name}, {fullname}, {sku}, {brand}, {model}, {cats}, {prod_id}, {cat_id}';
		}
        // SEO категории
		if (isset($this->request->post['exchange1c_seo_category_tags'])) {
			$data['exchange1c_seo_category_tags'] = $this->request->post['exchange1c_seo_category_tags'];
		} else {
			$data['exchange1c_seo_category_tags'] = '{cat}, {cat_id}';
		}
		// SEO производители
		if (isset($this->request->post['exchange1c_seo_manufacturertags'])) {
			$data['exchange1c_seo_manufacturer_tags'] = $this->request->post['exchange1c_seo_manufacturer_tags'];
		} else {
			$data['exchange1c_seo_manufacturer_tags'] = '{brand}, {brand_id}';
		}
		$list_product = array(
			'disable'		=> $this->language->get('text_disable'),
			'template'		=> $this->language->get('text_template')
			//'import'		=> $this->language->get('text_import')
		);
		$list_category = array(
			'disable'		=> $this->language->get('text_disable'),
			'template'		=> $this->language->get('text_template')
		);
		$list_seo_mode = array(
			'disable'		=> $this->language->get('text_disable')
		);
		if ($this->config->get('config_seo_url') == 1) {
			$list_seo_mode['if_empty'] 	= $this->language->get('text_if_empty');
			$list_seo_mode['overwrite']	= $this->language->get('text_overwrite');
		}

		// Статус товара по умолчанию при отсутствии
		$this->load->model('localisation/stock_status');
		$stock_statuses_info = $this->model_localisation_stock_status->getStockStatuses();
		$select_stock_statuses = array();
		$select_stock_statuses[] = $this->language->get('text_not_import');
		foreach ($stock_statuses_info as $status) {
			$select_stock_statuses[$status['stock_status_id']] = $status['name'];
		}

		// список статусов заказов
		$this->load->model('localisation/order_status');
		$order_statuses_info = $this->model_localisation_order_status->getOrderStatuses();
		$order_statuses = array();
		$order_statuses[] = $this->language->get('text_do_not_use');
		foreach ($order_statuses_info as $order_status) {
			$order_statuses[$order_status['order_status_id']] = $order_status['name'];
		}
		$data['order_statuses'] = $order_statuses;

		$data['order_event'] = array(
			'posted'			=> $this->language->get('text_order_posted'),
			'paid'				=> $this->language->get('text_paid'),
			'paid_shipped'		=> $this->language->get('text_paid_shipped'),
			'shipped'			=> $this->language->get('text_shipped'),
			'expired_payment'	=> $this->language->get('text_expired_payment')
		);

		// Дата и время выгрузки заказов
		if (isset($this->request->post['exchange1c_order_date'])) {
			$data['order_date_export'] = $this->request->post['exchange1c_order_date'];
		} else {
			if ($this->config->get('exchange1c_order_date')) {
				$data['order_date_export'] = strftime('%Y-%m-%dT%H:%M', strtotime($this->config->get('exchange1c_order_date')));
			} else {
				$data['order_date_export'] = strftime('%Y-%m-%dT%H:%M', strtotime('2000-01-01 00:00:00'));
			}
		}

		$list_options = array(
			'feature'	=> $this->language->get('text_product_options_feature')
			,'related'	=> $this->language->get('text_product_options_related')
			//,'certine'	=> $this->language->get('text_product_options_certine')
		);

		$list_options_type = array(
			'select'	=> $this->language->get('text_product_options_type_select'),
			'radio'		=> $this->language->get('text_product_options_type_radio')
		);

		$select_import_product = array(
			'disable'	=> $this->language->get('text_not_import'),
			'name'		=> $this->language->get('text_name'),
			'fullname'	=> $this->language->get('text_fullname'),
			'manually'	=> $this->language->get('text_field_manually')
		);

		$select_import_categories_mode = array(
			'groups'	=> $this->language->get('text_groups'),
			'categories'=> $this->language->get('text_categories'),
		);

		$select_sync_new_poroduct = array(
			'guid'  	=> $this->language->get('text_guid'),
			'sku'    	=> $this->language->get('text_sku'),
			'name'		=> $this->language->get('text_name'),
			'ean'		=> $this->language->get('text_ean')
		);

		$select_product_categories = array(
			'disable'  	=> $this->language->get('text_not_import'),
			'overwrite'	=> $this->language->get('text_overwrite'),
			'add'		=> $this->language->get('text_add'),
		);

		$select_sync_attributes = array(
			'guid'    	=> $this->language->get('text_guid'),
			'name'		=> $this->language->get('text_name'),
		);

		$list_price_import_to = array(
			'discount'    	=> $this->language->get('text_discount'),
			'special'		=> $this->language->get('text_special'),
		);

		$list_order_date_ship = array(
			'order'    		=> $this->language->get('text_date_order'),
			'exchange'		=> $this->language->get('text_date_exchange'),
			'disable'		=> $this->language->get('text_disable')
		);

		// Список
		$list_table_prices = array();
		$list_table_prices[] = array(
			'name'    		=> 'product',
			'desc'			=> $this->language->get('text_table_product')
		);
		$list_table_prices[] = array(
			'name'    		=> 'discount',
			'desc'			=> $this->language->get('text_table_discount')
		);
		$list_table_prices[] = array(
			'name'    		=> 'special',
			'desc'			=> $this->language->get('text_table_special')
		);

		// Очистка остатков
		$list_flush_quantity = array(
			'none'    		=> $this->language->get('text_disable'),
			'all'			=> $this->language->get('text_flush_quantity_all'),
			'category'		=> $this->language->get('text_flush_quantity_category')
		);
		$data['table_prices'] = $list_table_prices;

		$select_product_images_import_mode = array(
			'disable'  	=> $this->language->get('text_not_import'),
			'full'		=> $this->language->get('text_full'),
			'update'	=> $this->language->get('text_update'),
		);

		// Режим загрузки цен в товар
		$select_price_import_mode = array(
			'disable'   	=> $this->language->get('text_not_import'),
			'overwrite'		=> $this->language->get('text_overwrite'),
			'update'		=> $this->language->get('text_update')
		);

		// Список типов опций в товаре
		$select_product_options_type = array(
			'select'    	=> $this->language->get('text_html_select'),
			'radio'			=> $this->language->get('text_html_radio'),
			'chekbox'		=> $this->language->get('text_html_checkbox')
		);

		// Список типов загружаемых типов свойств в товаре
		$select_product_property_type = array(
			'text'    		=> $this->language->get('text_text'),
			'referefce'		=> $this->language->get('text_reference')
		);

		// Генерация опций
		$params = array(
			'cleaning_db' 								=> array('type' => 'button')
			,'cleaning_links' 							=> array('type' => 'button')
			,'cleaning_cache' 							=> array('type' => 'button')
			,'cleaning_old_images' 						=> array('type' => 'button')
			,'generate_seo' 							=> array('type' => 'button')
			,'flush_quantity'							=> array('type' => 'select', 'options' => $list_flush_quantity)
			,'watermark'								=> array('type' => 'image')
			,'allow_ip'									=> array('type' => 'textarea')
			,'status_new_product'						=> array('type' => 'radio', 'default' => 1, 'text' => 'on_off')
			,'import_product_description'				=> array('type' => 'radio', 'default' => 1)
			,'import_categories'						=> array('type' => 'radio', 'default' => 1)
			,'import_categories_mode'					=> array('type' => 'select', 'options' => $select_import_categories_mode, 'default' => 'groups')
			,'product_categories'						=> array('type' => 'select', 'options' => $select_product_categories)
			,'import_product_name'						=> array('type' => 'select', 'options' => $select_import_product, 'default' => 'name')
			,'import_product_name_field'				=> array('type' => 'input', 'default' => 'Наименование')
			,'import_product_manufacturer'				=> array('type' => 'radio', 'default' => 1)
			//,'status_new_product'						=> array('type' => 'radio', 'default' => 1, 'text' => 'on_off')
			,'status_new_category'						=> array('type' => 'radio', 'default' => 1, 'text' => 'on_off')
			,'description_html'							=> array('type' => 'radio', 'default' => 1)
			,'fill_parent_cats'							=> array('type' => 'radio', 'default' => 1)
			,'product_disable_if_quantity_zero'			=> array('type' => 'radio', 'default' => -1)
			,'product_disable_if_price_zero'			=> array('type' => 'radio', 'default' => -1)
			,'create_new_product'						=> array('type' => 'radio', 'default' => 1)
			,'create_new_category'						=> array('type' => 'radio', 'default' => 1)
			,'synchronize_by_code'						=> array('type' => 'radio', 'default' => -1)
			,'synchronize_new_product_by'        		=> array('type' => 'select', 'options' => $select_sync_new_poroduct, 'default' => 'sku')
			,'synchronize_attribute_by' 	     	 	=> array('type' => 'select', 'options' => $select_sync_attributes, 'default' => 'guid')
			,'module_status'							=> array('type' => 'radio', 'default' => 1, 'text' => 'on_off')
			,'flush_log'								=> array('type' => 'radio', 'default' => 1)
			,'currency_convert'							=> array('type' => 'radio', 'default' => 1)
			,'convert_orders_cp1251'					=> array('type' => 'radio', 'default' => 1)
			,'parse_only_types_item'					=> array('type' => 'input')
			,'username'									=> array('type' => 'input',)
			,'password'									=> array('type' => 'input',)
			,'seo_product_seo_url_import'				=> array('type' => 'input', 'width' => array(0,9,0), 'hidden' => 1)
			,'seo_product_seo_url_template'				=> array('type' => 'input', 'width' => array(0,9,0))
			,'seo_product_meta_title_import'			=> array('type' => 'input', 'width' => array(0,9,0), 'hidden' => 1)
			,'seo_product_meta_title_template'			=> array('type' => 'input', 'width' => array(0,9,0))
			,'seo_product_meta_description_import'		=> array('type' => 'input', 'width' => array(0,9,0), 'hidden' => 1)
			,'seo_product_meta_description_template'	=> array('type' => 'input', 'width' => array(0,9,0))
			,'seo_product_meta_keyword_import'			=> array('type' => 'input', 'width' => array(0,9,0), 'hidden' => 1)
			,'seo_product_meta_keyword_template'		=> array('type' => 'input', 'width' => array(0,9,0))
			,'seo_product_tag_import'					=> array('type' => 'input', 'width' => array(0,9,0), 'hidden' => 1)
			,'seo_product_tag_template'					=> array('type' => 'input', 'width' => array(0,9,0))
			,'seo_category_seo_url_template'			=> array('type' => 'input', 'width' => array(0,9,0))
			,'seo_category_meta_title_template'			=> array('type' => 'input', 'width' => array(0,9,0))
			,'seo_category_meta_description_template'	=> array('type' => 'input', 'width' => array(0,9,0))
			,'seo_category_meta_keyword_template'		=> array('type' => 'input', 'width' => array(0,9,0))
			,'seo_manufacturer_seo_url_template'		=> array('type' => 'input', 'width' => array(0,9,0))
			,'seo_manufacturer_meta_title_import'		=> array('type' => 'input', 'width' => array(0,9,0), 'hidden' => 1)
			,'seo_manufacturer_meta_title_template'		=> array('type' => 'input', 'width' => array(0,9,0))
			,'seo_manufacturer_meta_description_import'	=> array('type' => 'input', 'width' => array(0,9,0), 'hidden' => 1)
			,'seo_manufacturer_meta_description_template'	=> array('type' => 'input', 'width' => array(0,9,0))
			,'seo_manufacturer_meta_keyword_import'		=> array('type' => 'input', 'width' => array(0,9,0), 'hidden' => 1)
			,'seo_manufacturer_meta_keyword_template'	=> array('type' => 'input', 'width' => array(0,9,0))
			,'order_currency'							=> array('type' => 'input')
			,'ignore_price_zero'						=> array('type' => 'radio', 'default' => 1)
			,'log_memory_use_view'						=> array('type' => 'radio', 'default' => 1)
			,'log_debug_line_view'						=> array('type' => 'radio', 'default' => 1)
			,'order_notify'								=> array('type' => 'radio', 'default' => 1)
			,'product_options_mode'						=> array('type' => 'select', 'options' => $list_options)
			,'product_options_subtract'					=> array('type' => 'radio', 'default' => 1)
			,'default_stock_status'						=> array('type'	=> 'select', 'options' => $select_stock_statuses)
			,'log_level'								=> array('type' => 'select', 'options' => $log_level_list)
			,'file_exchange'							=> array('type' => 'select', 'options' => $file_exchange_list)
			,'seo_product_mode'							=> array('type' => 'select', 'options' => $list_seo_mode, 'width' => array(1,2,9))
			,'seo_product_seo_url'						=> array('type' => 'select', 'options' => $list_product, 'width' => array(1,2,0))
			,'seo_product_meta_title'					=> array('type' => 'select', 'options' => $list_product, 'width' => array(1,2,0))
			,'seo_product_meta_description'				=> array('type' => 'select', 'options' => $list_product, 'width' => array(1,2,0))
			,'seo_product_meta_keyword'					=> array('type' => 'select', 'options' => $list_product, 'width' => array(1,2,0))
			,'seo_product_tag'							=> array('type' => 'select', 'options' => $list_product, 'width' => array(1,2,0))
			,'seo_category_mode'						=> array('type' => 'select', 'options' => $list_seo_mode, 'width' => array(1,2,9))
			,'seo_category_seo_url'						=> array('type' => 'select', 'options' => $list_category, 'width' => array(1,2,0))
			,'seo_category_meta_title'					=> array('type' => 'select', 'options' => $list_category, 'width' => array(1,2,0))
			,'seo_category_meta_description'			=> array('type' => 'select', 'options' => $list_category, 'width' => array(1,2,0))
			,'seo_category_meta_keyword'				=> array('type' => 'select', 'options' => $list_category, 'width' => array(1,2,0))
			,'seo_manufacturer_mode'					=> array('type' => 'select', 'options' => $list_seo_mode, 'width' => array(1,2,9))
			,'seo_manufacturer_seo_url'					=> array('type' => 'select', 'options' => $list_product, 'width' => array(1,2,0))
			,'seo_manufacturer_meta_title'				=> array('type' => 'select', 'options' => $list_product, 'width' => array(1,2,0))
			,'seo_manufacturer_meta_description'		=> array('type' => 'select', 'options' => $list_product, 'width' => array(1,2,0))
			,'seo_manufacturer_meta_keyword'			=> array('type' => 'select', 'options' => $list_product, 'width' => array(1,2,0))
			,'orders_export_modify'						=> array('type' => 'radio')
			,'order_modify_exchange'					=> array('type' => 'radio', 'default' => 1, 'width' => array(2,3,7))
			,'order_status_export'						=> array('type' => 'select', 'options' => $order_statuses, 'width' => array(2,3,7))
			,'order_status_exported'					=> array('type' => 'select', 'options' => $order_statuses, 'width' => array(2,3,7))
			,'order_notify_subject'						=> array('type' => 'input')
			,'order_notify_text'						=> array('type' => 'textarea')
			,'order_status_shipped'						=> array('type' => 'select', 'options' => $order_statuses, 'width' => array(2,3,7))
			,'order_status_paid'						=> array('type' => 'select', 'options' => $order_statuses, 'width' => array(2,3,7))
			,'order_status_completed'					=> array('type' => 'select', 'options' => $order_statuses, 'width' => array(2,3,7))
			,'order_reserve_product'					=> array('type' => 'radio', 'default' => -1)
			,'orders_import'							=> array('type' => 'radio', 'default' => 1)
			,'set_quantity_if_zero'						=> array('type' => 'input')
			,'export_module_to_all'						=> array('type' => 'radio', 'default' => -1)
			,'fio_corrector'							=> array('type' => 'radio', 'default' => -1, 'disabled' => 1)
			,'order_date_ship'							=> array('type' => 'select', 'options' => $list_order_date_ship, 'disabled' => 1)
			//,'compatibility_unf16'						=> array('type' => 'radio', 'default' => -1, 'disabled' => 1)
			,'services_in_table_product'				=> array('type' => 'radio', 'default' => 1)
			,'clean_options'							=> array('type' => 'radio', 'default' => -1)
			,'clean_prices_full_import'					=> array('type' => 'radio', 'default' => 1)
			,'remove_doubles_links'						=> array('type' => 'button')
			//,'disable_product_full_import'				=> array('type' => 'radio', 'default' => -1)
			,'parse_categories_in_memory'				=> array('type' => 'radio', 'default' => 1)
			,'parse_units_in_memory'					=> array('type' => 'radio', 'default' => 1)
			,'product_not_import_disable'				=> array('type' => 'radio', 'default' => -1)
			,'price_types_auto_load'					=> array('type' => 'radio', 'default' => 1)
			,'price_import_mode'						=> array('type' => 'select', 'options' => $select_price_import_mode)
			,'product_images_import_mode'				=> array('type' => 'select', 'options' => $select_product_images_import_mode)
			,'order_customer_export'					=> array('type' => 'radio', 'default' => 1)
			,'product_feature_name'						=> array('type' => 'input', 'default' => 'Варианты')
			,'product_options_type'						=> array('type' => 'select', 'options' => $select_product_options_type)
			,'product_property_type'					=> array('type' => 'checkbox', 'options' => $select_product_property_type)
			,'product_options_image_folder'				=> array('type' => 'input')
			,'product_options_image_ext'				=> array('type' => 'input')
			,'product_options_image_use_path_product'	=> array('type' => 'radio')
			,'product_options_image_only_digit'			=> array('type' => 'radio')
			,'product_options_image_prefix'				=> array('type' => 'input')
			,'product_options_image_load'				=> array('type' => 'radio')
		);

		if (isset($settings['exchange1c_table_fields'])) {
			$tab_fields = $settings['exchange1c_table_fields'];
		}

		if (isset($tab_fields['product_description']['meta_h1'])) {
			$params['seo_product_meta_h1_import']			= array('type' => 'input', 'width' => array(0,9,0), 'hidden' => 1);
			$params['seo_product_meta_h1_template']			= array('type' => 'input', 'width' => array(0,9,0));
			$params['seo_product_meta_h1']					= array('type' => 'select', 'options' => $list_product, 'width' => array(1,2,0));
		}
		if (isset($tab_fields['category_description']['meta_h1'])) {
			$params['seo_category_meta_h1_import']			= array('type' => 'input', 'width' => array(0,9,0), 'hidden' => 1);
			$params['seo_category_meta_h1_template']		= array('type' => 'input', 'width' => array(0,9,0));
			$params['seo_category_meta_h1']					= array('type' => 'select', 'options' => $list_product, 'width' => array(1,2,0));
		}
		if (isset($tab_fields['manufacturer_description']['meta_h1'])) {
			$params['seo_manufacturer_meta_h1_import']		= array('type' => 'input', 'width' => array(0,9,0), 'hidden' => 1);
			$params['seo_manufacturer_meta_h1_template']	= array('type' => 'input', 'width' => array(0,9,0));
			$params['seo_manufacturer_meta_h1']				= array('type' => 'select', 'options' => $list_product, 'width' => array(1,2,0));
		}

		foreach ($params as $name => $param) {
			$html = '';
			switch ($param['type']) {
				case 'button':
					$html = $this->htmlButton($name, $param);
					break;
				case 'radio':
					$html = $this->htmlRadio($name, $param);
					break;
				case 'select':
					$html = $this->htmlSelect($name, $param);
					break;
				case 'image':
					$html = $this->htmlImage($name, $images[$name]);
					break;
				case 'input':
					$html = $this->htmlInput($name, $param);
					break;
				case 'textarea':
					$html = $this->htmlTextarea($name, $param);
					break;
				case 'checkbox':
					$html = $this->htmlCheckbox($name, $param);
					break;
			}
			if ($html)
				$data['html_'.$name] = $this->htmlParam($name, $html, $param);
		}


		// Группы покупателей
		$this->load->model('customer/customer_group');
		$data['customer_groups'] = $this->model_customer_customer_group->getCustomerGroups();
		array_unshift($data['customer_groups'], array('customer_group_id'=>0,'sort_order'=>0,'name'=>'--- Выберите ---'));

		// типы цен
		if (isset($this->request->post['exchange1c_price_type'])) {
			$data['exchange1c_price_type'] = $this->request->post['exchange1c_price_type'];
		}
		else {
			$data['exchange1c_price_type'] = $this->config->get('exchange1c_price_type');
			if(empty($data['exchange1c_price_type'])) {
				$data['exchange1c_price_type'] = array();
//				$data['exchange1c_price_type'][] = array(
//					'keyword'			=> '',
//					'id_cml'			=> '',
//					'customer_group_id'	=> $this->config->get('config_customer_group_id'),
//					'quantity'			=> 1,
//					'priority'			=> 1
//				);
			}
		}

	 	// максимальный размер загружаемых файлов
		$data['lang']['text_max_filesize'] = sprintf($this->language->get('text_max_filesize'), @ini_get('max_file_uploads'));
		$data['upload_max_filesize'] = ini_get('upload_max_filesize');
		$data['post_max_size'] = ini_get('post_max_size');

		$links_info = $this->model_extension_exchange1c->linksInfo();
		$data['links_product_info'] = $links_info['product_to_1c'];
		$data['links_category_info'] = $links_info['category_to_1c'];
		$data['links_manufacturer_info'] = $links_info['manufacturer_to_1c'];
		$data['links_attribute_info'] = $links_info['attribute_to_1c'];

	 	// информация о памяти
		$data['memory_limit'] = ini_get('memory_limit');

		// Вывод шаблона
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/exchange1c', $data));

	} // index()


	/**
	 * Установка модуля
	 */
	public function install() {

		$this->load->model('setting/setting');
		$settings = $this->model_setting_setting->getSetting('exchange1c', 0);

		$this->load->model('extension/exchange1c');
		$this->model_extension_exchange1c->setEvents();
		$module_version = $this->model_extension_exchange1c->version();

		$this->load->model('setting/setting');
		$settings['exchange1c_version'] 					= $module_version;
		$settings['exchange1c_name'] 						= 'Exchange 1C 8.x for OpenCart 2.x';
		$settings['exchange1c_CMS_version']					= VERSION;
		$settings['exchange1c_seo_category_name'] 			= '[category_name]';
		$settings['exchange1c_seo_parent_category_name'] 	= '[parent_category_name]';
		$settings['exchange1c_seo_product_name'] 			= '[product_name]';
		$settings['exchange1c_seo_product_price'] 			= '[product_price]';
		$settings['exchange1c_seo_manufacturer'] 			= '[manufacturer]';
		$settings['exchange1c_seo_sku'] 					= '[sku]';
		$settings['exchange1c_table_fields']				= $this->model_extension_exchange1c->defineTableFields();

		$this->model_setting_setting->editSetting('exchange1c', $settings);

		// Определение полей таблиц которые могут быть в разных версиях CMS

//		$this->load->model('extension/module');
//		$this->model_extension_module->addModule('exchange1c',
//			array(
//				'version'	=> $this->module_version,
//				'name'		=> $this->module_name
//			)
//		);

		// Изменения в базе данных
		@$this->db->query("ALTER TABLE  `" . DB_PREFIX . "cart` ADD  `product_feature_id` INT( 11 ) NOT NULL DEFAULT 0 AFTER  `option`");
		@$this->db->query("ALTER TABLE  `" . DB_PREFIX . "cart` ADD  `unit_id` INT( 11 ) NOT NULL DEFAULT 0 AFTER  `option`");
		@$this->db->query("ALTER TABLE  `" . DB_PREFIX . "cart` DROP INDEX  `cart_id` ,	ADD INDEX  `cart_id` (  `customer_id` ,  `session_id` ,  `product_id` ,  `recurring_id` ,  `product_feature_id` , `unit_id`)");

		// Общее количество теперь можно хранить не только целое число (для совместимости)
		// Увеличиваем точность поля веса до тысячных
		@$this->db->query("ALTER TABLE `" . DB_PREFIX . "product` CHANGE `quantity` `quantity` decimal(15,3) NOT NULL DEFAULT 0.000 COMMENT 'Количество'");
		@$this->db->query("ALTER TABLE `" . DB_PREFIX . "product` CHANGE `weight` `weight` decimal(15,3) NOT NULL DEFAULT 0.000 COMMENT 'Вес'");

		// Общее количество теперь можно хранить не только целое число (для совместимости)
		@$this->db->query("ALTER TABLE `" . DB_PREFIX . "product_option_value` CHANGE `quantity` `quantity` decimal(15,3) NOT NULL DEFAULT 0 COMMENT 'Количество'");

		// Связь товаров с 1С
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "product_to_1c`");
		$this->db->query(
			"CREATE TABLE `" . DB_PREFIX . "product_to_1c` (
				`product_id` 				INT(11) 		NOT NULL 				COMMENT 'ID товара',
				`guid` 					VARCHAR(64) 		NOT NULL 				COMMENT 'Ид товара в 1С',
				UNIQUE KEY `product_link` (`product_id`, `guid`),
				FOREIGN KEY (`product_id`) 				REFERENCES `". DB_PREFIX ."product`(`product_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8"
		);

		// Связь категорий с 1С
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "category_to_1c`");
		$this->db->query(
			"CREATE TABLE `" . DB_PREFIX . "category_to_1c` (
				`category_id` 				INT(11) 		NOT NULL 				COMMENT 'ID категории',
				`guid` 						VARCHAR(64) 	NOT NULL 				COMMENT 'Ид категории в 1С',
				UNIQUE KEY `category_link` (`category_id`,`guid`),
				FOREIGN KEY (`category_id`) 			REFERENCES `". DB_PREFIX ."category`(`category_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8"
		);

		// Свойства из 1С
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "attribute_to_1c`");
		$this->db->query(
			"CREATE TABLE `" . DB_PREFIX . "attribute_to_1c` (
				`attribute_id` 				INT(11) 		NOT NULL 				COMMENT 'ID атрибута',
				`guid`						VARCHAR(64) 	NOT NULL 				COMMENT 'Ид свойства в 1С',
				UNIQUE KEY `attribute_link` (`attribute_id`, `guid`),
				FOREIGN KEY (`attribute_id`) 			REFERENCES `". DB_PREFIX ."attribute`(`attribute_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8"
		);

		// Значения свойства из 1С
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "attribute_value`");
		$this->db->query(
			"CREATE TABLE `" . DB_PREFIX . "attribute_value` (
				`attribute_value_id` 		INT(11) 		NOT NULL AUTO_INCREMENT	COMMENT 'ID атрибута',
				`attribute_id` 				INT(11) 		NOT NULL 				COMMENT 'Ссылка на атрибут',
				`guid`						VARCHAR(64) 	NOT NULL 				COMMENT 'Ид свойства в 1С',
				`name`						VARCHAR(255) 	NOT NULL 				COMMENT 'Название свойства',
				PRIMARY KEY (`attribute_value_id`),
				UNIQUE KEY `attribute_value_key` (`attribute_id`, `guid`),
				FOREIGN KEY (`attribute_id`) 			REFERENCES `". DB_PREFIX ."attribute`(`attribute_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8"
		);

		// Привязка производителя к каталогу 1С
		// В Ид производителя из 1С записывается либо Ид свойства сопоставленное либо Ид элемента справочника с производителями
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "manufacturer_to_1c`");
		$this->db->query(
			"CREATE TABLE `" . DB_PREFIX . "manufacturer_to_1c` (
				`manufacturer_id` 			INT(11) 		NOT NULL 				COMMENT 'ID производителя',
				`guid` 						VARCHAR(64) 	NOT NULL 				COMMENT 'Ид производителя в 1С',
				PRIMARY KEY (`manufacturer_id`),
				UNIQUE KEY `manufacturer_link` (`manufacturer_id`, `guid`),
				FOREIGN KEY (`manufacturer_id`) 		REFERENCES `". DB_PREFIX ."manufacturer`(`manufacturer_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8"
		);

		// Привязка магазина к каталогу в 1С
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "store_to_1c`");
		$this->db->query(
			"CREATE TABLE `" . DB_PREFIX . "store_to_1c` (
				`store_id` 					INT(11) 		NOT NULL 				COMMENT 'Код магазина',
				`guid` 						VARCHAR(64) 	NOT NULL 				COMMENT 'Ид каталога в 1С',
				UNIQUE KEY `store_link` (`store_id`, `guid`),
				FOREIGN KEY (`store_id`) 				REFERENCES `". DB_PREFIX ."store`(`store_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8"
		);

		// Остатки товара
		// Хранятся остатки товара как с характеристиками, так и без.
		// Если склады и характеристики не используются, эта таблица будет пустая
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "product_quantity`");
		$this->db->query(
			"CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "product_quantity` (
				`product_quantity_id` 		INT(11) 		NOT NULL AUTO_INCREMENT	COMMENT 'Счетчик',
				`product_id` 				INT(11) 		NOT NULL 				COMMENT 'Ссылка на товар',
				`product_feature_id` 		INT(11) 		DEFAULT 0 NOT NULL		COMMENT 'Ссылка на характеристику товара',
				`warehouse_id` 				INT(11) 		DEFAULT 0 NOT NULL 		COMMENT 'Ссылка на склад',
				`quantity` 					DECIMAL(10,3) 	DEFAULT 0 				COMMENT 'Остаток',
				PRIMARY KEY (`product_quantity_id`),
				UNIQUE KEY `product_quantity_key` (`product_id`, `product_feature_id`, `warehouse_id`),
				FOREIGN KEY (`product_id`) 			REFERENCES `" . DB_PREFIX . "product`(`product_id`),
				FOREIGN KEY (`product_feature_id`) 	REFERENCES `" . DB_PREFIX . "product_feature`(`product_feature_id`),
				FOREIGN KEY (`warehouse_id`) 		REFERENCES `" . DB_PREFIX . "warehouse`(`warehouse_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8"
		);

		// склады
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "warehouse`");
		$this->db->query(
			"CREATE TABLE `" . DB_PREFIX . "warehouse` (
				`warehouse_id` 				SMALLINT(3) 	NOT NULL AUTO_INCREMENT,
				`name` 						VARCHAR(100) 	NOT NULL DEFAULT '' 	COMMENT 'Название склада в 1С',
				`guid` 						VARCHAR(64) 	NOT NULL				COMMENT 'Ид склада в 1С',
				PRIMARY KEY (`warehouse_id`),
				UNIQUE KEY `warehouse_link` (`warehouse_id`, `guid`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8"
		);

		// Описание картинок
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "product_image_description`");
		$this->db->query(
			"CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "product_image_description` (
				`product_image_id` 			INT(11) 		NOT NULL 				COMMENT 'Ссылка на картинку',
				`product_id` 				INT(11) 		NOT NULL 				COMMENT 'Ссылка на товар',
				`language_id` 				INT(11) 		NOT NULL 				COMMENT 'Ссылка на язык',
				`name` 						VARCHAR(255) 	NOT NULL DEFAULT '' 	COMMENT 'Название',
				UNIQUE KEY `product_image_desc_key` (`product_image_id`, `product_id`, `language_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8"
		);

		// Характеристики товара
		// Если характеристики не используются, эта таблица будет пустая
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "product_feature`");
		$this->db->query(
			"CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "product_feature` (
				`product_feature_id` 		INT(11) 		NOT NULL AUTO_INCREMENT COMMENT 'Счетчик',
				`product_id` 				INT(11) 		NOT NULL 				COMMENT 'Ссылка на товар',
				`ean` 						VARCHAR(14) 	NOT NULL DEFAULT '' 	COMMENT 'Штрихкод',
				`name` 						VARCHAR(255) 	NOT NULL DEFAULT '' 	COMMENT 'Название',
				`sku` 						VARCHAR(128) 	NOT NULL DEFAULT '' 	COMMENT 'Артикул',
				`guid` 						VARCHAR(64) 	NOT NULL 				COMMENT 'Ид характеристики в 1С',
				PRIMARY KEY (`product_feature_id`),
				UNIQUE KEY `product_feature_key` (`product_id`, `guid`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8"
		);

		// Значения характеристики товара(доп. значения)
		// Если характеристики не используются, эта таблица будет пустая
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "product_feature_value`");
		$this->db->query(
			"CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "product_feature_value` (
				`product_feature_id` 		INT(11) 		NOT NULL 				COMMENT 'ID характеристики товара',
				`product_option_id` 		INT(11) 		NOT NULL 				COMMENT 'ID опции товара',
				`product_id` 				INT(11) 		NOT NULL 				COMMENT 'ID товара',
				`product_option_value_id` 	INT(11) 		NOT NULL 				COMMENT 'ID значения опции товара',
				UNIQUE KEY `product_feature_value_key` (`product_feature_id`, `product_id`, `product_option_value_id`),
				FOREIGN KEY (`product_feature_id`) 		REFERENCES `" . DB_PREFIX . "product_feature`(`product_feature_id`),
				FOREIGN KEY (`product_option_id`) 		REFERENCES `" . DB_PREFIX . "product_option`(`product_option_id`),
				FOREIGN KEY (`product_id`) 				REFERENCES `" . DB_PREFIX . "product`(`product_id`),
				FOREIGN KEY (`product_option_value_id`)	REFERENCES `" . DB_PREFIX . "product_option_value`(`product_option_value_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8"
		);

		// Цены, если характеристики не используются, эта таблица будет пустая
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "product_price`");
		$this->db->query(
			"CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "product_price` (
				`product_price_id` 			INT(11) 		NOT NULL AUTO_INCREMENT	COMMENT 'Счетчик',
				`product_id` 				INT(11) 		NOT NULL 				COMMENT 'ID товара',
				`product_feature_id` 		INT(11) 		NOT NULL DEFAULT '0' 	COMMENT 'ID характеристики товара',
				`customer_group_id`			INT(11) 		NOT NULL DEFAULT '0'	COMMENT 'ID группы покупателя',
				`price` 					DECIMAL(15,4) 	NOT NULL DEFAULT '0'	COMMENT 'Цена',
				PRIMARY KEY (`product_price_id`),
				UNIQUE KEY `product_price_key` (`product_id`, `product_feature_id`, `customer_group_id`),
				FOREIGN KEY (`product_id`) 				REFERENCES `" . DB_PREFIX . "product`(`product_id`),
				FOREIGN KEY (`product_feature_id`) 		REFERENCES `" . DB_PREFIX . "product_feature`(`product_feature_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8"
		);

		// Единицы измерения товара (упаковки товара)
		// Если используются упаковки, то в эту таблицу записываются дополнительные единицы измерения
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "product_unit`");
		$this->db->query(
			"CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "product_unit` (
				`product_unit_id`			INT(11) 		NOT NULL AUTO_INCREMENT	COMMENT 'Счетчик',
				`product_id` 				INT(11) 		NOT NULL 				COMMENT 'ID товара',
				`product_feature_id` 		INT(11) 		NOT NULL DEFAULT '0' 	COMMENT 'ID характеристики товара',
				`unit_id` 					INT(11) 		DEFAULT '0' NOT NULL 	COMMENT 'ID единицы измерения',
				`ratio` 					INT(9) 			DEFAULT '1' 			COMMENT 'Коэффициент пересчета количества',
				PRIMARY KEY (`product_unit_id`),
				UNIQUE KEY `product_unit_key` (`product_id`, `product_feature_id`, `unit_id`),
				FOREIGN KEY (`product_id`) 				REFERENCES `" . DB_PREFIX . "product`(`product_id`),
				FOREIGN KEY (`product_feature_id`) 		REFERENCES `" . DB_PREFIX . "product_feature`(`product_feature_id`),
				FOREIGN KEY (`unit_id`) 				REFERENCES `" . DB_PREFIX . "unit`(`unit_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8"
		);

		// Привязка единиц измерения к торговой системе
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "unit_to_1c`");
		$this->db->query(
			"CREATE TABLE `" . DB_PREFIX . "unit_to_1c` (
				`unit_id` 					SMALLINT(6) 	NOT NULL 				COMMENT 'ID единицы измерения по каталогу',
				`guid` 						VARCHAR(64) 	NOT NULL 				COMMENT 'Ид единицы измерения в ТС',
				`name` 						VARCHAR(16) 	NOT NULL 				COMMENT 'Наименование краткое',
				`code` 						VARCHAR(4) 		NOT NULL 				COMMENT 'Код числовой',
				`fullname` 					VARCHAR(50) 	NOT NULL 				COMMENT 'Наименование полное',
				`eng_name2` 				VARCHAR(50)		NOT NULL 				COMMENT 'Международное сокращение',
				UNIQUE KEY `unit_link` (`unit_id`, `guid`),
				FOREIGN KEY (`unit_id`) 				REFERENCES `". DB_PREFIX ."unit`(`unit_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8"
		);

		$this->model_extension_exchange1c->installUnits();

		$this->log->write("Включен модуль " . $this->module_name . " версии " . $this->model_extension_exchange1c->version());

	} // install()


	/**
	 * ver 2
	 * update 2017-04-30
	 * Деинсталляция
	 */
	public function uninstall() {


		$this->load->model('extension/exchange1c');
		$table_fields = $this->model_extension_exchange1c->defineTableFields();

		$this->load->model('extension/event');
		$this->model_extension_event->deleteEvent('exchange1c');

		$this->load->model('setting/setting');
		$this->model_setting_setting->deleteSetting('exchange1c');

		//$this->load->model('extension/modification');
		//$modification = $this->model_extension_modification->getModificationByCode('exchange1c');
		//if ($modification) $this->model_extension_modification->deleteModification($modification['modification_id']);

		$query = $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "product_quantity`");
		$query = $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "category_to_1c`");
		$query = $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "attribute_to_1c`");
		$query = $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "manufacturer_to_1c`");
		$query = $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "store_to_1c`");
		$query = $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "product_quantity`");
		$query = $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "product_price`");
		$query = $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "product_to_1c`");
		$query = $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "product_unit`");
		$query = $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "product_feature`");
		$query = $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "product_feature_value`");
		$query = $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "product_image_description`");
		$query = $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "unit_to_1c`");
		$query = $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "unit`");
		$query = $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "unit_group`");
		$query = $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "unit_type`");
		$query = $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "warehouse`");
		$query = $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "attribute_value`");

		// Удаляем все корректировки в базе

		// Изменения в базе данных
		$change = false;
		if (isset($table_fields['cart']['product_feature_id'])) {
			$this->db->query("ALTER TABLE  `" . DB_PREFIX . "cart` DROP  `product_feature_id`");
			$change = true;
		}
		if (isset($table_fields['cart']['unit_id'])) {
			$this->db->query("ALTER TABLE  `" . DB_PREFIX . "cart` DROP  `unit_id`");
			$change = true;
		}
		if ($change) {
			$this->db->query("ALTER TABLE  `" . DB_PREFIX . "cart` DROP INDEX  `cart_id` ,	ADD INDEX  `cart_id` (  `customer_id` ,  `session_id` ,  `product_id` ,  `recurring_id`)");
		}

		// Общее количество теперь можно хранить не только целое число (для совместимости)
		// Увеличиваем точность поля веса до тысячных
		@$this->db->query("ALTER TABLE `" . DB_PREFIX . "product` CHANGE `quantity` `quantity` int(6) NOT NULL DEFAULT 0.000 COMMENT 'Количество'");
		@$this->db->query("ALTER TABLE `" . DB_PREFIX . "product` CHANGE `weight` `weight` int(6) NOT NULL DEFAULT 0.000 COMMENT 'Вес'");

		// Общее количество теперь можно хранить не только целое число (для совместимости)
		@$this->db->query("ALTER TABLE `" . DB_PREFIX . "product_option_value` CHANGE `quantity` `quantity` decimal(15,3) NOT NULL DEFAULT 0 COMMENT 'Количество'");

		// Общее количество теперь можно хранить не только целое число (для совместимости)
		$this->db->query("ALTER TABLE `" . DB_PREFIX . "product` CHANGE `quantity` `quantity` int(4) NOT NULL DEFAULT 0 COMMENT 'Количество'");

		// Общее количество теперь можно хранить не только целое число (для совместимости)
		$this->db->query("ALTER TABLE `" . DB_PREFIX . "product_option_value` CHANGE `quantity` `quantity` int(4) NOT NULL DEFAULT 0 COMMENT 'Количество'");

		$this->log->write("Отключен модуль " . $this->module_name);
		$this->log->write("Удалены таблицы: product_quantity, category_to_1c, attribute_to_1c, manufacturer_to_1c, store_to_1c, product_quantity, product_price, product_to_1c, product_unit, unit, unit_group, unit_type, warehouse.");
		$this->log->write("Восстановлены изменения таблиц product, product_option_value, cart");

	} // uninstall()


	/**
	 * ver 2
	 * update 2017-06-04
	 * Проверка доступа с IP адреса
	 */
	private function checkAccess($echo = false) {

		// Проверяем включен или нет модуль
		if (!$this->config->get('exchange1c_module_status')) {
			if ($echo) $this->echo_message(0, "The module is disabled");
			return false;
		}
		// Разрешен ли IP
		$config_allow_ips = $this->config->get('exchange1c_allow_ip');

		if ($config_allow_ips != '') {
			$ip = $_SERVER['REMOTE_ADDR'];
			$allow_ips = explode("\r\n", $config_allow_ips);
			foreach ($allow_ips as $allow_ip) {
				$length = strlen($allow_ip);
				if (substr($ip,0,$length) == $allow_ip) {
					return true;
				}
			}

		} else {
			return true;
		}
		if ($echo) $this->echo_message(0, "From Your IP address are not allowed");
		return false;

	} // checkAccess()


	/**
	 * Режим проверки авторизации через http запрос
	 */
	public function modeCheckauth() {

		if (!$this->checkAccess(true))
			exit;
		// Авторизуем
		if (($this->config->get('exchange1c_username') != '') && (@$_SERVER['PHP_AUTH_USER'] != $this->config->get('exchange1c_username'))) {
			$this->echo_message(0, "Incorrect login");
		}
		if (($this->config->get('exchange1c_password') != '') && (@$_SERVER['PHP_AUTH_PW'] != $this->config->get('exchange1c_password'))) {
			$this->echo_message(0, "Incorrect password");
			exit;
		}
		$this->echo_message(1, "key\n");
		echo md5($this->config->get('exchange1c_password')) . "\n";

	} // modeCheckauth()


	/**
	 * ver 2
	 * update 2017-05-02
	 * Очистка базы данных через админ-панель
	 */
	public function manualCleaning() {

		$this->load->language('extension/module/exchange1c');
		$json = array();
		// Проверим разрешение
		if ($this->user->hasPermission('modify', 'extension/module/exchange1c'))  {
			$this->load->model('extension/exchange1c');
			$result = $this->model_extension_exchange1c->cleanDB();
			if (!$result) {
				$json['error'] = "Таблицы не были очищены";
			} else {
				$json['success'] = "Успешно очищены таблицы: \n" . $result;
			}
		} else {
			$json['error'] = $this->language->get('error_permission');
		}
		$this->response->setOutput(json_encode($json));

	} // manualCleaning()


	/**
	 * ver 2
	 * update 2017-05-02
	 * Очистка связей с 1С через админ-панель
	 */
	public function manualCleaningLinks() {

		$this->load->language('extension/module/exchange1c');
		$json = array();
		// Проверим разрешение
		if ($this->user->hasPermission('modify', 'extension/module/exchange1c'))  {
			$this->load->model('extension/exchange1c');
			$result = $this->model_extension_exchange1c->cleanLinks();
			if (!$result) {
				$json['error'] = "Таблицы не были очищены";
			} else {
				$json['success'] = "Успешно очищены таблицы: \n" . $result;
			}
		} else {
			$json['error'] = $this->language->get('error_permission');
		}
		$this->response->setOutput(json_encode($json));

	} // manualCleaningLinks()


	/**
	 * ver 2
	 * update 2017-05-02
	 * Очистка старых ненужных картинок через админ-панель
	 */
	public function manualCleaningOldImages() {

		$this->load->language('extension/module/exchange1c');
		$json = array();
		// Проверим разрешение
		if ($this->user->hasPermission('modify', 'extension/module/exchange1c'))  {
			$this->load->model('extension/exchange1c');
			$result = $this->model_extension_exchange1c->cleanOldImages("import_files/");
			if ($result['error']) {
				$json['error'] = $result['error'];
			} else {
				$json['success'] = "Успешно удалено файлов: " . $result['num'];
			}
		} else {
			$json['error'] = $this->language->get('error_permission');
		}
		$this->response->setOutput(json_encode($json));

	} // manualCleaningLinks()


	/**
	 * ver 2
	 * update 2017-05-02
	 * Очистка кэша: системного, картинок
	 */
	public function manualCleaningCache() {

		$this->load->language('extension/module/exchange1c');
		$json = array();
		// Проверим разрешение
		if ($this->user->hasPermission('modify', 'extension/module/exchange1c'))  {
			$this->load->model('extension/exchange1c');

			$result = $this->cleanCache();

			if (!$result) {
				$json['error'] = "Ошибка очистки кэша";
			} else {
				$json['success'] = "Кэш успешно очищен: \n" . $result;
			}
		} else {
			$json['error'] = $this->language->get('error_permission');
		}

		$this->response->setOutput(json_encode($json));

	} // manualCleaningCache()


	/**
	 * ver 2
	 * update 2017-05-02
	 * Генерация SEO на все товары
	 */
	public function manualGenerateSeo() {

		$this->load->language('extension/module/exchange1c');
		$json = array();
		// Проверим разрешение
		if ($this->user->hasPermission('modify', 'extension/module/exchange1c'))  {
			$this->load->model('extension/exchange1c');
			$result = $this->model_extension_exchange1c->seoGenerate();
 			if ($result['error']) {
				$json['error'] = "Ошибка формирования SEO\n" . $result['error'];
			} else {
				$json['success'] = "SEO успешно сформирован, обработано:\nТоваров: " . $result['product'] . "\nКатегорий: " . $result['category'] . "\nПроизводителей: " . $result['manufacturer'];
			}
		} else {
			$json['error'] = $this->language->get('error_permission');;
		}
		$this->response->setOutput(json_encode($json));

	} // manualGenerateSeo()


	/**
	 * ver 2
	 * update 2017-06-13
	 * Удаляет дубули ссылок связей с торговой системой в таблицах *_to_1c
	 */
	public function manualRemoveDoublesLinks() {

		$this->load->language('extension/module/exchange1c');
		$json = array();
		// Проверим разрешение
		if ($this->user->hasPermission('modify', 'extension/module/exchange1c'))  {
			$this->load->model('extension/exchange1c');
			$result = $this->model_extension_exchange1c->removeDoublesLinks();
 			if ($result['error']) {
				$json['error'] = "Ошибка удаления ссылок\n" . $result['error'];
			} else {
				$json['success'] = "Ссылки успешно удалены, обработано:".
				"\nАтрибутов: " . $result['attribute'] .
				"\nКатегорий: " . $result['category'] .
				"\nПроизводителей: " . $result['manufacturer'] .
				"\nТоваров: " . $result['product'] .
				"\nМагазинов: " . $result['store'];
			}
		} else {
			$json['error'] = $this->language->get('error_permission');
		}
		$this->response->setOutput(json_encode($json));

	} // manualRemoveDoublesLinks()


	/**
	 * Проверка существования каталогов
	 */
	private function checkDirectories($name) {

		$path = DIR_IMAGE;
		$dir = explode("/", $name);
		for ($i = 0; $i < count($dir)-1; $i++) {
			$path .= $dir[$i] . "/";
			if (!is_dir($path)) {
				$error = "";
				@mkdir($path, 0775) or die ($error = "Ошибка создания директории '" . $path . "'");
				if ($error)
					return $error;
				$this->log("Создана директория: " . $path, 2);
			}
		}
		return "";
	}  // checkDirectories()


	/**
	 * ver 2
	 * update 2017-06-03
	 * Распаковываем картинки
	 */
	private function extractImage($zipArc, $zip_entry, $name) {

		$error = "";

		// Если стоит режим не загружать, картинки не распаковываем
		if ($this->config->get('exchange1c_product_images_import_mode') == 'disable') {
			return "";
		}

		$this->log("Распаковка картинки = " . $name, 2);

		if (substr($name, -1) == "/") {

			// проверим каталог
			if (is_dir(DIR_IMAGE.$name)) {
				//$this->log('[zip] directory exist: '.$name, 2);

			} else {
				//$this->log('[zip] create directory: '.$name, 2);
				@mkdir(DIR_IMAGE.$name, 0775) or die ($error = "Ошибка создания директории '" . DIR_IMAGE.$name . "'");
				if ($error) return $error;
			}

		} elseif (zip_entry_open($zipArc, $zip_entry, "r")) {

			$error = $this->checkDirectories($name);
			if ($error) return $error;

			if (is_file(DIR_IMAGE.$name)) {
				//$this->log('[zip] file exist: '.$name, 2);
			} else {
				$dump = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));

				// для безопасности проверим, не является ли этот файл php
				$pos = strpos($dump, "<?php");

				if ($pos !== false) {
					$this->log("[!] ВНИМАНИЕ Файл '" . $name . "' является PHP скриптом и не будет записан!");

				} else {

					if (file_exists(DIR_IMAGE . $name) && $this->config->get('exchange1c_product_images_import_mode') != 'full') {
						return "";
					}
					$fd = @fopen(DIR_IMAGE . $name, "w+");

					if ($fd === false) {
						return "Ошибка создания файла: " . DIR_IMAGE.$name . ", проверьте права доступа!";
					}

					//$this->log('[zip] create file: '.$name, 2);
					fwrite($fd, $dump);
					fclose($fd);

					// для безопасности проверим, является ли этот файл картинкой
//					$image_info = getimagesize(DIR_IMAGE.$name);
//					if ($image_info == NULL) {
//						$this->log("[!] ВНИМАНИЕ Файл '" . $name . "' не является картинкой, и будет удален!");
//						unlink(DIR_IMAGE.$name);
//					}
				}
			}
			zip_entry_close($zip_entry);
		}

		//$this->log("Завершена распаковка картинки", 2);
		return $error;

	} // extractImage()


	/**
	 * ver 2
	 * update 2017-05-05
	 * Распаковываем XML
	 */
	private function extractXML($zipArc, $zip_entry, $name, &$xmlFiles) {

		$error = "";
		$this->log("Распаковка XML,  name = " . $name, 2);
		$cache = DIR_CACHE . 'exchange1c/';

		if (substr($name, -1) == "/") {
			// это директория
			if (is_dir($cache.$name)) {
				//$this->log('[zip] directory exist: '.$name, 2);
			} else {
				//$this->log('[zip] create directory: '.$name, 2);
				@mkdir($cache.$name, 0775) or die ($error = "Ошибка создания директории '" . $cache.$name . "'");
				if ($error) return $error;
			}
		} elseif (zip_entry_open($zipArc, $zip_entry, "r")) {
			$dump = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
			// Удалим существующий файл
			if (file_exists($cache.$name)) {
				unlink($cache.$name);
			}
			// для безопасности проверим, является ли этот файл XML
			$str_xml = mb_substr($dump, 1, 5);
			if ($str_xml != "<?xml") {
				$this->log("[!] ВНИМАНИЕ Файл '" . $name . "' не является XML файлом и не будет записан!");

			} elseif ($fd = @fopen($cache.$name,"w+")) {
				$xmlFiles[] = $name;
				//$this->log('[zip] create file: '.$name, 2);
				fwrite($fd, $dump);
				fclose($fd);
			}
			zip_entry_close($zip_entry);
		}
		$this->log("Завершена распаковка XML", 2);
		return "";

	} // extractXML()


	/**
	 * ver 3
	 * update 2017-06-03
	 * Распаковываем ZIP архив
	 */
	private function extractZip($zipFile, &$error) {

		$this->log("extractZip(): Распаковка архива: = " . $zipFile, 2);
		$xmlFiles = array();
		$cache = DIR_CACHE . 'exchange1c/';

		// Проверим на доступность записи в папку кэша
		if (!is_writable($cache)) {
			$error = "extractZip(): Папка '" . $cache . "' не доступна для записи, распаковка прервана";
			return $xmlFiles;
		}

		if (is_file($zipFile)) {
			//$this->log("Файл существует: " .  $zipFile);
		}

		$zipArc = zip_open($zipFile);
		if (is_resource($zipArc)) {

			while ($zip_entry = zip_read($zipArc)) {
				$name = zip_entry_name($zip_entry);
				$pos = stripos($name, 'import_files/');

				if ($pos !== false) {
					$error = $this->extractImage($zipArc, $zip_entry, substr($name, $pos));
					if ($error) return $xmlFiles;

				} else {
					$error = $this->extractXML($zipArc, $zip_entry, $name, $xmlFiles);
					if ($error) return $xmlFiles;
				}
			}

		} else {

			return $xmlFiles;
		}

		zip_close($zipArc);

		$this->log("extractZip(): Завершена распаковка архива", 2);

		return $xmlFiles;

	} // extractZip()


	/**
	 * Определяет тип файла по наименованию
	 */
	public function detectFileType($fileName) {

		$types = array('import', 'offers', 'prices', 'rests');
		foreach ($types as $type) {
			$pos = stripos($fileName, $type);
			if ($pos !== false)
				return $type;
		}
		return '';

	} // detectFileType()


	/**
	 * Создание и скачивание заказов
	 */
	public function downloadOrders() {

		$this->load->model('extension/exchange1c');
		$orders = $this->model_extension_exchange1c->queryOrders(
			array(
				 'from_date' 		=> $this->config->get('exchange1c_order_date')
				,'new_status'		=> $this->config->get('exchange1c_order_status')
				,'notify'			=> $this->config->get('exchange1c_order_notify')
				,'currency'			=> $this->config->get('exchange1c_order_currency') ? $this->config->get('exchange1c_order_currency') : 'руб.'
			)
		);
		$this->response->addheader('Pragma: public');
		$this->response->addheader('Connection: Keep-Alive');
		$this->response->addheader('Expires: 0');
		$this->response->addheader('Content-Description: File Transfer');
		$this->response->addheader('Content-Type: application/octet-stream');
		$this->response->addheader('Content-Disposition: attachment; filename="orders.xml"');
		$this->response->addheader('Content-Transfer-Encoding: binary');
		$this->response->addheader('Content-Length: ' . strlen($orders));
		//$this->response->addheader('Content-Type: text/html; charset=windows-1251');

		//$this->response->setOutput(file_get_contents(DIR_CACHE . 'exchange1c/orders.xml', FILE_USE_INCLUDE_PATH, null));
        $this->response->setOutput($orders);

	} // downloadOrders()


	/**
	 * ver 2
	 * update 2017-06-10
	 * Импорт файла через админ-панель
	 */
	private function manualImportFile() {

		if ($this->config->get('exchange1c_flush_log') == 1) {
			$this->load->model('extension/exchange1c');
			$this->model_extension_exchange1c->clearLog();
		}

		$uploaded_file = $this->request->files['file']['tmp_name'];

		if (!empty($this->request->files['file']['name']) && is_file($uploaded_file)) {

			//$filename = basename(html_entity_decode($this->request->files['file']['name'], ENT_QUOTES, 'UTF-8'));

			$max_size_file = $this->modeCatalogInit(array(),FALSE);
			$xmlFiles = $this->extractZip($uploaded_file, $error);
			if ($error) return $error;

			if (count($xmlFiles)) {

				$goods = array();
				$properties = array();
				foreach ($xmlFiles as $key => $file) {
					$pos = strripos($file, "/goods/");
					if ($pos !== false) {
						$goods[] = $file;
						unset($xmlFiles[$key]);
					}
					$pos = strripos($file, "/properties/");
					if ($pos !== false) {
					$properties[] = $file;
						unset($xmlFiles[$key]);
					}
				}

				// Порядок обработки файлов
				sort($xmlFiles);
				foreach ($xmlFiles as $file) {
					$this->log('Обрабатывается файл основной: ' . $file, 2);
					$error = $this->modeImport($cache . $file);
					if ($error) return $error;
				}
				foreach ($properties as $file) {
					$this->log('Обрабатывается файл свойств: ' . $file, 2);
					$error = $this->modeImport($cache . $file);
					if ($error) return $error;
				}
				foreach ($goods as $file) {
					$this->log('Обрабатывается файл товаров: ' . $file, 2);
					$error = $this->modeImport($cache . $file);
					if ($error) return $error;
				}

			}
			else {
				$this->log( "Загружен файл: " . $uploaded_file, 2);
				$error = $this->modeImport($uploaded_file);

				if ($error) return $error;
			}

		} // if (!empty($this->request->files['file']['name']) && is_file($uploaded_file))

		return "";

	} // manualImportFile()


	/**
	 * ver 5
	 * update 2017-06-10
	 * Импорт файла через админ-панель
	 * ПРОБЛЕМА: не прерывается по ошибке чтения файлов, но в лог пишет ошибку
	 */
	public function manualImport() {

		$this->load->language('extension/module/exchange1c');
		$cache = DIR_CACHE . 'exchange1c/';
		$json = array();
		$error = "";

		// Разрешен ли IP
		if ($this->checkAccess()) {

			$error = $this->manualImportFile();

		}

		if ($error) {
			//$json['error'] = $this->language->get('text_upload_error');
			$json['error'] = $error;
			$this->log( "[!] Ручной обмен прошел ошибками", 2);

		} else {
			$json['success'] = $this->language->get('text_upload_success');
			$this->log( "[i] Ручной обмен прошел без ошибок", 2);

		}

		$this->cache->delete('product');
		$this->response->setOutput(json_encode($json));

	} // manualImport()


	/**
	 * Проверяет наличие куки ключа
	 */
	private function checkAuthKey($echo=true) {

		if (!isset($this->request->cookie['key'])) {
			if ($echo) $this->echo_message(0, "no cookie key");
			return false;
		}
		if ($this->request->cookie['key'] != md5($this->config->get('exchange1c_password'))) {
			if ($echo) $this->echo_message(0, "Session error");
			return false;
		}
		return true;
	}


	/**
	 * Возвращает максимальный объем файла в байта для загрузки
	 */
	private function getPostMaxFileSize() {
		$size = ini_get('post_max_size');
		$type = $size{strlen($size)-1};
		$size = (integer)$size;
		switch ($type) {
			case 'K': $size = $size*1024;
				break;
			case 'M': $size = $size*1024*1024;
				break;
			case 'G': $size = $size*1024*1024*1024;
				break;
		}
		return $size;
	}


	/**
	 * Очистка лога
	 */
	private function clearLog() {
		$file = DIR_LOGS . $this->config->get('config_error_filename');
		$handle = fopen($file, 'w+');
		fclose($handle);
	}


	/**
	 * Обрабатывает команду инициализации каталога
	 */
	public function modeCatalogInit($param = array(), $echo = true) {

		// Проверка на запись файлов в кэш
		$cache = DIR_CACHE . 'exchange1c/';
		if (!is_dir($cache)) {
			mkdir($cache);
			$this->log("[i] Создана директория: " . $cache,2);
		}

		$img = DIR_IMAGE . 'import_files/';
		if (!is_dir($img)) {
			mkdir($img);
			$this->log("[i] Создана директория: " . $img,2);
		}

		if ($echo) {
			if ($this->config->get('exchange1c_file_exchange') == 'zip') {
				echo "zip=yes\n";
			} else {
				echo "zip=no\n";
			}
			echo "file_limit=" . $this->getPostMaxFileSize() . "\n";
		}

//		$this->configSet('exchange_status', 1);
//		$this->log("[i] Exchange status=1");

		// При начале обмена запишем в регистр дату и время начала обмена, а после обмена удалим ее
		if ($this->config->has('exchange1c_date_exchange_stop')) {
			// Запишем в регистр время начала обмена
			$this->load->model('setting/setting');
			$config = $this->model_setting_setting->getSetting('exchange1c');
			unset($config['exchange1c_date_exchange_stop']);
			$config['exchange1c_date_exchange'] = date('Y-m-d H:i:s');
			$this->model_setting_setting->editSetting('exchange1c', $config);
			$this->log("> Начало обмена: " . $config['exchange1c_date_exchange'],2);
			$this->log("[PHP] file_limit = " . $this->getPostMaxFileSize(),2);

			// Очистка лога при начале обмена
			if ($this->config->get('exchange1c_flush_log')) {
				//$this->load->model('extension/exchange1c');
				// Очистка базы!!! ВРЕМЕННО!
				//$this->model_extension_exchange1c->cleanDB();
				$this->clearLog();
			}

		}
		//$this->clearLog();

		return $this->getPostMaxFileSize();
	} // modeCatalogInit()


	/**
	 * ver 2
	 * update 2017-05-27
	 * Обрабатывает загруженный файл на сервер
	 */
	public function modeSaleInit() {
		if ($this->config->get('exchange1c_file_exchange') == 'zip') {
			echo "zip=yes\n";
		} else {
			echo "zip=no\n";
		}
		echo "file_limit=" . $this->getPostMaxFileSize() . "\n";
	} // modeSaleInit()


	/**
	 * ver 1
	 * update 2017-06-02
	 * Обрабатывает загруженный файл на сервер
	 */
	private function modeFile($mode, &$error) {

    	$this->log('modeFile', 2);

        $xmlfiles = array();

		if (!$this->checkAuthKey()) exit;
		$cache = DIR_CACHE . 'exchange1c/';

		// Проверяем на наличие каталога
		if(!is_dir($cache)) mkdir($cache);

		// Проверяем на наличие имени файла
		if (isset($this->request->get['filename'])) {
			$uplod_file = $cache . $this->request->get['filename'];
		}
		else {
			$error = "modeFile(): No file name variable";
			return false;
		}

		// Проверяем XML или изображения
		if (strpos($this->request->get['filename'], 'import_files') !== false) {
			$cache = DIR_IMAGE;
			$uplod_file = $cache . $this->request->get['filename'];
			$this->checkUploadFileTree(dirname($this->request->get['filename']) , $cache);
		}

		// Проверка на запись файлов в кэш
		if (!is_writable($cache)) {
			$error = "modeFile(): The folder " . $cache . " is not writable!";
			return false;
		}

		$this->log("upload file: " . $uplod_file,2);

		$time_limit = ini_get('max_execution_time');
		$memory_limit = ini_get('memory_limit');
		$this->log("time_limit: " . $time_limit, 2);
		$this->log("memory_limit: " . $memory_limit, 2);

		set_time_limit(0);
		ini_set('memory_limit', '-1');

		// Получаем данные
		$data = file_get_contents("php://input");
		if ($data !== false) {

			// Записываем в файл
			$filesize = file_put_contents($uplod_file, $data, FILE_APPEND | LOCK_EX);
			$this->log("file size: " . $filesize, 2);

			if ($filesize) {
				chmod($uplod_file , 0664);

				$xmlfiles = $this->extractZip($uplod_file, $error);
				if ($error) {
					$this->echo_message(0, "modeFile(): Error extract file: " . $uplod_file);
					return false;
				};
				if (count($xmlfiles)) {
					// Это архив, удаляем архив
					unlink($uplod_file);
				}
			} else {
				$this->echo_message(0, "modeFile(): Error create file");
			}
		}
		else {
			$this->echo_message(0, "modeFile(): Data empty");
		}

		set_time_limit($time_limit);
		ini_set('memory_limit', $memory_limit);

		return $xmlfiles;

	} // modeFile()


	/**
	 * ver 5
	 * update 2017-06-01
	 * Обрабатывает загруженный файл на сервер
	 */
	public function modeFileCatalog() {

    	$this->log('modeFileCatalog', 2);
        $error = '';

		$this->modeFile('catalog', $error);

		if ($error) {
			$this->echo_message(0, $error);
		} else {
			$this->echo_message(1, "Successfully import catalog ");
		}

	} // modeFileCatalog()


	/**
	 * ver 4
	 * update 2017-06-05
	 * Обрабатывает загруженный файл заказов на сервер
	 */
	public function modeFileSale() {

    	$this->log('modeFileSale', 2);

		if ($this->config->get('exchange1c_orders_import') != 1) {
			$this->log("modeFileSale(): Загрузка заказов отключена");
			exit;
		}

		$cache = DIR_CACHE . 'exchange1c/';
		$error = '';

		// Загружаем файл
		$xmlfiles = $this->modeFile('sale', $error);

		// Если во время обработки файла произошла ошибка, то загрузка данных из файлов
		if ($error) {
			$this->echo_message(0, $error);
			exit;
		}

		if (!$xmlfiles) {
			$this->echo_message(0, 'modeFileSale(): no XML files');
			exit;
		}

		$this->log($xmlfiles, 2);

		$this->load->model('extension/exchange1c');

		foreach ($xmlfiles as $xmlfile) {

			$importFile = $cache . $xmlfile;

			// Загружаем файл
			$error = $this->model_extension_exchange1c->importFile($importFile, $this->detectFileType($importFile));
			if ($error) {
				$this->echo_message(0, $error);
				$this->log("modeFileSale(): Ошибка обработки файла: " . $importFile);
				return false;
			}

			// Удалим файл
			//$this->log("[i] Удаление файла: " . $importFile,2);
			//unlink($importFile);

		}

		$this->echo_message(1, "modeFileSale(): Successfully processed orders");
		//$this->cache->delete('order');

	} // modeFileSale()


	/**
	 * ver 4
	 * update 2017-06-10
	 * Обрабатывает *.XML файлы
	 *
	 * @param	boolean		true - ручной импорт
	 */
	public function modeImport($manual = false) {

    	$this->log('modeImport', 2);

		if ($manual) $this->log("modeImport(): Ручная загрузка данных.");

		$cache = DIR_CACHE . 'exchange1c/';
		if(!is_dir($cache)) mkdir($cache);

		// Определим имя файла
		if ($manual)

			$importFile = $manual;

		elseif (isset($this->request->get['filename']))

			$importFile = $cache . $this->request->get['filename'];

		else {

			if (!$manual) $this->echo_message(0, "modeImport(): No import file name");

			// Удалим файл
			$this->log("[i] Удаление файла: " . $importFile,2);
			unlink($importFile);

			return "modeImport(): No import file name";

		}

		// Определяем текущую локаль
		$this->load->model('extension/exchange1c');

		// Загружаем файл
		$error = $this->model_extension_exchange1c->importFile($importFile, $this->detectFileType($importFile));
		if ($error) {

			if (!$manual) {
				$this->echo_message(0, 'modeImport(): ' . $error);
				//$this->echo_message(0, "Error processing file " . $importFile);
			}

			$this->log("modeImport(): Ошибка загрузки файла: " . $importFile);

			// Удалим файл
			$this->log("[i] Удаление файла: " . $importFile,2);
			unlink($importFile);

			return $error;

		} else {
			if (!$manual) {
				$this->echo_message(1, "modeImport(): Successfully processed file " . $importFile);
			}
		}

		// Удалим файл
		$this->log("[i] Удаление файла: " . $importFile,2);
		unlink($importFile);

		$this->cache->delete('product');
		return "";

	} // modeImport()


	/**
	 * ver 2
	 * update 2017-05-28
	 * Режим запроса заказов
	 */
	public function modeQueryOrders() {

		if (!$this->checkAuthKey(true)) exit;

		$this->load->model('extension/exchange1c');

		$orders = $this->model_extension_exchange1c->queryOrders(
			array(
				 'from_date' 		=> $this->config->get('exchange1c_order_date')
				,'new_status'		=> $this->config->get('exchange1c_order_status')
				,'notify'			=> $this->config->get('exchange1c_order_notify')
				,'currency'			=> $this->config->get('exchange1c_order_currency') ? $this->config->get('exchange1c_order_currency') : 'руб.'
			)
		);
		if ($this->config->get('exchange1c_convert_orders_cp1251') == 1) {
			//echo header('Content-Type: text/html; charset=windows-1251', true);
			// посоветовал yuriygr с GitHub
			//echo iconv('utf-8', 'cp1251', $orders);
			echo iconv('utf-8', 'cp1251//TRANSLIT', $orders);
			//echo mb_convert_encoding($orders, 'cp1251//TRANSLIT', 'utf-8');
		} else {
			echo $orders;
		}

	} // modeQueryOrders()


	/**
	 * ver 3
	 * update 2017-06-01
	 * Изменение статусов заказов с момента последней выгрузки и после подтверждения получения торговой системы
	 */
	public function modeOrdersChangeStatus(){
		if (!$this->checkAuthKey(true)) exit;
		$this->load->model('extension/exchange1c');

		$result = $this->model_extension_exchange1c->queryOrdersStatus();

		if($result){

			$this->load->model('setting/setting');
			$config = $this->model_setting_setting->getSetting('exchange1c');
			$config['exchange1c_order_date'] = date('Y-m-d H:i:s');
			$this->model_setting_setting->editSetting('exchange1c', $config);
			$config['exchange1c_order_date'] = $this->config->get('exchange1c_order_date');
		}

		$this->echo_message(1,$result);

	} // modeOrdersChangeStatus()


	// -- Системные процедуры
	/**
	 * Очистка папки cache
	 */
	private function cleanCache() {
		// Проверяем есть ли директория
		$result = "";
		if (file_exists(DIR_CACHE . 'exchange1c')) {
			if (is_dir(DIR_CACHE . 'exchange1c')) {
				$this->cleanDir(DIR_CACHE . 'exchange1c/');
			}
			else {
				unlink(DIR_CACHE . 'exchange1c');
			}
		}
		@mkdir (DIR_CACHE . 'exchange1c');
		$result .= "Очищен кэш модуля: /system/storage/cache/exchange1c/*\n";

		// очистка системного кэша
		$files = glob(DIR_CACHE . 'cache.*');
		foreach ($files as $file) {
			$this->cleanDir($file);
		}
        $result .= "Очищен системный кэш: /system/storage/cache/cache*\n";

		// очистка кэша картинок
		$imgfiles = glob(DIR_IMAGE . 'cache/*');
		foreach ($imgfiles as $imgfile) {
			$this->cleanDir($imgfile);
			$this->log("Удаление картинки: " . $imgfile ,2);
		}
		$result .= "Очищен кэш картинок: /image/cache/*\n";

		return $result;

	} // cleanCache()


	/**
	 * Проверка дерева каталога для загрузки файлов
	 */
	private function checkUploadFileTree($path, $curDir = null) {
		if (!$curDir) $curDir = DIR_CACHE . 'exchange1c/';
		foreach (explode('/', $path) as $name) {
			if (!$name) continue;
			if (file_exists($curDir . $name)) {
				if (is_dir( $curDir . $name)) {
					$curDir = $curDir . $name . '/';
					continue;
				}
				unlink ($curDir . $name);
			}
			mkdir ($curDir . $name );
			$curDir = $curDir . $name . '/';
		}
	} // checkUploadFileTree()


	/**
	 * Очистка папки рекурсивно
	 */
	private function cleanDir($root, $self = false) {
		if (is_file($root)) {
			unlink($root);
		} else {
			if (substr($root, -1)!= '/') {
				$root .= '/';
			}
			$dir = dir($root);
			while ($file = $dir->read()) {
				if ($file == '.' || $file == '..') continue;
				if ($file == 'index.html') continue;
				if (file_exists($root . $file)) {
					if (is_file($root . $file)) { unlink($root . $file); continue; }
					if (is_dir($root . $file)) { $this->cleanDir($root . $file . '/', true); continue; }
					//var_dump ($file);
				}
				//var_dump($file);
			}
		}
		if ($self) {
			if(file_exists($root) && is_dir($root)) {
				rmdir($root); return 0;
			}
			//var_dump($root);
		}
		return 0;
	} // cleanDir()


	/**
	 * События
	 */
	public function eventDeleteProduct($product_id) {
		$this->load->model('extension/exchange1c');
		$this->model_extension_exchange1c->deleteLinkProduct($product_id);
	} // eventProductDelete()


	/**
	 * События
	 */
	public function eventDeleteCategory($category_id) {
		$this->load->model('extension/exchange1c');
		$this->model_extension_exchange1c->deleteLinkCategory($category_id);
	} // eventCategoryDelete()


	/**
	 * События
	 */
	public function eventDeleteManufacturer($manufacturer_id) {
		$this->load->model('extension/exchange1c');
		$this->model_extension_exchange1c->deleteLinkManufacturer($manufacturer_id);
	} // eventManufacturerDelete()


	/**
	 * Удаляет категорию и все что в ней
	 */
    public function delete($path) {
		if (is_dir($path)) {
			array_map(function($value) {
				$this->delete($value);
				rmdir($value);
			},glob($path . '/*', GLOB_ONLYDIR));
			array_map('unlink', glob($path."/*"));
		}
	} // delete()


	/**
	* Формирует архив модуля для инсталляции
	*/
	public function modeExportModule() {

		if ($this->config->get('exchange1c_export_module_to_all') != 1) {
			if (!$this->checkAccess(true)) {
				return false;
			}
		}

		$this->log("Экспорт модуля " . $this->module_name . " для IP " . $_SERVER['REMOTE_ADDR']);
		// создаем папку export в кэше

		// Короткое название версии
		$cms_short_version = substr($this->config->get('exchange1c_CMS_version'),0,3);

		$filename = DIR_CACHE . 'opencart' . $cms_short_version . '-exchange1c_' . $this->config->get('exchange1c_version') . '.ocmod.zip';
		if (is_file($filename))
			unlink($filename);

		$cms_folder = substr(DIR_APPLICATION, 0, strlen(DIR_APPLICATION) - 6);

		// Пакуем в архив
		$zip = new ZipArchive;
		$zip->open($filename, ZIPARCHIVE::CREATE);
		$zip->addFile(DIR_APPLICATION . 'controller/extension/module/exchange1c.php', 'upload/admin/controller/extension/module/exchange1c.php');
		$zip->addFile(DIR_APPLICATION . 'language/en-gb/extension/module/exchange1c.php', 'upload/admin/language/en-gb/extension/module/exchange1c.php');
		$zip->addFile(DIR_APPLICATION . 'language/ru-ru/extension/module/exchange1c.php', 'upload/admin/language/ru-ru/extension/module/exchange1c.php');
		$zip->addFile(DIR_APPLICATION . 'model/extension/exchange1c.php', 'upload/admin/model/extension/exchange1c.php');
		$zip->addFile(DIR_APPLICATION . 'view/template/extension/module/exchange1c.tpl', 'upload/admin/view/template/extension/module/exchange1c.tpl');
		$zip->addFile($cms_folder . 'export/exchange1c.php', 'upload/export/exchange1c.php');

		if (is_file($cms_folder . 'export/history.txt'))
			$zip->addFile($cms_folder . 'export/history.txt', 'history.txt');
		if (is_file($cms_folder . 'export/install.php'))
			$zip->addFile($cms_folder . 'export/install.php', 'install.php');
		if (is_file($cms_folder . 'export/README.md'))
			$zip->addFile($cms_folder . 'export/README.md', 'README.md');

		$sql = "SELECT xml FROM " . DB_PREFIX . "modification WHERE code = 'exchange1c'";
		$query = $this->db->query($sql);
		if ($query->num_rows) {
			if ($fp = fopen(DIR_CACHE . 'modification.xml', "wb")) {
				$result = fwrite($fp, $query->row['xml']);
				fclose($fp);
				$zip->addFile(DIR_CACHE . 'modification.xml', 'install.xml');
			}
		}

		$zip->close();
		if (is_file(DIR_CACHE . 'modification.xml'))
			unlink(DIR_CACHE . 'modification.xml');

		if ($fp = fopen($filename, "rb")) {
			echo '<a href="' . HTTP_CATALOG . 'system/storage/cache/' . substr($filename, strlen(DIR_CACHE)) . '">' . substr($filename, strlen(DIR_CACHE)) . '</a>';
		}

	} // modeExportModule()


	/**
	 * ver 2
	 * updare 2017-05-02
	* Удаляет модуль
	*/
	public function modeRemoveModule() {

		// Эта строчка защищает от несанкционированного удаления, для удаления модуля, закомментарьте строчку ниже
		return false;

		// Разрешен ли IP
		if ($this->config->get('exchange1c_allow_ip') != '') {
			$ip = $_SERVER['REMOTE_ADDR'];
			$allow_ips = explode("\r\n", $this->config->get('exchange1c_allow_ip'));
			if (!in_array($ip, $allow_ips)) {
				echo("Ваш IP адрес " . $ip . " не найден в списке разрешенных");
				return false;
			}
		} else {
			echo("Список IP адресов пуст, задайте адрес");
			return false;
		}

		$this->log("Удаление модуля " . $this->module_name,1);
		// создаем папку export в кэше

		$this->uninstall();

		$files = array();
		$files[] = DIR_APPLICATION . 'controller/extension/module/exchange1c.php';
		$files[] = DIR_APPLICATION . 'language/en-gb/extension/module/exchange1c.php';
		$files[] = DIR_APPLICATION . 'language/ru-ru/extension/module/exchange1c.php';
		$files[] = DIR_APPLICATION . 'model/extension/exchange1c.php';
		$files[] = DIR_APPLICATION . 'view/template/extension/module/exchange1c.tpl';
		$files[] = substr(DIR_APPLICATION, 0, strlen(DIR_APPLICATION) - 6) . 'export/exchange1c.php';
		foreach ($files as $file) {
			if (is_file($file)) {
				unlink($file);
				$this->log("Удален файл " . $file,1);
			}
		}

		// Удаление модификатора
		$this->load->model('extension/modification');
		$modification = $this->model_extension_modification->getModificationByCode('exchange1c');
		if ($modification) $this->model_extension_modification->deleteModification($modification['modification_id']);

		echo "Модуль успешно удален!";

	} // modeRemoveModule()

}
?>
