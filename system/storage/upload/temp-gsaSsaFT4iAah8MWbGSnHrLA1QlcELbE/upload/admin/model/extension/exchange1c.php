<?php

class ModelExtensionExchange1c extends Model {

	private $STORE_ID		= 0;
	private $LANG_ID		= 0;
	private $FULL_IMPORT	= false;
	private $NOW 			= '';
	private $TAB_FIELDS		= array();
	private $ERROR			= "";
	private $ERROR_NO		= '';



	/**
	 * ****************************** ОБЩИЕ ФУНКЦИИ ******************************
	 */


	/**
	 * Номер текущей версии
	 *
	 */
	public function version() {
		return "1.6.3.5";
	} // version()


	/**
	 * ver 1
	 * update 2017-04-08
	 * Пишет ошибку в лог
	 * Возвращает текст ошибки
	 */
	private function error() {
		$this->log($this->ERROR);
		return $this->ERROR;
	} // error()


	/**
	 * Пишет информацию в файл журнала
	 *
	 * @param	int				Уровень сообщения
	 * @param	string,object	Сообщение или объект
	 */
	private function log($message, $level = 1, $line = '') {
		if ($level <= $this->config->get('exchange1c_log_level')) {

			$memory_usage = '';
			if ($this->config->get('exchange1c_log_memory_use_view') == 1) {
				$memory_size = memory_get_usage() / 1024 / 1024;
				$memory_usage = sprintf("%.3f", $memory_size) . " Mb | ";
			}

			if ($this->config->get('exchange1c_log_debug_line_view') == 1) {
				if (!$line) {
					list ($di) = debug_backtrace();
					$line = sprintf("%04s",$di["line"]) . " | ";
				} else {
					$line .= " | ";
				}
			} else {
				$line = '';
			}

			if (is_array($message) || is_object($message)) {
				$this->log->write($memory_usage . $line);
				$this->log->write(print_r($message, true));
			} else {
				$this->log->write($memory_usage . $line . $message);
			}
		}
	} // log()


	/**
	 * Конвертирует XML в массив
	 *
	 * @param	array				data
	 * @param	SimpleXMLElement	XML
	 * @return	XML
	 */
	function array_to_xml($data, &$xml) {
		foreach($data as $key => $value) {
			if (is_array($value)) {
				if (!is_numeric($key)) {
					$subnode = $xml->addChild(preg_replace('/\d/', '', $key));
					$this->array_to_xml($value, $subnode);
				}
			}
			else {
				$xml->addChild($key, $value);
			}
		}
		return $xml;
	} // array_to_xml()


	/**
	 * ver 2
	 * update 2017-05-28
	 * Очистка лога
	 */
	public function clearLog() {

		$file = DIR_LOGS . $this->config->get('config_error_filename');
		$handle = fopen($file, 'w+');
		fclose($handle);

	} // clearLog()


	/**
	 * Возвращает строку даты
	 *
	 * @param	string	var
	 * @return	string
	 */
	function format($var){
		return preg_replace_callback(
		    '/\\\u([0-9a-fA-F]{4})/',
		    create_function('$match', 'return mb_convert_encoding("&#" . intval($match[1], 16) . ";", "UTF-8", "HTML-ENTITIES");'),
		    json_encode($var)
		);
	} // format()


	/**
	 * Выполняет запрос, записывает в лог в режим отладки и возвращает результат
	 */
	function query($sql){

		if ($this->config->get('exchange1c_log_debug_line_view') == 1) {
			list ($di) = debug_backtrace();
			$line = sprintf("%04s",$di["line"]);
		} else {
			$line = '';
		}

		$this->log($sql, 3, $line);
		return $this->db->query($sql);

	} // query()


	/**
	 * ver 2
	 * update 2017-04-06
	 * Проверим файл на стандарт Commerce ML
	 */
	private function checkCML($xml) {

		if ($xml['ВерсияСхемы']) {
			$this->log("Версия XML: " . (string)$xml['ВерсияСхемы'], 2);
		} else {
			$this->ERROR = "Файл не является стандартом Commerce ML!";
			$this->ERROR_NO = "0101";
			return false;
		}
		return true;

	} // checkCML()


	/**
	 * ver 2
	 * update 2017-05-28
	 * Очищает базу
	 */
	public function cleanDB() {

		$this->log("Очистка базы данных...",2);
		// Удаляем товары
		$result = "";

		$this->log("[i] Очистка таблиц товаров...",2);
		$this->query('TRUNCATE TABLE `' . DB_PREFIX . 'product`');
		$this->query('TRUNCATE TABLE `' . DB_PREFIX . 'product_attribute`');
		$this->query('TRUNCATE TABLE `' . DB_PREFIX . 'product_description`');
		$this->query('TRUNCATE TABLE `' . DB_PREFIX . 'product_discount`');
		$this->query('TRUNCATE TABLE `' . DB_PREFIX . 'product_image`');
		$this->query('TRUNCATE TABLE `' . DB_PREFIX . 'product_option`');
		$this->query('TRUNCATE TABLE `' . DB_PREFIX . 'product_option_value`');
		$this->query('TRUNCATE TABLE `' . DB_PREFIX . 'product_related`');
		$this->query('TRUNCATE TABLE `' . DB_PREFIX . 'product_reward`');
		$this->query('TRUNCATE TABLE `' . DB_PREFIX . 'product_special`');
		$this->query('TRUNCATE TABLE `' . DB_PREFIX . 'product_quantity`');
		$this->query('TRUNCATE TABLE `' . DB_PREFIX . 'product_to_1c`');
		$result .=  "Товары\n";

		$this->query('TRUNCATE TABLE `' . DB_PREFIX . 'product_to_category`');
		$this->query('TRUNCATE TABLE `' . DB_PREFIX . 'product_to_download`');
		$this->query('TRUNCATE TABLE `' . DB_PREFIX . 'product_to_layout`');
		$this->query('TRUNCATE TABLE `' . DB_PREFIX . 'product_to_store`');
		$this->query('TRUNCATE TABLE `' . DB_PREFIX . 'option_value_description`');
		$this->query('TRUNCATE TABLE `' . DB_PREFIX . 'option_description`');
		$this->query('TRUNCATE TABLE `' . DB_PREFIX . 'option_value`');
		$this->query('TRUNCATE TABLE `' . DB_PREFIX . 'order_option`');
		$this->query('TRUNCATE TABLE `' . DB_PREFIX . 'option`');
		$this->query('DELETE FROM `' . DB_PREFIX . 'url_alias` WHERE `query` LIKE "product_id=%"');
		$result .=  "Опции товаров\n";

		// Очищает таблицы категорий
		$this->log("Очистка таблиц категорий...",2);
		$this->query('TRUNCATE TABLE ' . DB_PREFIX . 'category');
		$this->query('TRUNCATE TABLE ' . DB_PREFIX . 'category_description');
		$this->query('TRUNCATE TABLE ' . DB_PREFIX . 'category_to_store');
		$this->query('TRUNCATE TABLE ' . DB_PREFIX . 'category_to_layout');
		$this->query('TRUNCATE TABLE ' . DB_PREFIX . 'category_path');
		$this->query('TRUNCATE TABLE ' . DB_PREFIX . 'category_to_1c');
		$this->query('DELETE FROM `' . DB_PREFIX . 'url_alias` WHERE `query` LIKE "category_id=%"');
		$result .=  "Категории\n";

  		// Очищает таблицы от всех производителей
		$this->log("Очистка таблиц производителей...",2);
		$this->query('TRUNCATE TABLE ' . DB_PREFIX . 'manufacturer');
		$this->query('TRUNCATE TABLE ' . DB_PREFIX . 'manufacturer_to_1c');
		$query = $this->query("SHOW TABLES FROM `" . DB_DATABASE . "` WHERE `Tables_in_" . DB_DATABASE . "` LIKE '" . DB_PREFIX . "manufacturer_description'");
		//$query = $this->db->query("SHOW TABLES FROM " . DB_DATABASE . " LIKE '" . DB_PREFIX . "manufacturer_description'");
		if ($query->num_rows) {
			$this->query('TRUNCATE TABLE ' . DB_PREFIX . 'manufacturer_description');
		}
		$this->query('TRUNCATE TABLE ' . DB_PREFIX . 'manufacturer_to_store');
		$this->query('DELETE FROM `' . DB_PREFIX . 'url_alias` WHERE `query` LIKE "manufacturer_id=%"');
		$result .=  "Производители\n";

		// Очищает атрибуты
		$this->log("Очистка таблиц атрибутов...",2);
		$this->query("TRUNCATE TABLE `" . DB_PREFIX . "attribute`");
		$this->query("TRUNCATE TABLE `" . DB_PREFIX . "attribute_description`");
		$this->query("TRUNCATE TABLE `" . DB_PREFIX . "attribute_to_1c`");
		$this->query("TRUNCATE TABLE `" . DB_PREFIX . "attribute_group`");
		$this->query("TRUNCATE TABLE `" . DB_PREFIX . "attribute_group_description`");
		$query = $this->query("SHOW TABLES FROM `" . DB_DATABASE . "` WHERE `Tables_in_" . DB_DATABASE . "` LIKE '" . DB_PREFIX . "attribute_value'");
		if ($query->num_rows) {
			$this->log("Очистка значения атрибутов",2);
			$this->query('TRUNCATE TABLE ' . DB_PREFIX . 'attribute_value');
			$result .=  "Значения атрибутов\n";
		}
		$result .=  "Атрибуты\n";

		// Удаляем все цены
		$this->log("Очистка цен...",2);
		$this->query("TRUNCATE TABLE `" . DB_PREFIX . "product_price`");
		$result .=  "Цены товаров\n";

		// Удаляем все характеристики
		$this->log("Очистка характеристик...",2);
		$this->query('TRUNCATE TABLE `' . DB_PREFIX . 'product_feature`');
		$this->query('TRUNCATE TABLE `' . DB_PREFIX . 'product_feature_value`');
		$result .=  "Характеристики\n";

		// Удаляем связи с магазинами
		$this->log("Очистка связей с магазинами...",2);
		$this->query('TRUNCATE TABLE `' . DB_PREFIX . 'store_to_1c`');
		$result .=  "Связи с магазинами\n";

		// Удаляем связи с единицами измерений
		$this->log("Очистка связей с единицами измерений...",2);
		$this->query('TRUNCATE TABLE `' . DB_PREFIX . 'unit_to_1c`');
		$result .=  "Связи с единицами измерений\n";

		// Единицы измерений товара
		$query = $this->query("SHOW TABLES FROM `" . DB_DATABASE . "` WHERE `Tables_in_" . DB_DATABASE . "` LIKE '" . DB_PREFIX . "product_unit'");
		if ($query->num_rows) {
			$this->log("Очистка единиц измерений товаров",2);
			$this->query('TRUNCATE TABLE ' . DB_PREFIX . 'product_unit');
			$result .=  "Единицы измерений товаров\n";
		}

		// Доработка от SunLit (Skype: strong_forever2000)
		// Удаляем все отзывы
		$this->log("Очистка отзывов...",2);
		$this->query('TRUNCATE TABLE `' . DB_PREFIX . 'review`');
		$result .=  "Отзывы\n";

		if ($this->config->get('exchange1c_flush_log') == 1) {
			$this->clearLog();
		}

		return $result;

	} // cleanDB()


	/**
	 * Очищает базу
	 */
	public function cleanLinks() {
		// Удаляем связи
		$result = "";

		$this->log("[i] Очистка таблиц товаров...", 2);
		$this->query('TRUNCATE TABLE `' . DB_PREFIX . 'product_to_1c`');
		$result .=  "Таблица связей товаров '" . DB_PREFIX . "product_to_1c'\n";
		$this->query('TRUNCATE TABLE `' . DB_PREFIX . 'category_to_1c`');
		$result .=  "Таблица связей категорий '" . DB_PREFIX . "category_to_1c'\n";
		$this->query('TRUNCATE TABLE `' . DB_PREFIX . 'manufacturer_to_1c`');
		$result .=  "Таблица связей производителей '" . DB_PREFIX . "manufacturer_to_1c'\n";
		$this->query("TRUNCATE TABLE `" . DB_PREFIX . "attribute_to_1c`");
		$result .=  "Таблица связей атрибутов '" . DB_PREFIX . "attribute_to_1c'\n";
		$this->query('TRUNCATE TABLE `' . DB_PREFIX . 'store_to_1c`');
		$result .=  "Таблица связей с магазинами\n";

		return $result;

	} // cleanLinks()


	/**
	 * Возвращает информацию о синхронизированных объектов с 1С товарок, категорий, атрибутов
	 */
	public function linksInfo() {

		$data = array();
		$query = $this->query('SELECT count(*) as num FROM `' . DB_PREFIX . 'product_to_1c`');
		$data['product_to_1c'] = $query->row['num'];
		$query = $this->query('SELECT count(*) as num FROM `' . DB_PREFIX . 'category_to_1c`');
		$data['category_to_1c'] = $query->row['num'];
		$query = $this->query('SELECT count(*) as num FROM `' . DB_PREFIX . 'manufacturer_to_1c`');
		$data['manufacturer_to_1c'] = $query->row['num'];
		$query = $this->query('SELECT count(*) as num FROM `' . DB_PREFIX . 'attribute_to_1c`');
		$data['attribute_to_1c'] = $query->row['num'];

		return $data;

	} // linksInfo()


	/**
	 * ver 2
	 * update 2017-04-05
	 * Удаляет все связи с товаром
	 */
	public function deleteLinkProduct($product_id) {

		$this->log("Удаление связей у товара product_id: " . $product_id, 2);

		// Удаляем линк
		if ($product_id){
			$this->query("DELETE FROM `" .  DB_PREFIX . "product_to_1c` WHERE `product_id` = " . (int)$product_id);
			$this->log("Удалена связь с товаром ID - GUID", 2);
		}

		$this->load->model('catalog/product');

		// Удаляет связи и сами файлы
		$product = $this->model_catalog_product->getProduct($product_id);
		if ($product['image']) {
			// Удаляем только в папке import_files
			if (substr($product['image'], 0, 12) == "import_files") {
				unlink(DIR_IMAGE . $product['image']);
				$this->log("Удален файл основной картинки: " . $product['image'], 2);
			}
		}

		// Удаляет связи и сами файлы
		$productImages = $this->model_catalog_product->getProductImages($product_id);
		foreach ($productImages as $image) {
			// Удаляем только в папке import_files
			if (substr($image['image'], 0, 12) == "import_files") {
				unlink(DIR_IMAGE . $image['image']);
				$this->log("Удален файл дополнительной картинки: " . $image['image'],2);
			}
		}

		// Удалим характеристики
		$this->query("DELETE FROM `" .  DB_PREFIX . "product_feature` WHERE `product_id` = " . $product_id);
		$this->query("DELETE FROM `" .  DB_PREFIX . "product_feature_value` WHERE `product_id` = " . $product_id);
		$this->log("Удалены характеристики", 2);

		// Удалим остатки
		$this->query("DELETE FROM `" .  DB_PREFIX . "product_quantity` WHERE `product_id` = " . $product_id);
		$this->log("Удалены остатки", 2);

		// Удалим единицы измерений
		$this->query("DELETE FROM `" .  DB_PREFIX . "product_unit` WHERE `product_id` = " . $product_id);
		$this->log("Удалены единицы измерения", 2);

		// Описания к картинкам
		$this->query("DELETE FROM `" .  DB_PREFIX . "product_image_description` WHERE `product_id` = " . $product_id);
		$this->log("Удалены описания к картинкам", 2);

	} // deleteLinkProduct()


	/**
	 * ver 2
	 * update 2017-04-05
	 * Удаляет все связи у категории
	 */
	public function deleteLinkCategory($category_id) {

		// Удаляем линк
		if ($category_id){
			$this->query("DELETE FROM `" .  DB_PREFIX . "category_to_1c` WHERE `category_id` = " . (int)$category_id);
			$this->log("Удалена связь у категории category_id: " . $category_id,2);
		}

	} //  deleteLinkCategory()


	/**
	 * ver 2
	 * update 2017-04-05
	 * Удаляет все связи у производителя
	 */
	public function deleteLinkManufacturer($manufacturer_id) {

		// Удаляем линк
		if ($manufacturer_id){
			$this->query("DELETE FROM `" .  DB_PREFIX . "manufacturer_to_1c` WHERE `manufacturer_id` = " . $manufacturer_id);
			$this->log("Удалена связь у производителя manufacturer_id: " . $manufacturer_id,2);
		}

	} //  deleteLinkManufacturer()


	/**
	 * Создает события
	 */
	public function setEvents() {

		// Установка событий
		$this->load->model('extension/event');
		// Удалим все события
		$this->model_extension_event->deleteEvent('exchange1c');
		// Добавим удаление связей при удалении товара
		$this->model_extension_event->addEvent('exchange1c', 'pre.admin.product.delete', 'module/exchange1c/eventDeleteProduct');
		// Добавим удаление связей при удалении категории
		$this->model_extension_event->addEvent('exchange1c', 'pre.admin.category.delete', 'module/exchange1c/eventDeleteCategory');
		// Добавим удаление связей при удалении Производителя
		$this->model_extension_event->addEvent('exchange1c', 'pre.admin.manufacturer.delete', 'module/exchange1c/eventDeleteManufacturer');
		// Добавим удаление связей при удалении Характеристики
		$this->model_extension_event->addEvent('exchange1c', 'pre.admin.option.delete', 'module/exchange1c/eventDeleteOption');

	} // setEvents()


	/**
	 * Получает language_id из code (ru, en, etc)
	 * Как ни странно, подходящей функции в API не нашлось
	 *
	 * @param	string
	 * @return	int
	 */
	public function getLanguageId($lang) {

		if ($this->LANG_ID) {
			return $this->LANG_ID;
		}
		$query = $this->query("SELECT `language_id` FROM `" . DB_PREFIX . "language` WHERE `code` = '" . $this->db->escape($lang) . "'");
		$this->LANG_ID = $query->row['language_id'];
		$this->log("Определен язык language_id: " . $this->LANG_ID, 2);
		return $this->LANG_ID;

	} // getLanguageId()


	/**
	 * ver 5
	 * update 2017-05-02
	 * Проверяет таблицы модуля
	 */
	public function checkDB() {

		$tables_db = array();
		$query = $this->query("SHOW TABLES FROM `" . DB_DATABASE . "`");
		if ($query->num_rows) {
			foreach ($query->rows as $table) {
				$tables_db[] = substr(array_shift($table), strlen(DB_PREFIX));
			}
		}

		$tables_module = array("product_to_1c","product_quantity","product_price","product_unit","category_to_1c","warehouse","product_feature","product_feature_value","store_to_1c","attribute_to_1c","manufacturer_to_1c","attribute_value","product_image_description");
		$tables_diff = array_diff($tables_module, $tables_db);

		if ($tables_diff) {
			$error = "Таблица(ы) " . implode(", ", $tables_diff) . " в базе отсутствует(ют)";
			$this->log($error);
			return $error;
		}
		return "";

	} // checkDB()


	/**
	 * ver 2
	 * update 2017-04-05
	 * Формирует строку запроса при наличии переменной
	 */
	private function setStrQuery($field_name, $type) {

		switch ($type){
			case 'string':
				return isset($data[$field_name]) ? ", " . $field_name . " = '" . $this->db->escape($data[$field_name]) . "'" : "";
			case 'int':
				return isset($data[$field_name]) ? ", " . $field_name . " = " . (int)$data[$field_name] : "";
			case 'float':
				return isset($data[$field_name]) ? ", " . $field_name . " = " . (float)$data[$field_name] : "";
		}
		return "";

	} //setStrQuery()


	/**
	 * Поиск guid товара по ID
	 */
	public function getGuidByProductId($product_id) {

		$query = $this->query("SELECT `guid` FROM `" . DB_PREFIX . "product_to_1c` WHERE `product_id` = " . $product_id);
		if ($query->num_rows) {
			return $query->row['guid'];
		}
		return '';

	} // getGuidByProductId()


	/**
	 * ****************************** ФУНКЦИИ ДЛЯ SEO ******************************
	 */


	/**
	 * Устанавливает SEO URL (ЧПУ) для заданного товара
	 * @param 	inf
	 * @param 	string
	 */
	private function setSeoURL($url_type, $element_id, $element_name) {

		// Проверка на одинаковые keyword
		$keyword = $element_name;

		// Получим все названия начинающиеся на $element_name
		$keywords = array();
		$query = $this->query("SELECT `url_alias_id`,`keyword` FROM `" . DB_PREFIX . "url_alias` WHERE `query` <> '" . $url_type . "=" . $element_id . "' AND `keyword` LIKE '" . $this->db->escape($keyword) . "%'");
		foreach ($query->rows as $row) {
			$keywords[$row['url_alias_id']] = $row['keyword'];
		}
		// Проверим на дубли
		$key = array_search($keyword, $keywords);
		$num = 0;
		while ($key) {
			// Есть дубли
			$num ++;
			$keyword = $element_name . "-" . (string)$num;
			$key = array_search($keyword, $keywords);
			if ($num > 100) {
				$this->log("[!] больше 100 дублей!", 2);
				break;
			}
		}

		$query = $this->query("SELECT `url_alias_id`,`keyword` FROM `" . DB_PREFIX . "url_alias` WHERE `query` = '" . $url_type . "=" . $element_id . "'");
		if ($query->num_rows) {
			// Обновляем если только были изменения
			$this->log("Старое keyword: " . $query->row['keyword'] . ", новое: " . $keyword);
			if ($query->row['keyword'] != $keyword) {
				$this->query("UPDATE `" . DB_PREFIX . "url_alias` SET `keyword` = '" . $this->db->escape($keyword) . "' WHERE `url_alias_id` = " . $query->row['url_alias_id']);
			}
		} else {
			$this->query("INSERT INTO `" . DB_PREFIX . "url_alias` SET `query` = '" . $url_type . "=" . $element_id ."', `keyword` = '" . $this->db->escape($keyword) . "'");
		}
		$this->log("SeoURL сформирован для категории, keyword " . $keyword);

	} // setSeoURL()


	/**
	 * Транслиетрирует RUS->ENG
	 * @param string $aString
	 * @return string type
	 */
	private function transString($aString) {

		$rus = array(" ", "/", "*", "-", "+", "`", "!", "@", "#", "$", "%", "^", "&", "*", "(", ")", "_", "+", "[", "]", "{", "}", "~", ";", ":", "'", "\"", "<", ">", ",", ".", "?", "А", "Б", "В", "Г", "Д", "Е", "З", "И", "Й", "К", "Л", "М", "Н", "О", "П", "Р", "С", "Т", "У", "Ф", "Х", "Ъ", "Ы", "Ь", "Э", "а", "б", "в", "г", "д", "е", "з", "и", "й", "к", "л", "м", "н", "о", "п", "р", "с", "т", "у", "ф", "х", "ъ", "ы", "ь", "э", "ё",  "ж",  "ц",  "ч",  "ш",  "щ",   "ю",  "я",  "Ё",  "Ж",  "Ц",  "Ч",  "Ш",  "Щ",   "Ю",  "Я");
		$lat = array("-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-",  "-", "-", "-", "-", "-", "-", "a", "b", "v", "g", "d", "e", "z", "i", "y", "k", "l", "m", "n", "o", "p", "r", "s", "t", "u", "f", "h", "",  "i", "",  "e", "a", "b", "v", "g", "d", "e", "z", "i", "j", "k", "l", "m", "n", "o", "p", "r", "s", "t", "u", "f", "h", "",  "i", "",  "e", "yo", "zh", "ts", "ch", "sh", "sch", "yu", "ya", "yo", "zh", "ts", "ch", "sh", "sch", "yu", "ya");
		$string = str_replace($rus, $lat, $aString);
		while (mb_strpos($string, '--')) {
			$string = str_replace('--', '-', $string);
		}
		$string = strtolower(trim($string, '-'));
		return $string;

	} // transString()


	/**
	 * ver 3
	 * update 2017-06-12
	 * Транслиетрирует RUS->ENG
	 * @param string $aString
	 * @return string type
	 * Автор: Константин Кирилюк
	 * url: http://www.chuvyr.ru/2013/11/translit.html
	 */
	private function translit($s, $space = '-') {

		$s = (string) $s; // преобразуем в строковое значение
		$s = strip_tags($s); // убираем HTML-теги
		$s = str_replace(array('\n', '\r'), ' ', $s); // убираем перевод каретки
		$s = trim($s); // убираем пробелы в начале и конце строки
		$s = function_exists('mb_strtolower') ? mb_strtolower($s) : strtolower($s); // переводим строку в нижний регистр (иногда надо задать локаль)
		$s = strtr($s, array('а'=>'a','б'=>'b','в'=>'v','г'=>'g','д'=>'d','е'=>'e','ё'=>'e','ж'=>'j','з'=>'z','и'=>'i','й'=>'y','к'=>'k','л'=>'l','м'=>'m','н'=>'n','о'=>'o','п'=>'p','р'=>'r','с'=>'s','т'=>'t','у'=>'u','ф'=>'f','х'=>'h','ц'=>'c','ч'=>'ch','ш'=>'sh','щ'=>'shch','ы'=>'y','э'=>'e','ю'=>'yu','я'=>'ya','ъ'=>'','ь'=>''));
		$s = preg_replace('/[^0-9a-z-_ ]/i', '', $s); // очищаем строку от недопустимых символов
  		$s = preg_replace('/\s+/', ' ', $s); // удаляем повторяющие пробелы
		$s = str_replace(' ', $space, $s); // заменяем пробелы знаком минус
  		return $s; // возвращаем результат

	} // translit()


	/**
	 * Получает SEO_URL
	 */
	private function getSeoUrl($element, $id, $last_symbol = "") {

    	$query = $this->query("SELECT `keyword` FROM `" . DB_PREFIX . "url_alias` WHERE `query` = '" . $element . "=" . (string)$id . "'");
    	if ($query->num_rows) {
    		return $query->row['keyword'] . $last_symbol;
    	}
    	return "";

	} // getSeoUrl()


	/**
	 * ver 2
	 * update 2017-04-05
	 * Получает название производителя в строку для SEO
	 */
    private function getProductManufacturerString($manufacturer_id) {

		$name = "";
		if (isset($this->TAB_FIELDS['manufacturer_description']['name'])) {
			$query = $this->query("SELECT `name` FROM `" . DB_PREFIX . "manufacturer_description` WHERE `language_id` = " . $this->LANG_ID . " AND `manufacturer_id` = " . $manufacturer_id);
			if ($query->num_rows) {
				if ($query->row['name']) {
					$name = $query->row['name'];
				}
			}
		}
		if (!$name) {
			$query = $this->query("SELECT `name` FROM `" . DB_PREFIX . "manufacturer` WHERE `manufacturer_id` = " . $manufacturer_id);
			if ($query->num_rows) {
				if ($query->row['name']) {
					$name = $query->row['name'];
				}
			}
		}
		return $name;

      } // getProductManufacturerString()


	/**
	 * ver 2
	 * update 2017-06-12
	 * Получает все категории продукта в строку для SEO
	 */
    private function getProductCategoriesString($product_id) {

 		$categories = array();

		$query = $this->query("SELECT `c`.`category_id`, `cd`.`name` FROM `" . DB_PREFIX . "category` `c` LEFT JOIN `" . DB_PREFIX . "category_description` `cd` ON (`c`.`category_id` = `cd`.`category_id`) INNER JOIN `" . DB_PREFIX . "product_to_category` `pc` ON (`pc`.`category_id` = `c`.`category_id`) WHERE `cd`.`language_id` = " . $this->LANG_ID . " AND `pc`.`product_id` = " . $product_id . " ORDER BY `c`.`sort_order`, `cd`.`name` ASC");
		foreach ($query->rows as $category) {
			$categories[] = $category['name'];
		}
		$cat_string = implode(',', $categories);
		return $cat_string;

      } // getProductCategoriesString()


	/**
	 * ver 2
	 * update 2017-06-12
	 * Получает все категории продукта в массив
	 * первым в массиме будет главная категория
	 */
    private function getProductCategories($product_id, $limit = 0) {

 		// Ограничение по количеству категорий
		$sql_limit = $limit > 0 ? ' LIMIT ' . $limit : '';

		$main_category = isset($this->TAB_FIELDS['product_to_category']['main_category']) ? ",`main_category`" : "";
		$query = $this->query("SELECT `category_id`" . $main_category . " FROM `" . DB_PREFIX . "product_to_category` WHERE `product_id` = " . $product_id . $sql_limit);
		$categories = array();
		foreach ($query->rows as $category) {
			if ($main_category && $category['main_category']) {
				// главную категорию добавляем в начало массива
				array_unshift($categories, $category['category_id']);
			} else {
				$categories[] = $category['category_id'];
			}
		}
		return $categories;

    } // getProductCategories()


	/**
	 * ver 2
	 * update 2017-04-18
	 * Генерит SEO строк. Заменяет паттерны на их значения
	 */
	private function seoGenerateString($template, $product_tags, $trans = false, $split = false) {

		// Выберем все теги которые используются в шаблоне
		preg_match_all('/\{(\w+)\}/', $template, $matches);
		$values = array();

		foreach ($matches[0] as $match) {
			$value = isset($product_tags[$match]) ? $product_tags[$match] : '';
			if ($trans) {
				$values[] = $this->translit($value);
			} else {
				$values[] = $value;
			}
		}
		$seo_string = trim(str_replace($matches[0], $values, $template));
		if ($split) {
			$seo_string = $this->getKeywordString($seo_string);
		}
		return $seo_string;

	} // seoGenerateString()


	/**
	 * Генерит ключевую строку из строки
	 */
	private function getKeywordString($str) {

		// Переведем в массив по пробелам
		$s = strip_tags($str); // убираем HTML-теги
  		$s = preg_replace("/\s+/", " ", $s); // удаляем повторяющие пробелы
  		$s = preg_replace("/\,+/", "", $s); // удаляем повторяющие запятые
  		$s = preg_replace("~(&lt;)([^&]+)(&gt;)~isu", "", $s); // удаляем HTML символы
		$s = preg_replace("![^\w\d\s]*!", "", $s); // очищаем строку от недопустимых символов
		$in_obj = explode(' ', $s);
		$out_obj = array();
		foreach ($in_obj as $s) {
			if (function_exists('mb_strlen')) {
				if (mb_strlen($s) < 3) {
					// пропускаем слова длиной менее 3 символов
					continue;
				}
			}
			$out_obj[] = $s;
		}
		// Удаляем повторяющиеся значения
		$out_obj = array_unique($out_obj);
		$str_out = implode(', ', $out_obj);

		return $str_out;

	} // getKeywordString()


	/**
	 * Генерит SEO переменные шаблона для товара
	 */
	private function seoGenerateProduct(&$data) {

		if ($this->config->get('exchange1c_seo_product_mode') == 'disable') {
			return;
		}
		// Товары, Категории
		$seo_fields = array(
			'seo_url'			=> array('trans' => true),
			'meta_title'		=> array(),
			'meta_description'	=> array(),
			'meta_keyword'		=> array(),
			'tag'				=> array()
		);
		// Сопоставляем значения
		$tags = array(
			'{name}'		=> isset($data['name']) 			? $data['name']			: '',
			'{fullname}'	=> isset($data['full_name']) 		? $data['full_name']	: $data['name'],
			'{sku}'			=> isset($data['sku'])				? $data['sku']			: '',
			'{model}'		=> isset($data['model'])			? $data['model']		: '',
			'{brand}'		=> isset($data['manufacturer_id'])	? $this->getProductManufacturerString($data['manufacturer_id']) : '',
			'{cats}'		=> $this->getProductCategoriesString($data['product_id']),
			'{prod_id}'		=> isset($data['product_id'])		? $data['product_id']	: '',
			'{cat_id}'		=> isset($data['category_id'])		? $data['category_id']	: ''
		);
		if (isset($this->TAB_FIELDS['product_description']['meta_h1'])) {
			$seo_fields['meta_h1'] = array();
		}
		// Получим поля для сравнения
		$fields_list = array();

		foreach ($seo_fields as $field=>$param) {
			if ($field == 'seo_url') {
				continue;
			}
			$fields_list[] = $field;
		}
		$fields	= implode($fields_list,', ');

		if (!isset($data['name']))
			$fields .= ", name";
		$query = $this->query("SELECT " . $fields . " FROM `" . DB_PREFIX . "product_description` WHERE `product_id` = " . $data['product_id'] . " AND `language_id` = " . $this->LANG_ID);

		foreach ($fields_list as $field) {
			$data[$field] = isset($query->row[$field]) ?  $query->row[$field] : "";
		}

		if (!isset($data['name']) && isset($query->row['name'])) {
			$data['name'] = $query->row['name'];
			$tags['{name}']	= $data['name'];
		}

		// Прочитаем старый SEO URL
		if (isset($seo_fields['seo_url'])) {
			$data['seo_url'] = $this->getSeoUrl("product_id", $data['product_id']);
			$data['seo_url_old'] = $data['seo_url'];
		}

		$update = false;
		// Формируем массив с замененными значениями
		foreach ($seo_fields as $field=>$param) {
			$template = '';
			if ($this->config->get('exchange1c_seo_product_'.$field) == 'template') {
				$template = $this->config->get('exchange1c_seo_product_'.$field.'_template');

				if (!$template) {
					unset($data[$field]);
					continue;
				}

				if ($this->config->get('exchange1c_seo_product_mode') == 'overwrite') {
					// Перезаписывать
					$old_value = '';
					if (isset($data[$field])) {
						$old_value = $data[$field];
					}
					if ($field == 'meta_keyword' || $field == 'tag') {
						$data[$field] = $this->seoGenerateString($template, $tags, isset($param['trans']), true);
					} else {
						$data[$field] = $this->seoGenerateString($template, $tags, isset($param['trans']));
					}

					// Если поле не изменилось, нет смысла его перезаписывать
					if ($old_value == $data[$field]) {
						unset($data[$field]);
					} else {
						$update = true;
						$this->log("Старое значение '".$field."' = '" . $old_value . "'", 2);
						$this->log("Новое значение '" . $field . "' = '" . $data[$field] . "'", 2);
					}

				} else {
					if (!isset($data[$field])) {
						continue;
					}
					// Только если поле пустое
					$this->log("Старое значение '".$field."' = '" . $data[$field] . "'", 2);
					if (empty($data[$field])) {
						$data[$field] = $this->seoGenerateString($template, $tags, isset($param['trans']));
						$update = true;
					} else {
						$this->log("Пропускаем '" . $field . "', т.к. не пустое", 2);
						unset($data[$field]);
						continue;
					}
				}
			} else {
				unset($data[$field]);
				continue;
			}
		}
		if (isset($data['seo_url']) && $data['product_id']) {
			if ($data['seo_url_old'] != $data['seo_url']) {
				$this->setSeoURL('product_id', $data['product_id'], $data['seo_url']);
			}
		}
		if (isset($data['seo_url_old'])) {
			unset($data['seo_url_old']);
		}
		$this->log("Сформировано SEO для товара product_id: " . $data['product_id']);
		return $update;

	} // seoGenerateProduct()


	/**
	 * ver 2
	 * update 2017-04-30
	 * Генерит SEO переменные шаблона для категории
	 */
	private function seoGenerateCategory(&$data) {

		if ($this->config->get('exchange1c_seo_category_mode') == 'disable') {
			return false;
		}

		// Товары, Категории
		$seo_fields = array(
			'seo_url'			=> array('trans' => true),
			'meta_title'		=> array(),
			'meta_description'	=> array(),
			'meta_keyword'		=> array(),
		);

		if (isset($this->TAB_FIELDS['category_description']['meta_h1'])) {
			$seo_fields['meta_h1'] = array();
		}

		// Получим поля для сравнения
		$fields_list = array();
		foreach ($seo_fields as $field=>$param) {
			if ($field == 'seo_url') {
				continue;
			}
			$fields_list[] = $field;
		}
		$fields	= implode($fields_list,', ');
		$query = $this->query("SELECT " . $fields . " FROM `" . DB_PREFIX . "category_description` WHERE `category_id` = " . $data['category_id'] . " AND `language_id` = " . $this->LANG_ID);

		// Если записей вообще небыло, присваиваем пустые
		foreach ($fields_list as $field) {
			$data[$field] = isset($query->row[$field]) ?  $query->row[$field] : "";
		}

		// Прочитаем старый SEO URL
		if (isset($seo_fields['seo_url'])) {
			$data['seo_url'] = $this->getSeoUrl("category_id", $data['category_id']);
			$data['seo_url_old'] = $data['seo_url'];
		}

		// Сопоставляем значения к тегам
		$tags = array(
			'{cat}'			=> isset($data['name']) 		? $data['name'] 		: '',
			'{cat_id}'		=> isset($data['category_id'])	? $data['category_id'] 	: ''
		);

		$update = false;
		// Формируем массив с замененными значениями
		foreach ($seo_fields as $field=>$param) {
			$template = '';
			if ($this->config->get('exchange1c_seo_category_'.$field) == 'template') {
				$template = $this->config->get('exchange1c_seo_category_'.$field.'_template');

				if (!$template) {
					unset($data[$field]);
					continue;
				}

				if ($this->config->get('exchange1c_seo_category_mode') == 'overwrite') {

					$old_value = $data[$field];

					// Перезаписывать
					$data[$field] = $this->seoGenerateString($template, $tags, isset($param['trans']));

					// Если поле не изменилось, нет смысла его перезаписывать
					if ($old_value == $data[$field]) {
						unset($data[$field]);
					} else {
						$this->log("Поле: '" . $field . "' старое: '" . $old_value . "', новое: " . $data[$field] . "'", 2);
						$update = true;
					}

				} else {
					if (!isset($data[$field])) {
						continue;
					}
					// Только если поле пустое
					$this->log("Старое значение '" . $field . "' = '" . $data[$field] . "'", 2);
					if (empty($data[$field])) {
						$data[$field] = $this->seoGenerateString($template, $tags, isset($param['trans']));
						$update = true;
					} else {
						$this->log("Пропускаем '" . $field . "', т.к. не пустое", 2);
						unset($data[$field]);
					}
				}

			} else {
				unset($data[$field]);
				continue;
			}
		}

		if (isset($data['seo_url']) && $data['category_id']) {
			if ($data['seo_url_old'] != $data['seo_url']) {
				$this->setSeoURL('category_id', $data['category_id'], $data['seo_url']);
			}
			unset($data['seo_url_old']);
		}
		if (isset($data['seo_url_old'])) {
			unset($data['seo_url_old']);
		}

		$this->log("Сформировано SEO для категории category_id: " . $data['category_id']);
		return $update;

	} // seoGenerateCategory()


	/**
	 * ver 6
	 * update 2017-05-26
	 * Генерит SEO переменные шаблона для категории
	 */
	private function seoGenerateManufacturer(&$data) {

		if ($this->config->get('exchange1c_seo_manufacturer_mode') == 'disable') {
			return false;
		}

		// Производители
		$seo_fields = array(
			'seo_url' => array('trans' => true)
		);

		if (isset($this->TAB_FIELDS['product_description'])) {
			if (isset($this->TAB_FIELDS['manufacturer_description']['meta_h1'])) {
				$seo_fields['meta_h1'] = array();
			}
			if (isset($this->TAB_FIELDS['manufacturer_description']['meta_title'])) {
				$seo_fields['meta_title'] = array();
			}
			if (isset($this->TAB_FIELDS['manufacturer_description']['meta_description'])) {
				$seo_fields['meta_description']	= array();
			}
			if (isset($this->TAB_FIELDS['manufacturer_description']['meta_keyword'])) {
				$seo_fields['meta_keyword']	= array();
			}
			// Получим поля для сравнения
			$fields_list = array();
			foreach ($seo_fields as $field => $param) {
				if ($field == 'seo_url') {
					continue;
				}
				$fields_list[] = $field;
			}
			$fields	= implode($fields_list,', ');

			if (isset($this->TAB_FIELDS['manufacturer_description'])) {
				$query = $this->query("SELECT " . $fields . " FROM `" . DB_PREFIX . "manufacturer_description` WHERE `manufacturer_id` = " . $data['manufacturer_id'] . " AND `language_id` = " . $this->LANG_ID);
				foreach ($fields_list as $field) {
					$data[$field] = isset($query->row[$field]) ?  $query->row[$field] : "";
				}
			}
		}

		// Прочитаем старый SEO URL
		if (isset($seo_fields['seo_url'])) {
			$data['seo_url'] = $this->getSeoUrl("manufacturer_id", $data['manufacturer_id']);
			$data['seo_url_old'] = $data['seo_url'];
		}

		// Сопоставляем значения к тегам
		$tags = array(
			'{brand}'		=> isset($data['name']) 			? $data['name'] 			: '',
			'{brand_id}'	=> isset($data['manufacturer_id'])	? $data['manufacturer_id'] 	: ''
		);

		$update = false;
		// Формируем массив с замененными значениями
		foreach ($seo_fields as $field=>$param) {
			$template = '';
			if ($this->config->get('exchange1c_seo_manufacturer_' . $field) == 'template') {
				$template = $this->config->get('exchange1c_seo_manufacturer_' . $field . '_template');

				if (!$template) {
					unset($data[$field]);
					continue;
				}

				if ($this->config->get('exchange1c_seo_manufacturer_mode') == 'overwrite') {

					$old_value = $data[$field];

					// Перезаписывать
					$data[$field] = $this->seoGenerateString($template, $tags, isset($param['trans']));

					// Если поле не изменилось, нет смысла его перезаписывать
					if ($old_value == $data[$field]) {
						unset($data[$field]);
					} else {
						$this->log("Значение поля:  '" . $field . "', старое:  '" . $old_value . "', новое: " . $data[$field], 2);
						$update = true;
					}

				} else {
					if (!isset($data[$field])) {
						continue;
					}
					// Только если поле пустое
					$this->log("Старое значение '" . $field . "' = '" . $data[$field] . "'", 2);
					if (empty($data[$field])) {
						$data[$field] = $this->seoGenerateString($template, $tags, isset($param['trans']));
						$update = true;
					} else {
						$this->log("Пропускаем '" . $field . "', т.к. не пустое", 2);
						unset($data[$field]);
					}
				}
			} else {
				unset($data[$field]);
				continue;
			}

		}

		if (isset($data['seo_url']) && $data['manufacturer_id']) {
			if ($data['seo_url_old'] != $data['seo_url']) {
				$this->setSeoURL('manufacturer_id', $data['manufacturer_id'], $data['seo_url']);
			}
			unset($data['seo_url_old']);
		}
		if (isset($data['seo_url_old'])) {
			unset($data['seo_url_old']);
		}

		if ($update) {
			$this->log("Сформировано SEO для производителя: " . $data['name']);
		}
		return $update;

	} // seoGenerateManufacturer()


	/**
	 * ver 2
	 * update 2017-05-03
	 * Генерит SEO переменные шаблона для товара
	 */
	public function seoGenerate() {

        $now = date('Y-m-d H:i:s');
		$result = array('error'=>'','product'=>0,'category'=>0,'manufacturer'=>0);

		$language_id = $this->getLanguageId($this->config->get('config_language'));

		// Выбрать все товары, нужны поля:
		// name, sku, model, manufacturer_id, description, product_id, category_id
		if (isset($this->TAB_FIELDS['product_description']['meta_h1'])) {
			$sql = "SELECT `p`.`product_id`, `p`.`sku`, `p`.`model`, `p`.`manufacturer_id`, `pd`.`name`, `pd`.`tag`, `pd`.`meta_title`, `pd`.`meta_description`, `pd`.`meta_keyword`, `pd`.`meta_h1` FROM `" . DB_PREFIX . "product` `p` LEFT JOIN `" . DB_PREFIX . "product_description` `pd` ON (`p`.`product_id` = `pd`.`product_id`) WHERE `pd.`language_id` = " . $language_id;
			$fields_include = 'name,tag,meta_title,meta_description,meta_keyword,meta_h1';
		} else {
			$sql = "SELECT `p`.`product_id`, `p`.`sku`, `p`.`model`, `p`.`manufacturer_id`, `pd`.`name`, `pd`.`tag`, `pd`.`meta_title`, `pd`.`meta_description`, `pd`.`meta_keyword` FROM `" . DB_PREFIX . "product` `p` LEFT JOIN `" . DB_PREFIX . "product_description` `pd` ON (`p`.`product_id` = `pd`.`product_id`) WHERE `pd`.`language_id` = " . $language_id;
			$fields_include = 'name,tag,meta_title,meta_description,meta_keyword';
		}

		$query = $this->query($sql);
		if ($query->num_rows) {
			foreach ($query->rows as $data) {

				$result['product']++;
 				$data_old = $data;
				$update = $this->seoGenerateProduct($data);

				if (!$update) {
					continue;
				}

				// Сравнение
				$fields = $this->compareArraysNew($data_old, $data, 'sku,model,manufacturer_id');

				// Если есть что обновлять
				if ($fields) {
					$this->query("UPDATE `" . DB_PREFIX . "product` SET " . $fields . ", `date_modified` = '" . $now . "' WHERE `product_id` = " . $data['product_id']);
				}

				// Сравнение
				$fields = $this->compareArraysNew($data_old, $data, $fields_include);

				// Если есть что обновлять
				if ($fields) {
					$this->query("UPDATE `" . DB_PREFIX . "product_description` SET " . $fields . " WHERE `product_id` = " . $data['product_id'] . " AND `language_id` = " . $language_id);
				}
			}
		}

		// Категории

		// Выбрать все категории, нужны поля:
		// name, sku, model, manufacturer_id, description, product_id, category_id
		if (isset($this->TAB_FIELDS['category_description']['meta_h1'])) {
			$sql = "SELECT `c`.`category_id`, `cd`.`name`, `cd`.`meta_title`, `cd`.`meta_description`, `cd`.`meta_keyword`, `cd`.`meta_h1` FROM `" . DB_PREFIX . "category` `c` LEFT JOIN `" . DB_PREFIX . "category_description` `cd` ON (`c`.`category_id` = `cd`.`category_id`) WHERE `cd`.`language_id` = " . $language_id;
			$fields_include = 'name,meta_title,meta_description,meta_keyword,meta_h1';
		} else {
			$sql = "SELECT `c`.`category_id`, `cd`.`name`, `cd`.`meta_title`, `cd`.`meta_description`, `cd`.`meta_keyword` FROM `" . DB_PREFIX . "category` `c` LEFT JOIN `" . DB_PREFIX . "category_description` `cd` ON (`c`.`category_id` = `cd`.`category_id`) WHERE `cd`.`language_id` = " . $language_id;
			$fields_include = 'name,meta_title,meta_description,meta_keyword';
		}

		$query = $this->query($sql);
		if ($query->num_rows) {
			foreach ($query->rows as $data) {

				$result['category']++;
				$this->seoGenerateCategory($data);

				// Сравнение
				$fields = $this->compareArraysNew($data_old, $data, $fields_include);

				// Если есть что обновлять
				if ($fields) {
					$this->query("UPDATE `" . DB_PREFIX . "category_description` SET " . $fields . " WHERE `category_id` = " . $data['category_id'] . " AND `language_id` = " . $language_id);
					$this->query("UPDATE `" . DB_PREFIX . "category` SET `date_modified` = '" . $now . "' WHERE `category_id` = " . $data['category_id']);
				}
			}
		}

		// Производители

		if (isset($this->TAB_FIELDS['manufacturer_description'])) {
			// Выбрать все категории, нужны поля:
			// name, sku, model, manufacturer_id, description, product_id, category_id
			if (isset($this->TAB_FIELDS['manufacturer_description']['meta_h1'])) {
				$sql = "SELECT `m`.`manufacturer_id`, `md`.`name`, `md`.`meta_title`, `md`.`meta_description`, `md`.`meta_keyword`, `md`.`meta_h1` FROM `" . DB_PREFIX . "manufacturer` `m` LEFT JOIN `" . DB_PREFIX . "manufacturer_description` `md` ON (`m`.`manufacturer_id` = `md`.`manufacturer_id`) WHERE `md`.`language_id` = " . $language_id;
				$fields_include = 'name,meta_title,meta_description,meta_keyword,meta_h1';
			} else {
				$sql = "SELECT `m`.`manufacturer_id`, `md`.`name`, `md`.`meta_title`, `md`.`meta_description`, `md`.`meta_keyword` FROM `" . DB_PREFIX . "manufacturer` `m` LEFT JOIN `" . DB_PREFIX . "manufacturer_description` `md` ON (`m`.`manufacturer_id` = `md`.`manufacturer_id`) WHERE `md`.`language_id` = " . $language_id;
				$fields_include = 'name,meta_title,meta_description,meta_keyword';
			}

			$query = $this->query($sql);
			if ($query->num_rows) {
				foreach ($query->rows as $data) {

					$result['manufacturer']++;

					$data_old = $data;
					$update = $this->seoGenerateManufacturer($data);

					if (!$update) {
						continue;
					}

					// Сравнение
					$fields = $this->compareArraysNew($data_old, $data, $fields_include);

					// Если есть что обновлять
					if ($fields) {
						$this->query("UPDATE `" . DB_PREFIX . "category_description` SET " . $fields . " WHERE `category_id` = " . $data['category_id'] . " AND `language_id` = " . $language_id);
						$this->query("UPDATE `" . DB_PREFIX . "category` SET `date_modified` = '" . $now . "' WHERE `category_id` = " . $data['category_id']);
					}
				}
			}

		}

		return $result;

	} // seoGenerate()


	/**
	 * ****************************** ФУНКЦИИ ДЛЯ ЗАГРУЗКИ КАТАЛОГА ******************************
	 */

	/**
	 * Формирует строку запроса для категории
	 */
	private function prepareStrQueryCategory($data, $mode = 'set') {

		$sql = array();

		if (isset($data['top']))
			$sql[] = $mode == 'set' ? "`top` = " .			(int)$data['top']								: "top";
		if (isset($data['column']))
			$sql[] = $mode == 'set' ? "`column` = " .		(int)$data['column']							: "column";
		if (isset($data['sort_order']))
			$sql[] = $mode == 'set' ? "`sort_order` = " . 	(int)$data['sort_order']						: "sort_order";
		if (isset($data['status']))
			$sql[] = $mode == 'set' ? "`status` = " . 		(int)$data['status']							: "status";
		if (isset($data['noindex']))
			$sql[] = $mode == 'set' ? "`noindex` = " . 		(int)$data['noindex']							: "noindex";
		if (isset($data['parent_id']))
			$sql[] = $mode == 'set' ? "`parent_id` = " . 	(int)$data['parent_id']							: "parent_id";
		if (isset($data['image']))
			$sql[] = $mode == 'set' ? "`image` = '" . 		$this->db->escape((string)$data['image']) . "'"	: "image";

		return implode(($mode = 'set' ? ', ' : ' AND '), $sql);

	} //prepareStrQueryCategory()


	/**
	 * Формирует строку запроса для описания категорий и товаров
	 */
	private function prepareStrQueryDescription($data, $mode = 'set') {

		$sql = array();
		if (isset($data['name']))
			$sql[] = $mode == 'set' 	? "`name` = '" .				$this->db->escape($data['name']) . "'"				: "`name`";
		if (isset($data['description']))
			$sql[] = $mode == 'set' 	? "`description` = '" .			$this->db->escape($data['description']) . "'"		: "`description`";
		if (isset($data['meta_title']))
			$sql[] = $mode == 'set' 	? "`meta_title` = '" .			$this->db->escape($data['meta_title']) . "'"		: "`meta_title`";
		if (isset($data['meta_h1']))
			$sql[] = $mode == 'set' 	? "`meta_h1` = '" .				$this->db->escape($data['meta_h1']) . "'"			: "`meta_h1`";
		if (isset($data['meta_description']))
			$sql[] = $mode == 'set' 	? "`meta_description` = '" .	$this->db->escape($data['meta_description']) . "'"	: "`meta_description`";
		if (isset($data['meta_keyword']))
			$sql[] = $mode == 'set' 	? "`meta_keyword` = '" .		$this->db->escape($data['meta_keyword']) . "'"		: "`meta_keyword`";
		if (isset($data['tag']))
			$sql[] = $mode == 'set' 	? "`tag` = '" .					$this->db->escape($data['tag']) . "'"				: "`tag`";

		return implode(($mode = 'set' ? ', ' : ' AND '), $sql);

	} //prepareStrQueryDescription()


	/**
	 * ver 2
	 * update 2017-04-10
	 * Подготавливает запрос для товара
	 */
	private function prepareQueryProduct($data, $mode = 'set') {

		$sql = array();
		if (isset($data['model']))
	 		$sql[] = $mode == 'set'		? "`model` = '" .				$this->db->escape($data['model']) . "'"				: "`model`";
		if (isset($data['sku']))
	 		$sql[] = $mode == 'set'		? "`sku` = '" .					$this->db->escape($data['sku']) . "'"				: "`sku`";
		if (isset($data['upc']))
	 		$sql[] = $mode == 'set'		? "`upc` = '" .					$this->db->escape($data['upc']) . "'"				: "`upc`";
		if (isset($data['ean']))
	 		$sql[] = $mode == 'set'		? "`ean` = '" .					$this->db->escape($data['ean']) . "'"				: "`ean`";
		if (isset($data['jan']))
	 		$sql[] = $mode == 'set'		? "`jan` = '" .					$this->db->escape($data['jan']) . "'"				: "`jan`";
		if (isset($data['isbn']))
	 		$sql[] = $mode == 'set'		? "`isbn` = '" .				$this->db->escape($data['isbn']) . "'"				: "`isbn`";
		if (isset($data['mpn']))
	 		$sql[] = $mode == 'set'		? "`mpn` = '" .					$this->db->escape($data['mpn']) . "'"				: "`mpn`";
		if (isset($data['location']))
	 		$sql[] = $mode == 'set'		? "`location` = '" .			$this->db->escape($data['location']) . "'"			: "`location`";
		if (isset($data['quantity']))
	 		$sql[] = $mode == 'set'		? "`quantity` = '" .			(float)$data['quantity'] . "'"						: "`quantity`";
		if (isset($data['minimum']))
	 		$sql[] = $mode == 'set'		? "`minimum` = '" .				(float)$data['minimum'] . "'"						: "`minimum`";
		if (isset($data['subtract']))
	 		$sql[] = $mode == 'set'		? "`subtract` = '" .			(int)$data['subtract'] . "'"						: "`subtract`";
		if (isset($data['stock_status_id']))
	 		$sql[] = $mode == 'set'		? "`stock_status_id` = '" .		(int)$data['stock_status_id'] . "'"					: "`stock_status_id`";
		if (isset($data['image']))
	 		$sql[] = $mode == 'set'		? "`image` = '" .				$this->db->escape($data['image']) . "'"				: "`image`";
		if (isset($data['date_available']))
	 		$sql[] = $mode == 'set'		? "`date_available` = '" .		$this->db->escape($data['date_available']) . "'"	: "`date_available`";
		if (isset($data['manufacturer_id']))
	 		$sql[] = $mode == 'set'		? "`manufacturer_id` = '" .		(int)$data['manufacturer_id'] . "'"					: "`manufacturer_id`";
		if (isset($data['shipping']))
	 		$sql[] = $mode == 'set'		? "`shipping` = '" .			(int)$data['shipping'] . "'"						: "`shipping`";
		if (isset($data['price']))
	 		$sql[] = $mode == 'set'		? "`price` = '" .				(float)$data['price'] . "'"							: "`price`";
		if (isset($data['points']))
	 		$sql[] = $mode == 'set'		? "`points` = '" .				(int)$data['points'] . "'"							: "`points`";
		if (isset($data['length']))
	 		$sql[] = $mode == 'set'		? "`length` = '" .				(float)$data['length'] . "'"						: "`length`";
		if (isset($data['width']))
	 		$sql[] = $mode == 'set'		? "`width` = '" .				(float)$data['width'] . "'"							: "`width`";
		if (isset($data['weight']))
	 		$sql[] = $mode == 'set'		? "`weight` = '" .				(float)$data['weight'] . "'"						: "`weight`";
		if (isset($data['height']))
	 		$sql[] = $mode == 'set'		? "`height` = '" .				(float)$data['height'] . "'"						: "`height`";
		if (isset($data['status']))
	 		$sql[] = $mode == 'set'		? "`status` = '" .				(int)$data['status'] . "'"							: "`status`";
		if (isset($data['noindex']))
	 		$sql[] = $mode == 'set'		? "`noindex` = '" .				(int)$data['noindex'] . "'"							: "`noindex`";
		if (isset($data['tax_class_id']))
	 		$sql[] = $mode == 'set'		? "`tax_class_id` = '" .		(int)$data['tax_class_id'] . "'"					: "`tax_class_id`";
		if (isset($data['sort_order']))
	 		$sql[] = $mode == 'set'		? "`sort_order` = '" .			(int)$data['sort_order'] . "'"						: "`sort_order`";
		if (isset($data['length_class_id']))
	 		$sql[] = $mode == 'set'		? "`length_class_id` = '" .		(int)$data['length_class_id'] . "'"					: "`length_class_id`";
		if (isset($data['weight_class_id']))
	 		$sql[] = $mode == 'set'		? "`weight_class_id` = '" .		(int)$data['weight_class_id'] . "'"					: "`weight_class_id`";

		return implode(($mode = 'set' ? ', ' : ' AND '),$sql);

	} // prepareQueryProduct()



	/**
	 * Формирует строку запроса для описания производителя
	 */
	private function prepareStrQueryManufacturerDescription($data) {

		$sql  = isset($data['description']) 		? ", `description` = '" . $this->db->escape($data['description']) . "'"					: "";
		if (isset($this->TAB_FIELDS['manufacturer_description']['name'])) {
			$sql .= isset($data['name']) 				? ", `name` = '" . $this->db->escape($data['name']) . "'" 							: "";
		}
		$sql .= isset($data['meta_description']) 	? ", `meta_description` = '" . $this->db->escape($data['meta_description']) . "'" 		: "";
		$sql .= isset($data['meta_keyword']) 		? ", `meta_keyword` = '" . $this->db->escape($data['meta_keyword']) . "'"				: "";
		$sql .= isset($data['meta_title']) 			? ", `meta_title` = '" . $this->db->escape($data['meta_title']) . "'"					: "";
		$sql .= isset($data['meta_h1']) 			? ", `meta_h1` = '" . $this->db->escape($data['meta_h1']) . "'" 						: "";

		return $sql;

	} //prepareStrQueryManufacturerDescription()


	/**
	 * Сравнивает запрос с массивом данных и формирует список измененных полей
	 */
	private function compareArrays($query, $data) {

		// Сравниваем значения полей, если есть изменения, формируем поля для запроса
		$upd_fields = array();
		if ($query->num_rows) {
			foreach($query->row as $key => $row) {
				if (!isset($data[$key])) continue;
				if ($row <> $data[$key]) {
					$upd_fields[] = "`" . $key . "` = '" . $this->db->escape($data[$key]) . "'";
					$this->log("[i] Отличается поле '" . $key . "', старое: " . $row . ", новое: " . $data[$key], 2);
				}
			}
		}

		return implode(', ', $upd_fields);

	} // compareArrays()


	/**
	 * ver 3
	 * update 2017-005-17
	 * Заполняет родительские категории у продукта
	 */
	public function fillParentsCategories($product_categories) {

		// Подгружаем только один раз
		if (empty($product_categories)) {
			$this->log("fillParentsCategories() - нет категорий, заполнение родительских категорий отменено", 2);
			return $product_categories;
		}

		$this->load->model('catalog/product');

		foreach ($product_categories as $category_id) {
			$parents = $this->findParentsCategories($category_id);
			foreach ($parents as $parent_id) {
				$key = array_search($parent_id, $product_categories);
				if ($key === false)
					$product_categories[] = $parent_id;
			}
		}

		return $product_categories;

	} // fillParentsCategories()


	/**
	 * Ищет все родительские категории
	 *
	 * @param	int
	 * @return	array
	 */
	private function findParentsCategories($category_id) {

		$result = array();
		$query = $this->query("SELECT * FROM `" . DB_PREFIX ."category` WHERE `category_id` = " . (int)$category_id);
		if (isset($query->row['parent_id'])) {
			if ($query->row['parent_id'] <> 0) {
				$result[] = $query->row['parent_id'];
				$result = array_merge($result, $this->findParentsCategories($query->row['parent_id']));
			}
		}
		return $result;

	} // findParentsCategories()


	/**
	 * Устанавливает в какой магазин загружать данные
	 */
	private function setStore($classifier_name) {

		$config_stores = $this->config->get('exchange1c_stores');
		if (!$config_stores) {
			$this->STORE_ID = 0;
			return;
		}

		// Если ничего не заполнено - по умолчанию
		foreach ($config_stores as $key => $config_store) {
			if ($classifier_name == "Классификатор (" . $config_store['name'] . ")") {
				$this->STORE_ID = $config_store['store_id'];
			}
		}
		$this->log("Установлен магазин store_id: " . $this->STORE_ID);

	} // setStore()


	/**
	 * Возвращает id по GUID
	 */
	private function getCategoryIdByGuid($guid) {

		$query = $this->query("SELECT * FROM `" . DB_PREFIX . "category_to_1c` WHERE `guid` = '" . $this->db->escape($guid) . "'");
		$category_id = isset($query->row['category_id']) ? $query->row['category_id'] : 0;

		// Проверим существование такого товара
		if ($category_id) {
			$query = $this->query("SELECT `category_id` FROM `" . DB_PREFIX . "category` WHERE `category_id` = " . (int)$category_id);
			if (!$query->num_rows) {

				// Удалим неправильную связь
				$this->deleteLinkCategory($category_id);
				$category_id = 0;
			}
		}
		return $category_id;

	} // getCategoryIdByGuid()


	/**
	 * Возвращает id по коду
	 */
	private function getCategoryIdByCode($code) {

		$query = $this->query("SELECT `category_id` FROM `" . DB_PREFIX . "category` WHERE `category_id` = " . (int)$code);
		if (isset($query->row['category_id'])) {
			return $query->row['category_id'];
		} else {
			return 0;
		}

	} // getCategoryIdByCode()


	/**
	 * Возвращает id по названию и уровню категории
	 */
	private function getCategoryIdByName($name, $parent_id = 0) {

		$query = $this->query("SELECT `c`.`category_id` FROM `" . DB_PREFIX . "category` `c` LEFT JOIN `" . DB_PREFIX. "category_description` `cd` ON (`c`.`category_id` = `cd`.`category_id`) WHERE `cd`.`name` = LOWER('" . $this->db->escape(strtolower($name)) . "') AND `cd`.`language_id` = " . $this->LANG_ID . " AND `c`.`parent_id` = " . $parent_id);
		return $query->num_rows ? $query->row['category_id'] : 0;

	} // getCategoryIdByName()


	/**
	 * Возвращает массив id,name категории по GUID
	 */
	private function getCategoryByGuid($guid) {

		$query = $this->query("SELECT `c`.`category_id`, `cd`.`name` FROM `" . DB_PREFIX . "category_to_1c` `c` LEFT JOIN `" . DB_PREFIX. "category_description` `cd` ON (`c`.`category_id` = `cd`.`category_id`) WHERE `c`.`guid` = '" . $this->db->escape($guid) . "' AND `cd`.`language_id` = " . $this->LANG_ID);
		return $query->num_rows ? $query->rows : 0;

	} // getCategoryByGuid()


	/**
	 * ver 2
	 * update 2017-05-02
	 * Обновляет описание категории
	 */
	private function updateCategoryDescription($data) {

		// Надо ли обновлять
		$fields = $this->prepareStrQueryDescription($data, 'get');
		if ($fields) {
			$query = $this->query("SELECT " . $fields . " FROM `" . DB_PREFIX . "category_description` `cd` LEFT JOIN `" . DB_PREFIX . "category_to_store` `cs` ON (`cd`.`category_id` = `cs`.`category_id`) WHERE `cd`.`category_id` = " . $data['category_id'] . " AND `cd`.`language_id` = " . $this->LANG_ID . " AND `cs`.`store_id` = " . $this->STORE_ID);
			if (!$query->num_rows) {
				$set_fields = $this->prepareStrQueryDescription($data, 'set');
				$this->query("INSERT INTO `" . DB_PREFIX . "category_description` SET " . $set_fields . ", `category_id` = " . $data['category_id'] . ", `language_id` = " . $this->LANG_ID);
			}
		} else {
			// Нечего даже обновлять
			return false;
		}

		// Сравнивает запрос с массивом данных и формирует список измененных полей
		$fields = $this->compareArrays($query, $data);

		// Если есть расхождения, производим обновление
		if ($fields) {
			$this->query("UPDATE `" . DB_PREFIX . "category_description` SET " . $fields . " WHERE `category_id` = " . $data['category_id'] . " AND `language_id` = " . $this->LANG_ID);
			$this->query("UPDATE `" . DB_PREFIX . "category` SET `date_modified` = '" . $this->NOW . "' WHERE `category_id` = " . $data['category_id']);
			$this->log("> Обновлены поля категории: '" . $fields . "'", 2);
			return true;
		}
		return false;

	} // updateCategoryDescription()


	/**
	 * Добавляет иерархию категории
	 */
	private function addHierarchical($category_id, $data) {

		// MySQL Hierarchical Data Closure Table Pattern
		$level = 0;
		$query = $this->query("SELECT * FROM `" . DB_PREFIX . "category_path` WHERE `category_id` = " . $data['parent_id'] . " ORDER BY `level` ASC");
		foreach ($query->rows as $result) {
			$this->query("INSERT INTO `" . DB_PREFIX . "category_path` SET `category_id` = " . $category_id . ", `path_id` = " . (int)$result['path_id'] . ", `level` = " . $level);
			$level++;
		}
		$this->query("INSERT INTO `" . DB_PREFIX . "category_path` SET `category_id` = " . $category_id . ", `path_id` = " . $category_id . ", `level` = " . $level);

	} // addHierarchical()


	/**
	 * Обновляет иерархию категории
	 */
	private function updateHierarchical($data) {

		// MySQL Hierarchical Data Closure Table Pattern
		$query = $this->query("SELECT * FROM `" . DB_PREFIX . "category_path` WHERE `path_id` = " . $data['category_id'] . " ORDER BY `level` ASC");

		if ($query->rows) {
			foreach ($query->rows as $category_path) {
				// Delete the path below the current one
				$this->query("DELETE FROM `" . DB_PREFIX . "category_path` WHERE `category_id` = " . (int)$category_path['category_id'] . " AND `level` < " . (int)$category_path['level']);
				$path = array();
				// Get the nodes new parents
				$query = $this->query("SELECT * FROM `" . DB_PREFIX . "category_path` WHERE `category_id` = " . $data['parent_id'] . " ORDER BY `level` ASC");
				foreach ($query->rows as $result) {
					$path[] = $result['path_id'];
				}
				// Get whats left of the nodes current path
				$query = $this->query("SELECT * FROM `" . DB_PREFIX . "category_path` WHERE `category_id` = " . $category_path['category_id'] . " ORDER BY `level` ASC");
				foreach ($query->rows as $result) {
					$path[] = $result['path_id'];
				}
				// Combine the paths with a new level
				$level = 0;
				foreach ($path as $path_id) {
					$this->query("REPLACE INTO `" . DB_PREFIX . "category_path` SET `category_id` = " . $category_path['category_id'] . ", `path_id` = " . $path_id . ", `level` = " . $level);

					$level++;
				}
			}
		} else {
			// Delete the path below the current one
			$this->query("DELETE FROM `" . DB_PREFIX . "category_path` WHERE `category_id` = " . $data['category_id']);
			// Fix for records with no paths
			$level = 0;
			$query = $this->query("SELECT * FROM `" . DB_PREFIX . "category_path` WHERE `category_id` = " . $data['parent_id'] . " ORDER BY `level` ASC");
 			foreach ($query->rows as $result) {
				$this->query("INSERT INTO `" . DB_PREFIX . "category_path` SET `category_id` = " . $data['category_id'] . ", `path_id` = " . (int)$result['path_id'] . ", `level` = " . $level);

				$level++;
			}
 			$this->query("REPLACE INTO `" . DB_PREFIX . "category_path` SET `category_id` = " . $data['category_id'] . ", `path_id` = " . $data['category_id'] . ", `level` = " . $level);
		}

		$this->log("<== updateHierarchical()", 2);

	} // updateHierarchical()


	/**
	 * Обновляет категорию
	 */
	private function updateCategory($data) {

		// Читаем старые данные
		$sql = $this->prepareStrQueryCategory($data, 'get');
		if ($sql) {
			$query = $this->query("SELECT " . $sql . " FROM `" . DB_PREFIX . "category` WHERE `category_id` = " . $data['category_id']);

			// Сравнивает запрос с массивом данных и формирует список измененных полей
			$fields = $this->compareArrays($query, $data);

			if ($fields) {
				$this->query("UPDATE `" . DB_PREFIX . "category` SET " . $fields . ", `date_modified` = '" . $this->NOW . "' WHERE `category_id` = " . $data['category_id']);

				$this->log("Обновлена категория '" . $data['name'] . "'", 2);

				// Запись иерархии категорий если были изменения
				$this->updateHierarchical($data);
			}
		} else {
			$this->log("Нет данных для обновления категории", 2);
			return false;
		}

		// SEO
		$this->seoGenerateCategory($data);

		// Если было обновление описания
		$this->updateCategoryDescription($data);

		// Очистка кэша
		$this->cache->delete('category');

	} // updateCategory()


	/**
	 * Добавляет связь категории с ТС
	 */
	private function insertCategoryLinkToGuid($category_id, $guid) {

		$this->query("INSERT INTO `" . DB_PREFIX . "category_to_1c` SET `category_id` = " . (int)$category_id . ", `guid` = '" . $this->db->escape($guid) . "'");

	}


	/**
	 * Добавляет категорию
	 */
	private function addCategory($data) {

		if ($data == false) return 0;

		$data['status'] = $this->config->get('exchange1c_status_new_category') == 1 ? 1 : 0;

		$sql = $this->prepareStrQueryCategory($data);
		if ($this->config->get('exchange1c_synchronize_by_code') == 1) {
			$query_category_id = isset($data['code']) ? ", `category_id` = " . (int)$data['code'] : "";
		} else {
			$query_category_id = "";
		}
		$this->query("INSERT INTO `" . DB_PREFIX . "category` SET " . $sql . $query_category_id . ", `date_modified` = '" . $this->NOW . "', `date_added` = '" . $this->NOW . "'");
		$data['category_id'] = $this->db->getLastId();

		// Формируем SEO
 		$this->seoGenerateCategory($data);

		// Подготовим строку запроса для описания категории
		$fields = $this->prepareStrQueryDescription($data, 'set');

		if ($fields) {
			$query = $this->query("SELECT category_id FROM `" . DB_PREFIX . "category_description` WHERE `category_id` = " . $data['category_id'] . " AND `language_id` = " . $this->LANG_ID);
			if (!$query->num_rows) {
				$this->query("INSERT INTO `" . DB_PREFIX . "category_description` SET `category_id` = " . $data['category_id'] . ", `language_id` = " . $this->LANG_ID . ", " . $fields);
			}
		}

		// Запись иерархии категорий для админки
		$this->addHierarchical($data['category_id'], $data);

		// Магазин
		$this->query("INSERT INTO `" . DB_PREFIX . "category_to_store` SET `category_id` = " . $data['category_id'] . ", `store_id` = " . $this->STORE_ID);

		// Добавим линк
		$this->insertCategoryLinkToGuid($data['category_id'], $data['guid']);

		// Чистим кэш
		$this->cache->delete('category');

		$this->log("Добавлена категория: '" . $data['name'] . "'");

		return $data['category_id'];

	} // addCategory()


	/**
	 * ver 1
	 * update 2017-04-24
	 * Парсит свойства товарных категорий из XML
	 */
	private function parseCategoryAttributes($xml, $data, $attributes) {

		if (!isset($attributes)) {
			$this->ERROR = "parseCategoryAttributes() - Классификатор не содержит атрибутов";
			return false;
		}

		foreach ($xml->Ид as $attribute) {
			$guid = (string)$xml->Ид;
			$this->log("> Свойство, Ид: " . $guid, 2);
			//if (isset($attributes[$guid])) {
			//	$this->log($attributes[$guid], 2);
			//}
		}
		return true;

	} //parseCategoryAttributes()


	/**
	 * ver 4
	 * update 2017-05-18
	 * Парсит товарные категории из классификатора
	 */
	private function parseClassifierProductCategories($xml, $parent_id = 0, $classifier) {

		$this->log($classifier, 2);

		$result = array();
		$array = $this->config->get('exchange1c_parse_categories_in_memory');

		if (!$xml->Категория) {
			$this->ERROR = "parseClassifierProductCategories() - Элемент с названием 'Категория' не найдена";
			return false;
		}
		foreach ($xml->Категория as $category){
			$data = array();
			$data['guid']			= (string)$category->Ид;
			$data['name']			= (string)$category->Наименование;
			$data['parent_id']		= $parent_id;
			$data['status']			= 1;
			if ($parent_id == 0)
				$data['top']		= 1;
			$data['category_id']	= $this->getCategoryIdByGuid($data['guid']);
			if (!$data['category_id']) {
				$this->addCategory($data);
			} else {
				$this->updateCategory($data);
			}
			if ($array == 1) {
				$result[$data['guid']] = $data['category_id'];
			}

			if ($category->Категории) {

				$result1 = $this->parseClassifierProductCategories($category->Категории, $data['category_id'], $classifier);
				if ($this->ERROR) return false;

				if ($array == 1) {
					$result = array_merge($result, $result1);
				}
			}

			// Свойства для категории
			if ($category->Свойства && isset($classifier['attributes'])) {

				$this->parseCategoryAttributes($category->Свойства, $data, $classifier['attributes']);
				if ($this->ERROR) return false;

			}

			unset($data);
		}
		return $result;

	} // parseClassifierProductCategories()


	/**
	 * ver 4
	 * update 2017-04-27
	 * Парсит группы в классификаторе в XML
	 */
	private function parseClassifierCategories($xml, $parent_id = 0, $classifier) {

		$result = array();
		$array = $this->config->get('exchange1c_parse_categories_in_memory');

		foreach ($xml->Группа as $category) {
			if (isset($category->Ид) && isset($category->Наименование) ){

				$data = array();
				$data['guid']			= (string)$category->Ид;
				if ($category->Код && $this->config->get('exchange1c_synchronize_by_code') == 1) {
					$data['code'] 		= (int)$category->Код;
					$data['category_id'] = $this->getCategoryIdByCode($data['code']);
				} else {
					$data['category_id']= $this->getCategoryIdByGuid($data['guid']);
				}
				$data['parent_id']		= $parent_id;

				// По умолчанию включена категория
				$data['status']			= 1;

				// Сортировка категории (по просьбе Val)
				if ($category->Сортировка) {
					$data['sort_order']	= (int)$category->Сортировка;
				}

				// Картинка категории (по просьбе Val)
				if ($category->Картинка) {
					$data['image']		= (string)$category->Картинка;
				}

				// Если пометка удаления есть, значит будет отключен
				if ((string)$category->ПометкаУдаления == 'true') {
					$data['status'] = 0;
				}


				if ($parent_id == 0)
					$data['top']		= 1;

				// Определяем наименование и порядок, сортировка - число до точки, наименование все что после точки
				$data['name'] = (string)$category->Наименование;
				$split = $this->splitNameStr($data['name'], false);
				if ($split['order']) {
					$data['sort_order']	= $split['order'];
				}
				if ($split['name']) {
					$data['name']	= $split['name'];
				}

				// Свойства для группы
				if ($category->ЗначенияСвойств && isset($classifier['attributes'])) {
					if (!$this->parseAttributes($category->ЗначенияСвойств, $data, $classifier['attributes'])) {
						return false;
					}
				}

				// Обработка свойств
				if (isset($data['attributes'])) {
					foreach ($data['attributes'] as $attribute) {
						if ($attribute['name'] == 'Картинка') {
							$data['image'] = "catalog/" . $attribute['value'];
							$this->log("Установлена картинка для категории из свойства = " . $data['image']);
						} elseif ($attribute['name'] == 'Сортировка') {
							$data['sort_order'] = $attribute['value'];
							$this->log("Установлена сортировка для категории из свойства = " . $data['sort_order']);
						}
					}
				}

				$this->log("- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -", 2);
				$this->log("КАТЕГОРИЯ: '" . $data['name'] . "', Ид: " . $data['guid'], 2);

				// Если не нашли категорию по Ид, пытаемся найти по имени учитывая id родительской категории
				if (!$data['category_id']) {
					$data['category_id'] = $this->getCategoryIdByName($data['name'], $parent_id);
					// Если нашли, добавляем связь
					if ($data['category_id'])
						$this->insertCategoryLinkToGuid($data['category_id'], $data['guid']);
				}

				if (!$data['category_id']) {
					if ($this->config->get('exchange1c_create_new_category') == 1) {
						$data['category_id'] = $this->addCategory($data);
					}
				} else {
					$this->updateCategory($data);
				}
				if ($array == 1) {
					$result[$data['guid']] = $data['category_id'];
				}
			}

			// Обнуляем остаток у товаров в этой категории
			if ($this->config->get('exchange1c_flush_quantity') == 'category') {
				// Обнуляем остаток только в текущем магазине
				$query = $this->query("SELECT `p`.`product_id` FROM `" . DB_PREFIX . "product` `p` LEFT JOIN `" . DB_PREFIX . "product_to_category` `p2c` ON (`p`.`product_id` = `p2c`.`product_id`) LEFT JOIN `" . DB_PREFIX . "product_to_store` `p2s` ON (`p`.`product_id` = `p2s`.`product_id`) WHERE `p2c`.`category_id` = " . $data['category_id'] . " AND `p2s`.`store_id` = " . $this->STORE_ID);
				if ($query->num_rows) {
					if ($this->config->get('exchange1c_product_disable_if_quantity_zero') == 1) {
						$status = ", `status` = 0";
					} else {
						$status = "";
					}
					foreach ($query->rows as $row) {
						$this->query("UPDATE `" . DB_PREFIX . "product` SET `quantity` = 0 " . $status . " WHERE `product_id` = " . $row['product_id']);
						$this->query("UPDATE `" . DB_PREFIX . "product_quantity` SET `quantity` = 0 WHERE `product_id` = " . $row['product_id']);
					}
				}
			}

			if ($category->Группы) {
				$result1 = $this->parseClassifierCategories($category->Группы, $data['category_id'], $classifier);
				if ($this->ERROR) return false;

				if ($array == 1) {
					$result = array_merge($result, $result1);
				}
			}
		}
		return $result;

	} // parseClassifierCategories()


	/**
	 * ******************************************* ОПЦИИ *********************************************
	 */


	/**
	 * ver 2
	 * update 2017-06-12
	 * Добавляет или получает значение опции по названию
	 */
	private function setOptionValue($option_id, $value, $sort_order = '', $image = '') {

		$option_value_id = 0;

		$data = array();
		if ($sort_order) {
			$data['sort_order'] = $sort_order;
		}
		if ($image) {
			$data['image'] = $image;
		}

		// Проверим есть ли такое значение
		$query = $this->query("SELECT `ovd`.`option_value_id`,`ov`.`sort_order`,`ov`.`image` FROM `" . DB_PREFIX . "option_value_description` `ovd` LEFT JOIN `" . DB_PREFIX . "option_value` `ov` ON (`ovd`.`option_value_id` = `ov`.`option_value_id`) WHERE `ovd`.`language_id` = " . $this->LANG_ID . " AND `ovd`.`option_id` = " . $option_id . " AND `ovd`.`name` = '" . $this->db->escape($value) . "'");
		if ($query->num_rows) {
			$option_value_id = $query->row['option_value_id'];

			// Сравнивает запрос с массивом данных и формирует список измененных полей
			$fields = $this->compareArrays($query, $data);

			// Если есть расхождения, производим обновление
			if ($fields) {
				$this->query("UPDATE `" . DB_PREFIX . "option_value` SET " . $fields . " WHERE `option_value_id` = " . $option_value_id);
				$this->log("Значение опции обновлено: '" . $value . "'");
			}

			return $option_value_id;
		}

		$sql = $sort_order == "" ? "" : ", `sort_order` = " . (int)$sort_order;
		$query = $this->query("INSERT INTO `" . DB_PREFIX . "option_value` SET `option_id` = " . $option_id . ", `image` = '" . $this->db->escape($image) . "'" . $sql);
		$option_value_id = $this->db->getLastId();

		if ($option_value_id) {
 			$query = $this->query("INSERT INTO `" . DB_PREFIX . "option_value_description` SET `option_id` = " . $option_id . ", `option_value_id` = " . $option_value_id . ", `language_id` = " . $this->LANG_ID . ", `name` = '" . $this->db->escape($value) . "'");
			$this->log("Значение опции добавлено: '" . $value . "'");
		}

		return $option_value_id;

	} // setOptionValue()



	/**
	 * Установка опции
	 */
	private function setOption($name, $type = 'select', $sort_order = 0) {

		$query = $this->query("SELECT `o`.`option_id`, `o`.`type`, `o`.`sort_order` FROM `" . DB_PREFIX . "option` `o` LEFT JOIN `" . DB_PREFIX . "option_description` `od` ON (`o`.`option_id` = `od`.`option_id`) WHERE `od`.`name` = '" . $this->db->escape($name) . "' AND `od`.`language_id` = " . $this->LANG_ID);
        if ($query->num_rows) {

			$option_id = $query->row['option_id'];

			$fields = array();
        	if ($query->row['type'] <> $type) {
        		$fields[] = "`type` = '" . $type . "'";
        	}

        	if ($sort_order) {
				if ($query->row['sort_order'] <> $sort_order) {
	        		$fields[] = "`sort_order` = " . (int)$sort_order;
	        	}
        	}
         	$fields = implode(', ', $fields);
        	if ($fields) {
				$this->query("UPDATE `" . DB_PREFIX . "option` SET " . $fields . " WHERE `option_id` = " . $option_id);
				$this->log("Опция обновлена: '" . $name . "'");
        	}

        } else {
			// Если опции нет, добавляем
			$option_id = $this->addOption($name, $type);
        }
		return $option_id;

	} // setOption()


	/**
	 * **************************************** ОПЦИИ ТОВАРА ******************************************
	 */


	/**
	 * ver 9
	 * update 2017-04-16
	 * Добавляет или обновляет опции в товаре
	 */
	private function setProductOptions($options, $product_id, $product_feature_id = 0, $new = false) {

		if (empty($options)) {
			$this->ERROR = "setProductOptions() - нет опций";
			return false;
		}

//		if (!$new) {
//			$old_option = array();
//			$old_option_value = array();
//			// Читаем старые опции товара текущей характеристики
//			$query = $this->query("SELECT `product_option_value_id`,`product_option_id` FROM `" . DB_PREFIX . "product_option_value` WHERE `product_id` = " . $product_id);
//			foreach ($query->rows as $field) {
//				$old_option[$field['product_option_id']] 				= $field['product_option_id'];
//				$old_option_value[$field['product_option_value_id']]	= $field['product_option_value_id'];
//			}
//			$this->log($old_option, 2);
//			$this->log($old_option_value, 2);
//		}

		foreach ($options as $option_value_id => $option) {

			// Запишем опции в товар
			$option['product_option_id'] = $this->setProductOption($option['option_id'], $product_id, 1, $new);
//			if (!$new) {
//				$key = array_search($option['product_option_id'], $old_option);
//				if ($key !== false) {
//					unset($old_option[$key]);
//				}
//			}

			// Запишем значения опции в товар
			$product_option_value_id = $this->setProductOptionValue($option, $product_id, $option_value_id, $new);
//			if (!$new) {
//				$key = array_search($product_option_value_id, $old_option_value);
//				if ($key !== false) {
//					unset($old_option_value[$key]);
//				}
//			}

			// Запишем значения значения характеристики
			$this->setProductFeatureValue($product_feature_id, $product_id, $product_option_value_id, $new);
//			if (!$new) {
//				$key = array_search($product_option_value_id, $old_option_value);
//				if ($key !== false) {
//					unset($old_option_value[$key]);
//				}
//			}

		}

//		if (!$new) {
//			$this->log($old_option, 2);
//			$this->log($old_option_value, 2);
//			// Удалим старые значения опции из характеристики
//			if (count($old_option)) {
//				$this->query("DELETE FROM `" . DB_PREFIX . "product_option` WHERE `product_option_id` IN (" . implode(",",$old_option) . ")");
//			}
//			if (count($old_option_value)) {
//				$this->query("DELETE FROM `" . DB_PREFIX . "product_option_value` WHERE `product_option_value_id` IN (" . implode(",",$old_option_value) . ")");
//			}
//		}

		return true;

	}  // setProductOptions()


	/**
	 * ver 3
	 * update 2017-04-17
	 * Устанавливает опцию в товар и возвращает ID
	 */
	private function setProductOption($option_id, $product_id, $required = 1, $new = false) {

		$product_option_id = 0;
		if (!$new) {
			$query = $this->query("SELECT `product_option_id` FROM `" . DB_PREFIX . "product_option` WHERE `product_id` = " . $product_id . " AND `option_id` = " . $option_id);
			if ($query->num_rows) {
				$product_option_id = $query->row['product_option_id'];
			}
		}
		if (!$product_option_id) {
			$this->query("INSERT INTO `" . DB_PREFIX . "product_option` SET `product_id` = " . $product_id . ", `option_id` = " . $option_id . ", `required` = " . $required);
			$product_option_id = $this->db->getLastId();
		}
		return $product_option_id;

	} // setProductOption()


	/**
	 * ver 6
	 * update 2017-04-18
	 * Устанавливаем значение опции в товар
	 */
	private function setProductOptionValue($option, $product_id, $option_value_id, $new = false) {

		$product_option_value_id = 0;
		if (!$new) {
			$query = $this->query("SELECT `product_option_value_id`,`quantity`,`price_prefix`,`price` FROM `" . DB_PREFIX . "product_option_value` WHERE `product_option_id` = " . $option['product_option_id'] . " AND `product_id` = " . $product_id . " AND `option_id` = " . $option['option_id'] . " AND option_value_id = " . $option_value_id);
			if ($query->num_rows) {
				$product_option_value_id = $query->row['product_option_value_id'];
			}
			// изменения
			$fields = $this->compareArrays($query, $option);
			if ($fields) {
				$this->query("UPDATE `" . DB_PREFIX . "product_option_value` SET " . $fields . " WHERE `product_option_value_id` = " . $product_option_value_id);
			}
		}
		if (!$product_option_value_id) {
			$this->query("INSERT INTO `" . DB_PREFIX . "product_option_value` SET `product_option_id` = " . $option['product_option_id'] . ", `product_id` = " . $product_id . ", `option_id` = " . $option['option_id'] . ", `option_value_id` = " . $option_value_id . ", quantity = " . (float)$option['quantity'] . ", `subtract` = " . $option['subtract']);
	 		$product_option_value_id = $this->db->getLastId();
		}
		return $product_option_value_id;

	} // setProductOptionValue()


	/**
	 * ************************************ ФУНКЦИИ ДЛЯ РАБОТЫ С ХАРАКТЕРИСТИКАМИ *************************************
	 */


	/**
	 * ver 10
	 * update 2017-06-14
	 * Создает или возвращает характеристику по Ид
	 * устанавливает цены в таблицу product_price
	 * устанавливает стандартные опции товара
	 * устанавливает остатки характеристики в таблицу product
	 */
	private function setProductFeature($feature_data, $product_id, $feature_guid, $new = false) {

		if (!$feature_guid) {
			$this->ERROR = "setProductFeature() - Не указан Ид характеристики";
			return false;
		}

		$product_feature_id = 0;
		if (!$new) {
			$query = $this->query("SELECT `product_feature_id`,`ean`,`name`,`sku` FROM `" . DB_PREFIX . "product_feature` WHERE `guid` = '" . $this->db->escape($feature_guid) . "' AND `product_id` = " . $product_id);
			if ($query->num_rows) {
				$product_feature_id = $query->row['product_feature_id'];
			}
			if ($product_feature_id) {
				// Сравнивает запрос с массивом данных и формирует список измененных полей
				$fields = $this->compareArrays($query, $feature_data);

				if ($fields) {
					$this->query("UPDATE `" . DB_PREFIX . "product_feature` SET " . $fields . " WHERE `product_feature_id` = " . $product_feature_id);
				}
			}
		}
		if (!$product_feature_id) {
			// добавляем
			$this->query("INSERT INTO `" . DB_PREFIX . "product_feature` SET `product_id` = " . $product_id . ", `guid` = '" . $this->db->escape($feature_guid) . "'");
			$product_feature_id = $this->db->getLastId();
		}

		// Опции в характеристике
		if (isset($feature_data['options'])) {
			$this->setProductOptions($feature_data['options'], $product_id, $product_feature_id, $new);
		}

		// Единицы измерения
		if (isset($feature_data['unit'])) {
			$this->log($feature_data['unit'], 2);
			$this->setProductUnit($feature_data['unit'], $product_id, $product_feature_id, $new);
  		}

		// Цены
		if (isset($feature_data['prices'])) {
			$this->setProductFeaturePrices($feature_data['prices'], $product_id, $product_feature_id, $new);
		}

		// Остатки по складам
		if (isset($feature_data['quantities'])) {
			$this->setProductQuantities($feature_data['quantities'], $product_id, $product_feature_id, $new);
			if ($this->ERROR) return false;
		}

		return $product_feature_id;

	} // setProductFeature()


	/**
	 * ver 1
	 * update 2017-04-18
	 * Устанавливаем значение характеристики
	 */
	private function setProductFeatureValue($product_feature_id, $product_id, $product_option_value_id, $new = false) {

		if (!$new) {
			$query = $this->query("SELECT * FROM `" . DB_PREFIX . "product_feature_value` WHERE `product_feature_id` = " . $product_feature_id . " AND `product_id` = " . $product_id . " AND `product_option_value_id` = " . $product_option_value_id);
			if ($query->num_rows) {
				return false;
			}
		}
		$this->query("INSERT INTO `" . DB_PREFIX . "product_feature_value` SET `product_feature_id` = " . $product_feature_id . ", `product_id` = " . $product_id . ", `product_option_value_id` = " . $product_option_value_id);
 		$product_option_value_id = $this->db->getLastId();
		return true;

	} // setProductFeatureValue()


	/**
	 * Находит характеристику товара по GUID
	 */
	private function getProductFeatureIdByGUID($feature_guid) {

		// Ищем характеристику по Ид
		$query = $this->query("SELECT `product_feature_id` FROM `" . DB_PREFIX . "product_feature` WHERE `guid` = '" . $this->db->escape($feature_guid) . "'");
		if ($query->num_rows) {
			return $query->row['product_feature_id'];
		}
		return 0;

	} // getProductFeatureIdByGUID()


	/**
	 * **************************************** ФУНКЦИИ ДЛЯ РАБОТЫ С ТОВАРОМ ******************************************
	 */


	/**
	 * ver 10
	 * update 2017-06-14
	 * Добавляет товар в базу
	 */
	private function addProduct(&$data) {

		$data['status'] = $this->config->get('exchange1c_status_new_product') == 1 ? 1 : 0;

		// Подготовим список полей по которым есть данные
		$fields = $this->prepareQueryProduct($data);
		if ($fields) {
			if ($this->config->get('exchange1c_synchronize_by_code') == 1) {
				$query_product_id = isset($data['code']) ? ", `product_id` = " . (int)$data['code'] : "";
			} else {
				$query_product_id = "";
			}

			$this->query("INSERT INTO `" . DB_PREFIX . "product` SET " . $fields . $query_product_id . ", `date_added` = '" . $this->NOW . "', `date_modified` = '" . $this->NOW . "'");
			$data['product_id'] = $this->db->getLastId();
		} else {
			// Если нет данных - выходим
			$this->ERROR = "addProduct() - нет данных";
			return false;
		}

//		// Полное наименование из 1С в товар
		if ($this->config->get('exchange1c_import_product_name') == 'fullname' && !empty($data['full_name'])) {
			if ($data['full_name'])
				$data['name'] = $data['full_name'];
		}

		// Связь с 1С только по Ид объекта из торговой системы
		$this->query("INSERT INTO `" . DB_PREFIX . "product_to_1c` SET `product_id` = " . $data['product_id'] . ", `guid` = '" . $this->db->escape($data['product_guid']) . "'");

		// Устанавливаем магазин
		$this->query("INSERT INTO `" . DB_PREFIX . "product_to_store` SET `product_id` = " . $data['product_id'] . ", `store_id` = " . $this->STORE_ID);

		// Записываем атрибуты в товар
		if (isset($data['attributes'])) {
			foreach ($data['attributes'] as $attribute) {
				$this->query("INSERT INTO `" . DB_PREFIX . "product_attribute` SET `product_id` = " . $data['product_id'] . ", `attribute_id` = " . $attribute['attribute_id'] . ", `language_id` = " . $this->LANG_ID . ", `text` = '" .  $this->db->escape($attribute['value']) . "'");
			}
		}

		// Отзывы парсятся с Яндекса в 1С, а затем на сайт
		// Доработка от SunLit (Skype: strong_forever2000)
		// Записываем отзывы в товар
		if (isset($data['review'])) {
			$this->setProductReview($data, $data['product_id']);
			if ($this->ERROR) return false;
		}

		// Категории
		if (isset($data['product_categories'])) {
			// Заполнение родительских категорий в товаре
			if ($this->config->get('exchange1c_fill_parent_cats') == 1) {
				$data['product_categories'] = $this->fillParentsCategories($data['product_categories']);
				if ($this->ERROR) return false;
			}
			$this->addProductCategories($data['product_categories'], $data['product_id']);
			if ($this->ERROR) return false;
		}

		// Картинки
		if (isset($data['images'])) {

			$this->setProductImages($data['images'], $data['product_id'], true);
			if ($this->ERROR) return false;
		}

		if (isset($data['features'])) {

			// Несколько характеристик
			foreach ($data['features'] as $feature_guid => $feature_data) {
				$this->setProductFeature($feature_data, $data['product_id'], $feature_guid, true);
				if ($this->ERROR) return false;
			}

		} elseif ($data['feature_guid']) {

			// Предложение является одной из характеристик товара
			$this->setProductFeature($data, $data['product_id'], $data['feature_guid']);
			if ($this->ERROR) return false;

		} else {

			// БЕЗ ХАРАКТЕРИСТИК
			// Установим единицу измерения
			$this->setProductUnit($data['unit'], $data['product_id'], 0, true);
		}

		// Очистим кэш товаров
		$this->cache->delete('product');
		$this->log("Товар добавлен, product_id: " . $data['product_id'],2);
		return true;

	} // addProduct()


	/**
	 * ver 2
	 * update 2017-04-16
	 * Устанавливает товар в магазин который производится загрузка
	 * Если това в этом магазине не найден, то добавляем
	 */
	private function setProductShop($product_id) {

		$query = $this->query("SELECT `store_id`  FROM `" . DB_PREFIX . "product_to_store` WHERE `product_id` = " . $product_id . " AND `store_id` = " . $this->STORE_ID);
		if (!$query->num_rows) {
			$this->query("INSERT INTO `" . DB_PREFIX . "product_to_store` SET `product_id` = " . $product_id . ", `store_id` = " . $this->STORE_ID);
			$this->log("> Добавлена привязка товара к магазину store_id: " . $this->STORE_ID,2);
		}

	} // setProductShop()


	/**
	 * ver 5
	 * update 2017-006-17
	 * Устанавливает единицу измерения товара
	 */
	private function setProductUnit($unit_data, $product_id, $product_feature_id = 0, $new = false) {

		$this->log("setProductUnit()", 2);
		$this->log($unit_data,2);

		$result = array();
		$product_unit_id = 0;
		$unit_id = 0;

		if (!isset($unit_data['unit_id'])) {

			// Поищем название единицы в классификаторе
			$where = "";
			if (isset($unit_data['guid'])) {
				$where = " WHERE `guid` = " . $unit_data['guid'];
			}

			// Если не указан Ид
			if (!$where && isset($unit_data['number_code'])) {
				$where = " WHERE `number_code` = " . $unit_data['number_code'];
			}

			// Если нет Ид и Кода, то ищем по наименованию
			if (!$where) {
				$where = " WHERE `name` = '" . $this->db->escape($unit_data['name']) . "'";
			}

			$unit_id = 0;
			$query = $this->query("SELECT `unit_id` FROM `" . DB_PREFIX . "unit_to_1c`" . $where);
			if ($query->num_rows) {
				$unit_id = $query->row['unit_id'];
			}

		} else {

			$unit_id = $unit_data['unit_id'];
		}

		// Если не определена единица из классификатора то создаем ее
		// Добавим единицу в классификатор
		if (!$unit_id) {
			if (!isset($unit_data['number_code'])) {
				$unit_split = $this->splitNameStr($unit_data['name']);
				$query = $this->query("SELECT `number_code`,`name` FROM `" . DB_PREFIX . "unit` WHERE `rus_name1` = '" . $this->db->escape($unit_split['name']) . "'");
				if ($query->num_rows) {
					$unit_data['number_code'] = $query->row['number_code'];
					if (!isset($unit_data['full_name'])) {
						$unit_data['full_name'] = $query->row['name'];
					}
				}
			}
			$number_code = isset($unit_data['number_code']) ? ", `number_code` = " . $unit_data['number_code'] : "";
			$full_name = isset($unit_data['full_name']) ? ", `full_name` = '" . $this->db->escape($unit_data['full_name']) . "'" : "";
			$this->query("INSERT INTO `" . DB_PREFIX . "unit_to_1c` SET `name` = '" . $this->db->escape($unit_data['name']) . "'" . $full_name . $number_code);
			$unit_id = $this->db->getLastId();
		}

		if (!$new) {

			// Прочитаем старые единицы, с указанным коэффициентом у товара может быть только одна единица
			$query = $this->query("SELECT `product_unit_id` FROM `" . DB_PREFIX . "product_unit` WHERE `product_id` = " . $product_id . " AND `product_feature_id` = " . $product_feature_id . " AND `unit_id` = " . $unit_id . " AND `ratio` = " . $unit_data['ratio']);

			if ($query->num_rows) {
				$product_unit_id = $query->row['product_unit_id'];
			}
		}

		// Добавим единицу в товар
		if (!$product_unit_id) {

			$this->query("INSERT INTO `" . DB_PREFIX . "product_unit` SET `product_id` = " . $product_id . ", `product_feature_id` = " . $product_feature_id . ", `unit_id` = " . $unit_id . ", `ratio` = " . $unit_data['ratio']);
			$product_unit_id = $this->db->getLastId();
		}

		$result['product_unit_id'] = $product_unit_id;
		$result['unit_id'] = $unit_id;

		return $result;

	} // setProductUnit()


	/**
	 * ver 1
	 * update 2017-04-14
	 * Добавляет в товаре категории
	 */
	private function addProductCategories($product_categories, $product_id) {

		// если в CMS ведется учет главной категории
		$main_category = isset($this->TAB_FIELDS['product_to_category']['main_category']);

		foreach ($product_categories as $index => $category_id) {
			// старой такой нет категориии
			$sql  = "INSERT INTO `" . DB_PREFIX . "product_to_category` SET `product_id` = " . $product_id . ", `category_id` = " . $category_id;
			if ($main_category) {
				$sql .= $index == 0 ? ", `main_category` = 1" : ", `main_category` = 0";
			}
			$this->query($sql);
		}

		$this->log("Категории добавлены в товар");
		return true;

	} // addProductCategories()


	/**
	 * ver 5
	 * update 2017-06-01
	 * Обновляет в товаре категории
	 */
	private function updateProductCategories($product_categories, $product_id) {

		// если в CMS ведется учет главной категории
		$main_category = isset($this->TAB_FIELDS['product_to_category']['main_category']);

		$field = "";
		if (isset($this->TAB_FIELDS['product_to_category']['main_category'])) {
			$field = ", `main_category`";
			$order_by = " ORDER BY `main_category` DESC";
		}

		$old_categories = array();
		$sql  = "SELECT `category_id`";
		$sql .= $main_category ? ", `main_category`": "";
		$sql .= "  FROM `" . DB_PREFIX . "product_to_category` WHERE `product_id` = " . $product_id;
		$sql .= $main_category ? " ORDER BY `main_category` DESC" : "";
		$query = $this->query($sql);

		foreach ($query->rows as $category) {
			$old_categories[] = $category['category_id'];
		}

		foreach ($product_categories as $index => $category_id) {
			$key = array_search($category_id, $old_categories);
			if ($key !== false) {
				unset($old_categories[$key]);
				$this->log("Категория уже есть в товаре, id: " . $category_id, 2);
			} else {
				// старой такой нет категориии
				$sql  = "INSERT INTO `" . DB_PREFIX . "product_to_category` SET `product_id` = " . $product_id . ", `category_id` = " . $category_id;
				if ($main_category) {
					$sql .= $index == 0 ? ", `main_category` = 1" : ", `main_category` = 0";
				}
				$this->query($sql);
				$this->log("Категория добавлена в товар, id: " . $category_id, 2);
			}
		}

		// Если категории товара перезаписывать, тогда удаляем которых нет в торговой системе
		if ($this->config->get('exchange1c_product_categories') == 'overwrite') {
			// Старые неиспользуемые категории удаляем
			if (count($old_categories) > 0) {
				$this->query("DELETE FROM `" . DB_PREFIX . "product_to_category` WHERE `product_id` = " . $product_id . " AND `category_id` IN (" . implode(",",$old_categories) . ")");
				$this->log("Удалены старые категории товара, id: " . implode(",",$old_categories), 2);
			}
		}

		return true;

	} // updateProductCategories()


	/**
	 * ver 2
	 * update 2017-04-14
	 * Отзывы парсятся с Яндекса в 1С, а затем на сайт
	 * Доработка от SunLit (Skype: strong_forever2000)
	 * Устанавливает отзывы в товар из массива
	 */
	private function setProductReview($data, $product_id) {

		// Проверяем
		$product_review = array();
		$query = $this->query("SELECT `guid` FROM `" . DB_PREFIX . "review` WHERE `product_id` = " . $product_id);
		foreach ($query->rows as $review) {
			$product_review[$review['guid']] = "";
		}

		foreach ($data['review'] as $property) {

			if (isset($product_review[$property['id']])) {

				$this->log("[i] Отзыв с id: '" . $property['id'] . "' есть в базе сайта. Пропускаем.",2);
				unset($product_review[$property['id']]);
			} else {
				// Добавим в товар
				$text = '<i class="fa fa-plus-square"></i> ' .$this->db->escape($property['yes']).'<br><i class="fa fa-minus-square"></i> '.$this->db->escape($property['no']).'<br>'.$this->db->escape($property['text']);
				$this->query("INSERT INTO `" . DB_PREFIX . "review` SET `guid` = '".$property['id']."',`product_id` = " . $product_id . ", `status` = 1, `author` = '" . $this->db->escape($property['name']) . "', `rating` = " . $property['rate'] . ", `text` = '" .  $text . "', `date_added` = '".$property['date']."'");
				$this->log("Отзыв от '" . $this->db->escape($property['name']) . "' записан в товар id: " . $product_id,2);
			}
		}
		$this->log("Отзывы товаров обработаны", 2);

	} // setProductReview()


	/**
	 * ver 13
	 * update 2017-06-14
	 * Обновляет товар в базе поля в таблице product
	 * Если есть характеристики, тогда получает общий остаток по уже загруженным характеристикам прибавляет текущий и обновляет в таблице product
	 */
	private function updateProduct($data) {

		$this->log($data, 2);

		// Если товар существует и полная выгрузка, и не является характеристикой
		// очистка будет происходить даже если из import были прочитаны несколько характеристик, в этом случае старые же не нужны.
 		if ($data['product_id'] && $this->FULL_IMPORT && !$data['feature_guid'])  {
			$this->cleanProductData($data['product_id']);
 		}

		//if ($this->config->get('exchange1c_disable_product_full_import') == 1) {
		//	$this->log("[!] Перед полной загрузкой товар отключается");
		//	$data['status'] = 0;
		//}

		$update = false;

		// ФИЛЬТР ОБНОВЛЕНИЯ
		// Наименование товара
		if (isset($data['name'])) {
			if ($this->config->get('exchange1c_import_product_name') == 'disable' || $data['name'] == '') {
				unset($data['name']);
				$this->log("[i] Обновление названия отключено",2);
			}
		}
		// КОНЕЦ ФИЛЬТРА

		// Записываем атрибуты в товар
		if (isset($data['attributes'])) {
			$this->updateProductAttributes($data['attributes'], $data['product_id']);
			if ($this->ERROR) return false;
		}

		// Отзывы парсятся с Яндекса в 1С, а затем на сайт
		// Доработка от SunLit (Skype: strong_forever2000)
		// Записываем отзывы в товар
		if (isset($data['review'])) {
			$this->setProductReview($data);
			if ($this->ERROR) return false;
		}

		// Категории
		if (isset($data['product_categories'])) {
			// Заполнение родительских категорий в товаре
			if ($this->config->get('exchange1c_fill_parent_cats') == 1) {
				$data['product_categories'] = $this->fillParentsCategories($data['product_categories']);
				if ($this->ERROR) return false;
			}
			$this->updateProductCategories($data['product_categories'], $data['product_id']);
			if ($this->ERROR) return false;
		}

		// Картинки
		if (isset($data['images'])) {
			$this->setProductImages($data['images'], $data['product_id']);
			if ($this->ERROR) return false;
		}

		// Предложение является одной характеристикой
		$product_feature_id = 0;
		if ($data['feature_guid']) {
			// Предложение является одной из характеристик товара
			$product_feature_id = $this->setProductFeature($data, $data['product_id'], $data['feature_guid']);
			if ($this->ERROR) return false;
		} else {
			// В предложении несколько характеристик, обычно там только опции
			if (isset($data['features'])) {
				foreach ($data['features'] as $feature_guid => $feature_data) {
					$this->setProductFeature($feature_data, $data['product_id'], $feature_guid);
					if ($this->ERROR) return false;
				}
			}
		}

		// Остатки товара по складам
		if (isset($data['quantities'])) {
			$this->setProductQuantities($data['quantities'], $data['product_id'], $product_feature_id);
			if ($this->ERROR) return false;

			// Получим общий остаток товара
			$quantity_total = $this->getProductQuantityTotal($data['product_id']);
			if ($quantity_total !== false) {
				$this->log("Остаток общий: " . $quantity_total);
				$data['quantity'] = $quantity_total;

				if ($this->config->get('exchange1c_product_disable_if_quantity_zero') == 1 && $data['quantity'] <= 0) {
					$data['status'] = 0;
					$this->log("Товар отключен, так как общий остаток товара <= 0");
				}
			}

			//unset($data['quantities']);
		}

		// цены по складам, характеристикам
		if (isset($data['prices'])) {
			// Записываем цены в акции или скидки и возвращает цену для записи в товар
			$data['price'] = $this->setProductPrices($data['prices'], $data['product_id'], $product_feature_id);
			if ($this->ERROR) return false;

			// Если это характеристика
			if ($data['feature_guid']) {
				$price = $this->getProductPriceMin($data['product_id']);
				if ($price !== false) {
					$data['price'] = $price;
					$this->log("Основная цена (мин): " . $data['price'], 2);
				}
			}
			//unset($data['prices']);
			// Отключим товар если не показывать с нулевой ценой
			if ($this->config->get('exchange1c_product_disable_if_price_zero') == 1 && $data['price'] <= 0 ) {
				$data['status'] = 0;
				$this->log("Товар отключен, так как цена <= 0");
			}
		}

		// Полное наименование из 1С в товар
		if ($this->config->get('exchange1c_import_product_name') == 'fullname' && isset($data['full_name'])) {
			if ($data['full_name']) {
				$data['name'] = $data['full_name'];
			}
		}

		// Читаем только те данные, которые получены из файла
		$fields = $this->prepareQueryProduct($data, 'get');
		if ($fields) {
			$query = $this->query("SELECT " . $fields . "  FROM `" . DB_PREFIX . "product` WHERE `product_id` = " . $data['product_id']);
		}

		// Сравнивает запрос с массивом данных и формирует список измененных полей
		$fields = $this->compareArrays($query, $data);

		// Если есть что обновлять
		if ($fields) {
			$this->query("UPDATE `" . DB_PREFIX . "product` SET " . $fields . ", `date_modified` = '" . $this->NOW . "' WHERE `product_id` = " . $data['product_id']);
			$this->log("Товар обновлен, product_id = " . $data['product_id'], 2);
			$update = true;
		} else {
			// Обновляем date_modified для того чтобы отключить те товары которые не были в выгрузке при полном обмене
			if ($this->FULL_IMPORT) {
				$this->query("UPDATE `" . DB_PREFIX . "product` SET `date_modified` = '" . $this->NOW . "' WHERE `product_id` = " . $data['product_id']);
				$this->log("В товаре обновлено поле date_modified", 2);
			}
		}

		// Устанавливаем магазин
		$this->setProductShop($data['product_id']);

		// Очистим кэш товаров
		$this->cache->delete('product');
		return $update;

	} // updateProduct()


	/**
	 * Устанавливает описание товара в базе для одного языка
	 */
	private function setProductDescription($data, $new = false) {

		$this->log("Обновление описания товара");

		if (!$new) {
			$select_fields = $this->prepareStrQueryDescription($data, 'get');
			$update_fields = false;
			if ($select_fields) {
				$query = $this->query("SELECT " . $select_fields . " FROM `" . DB_PREFIX . "product_description` WHERE `product_id` = " . $data['product_id'] . " AND `language_id` = " . $this->LANG_ID);
				if ($query->num_rows) {
					// Сравнивает запрос с массивом данных и формирует список измененных полей
					$update_fields = $this->compareArrays($query, $data);
				} else {
					$new = true;
				}
			}
			// Если есть расхождения, производим обновление
			if ($update_fields) {
				$this->query("UPDATE `" . DB_PREFIX . "product_description` SET " . $update_fields . " WHERE `product_id` = " . $data['product_id'] .  " AND `language_id` = " . $this->LANG_ID);
				$this->log("Описание товара обновлено, поля: '" . $update_fields . "'",2);
				return true;
			}
		}
		if ($new) {
			$insert_fields = $this->prepareStrQueryDescription($data, 'set');
			$this->query("INSERT INTO `" . DB_PREFIX . "product_description` SET `product_id` = " . $data['product_id'] . ", `language_id` = " . $this->LANG_ID . ", " . $insert_fields);
		}

		return false;

	} // setProductDescription()


	/**
	 * Получает product_id по артикулу
	 */
	private function getProductBySKU($sku) {

		$query = $this->query("SELECT `product_id` FROM `" . DB_PREFIX . "product` WHERE `sku` = '" . $this->db->escape($sku) . "'");
		if ($query->num_rows) {
	 		$this->log("Найден product_id: " . $query->row['product_id'] . " по артикулу '" . $sku . "'",2);
			return $query->row['product_id'];
		}
		$this->log("Не найден товар по артикулу '" . $sku . "'",2);
		return 0;

	} // getProductBySKU()


	/**
	 * Получает product_id по наименованию товара
	 */
	private function getProductByName($name) {

		$query = $this->query("SELECT `pd`.`product_id` FROM `" . DB_PREFIX . "product` `p` LEFT JOIN `" . DB_PREFIX . "product_description` `pd` ON (`p`.`product_id` = `pd`.`product_id`) WHERE `name` = LOWER('" . $this->db->escape(strtolower($name)) . "')");
		if ($query->num_rows) {
	 		$this->log("Найден product_id: " . $query->row['product_id'] . " по названию '" . $name . "'",2);
			return $query->row['product_id'];
		}
		$this->log("Не найден товар по названию '" . $name . "'",2);
		return 0;

	} // getProductByName()


	/**
	 * Получает product_id по наименованию товара
	 */
	private function getProductByEAN($ean) {

		$query = $this->query("SELECT `product_id` FROM `" . DB_PREFIX . "product` WHERE `ean` = '" . $ean . "'");
		if ($query->num_rows) {
	 		$this->log("Найден товар по штрихкоду, product_id: " . $query->row['product_id'] . " по штрихкоду '" . $ean . "'",2);
			return $query->row['product_id'];
		}
		$this->log("Не найден товар по штрихкоду '" . $ean . "'",2);
		return 0;

	} // getProductByEAN()


	/**
	 * ver 7
	 * update 2017-06-14
	 * Обновление или добавление товара
	 * вызывается при обработке каталога
	 */
 	private function setProduct(&$data) {

		// Проверка на ошибки
		if (empty($data)) {
			$this->ERROR = "setProduct() - Нет входящих данных";
			return false;
		}

		if (!$data['product_id']) {
			// Поиск существующего товара
			if (isset($data['code']) && $this->config->get('exchange1c_synchronize_by_code') == 1) {
				// Синхронизация по Коду с 1С
				$data['product_id'] = $this->getProductIdByCode($data['code']);
				$this->log("Синхронизация товара по Коду: " . $data['code'], 2);
			}
		}

		// Синхронизация по Ид
		if (!$data['product_id']) {
			if (!$data['product_guid']) {
				$this->ERROR = "setProduct() - Не задан Ид товара из торговой системы";
				return false;
			} else {
				$data['product_id'] = $this->getProductIdByGuid($data['product_guid']);
			}
		}

		if (!$data['product_id']) {
			// Синхронизация по артикулу
	 		if ($this->config->get('exchange1c_synchronize_new_product_by') == 'sku') {
				if (empty($data['sku'])) {
 					$this->log("setProduct() - При синхронизации по артикулу, артикул не должен быть пустым! Товар пропущен. Проверьте товар " . $data['name'], 2);
 					// Пропускаем товар
 					return false;
 				}
				$data['product_id'] = $this->getProductBySKU($data['sku']);
			// Синхронизация по наименованию
			} elseif ($this->config->get('exchange1c_synchronize_new_product_by') == 'name') {
				if (empty($data['name'])) {
 					$this->log("setProduct() - При синхронизации по наименованию, наименование не должно быть пустым! Товар пропущен. Проверьте товар Ид: " . $data['product_guid'], 2);
					// Пропускаем товар
					return false;
				}
				$data['product_id'] = $this->getProductByName($data['name']);
			// Синхронизация по штрихкоду
			} elseif ($this->config->get('exchange1c_synchronize_new_product_by') == 'ean') {
 				if (empty($data['ean'])) {
 					$this->log("setProduct() - При синхронизации по штрихкоду, штрихкод не должен быть пустым! Товар пропущен. Проверьте товар " . $data['name'], 2);
 					return false;
 				}
				$data['product_id'] = $this->getProductByEan($data['name']);
 			}
 			// Если нашли, создадим связь
			if ($data['product_id']) {
				// Связь с 1С только по Ид объекта из торговой системы
				$this->query("INSERT INTO `" . DB_PREFIX . "product_to_1c` SET `product_id` = " . $data['product_id'] . ", `guid` = '" . $this->db->escape($data['product_guid']) . "'");
			}
		}

		$new = false;
		// Если не найден товар...
 		if (!$data['product_id']) {
 			if ($this->config->get('exchange1c_create_new_product') == 1) {
 				$new = $this->addProduct($data);
 				if ($this->ERROR) return false;

 			} else {
				$this->log("Отключено добавление новых товаров!");
 			}
 		} else {
 			$this->updateProduct($data);
			if ($this->ERROR) return false;
 		}

		// SEO формируем когда известен product_id и товар записан
		$update = $this->seoGenerateProduct($data);
		if ($this->ERROR) return false;

		if ($update || $new) {
			// Обновляем описание товара после генерации SEO
			$this->setProductDescription($data, $new);
		}

		$this->log("setProduct(): Товар обработан");

 		return true;

 	} // setProduct()


	/**
	 * Читает реквизиты товара из XML в массив
	 */
	private function parseRequisite($xml, &$data) {

		foreach ($xml->ЗначениеРеквизита as $requisite){
			$name 	= (string)$requisite->Наименование;
			$value 	= $requisite->Значение;

			switch ($name){
				case 'Вес':
					$data['weight'] = $value ? (float)str_replace(',','.',$value) : 0;
					$this->log("> Реквизит: " . $name. " => weight",2);
				break;
				case 'ТипНоменклатуры':
					$data['item_type'] = $value ? (string)$value : '';
					$this->log("> Реквизит: " . $name. " => item_type",2);
				break;
				case 'ВидНоменклатуры':
					$data['item_view'] = $value ? (string)$value : '';
					$this->log("> Реквизит: " . $name. " => item_view",2);
				break;
				case 'ОписаниеВФорматеHTML':
					if ($value && $this->config->get('exchange1c_import_product_description') == 1) {
						$data['description'] =  (string)$value;
						$this->log("> Реквизит: " . $name. " => description (HTML format)",2);
					}
				break;
				case 'Полное наименование':
					$data['full_name'] = $value ? htmlspecialchars((string)$value) : '';
					$this->log("> Реквизит: " . $name. " => full_name",2);
				break;
				case 'ОписаниеФайла':
					$this->parseDescriptionFile((string)$value, $data);
					if ($this->ERROR) return false;
					$this->log("> Реквизит: " . $name, 2);
				break;
				case 'Производитель':
					// Устанавливаем производителя из свойства только если он не был еще загружен в секции Товар
					if ($this->config->get('exchange1c_import_product_manufacturer') == 1) {
						if (!isset($data['manufacturer_id'])) {
							$data['manufacturer_id'] = $this->setManufacturer($value);
							$this->log("> Производитель (из реквизита): '" . $value . "', id: " . $data['manufacturer_id'],2);
						}
					}
				break;
				case 'Код':
					$this->log("> Реквизит: " . $name. " => " . (string)$value, 2);
				break;
				default:
					$this->log("[!] Неиспользуемый реквизит: " . $name. " = " . (string)$value,2);
			}
		}

	} // parseRequisite()


	/**
	 * ver 2
	 * update 2017-05-22
	 * Получает путь к картинке и накладывает водяные знаки
	 */
	private function applyWatermark($filename, $watermark) {

		$filename_path = DIR_IMAGE . $filename;
		$watermark_path = DIR_IMAGE . $watermark;

		if (is_file($filename_path)) {

			// Создаем объект картинка из водяного знака и получаем информацию о картинке
			$image = new Image($filename_path);
			$image->watermark(new Image($watermark_path));

			// Сохраняем картинку с водяным знаком
			$image->save($filename_path);

			return true;
		}
		else {
			return false;
		}

	} // applyWatermark()


	/**
	 * ver 2
	 * update 2017-04-18
	 * Определяет что за файл и принимает дальнейшее действие
	 */
	private function setFile($filename, $product_id) {

		$info = pathinfo($filename);
		if (isset($info['extension'])) {

			// Если расширение txt - грузим в описание
			if ($info['extension'] == "txt") {
				$description = file_get_contents($filename);
				// если не в кодировке UTF-8, переводим
				if (!mb_check_encoding($description, 'UTF-8')) {
					$description = nl2br(htmlspecialchars(iconv('windows-1251', 'utf-8', $description)));
				}
				// обновляем только описание
				$this->setProductDescription(array('description'	=> $description, 'product_id' => $product_id));
				$this->log("> Добавлено описание товара из файла: " . $info['basename'],1);
				return true;
			}
		}
		return false;

	} // setFile())


	/**
	 * ver 3
	 * update 2017-05-19
	 * Накладывает водяной знак на картинку
	 */
	private function setWatermarkImage($image) {

		$watermark = $this->config->get('exchange1c_watermark');
		if (empty($watermark)) {
			$this->ERROR = "setWatermarkImage() - файл водяных знаков пустой";
			return false;
		}

		if ($this->applyWatermark($image, $watermark)) {
			$this->log("> Сформирован файл с водяным знаком: " . $image);
		} else {
			$this->ERROR = "setWatermarkImage() - Ошибка наложения водяного знака на картинку: '" . $image . "'";
			return false;
		}

		return true;

	} // setWatermarkImage()


	/**
	 * ver 9
	 * update 2017-06-03
	 * Устанавливает дополнительные картинки в товаре
	 */
	private function setProductImages($images_data, $product_id, $new = false) {

		$old_images = array();
		if (!$new) {
			// Прочитаем  все старые картинки
			$query = $this->query("SELECT `product_image_id`,`image` FROM `" . DB_PREFIX . "product_image` WHERE `product_id` = " . $product_id);
			foreach ($query->rows as $image) {
				$old_images[$image['product_image_id']] = $image['image'];
			}
		}

		foreach ($images_data as $index => $image_data) {

			$image 			= $image_data['file'];

			if (file_exists(DIR_IMAGE . $image)) {
				// Удалим эту картинку в кэше
				$image_info = pathinfo(DIR_IMAGE . $image);
				$this->deleteCacheImage($image_info);
			}

			// Накладываем водяные знаки
			if ($this->config->get('exchange1c_watermark')) {
				$this->setWatermarkImage($image);
				if ($this->ERROR) return false;
			}

			// Основная картинка
			if ($index == 0) continue;

			$description 	= $image_data['description'];
			$this->log("Картинка: " . $image, 2);
			$this->log("Описание: " . $description, 2);

			// Установим картинку в товар, т.е. если нет - добавим, если есть возвратим product_image_id
			$product_image_id = array_search($image, $old_images);
			if (!$product_image_id) {
				$this->query("INSERT INTO `" . DB_PREFIX . "product_image` SET `product_id` = " . $product_id . ", `image` = '" . $this->db->escape($image) . "', `sort_order` = " . $index);
				//$product_image_id = $this->db->getLastId();
				//$this->query("INSERT INTO `" . DB_PREFIX . "product_image_description` SET `product_id` = " . $product_id . ", `product_image_id` = " . $product_image_id . ", `name` = '" . $this->db->escape($description) . "', `language_id` = " . $this->LANG_ID);
				//$this->log("> Картинка дополнительная: '" . $image . "'", 2);
			} else {
				if (!$new) {
					unset($old_images[$product_image_id]);
				}
			}

		} // foreach ($images_data as $index => $image_data)

		if (!$new && $this->config->get('exchange1c_product_images_import_mode') == 'full') {
			// Удалим старые неиспользованные картинки
			$delete_images = array();
			foreach ($old_images as $product_image_id => $image) {
				//$this->log($image, 2);
				$delete_images[] = $product_image_id;
				if (is_file(DIR_IMAGE . $image)) {
					// Также удалим файл с диска
					unlink(DIR_IMAGE . $image);
					$this->log("> Удалена старая картинка: " . DIR_IMAGE . $image);
				}
			}
			if (count($delete_images)) {
				$this->query("DELETE FROM `" . DB_PREFIX . "product_image` WHERE `product_image_id` IN (" . implode(",",$delete_images) . ")");
			}
		}

	} // setProductImages()


	/**
	 * ver 3
	 * update 2017-06-04
	 * Удаляет в кэше эту картинку
	 */
	private function deleteCacheImage($image_info) {

		if (!$image_info) {
			// Нечего удалять
			return false;
		}

		// Путь в папке кэш к картинке
		$path = str_replace(DIR_IMAGE, DIR_IMAGE . "cache/" , $image_info['dirname']);

		// Откроем папку для чтения
		$delete_files = array();
		$dh = @opendir($path);

		// Если каталог не открывается
		if (!$dh) {
			$this->log("Каталог не существует: " . $path);
			return false;
		}

		while(($file = readdir($dh)) !== false) {
			$find = strstr($file, $image_info['filename']);
			if ($find != "") {
				$delete_files[] = $find;
			}
		}
		closedir($dh);

		if ($delete_files) {
			foreach ($delete_files as $filename) {
				unlink($path . "/" . $filename);
				$this->log("Удалена картинка из кэша: " . $filename);
			}
		}

		return true;

	} // deleteCacheImage()


	/**
	 * Читает описание файла из XML в массив
	 */
	private function parseDescriptionFile($value, &$data) {

		if (!$value) {
			$this->ERROR = "Описание пустое";
			return false;
		}

		if (!isset($data['description_files'])) {
			$data['description_files'] = array();
		}

		$value_array 	= explode("#", (string)$value);
		$file			= $value_array[0];
		$description 	= isset($value_array[1]) ? $value_array[1] : '';

		$data['description_files'][$file] = $description;

	} // parseDescriptionFile()


	/**
	 * ver 4
	 * update 2017-05-23
	 * Читает картинки из XML в массив
	 */
	private function parseImages($xml, $images, $descriptions) {

		if (!$xml) {
			$this->ERROR = "parseImages() - Нет картинок в XML";
			return false;
		}

		foreach ($xml as $image) {

			$image = (string)$image;

			// Пропускаем файл с пустым именем
			if (empty($image)) continue;

			// Пропускаем несуществующие файлы
			if (!file_exists(DIR_IMAGE . $image)) {
				$this->log("parseImages() - файл не существует: " . $image);
				continue;
			}

			// Обрабатываем только картинки
			$image_info = @getimagesize(DIR_IMAGE . $image);
			if ($image_info == NULL) {
				$this->log("Это не картинка: " . DIR_IMAGE . $image);
			};

			$description = "";
			if (isset($descriptions[$image])) {
				$description = $descriptions[$image];
			}

			$this->log("Картинка: " . $image, 2);
			$this->log("Описание файла: " . $description, 2);
			$images[] = array(
				'file'			=> $image,
				'description'	=> $description
			);

		}
		return $images;

	} // parseImages()


	/**
	 * Возвращает id группы для свойств
	 */
	private function setAttributeGroup($name) {

		$query = $this->query("SELECT `attribute_group_id` FROM `" . DB_PREFIX . "attribute_group_description` WHERE `name` = '" . $this->db->escape($name) . "'");
		if ($query->rows) {
	   		$this->log("Группа атрибута: '" . $name . "'", 2);
			return $query->row['attribute_group_id'];
		}

		// Добавляем группу
		$this->query("INSERT INTO `" . DB_PREFIX . "attribute_group` SET `sort_order` = 1");

		$attribute_group_id = $this->db->getLastId();
		$this->query("INSERT INTO `" . DB_PREFIX . "attribute_group_description` SET `attribute_group_id` = " . $attribute_group_id . ", `language_id` = " . $this->LANG_ID . ", `name` = '" . $this->db->escape($name) . "'");

   		$this->log("Группа атрибута добавлена: '" . $name . "'", 2);
		return $attribute_group_id;

	} // setAttributeGroup()


	/**
	 * Возвращает id атрибута из базы
	 */
	private function setAttribute($guid, $attribute_group_id, $name, $sort_order) {

		// Ищем свойства по 1С Ид
		$attribute_id = 0;
		if ($guid && $this->config->get('exchange1c_synchronize_attribute_by') == 'guid') {
			$query = $this->query("SELECT `attribute_id` FROM `" . DB_PREFIX . "attribute_to_1c` WHERE `guid` = '" . $this->db->escape($guid) . "'");
			if ($query->num_rows) {
				$attribute_id = $query->row['attribute_id'];
			}
		} else {
			// Попытаемся найти по наименованию
			$query = $this->query("SELECT `a`.`attribute_id` FROM `" . DB_PREFIX . "attribute` `a` LEFT JOIN `" . DB_PREFIX . "attribute_description` `ad` ON (`a`.`attribute_id` = `ad`.`attribute_id`) WHERE `ad`.`language_id` = " . $this->LANG_ID . " AND `ad`.`name` LIKE '" . $this->db->escape($name) . "' AND `a`.`attribute_group_id` = " . $attribute_group_id);
			if ($query->num_rows) {
				$attribute_id = $query->row['attribute_id'];
			}
		}

		// Обновление
		if ($attribute_id) {
			$query = $this->query("SELECT `a`.`attribute_group_id`,`ad`.`name` FROM `" . DB_PREFIX . "attribute` `a` LEFT JOIN `" . DB_PREFIX . "attribute_description` `ad` ON (`a`.`attribute_id` = `ad`.`attribute_id`) WHERE `ad`.`language_id` = " . $this->LANG_ID . " AND `a`.`attribute_id` = " . $attribute_id);
			if ($query->num_rows) {
				// Изменилась группа свойства
				if ($query->row['attribute_group_id'] <> $attribute_group_id) {
					$this->query("UPDATE `" . DB_PREFIX . "attribute` SET `attribute_group_id` = " . (int)$attribute_group_id . " WHERE `attribute_id` = " . $attribute_id);
					$this->log("Группа атрибута обновлена: " . $attribute_id, 2);
				}
				// Изменилось имя
				if ($query->row['name'] <> $name) {
					$this->query("UPDATE `" . DB_PREFIX . "attribute_description` SET `name` = '" . $this->db->escape($name) . "' WHERE `attribute_id` = " . $attribute_id . " AND `language_id` = " . $this->LANG_ID);
					$this->log("Атрибут обновлен: '" . $name . "'", 2);
				}
			}

			return $attribute_id;
		}

		// Добавим в базу характеристику
		$this->query("INSERT INTO `" . DB_PREFIX . "attribute` SET `attribute_group_id` = " . $attribute_group_id . ", `sort_order` = " . $sort_order);
		$attribute_id = $this->db->getLastId();
		$this->query("INSERT INTO `" . DB_PREFIX . "attribute_description` SET `attribute_id` = " . $attribute_id . ", `language_id` = " . $this->LANG_ID . ", `name` = '" . $this->db->escape($name) . "'");
		$this->log("Атрибут добавлен: '" . $name . "'", 2);


		if ($this->config->get('exchange1c_synchronize_attribute_by') == 'guid') {
			// Добавляем ссылку для 1С Ид
			$this->query("INSERT INTO `" .  DB_PREFIX . "attribute_to_1c` SET `attribute_id` = " . $attribute_id . ", `guid` = '" . $this->db->escape($guid) . "'");
		}

		return $attribute_id;

	} // setAttribute()


	/**
	 * ver 2
	 * update 2017-04-27
	 * Загружает значения атрибута (Свойства из 1С)
	 */
	private function parseAttributesValues($xml, $attribute_id = 0) {

		$data = array();
		if (!$xml) {
			return $data;
		}

		if ($xml->ПометкаУдаления) {
			$delete = (string)$xml->ПометкаУдаления == 'true' ? true : false;
		} else {
			$delete = false;
		}

		if ($xml->ВариантыЗначений) {
			if ($xml->ВариантыЗначений->Справочник) {
				foreach ($xml->ВариантыЗначений->Справочник as $item) {
					$value = trim(htmlspecialchars((string)$item->Значение, 2));
					$guid = (string)$item->ИдЗначения;

					if (!$value) {
						continue;
					}

					$query = $this->query("SELECT `attribute_value_id`,`name` FROM `" . DB_PREFIX . "attribute_value` WHERE `guid` = '" . $this->db->escape($guid) . "'");
					if ($query->num_rows) {
						if ($delete) {
							$this->query("DELETE FROM `" . DB_PREFIX . "attribute_value` WHERE `guid` = '" . $this->db->escape($guid) . "'");
							$value_id = 0;
							$this->log("Значение атрибута удалено (пометка удаления в ТС): " . $value,2);
						} else {
							if ($query->row['name'] <> $value) {
								$this->query("UPDATE `" . DB_PREFIX . "attribute_value` SET `name` = '" . $this->db->escape($value) . "' WHERE `attribute_value_id` = " . $query->row['attribute_value_id']);
								$this->log("Значение атрибута обновлено: " . $value, 2);
							}
							$value_id = $query->row['attribute_value_id'];
						}

					} else {
						if (!$delete) {
							if ($attribute_id) {
								$query = $this->query("INSERT INTO `" . DB_PREFIX . "attribute_value` SET `attribute_id` = " . $attribute_id . ", `guid` = '" . $this->db->escape($guid) . "', `name` = '" . $this->db->escape($value) . "'");
								$value_id = $this->db->getlastId();
								$this->log("Значение атрибута добавлено: " . $value, 2);
							} else {
								$value_id = 0;
							}
						} else {
							$this->log("Значение атрибута было удалено (помечен на удаление в ТС): " . $value, 2);
							$value_id = 0;
						}
					}

					$data[$guid] = array(
						'name'		=> $value,
						'value_id'	=> $value_id
					);

				}
			}
		}
		return $data;

	} // parseAttributesValues()


	/**
	 * Загружает атрибуты (Свойства из 1С) в классификаторе
	 */
	private function parseClassifierAttributes($xml) {

		$data = array();
		$sort_order = 0;
		if ($xml->Свойство) {
			$properties = $xml->Свойство;
		} else {
			$properties = $xml->СвойствоНоменклатуры;
		}

		foreach ($properties as $property) {

			$name 		= trim((string)$property->Наименование);
			$guid		= (string)$property->Ид;

			// Название группы свойств по умолчанию (в дальнейшем сделать определение в настройках)
			$group_name = "Свойства";

			// Определим название группы в название свойства в круглых скобках в конце названия
			$name_split = $this->splitNameStr($name);
			//$this->log($name_split, 2);
			if ($name_split['option']) {
				$group_name = $name_split['option'];
				$this->log("> Группа свойства: " . $group_name, 2);
			}
			$name = $name_split['name'];
			// Установим группу для свойств
			$attribute_group_id = $this->setAttributeGroup($group_name);

			// Использование
			if ($property->ИспользованиеСвойства) {
				$status = (string)$property->ИспользованиеСвойства == 'true' ? 1 : 0;
			} else {
				$status = 1;
			}

			// Для товаров
			if ($property->ДляТоваров) {
				$for_product = (string)$property->ДляТоваров == 'true' ? 1 : 0;
			} else {
				$for_product = 1;
			}

			// Обязательное
			if ($property->Обязательное) {
				$required = (string)$property->Обязательное == 'true' ? 1 : 0;
			} else {
				$required = 0;
			}

			// Множественное
			if ($property->Множественное) {
				$multiple = (string)$property->Множественное == 'true' ? 1 : 0;
			} else {
				$multiple = 0;
			}

			if ($property->ДляПредложений) {
				// Свойства для характеристик скорее всего
				if ((string)$property->ДляПредложений == 'true') {
					$this->log("> Свойство '" . $name . "' только для предложений, в атрибуты не будет добавлено", 2);
					continue;
				}
			}

			switch ($name) {
				case 'Производитель':
					$values = $this->parseAttributesValues($property);
					foreach ($values as $manufacturer_guid => $value) {
						$this->setManufacturer($value['name'], $manufacturer_guid);
					}
				//break;
				case 'Изготовитель':
					$values = $this->parseAttributesValues($property);
					foreach ($values as $manufacturer_guid => $value) {
						$this->setManufacturer($value['name'], $manufacturer_guid);
					}
				//break;
				default:
					$attribute_id = $this->setAttribute($guid, $attribute_group_id, $name, $sort_order);
					$values = $this->parseAttributesValues($property, $attribute_id);
					$data[$guid] = array(
						'name'			=> $name,
						'attribute_id'	=> $attribute_id,
						'values'		=> $values,
						'for_product'	=> $for_product,
						'status'		=> $status,
						'required'		=> $required,
						'multiple'		=> $multiple
					);

					$sort_order ++;
			}

		}

		$this->log("Атрибутов прочитано: " . sizeof($properties), 2);
		return $data;

	} // parseClassifierAttributes()


	/**
	 * Читает свойства из базы данных в массив
	 */
	private function getAttributes() {

		$data = array();

		$query_attribute = $this->query("SELECT `a`.`attribute_id`, `ad`.`name`, `a2c`.`guid` FROM `" . DB_PREFIX . "attribute` `a` LEFT JOIN `" . DB_PREFIX . "attribute_description` `ad` ON (`a`.`attribute_id` = `ad`.`attribute_id`) LEFT JOIN `" . DB_PREFIX . "attribute_to_1c` `a2c` ON (`a`.`attribute_id` = `a2c`.`attribute_id`) WHERE `ad`.`language_id` = " . $this->LANG_ID);
		if ($query_attribute->num_rows) {
			foreach ($query_attribute->rows as $row_attribute) {

				$attribute_guid = $row_attribute['guid'];
				$attribute_id = $row_attribute['attribute_id'];
				if (!isset($data[$attribute_guid])) {
					$data[$attribute_guid] = array(
						'name'			=> $row_attribute['name'],
						'attribute_id'	=> $attribute_id,
						'values'		=> array()
					);
				}

				$query_value = $this->query("SELECT `attribute_value_id`, `name`, `guid` FROM `" . DB_PREFIX . "attribute_value` WHERE `attribute_id` = " . $attribute_id);

				if ($query_value->num_rows) {
					foreach ($query_value->rows as $row_value) {

						$values = &$data[$attribute_guid]['values'];

						$attribute_value_guid = $row_value['guid'];
						if (!isset($values[$attribute_value_guid])) {
							$values[$attribute_value_guid] = array(
								'name'		=> $row_value['name'],
								'value_id'	=> $row_value['attribute_value_id']
							);
						}
					}
				}
			}
		}

		$this->log("Свойства (атрибуты) получены из БД",2);
		return $data;

	}  // getAttributes()


	/**
	 * ver 2
	 * update 2017-05-26
	 * Читает свойства из объектов (товар, категория) и записывает их в массив
	 */
	private function parseAttributes($xml, &$data, &$classifier) {

		$product_attributes = array();
        $error = "";

		if (!isset($classifier['attributes'])) {
			$classifier['attributes'] = $this->getAttributes();
			if ($this->ERROR) {
				return false;
			}
		}
		$attributes = $classifier['attributes'];

		$attributes_filter = $this->config->get('exchange1c_properties');

		// Предопределенные названия свойств
		$predefined_attributes = array(
			'weight' 	=> "Вес"
			,'width' 	=> "Ширина"
			,'height' 	=> "Высота"
			,'length' 	=> "Длина"
			,'model' 	=> "Модель"
			,'mpn' 		=> "MPN"
			,'sku' 		=> "Артикул"
		);


		foreach ($xml->ЗначенияСвойства as $property) {

			// Ид объекта в 1С
			$guid = (string)$property->Ид;
			$import = true;

			// Загружаем только те что в классификаторе
			if (!isset($attributes[$guid])) {
				$this->log("[i] Свойство не было загружено в классификаторе, Ид: " . $guid, 2);
				continue;
			}

			$name 	= trim($attributes[$guid]['name']);
			$value 	= trim((string)$property->Значение);
			$value_id = 0;

			if ($value) {
				if ($attributes[$guid]) {
					// агрегатный тип, под value подразумеваем Ид объекта
					if (!empty($attributes[$guid]['values'][$value])) {
						$values = $attributes[$guid]['values'][$value];
						$value = trim($values['name']);
						$value_id = $values['value_id'];
					}
				}
			}

			// Фильтруем по таблице свойств
			$attributes_filter = $this->config->get('exchange1c_properties');
			if (is_array($attributes_filter)) {

				foreach ($attributes_filter as $attr_filter) {

					if ($attr_filter['name'] == $name) {

						if ($attr_filter['product_field_name'] == '') {

							$value = "";
							$this->log("Свойство отключено для загрузки в товар: '" . $attr_filter['name'] . "'", 2);
							break;

						} else {

							if (isset($predefined_attributes[$attr_filter['product_field_name']])) {
								$predefined_attributes[$attr_filter['product_field_name']] = $attr_filter['name'];

								// Не надо записывать в атрибуты товара
								if (!isset($attr_filter['import'])) {
									$import = false;
								}
							}

						} // $attr_filter['product_field_name'] == ''

					} // $attr_filter['name'] == $name

				} // foreach

			} // is_array($attributes_filter

			// Пропускаем с пустыми значениями
			if (empty($value)) {
				$this->log("[i] У свойства '" . $name . "' нет значения, не будет обработано", 2);
				continue;
			}

			switch ($name) {
				case 'Производитель':
					// Устанавливаем производителя из свойства только если он не был еще загружен в секции Товар
					if ($this->config->get('exchange1c_import_product_manufacturer') == 1) {
						if (!isset($data['manufacturer_id'])) {
							$data['manufacturer_id'] = $this->setManufacturer($value);
							$this->log("> Производитель (из свойства): '" . $value . "', id: " . $data['manufacturer_id'],2);
						}
					}
				break;
				case 'Изготовитель':
					// Устанавливаем производителя из свойства только если он не был еще загружен в секции Товар
					if ($this->config->get('exchange1c_import_product_manufacturer') == 1) {
						if (!isset($data['manufacturer_id'])) {
							$data['manufacturer_id'] = $this->setManufacturer($value);
							$this->log("> Изготовитель (из свойства): '" . $value . "', id: " . $data['manufacturer_id'],2);
						}
					}
				break;
				case $predefined_attributes['weight']:
					$data['weight'] = round((float)str_replace(',','.',$value), 3);
					$this->log("> Вес => weight = ".$data['weight'],2);
				break;
				case $predefined_attributes['width']:
					$data['width'] = round((float)str_replace(',','.',$value), 2);
					$this->log("> Ширина => width",2);
				break;
				case $predefined_attributes['height']:
					$data['height'] = round((float)str_replace(',','.',$value), 2);
					$this->log("> Высота => height",2);
				break;
				case $predefined_attributes['length']:
					$data['length'] = round((float)str_replace(',','.',$value), 2);
					$this->log("> Длина => length",2);
				break;
				case $predefined_attributes['model']:
					$data['model'] = (string)$value;
					$this->log("> Модель => model",2);
				break;
				case $predefined_attributes['sku']:
					$data['sku'] = (string)$value;
					$this->log("> Артикул => sku",2);
				break;
				default:
					if ($import) {
						$product_attributes[$attributes[$guid]['attribute_id']] = array(
							'name'			=> $name,
							'value'			=> $value,
							'guid'			=> $guid,
							'value_id'		=> $value_id,
							'attribute_id'	=> $attributes[$guid]['attribute_id']
						);
						$this->log("Свойство '" . $name . "' = '" . $value . "'",2);
					}
			}
		} // foreach

		$data['attributes'] = $product_attributes;
		$this->log("Свойства товара прочитаны",2);
		return true;

	} // parseProductAttributes()


	/**
	 * ver 4
	 * update 2017-04-16
	 * Обновляет свойства в товар из массива
	 */
	private function updateProductAttributes($attributes, $product_id) {

		// Проверяем
		$product_attributes = array();
		$query = $this->query("SELECT `attribute_id`,`text` FROM `" . DB_PREFIX . "product_attribute` WHERE `product_id` = " . $product_id . " AND `language_id` = " . $this->LANG_ID);
		foreach ($query->rows as $attribute) {
			$product_attributes[$attribute['attribute_id']] = $attribute['text'];
		}

		foreach ($attributes as $attribute) {
			// Проверим есть ли такой атрибут

			if (isset($product_attributes[$attribute['attribute_id']])) {

				// Проверим значение и обновим при необходимости
				if ($product_attributes[$attribute['attribute_id']] != $attribute['value']) {
					$this->query("UPDATE `" . DB_PREFIX . "product_attribute` SET `text` = '" . $this->db->escape($attribute['value']) . "' WHERE `product_id` = " . $product_id . " AND `attribute_id` = " . $attribute['attribute_id'] . " AND `language_id` = " . $this->LANG_ID);
					$this->log("Атрибут товара обновлен'" . $this->db->escape($attribute['name']) . "' = '" . $this->db->escape($attribute['value']) . "' записано в товар id: " . $product_id, 2);
				}

				unset($product_attributes[$attribute['attribute_id']]);
			} else {
				// Добавим в товар
				$this->query("INSERT INTO `" . DB_PREFIX . "product_attribute` SET `product_id` = " . $product_id . ", `attribute_id` = " . $attribute['attribute_id'] . ", `language_id` = " . $this->LANG_ID . ", `text` = '" .  $this->db->escape($attribute['value']) . "'");
				$this->log("Атрибут товара добавлен '" . $this->db->escape($attribute['name']) . "' = '" . $this->db->escape($attribute['value']) . "' записано в товар id: " . $product_id, 2);
			}
		}

		// Удалим неиспользованные
		if (count($product_attributes)) {
			$delete_attribute = array();
			foreach ($product_attributes as $attribute_id => $attribute) {
				$delete_attribute[] = $attribute_id;
			}
			$this->query("DELETE FROM `" . DB_PREFIX . "product_attribute` WHERE `product_id` = " . $product_id . " AND `language_id` = " . $this->LANG_ID . " AND `attribute_id` IN (" . implode(",",$delete_attribute) . ")");
			$this->log("Старые атрибуты товара удалены", 2);
		}

	} // updateProductAttributes()


	/**
	 * ver 2
	 * update 2017-04-29
	 * Обновляем производителя в базе данных
	 */
	private function updateManufacturer($data) {

		$query = $this->query("SELECT `name` FROM `" . DB_PREFIX . "manufacturer` WHERE `manufacturer_id` = " . $data['manufacturer_id']);
		if ($query->row['name'] <> $data['name']) {
			// Обновляем
			$sql  = " `name` = '" . $this->db->escape($data['name']) . "'";
			$sql .= isset($data['noindex']) ? ", `noindex` = " . $data['noindex'] : "";
			$this->query("UPDATE `" . DB_PREFIX . "manufacturer` SET " . $sql . " WHERE `manufacturer_id` = " . $data['manufacturer_id']);
			$this->log("Производитель обновлен: '" . $data['name'] . "'", 2);
		}

		if (isset($this->TAB_FIELDS['manufacturer_description'])) {

			$this->seoGenerateManufacturer($data);
			$select_name = isset($this->TAB_FIELDS['manufacturer_description']['name']) ? ", `name`" : "";
			$query = $this->query("SELECT `description`,`meta_title`,`meta_description`,`meta_keyword`" . $select_name . " FROM `" . DB_PREFIX . "manufacturer_description` WHERE `manufacturer_id` = " . $data['manufacturer_id'] . " AND `language_id` = " . $this->LANG_ID);

			// Сравнивает запрос с массивом данных и формирует список измененных полей
			$update_fields = $this->compareArrays($query, $data);

			if ($update_fields) {
				$this->query("UPDATE `" . DB_PREFIX . "manufacturer_description` SET " . $update_fields . " WHERE `manufacturer_id` = " . $data['manufacturer_id'] . " AND `language_id` = " . $this->LANG_ID);
			}
		}
		return true;

	} // updateManufacturer()


	/**
	 * ver 2
	 * update 2017-04-29
	 * Добавляем производителя
	 */
	private function addManufacturer(&$manufacturer_data) {

		$sql = array();
		if (!isset($this->TAB_FIELDS['manufacturer_description']['name']) && isset($manufacturer_data['name'])) {
			$sql[] = "`name` = '" . $this->db->escape($manufacturer_data['name']) . "'";
		}
		if (isset($manufacturer_data['sort_order'])) {
			$sql[] = "`sort_order` = " . $manufacturer_data['sort_order'];
		}
		if (isset($manufacturer_data['image'])) {
			$sql[] = "`image` = '" . $this->db->escape($manufacturer_data['image']) . "'";
		}
		if (isset($manufacturer_data['noindex'])) {
			$sql[] = "`noindex` = " . $manufacturer_data['noindex'];
		}
		if (!$sql) {
			$this->log("Производитель не добавлен, так как нет данных!");
			$this->log($manufacturer_data);
			return true;
		}

		$query = $this->query("INSERT INTO `" . DB_PREFIX . "manufacturer` SET" . implode(", ", $sql));

		$manufacturer_data['manufacturer_id'] = $this->db->getLastId();
        $this->seoGenerateManufacturer($manufacturer_data);

		if (isset($this->TAB_FIELDS['manufacturer_description'])) {
			$sql = $this->prepareStrQueryManufacturerDescription($manufacturer_data);
			if ($sql) {
				$this->query("INSERT INTO `" . DB_PREFIX . "manufacturer_description` SET `manufacturer_id` = " . $manufacturer_data['manufacturer_id'] . ", `language_id` = " . $this->LANG_ID . $sql);
			}
		}

		// добавляем связь
		if (isset($manufacturer_data['guid'])) {
			$this->query("INSERT INTO `" . DB_PREFIX . "manufacturer_to_1c` SET `guid` = '" . $this->db->escape($manufacturer_data['guid']) . "', `manufacturer_id` = " . $manufacturer_data['manufacturer_id']);
		}

		$this->query("INSERT INTO `" . DB_PREFIX . "manufacturer_to_store` SET `manufacturer_id` = " . $manufacturer_data['manufacturer_id'] . ", `store_id` = " . $this->STORE_ID);
 		$this->log("Производитель добавлен: '" . $manufacturer_data['name'] . "'");

	} // addManufacturer()


	/**
	 * Устанавливаем производителя
	 */
	private function setManufacturer($name, $manufacturer_guid = '') {

		$manufacturer_data = array();
		$manufacturer_data['name']			= (string)$name;
		$manufacturer_data['description'] 	= 'Производитель ' . $manufacturer_data['name'];
		$manufacturer_data['sort_order']	= 1;
		$manufacturer_data['guid']			= (string)$manufacturer_guid;

		if (isset($this->FIELDS['manufacturer']['noindex'])) {
			$manufacturer_data['noindex'] = 1;	// значение по умолчанию
		}

		if ($manufacturer_guid) {
			// Поиск (производителя) изготовителя по 1C Ид
			$query = $this->query("SELECT mc.manufacturer_id FROM `" . DB_PREFIX . "manufacturer_to_1c` mc LEFT JOIN `" . DB_PREFIX . "manufacturer_to_store` ms ON (mc.manufacturer_id = ms.manufacturer_id) WHERE mc.guid = '" . $this->db->escape($manufacturer_data['guid']) . "' AND ms.store_id = " . $this->STORE_ID);
		} else {
			// Поиск по имени
			$query = $this->query("SELECT m.manufacturer_id FROM `" . DB_PREFIX . "manufacturer` m LEFT JOIN `" . DB_PREFIX . "manufacturer_to_store` ms ON (m.manufacturer_id = ms.manufacturer_id) WHERE m.name LIKE '" . $this->db->escape($manufacturer_data['name']) . "' AND ms.store_id = " . $this->STORE_ID);
		}

		if ($query->num_rows) {
			$manufacturer_data['manufacturer_id'] = $query->row['manufacturer_id'];
		}

		if (!isset($manufacturer_data['manufacturer_id'])) {
			// Создаем
			$this->addManufacturer($manufacturer_data);
		} else {
			// Обновляем
			$this->updateManufacturer($manufacturer_data);
		}

		return $manufacturer_data['manufacturer_id'];

	} // setManufacturer()


	/**
	 * ver 2
	 * update 2017-06-13
	 * Обрабатывает единицу измерения товара
	 * Возвращает массив
	 */
	private function parseProductUnit($xml = null) {

		$unit_data = array();

		if (!$xml) {
			$unit_data['full_name']			= "Штука";
			$unit_data['name']				= "шт";
			$unit_data['eng_name']			= "PCE";
			$unit_data['ratio'] 			= 1;
			return $unit_data;
		}

		if (isset($xml['Код'])) {
			$unit_data['number_code'] = (int)$xml['Код'];
		}

		if (isset($xml['НаименованиеПолное'])) {
			$unit_data['full_name'] = htmlspecialchars((string)$xml['НаименованиеПолное']);
		}

		// Короткое наименование
		$unit_data['name'] = (string)$xml;

		if (isset($xml['МеждународноеСокращение'])) {
			$unit_data['eng_name'] = (string)$xml['МеждународноеСокращение'];
		}

		// Коэффициент пересчета от базовой единицы
		if ($xml->Пересчет) {
			$unit_data['ratio']	= (float)$xml->Пересчет->Коэффициент;
		} else {
			$unit_data['ratio']	= 1;
		}

		$this->log("> Единица измерения: '" . $unit_data['name'] . "', коэффициент: " . $unit_data['ratio']);

		return $unit_data;

	} // parseProductUnit()


	/**
	 * ver 4
	 * update 2017-06-13
	 * Обрабатывает единицы измерения в классификаторе ver. XML >= 2.09
	 * Заполняем справочник единиц которые будут использованы в товарах
	 */
	private function parseClassifierUnits($xml) {

		$result = array();
		$old_units = array();

		// Прочитаем старые соответствия единиц измерения
		$query = $this->query("SELECT * FROM `" . DB_PREFIX . "unit_to_1c`");
		if ($query->num_rows) {
			foreach ($query->rows as $row) {
				$old_units[$query->row['guid']] = array(
					'unit_id'		=> $query->row['unit_id'],
					'name'			=> $query->row['name'],
					'full_name'		=> $query->row['full_name'],
					'number_code'	=> $query->row['number_code']
				);
			}
		}
		$this->log($old_units, 2);

		foreach ($xml->ЕдиницаИзмерения as $unit) {

			// Сопоставляет Ид с id единицей измерения CMS
			$delete			= (string)$unit->ПометкаУдаления == 'false' ? false : true;
			$unit_guid			= (string)$unit->Ид;
			$data = array(
				'name'				=> (string)$unit->НаименованиеКраткое,
				'full_name'			=> (string)$unit->НаименованиеПолное,
				'number_code'		=> (string)$unit->Код
			);

			// Проверим наличие единицы в базе
			if (isset($old_units[$unit_guid])) {

				$fields = $this->compareArraysNew($old_units[$unit_guid], $data);
				if ($fields) {
					$this->query("UPDATE `" . DB_PREFIX . "unit_to_1c` SET " . $fields . " WHERE `guid` = " . $unit_guid);
				}
				// отметим что такую единицу удалять не нужно, за исключением помеченных на удаление
				if (!$delete) {
					unset($old_units[$unit_guid]);
				}

			} else {

				// Добавляем только не помеченные на удаление
				if (!$delete) {
					$this->query("INSERT INTO `" . DB_PREFIX . "unit_to_1c` SET `name` = '" . $this->db->escape($data['name']) . "', `full_name` = '" . $this->db->escape($data['full_name']) . "', `guid` = '" . $this->db->escape($unit_guid) . "', `number_code` = " . $data['number_code']);
					$unit_id = $this->db->getLastId();
				}
			}

			if ($this->config->get('exchange1c_parse_unit_in_memory') == 1) {
				if ($unit_id) {
					$result[$unit_guid] = $unit_id;
				}
			}
		}

		// удаляем неиспользуемые только при полной выгрузке
		if ($this->FULL_IMPORT) {
			// Если есть что удалять
			if (count($old_units)) {
				$delete_units = array();
				foreach ($old_units as $old_unit) {
					$delete_units[] = $old_unit['unit_id'];
				}
				$this->query("DELETE FROM `" . DB_PREFIX . "unit_to_1c` WHERE `unit_id` IN (" . implode(',', $delete_units) . ")");
				$this->log("Удалены неиспользуемые единицы, unit_id: " . implode(',', $delete_units), 2);
			}
		}

		$this->log("Прочитаны единицы измерения в классификаторе (XML >= 2.09)", 2);

		return $result;

	} // parseClassifierUnits()


	/**
	 * ver 2
	 * update 2017-04-14
	 * Отзывы парсятся с Яндекса в 1С, а затем на сайт
	 * Доработка от SunLit (Skype: strong_forever2000)
	 * Читает отзывы из классификатора и записывает их в массив
	 */
	private function parseReview($xml) {

		$product_review = array();
		foreach ($xml->Отзыв as $property) {
			$product_review[trim((string)$property->Ид)] = array(
				'id'	=> trim((string)$property->Ид),
				'name'	=> trim((string)$property->Имя),
				'yes'	=> trim((string)$property->Да),
				'no'	=> trim((string)$property->Нет),
				'text'	=> trim((string)$property->Текст),
				'rate'	=> (int)$property->Рейтинг,
				'date'	=> trim((string)$property->Дата),
			);
			$this->log("> " . trim((string)$property->Имя) . "'",2);
		}
		$this->log("Отзывы прочитаны",2);
		return $product_review;

	} // parseReview()


	/**
	 * Удаляет старые неиспользуемые картинки
	 * Сканирует все файлы в папке import_files и ищет где они указаны в товаре, иначе удаляет файл
	 */
	public function cleanOldImages($folder) {

		$result = array('error' => "", 'num' => 0);
		if (!file_exists(DIR_IMAGE . $folder)) {
			$result['error'] = "Папка не существует: /image/" . $folder;
			return $result;
		}
		$dir = dir(DIR_IMAGE . $folder);
		while ($file = $dir->read()) {

			if ($file == '.' || $file == '..') {
				continue;
			}

			$path = $folder . $file;

			if (file_exists(DIR_IMAGE . $path)) {
				if (is_file(DIR_IMAGE . $path)) {

					// это файл, проверим его причастность к товару
					$query = $this->query("SELECT `product_id`,`image` FROM `" . DB_PREFIX . "product` WHERE `image` LIKE '". $path . "'");
					if ($query->num_rows) {
						$this->log("> файл: '" . $path . "' принадлежит товару: " . $query->row['product_id'], 2);
						continue;
					} else {
						$this->log("> Не найден в базе, нужно удалить файл: " . $path, 2);
						$result_ = @unlink(DIR_IMAGE . $path);
						if ($result_) {
							$result['num']++;
						} else {
							$this->log("[!] Ошибка удаления файла: " . $path, 2);
							$result['error'] .= "Ошибка удаления файла: " . $path . "\n";
							return $result;
						}
					}

				} elseif (is_dir(DIR_IMAGE . $path)) {
					$this->cleanOldImages($path . '/', $result['num']);
					// Попытка удалить папку, если она не пустая, то произойдет удаление
					$result_ = @rmdir(DIR_IMAGE . $path);
					if ($result_) {
						$this->log("> Удалена пустая папка: " . $path, 2);
					}
					continue;
				}
			}

		}
		return $result;

	} // cleanOldImages()



	/**
	 * Отключает все товары, можно сделать опцию удаления ненужных и их связи и так далее
	 */
	private function cleanProductData($product_id) {

		if ($this->config->get('exchange1c_clean_options') == 1) {
			$this->log("[!] Перед полной загрузкой удаляются у товара все характеристики, опции, цены, остатки и единицы измерений");
			$this->query("DELETE FROM `" . DB_PREFIX . "product_feature` WHERE `product_id` = " . $product_id);
			$this->query("DELETE FROM `" . DB_PREFIX . "product_feature_value` WHERE `product_id` = " . $product_id);
			$this->query("DELETE FROM `" . DB_PREFIX . "product_option` WHERE `product_id` = " . $product_id);
			$this->query("DELETE FROM `" . DB_PREFIX . "product_option_value` WHERE `product_id` = " . $product_id);
			$this->query("DELETE FROM `" . DB_PREFIX . "product_price` WHERE `product_id` = " . $product_id);
			$this->query("DELETE FROM `" . DB_PREFIX . "product_quantity` WHERE `product_id` = " . $product_id);
			$this->query("DELETE FROM `" . DB_PREFIX . "product_unit` WHERE `product_id` = " . $product_id);
		}

	} // cleanProductData()


	/**
	 * ver 2
	 * update 2017-06-13
	 * Удаляет все дубли связей с торговой системой
	 */
	public function removeDoublesLinks() {

		$tables = array('attribute','category','manufacturer','product','store');
		$result = array('error'=>"");

		// начинаем работать с каждой таблицей
		foreach ($tables as $table) {
			$field_id = $table . "_id";
			$result[$table] = 0;
			$query = $this->query("SELECT `" . $field_id . "`, `guid`, COUNT(*) as `count` FROM `" . DB_PREFIX . $table . "_to_1c` GROUP BY `" . $field_id . "`,`guid` HAVING COUNT(*)>1 ORDER BY COUNT(*) DESC");
			if ($query->num_rows) {
				$this->log("Есть дубликаты GUID", 2);
				$this->log($query, 2);
				foreach ($query->rows as $row) {
					$limit = (int)$row['count'] - 1;
					$result[$table] += $limit;
					$this->query("DELETE FROM `" . DB_PREFIX . $table . "_to_1c` WHERE `" . $field_id . "` = " . $row[$field_id] . " AND `guid` = '" . $this->db->escape($row['guid']) . "' LIMIT " . $limit);
				}
			}

		}
		$this->log("Дубли ссылок удалены");
		return $result;

	} // removeDoublesLinks()


	/**
	 * ver 2
	 * update 2017-05-23
	 * Возвращает название товара
	 */
	private function parseProductName($product, &$data) {

		$name = "";

		if ($product->ПолноеНаименование) {
			$data['full_name']		= htmlspecialchars((string)$product->ПолноеНаименование);
			$this->log("> Найдено полное наименование: '" . $data['full_name'] . "'", 2);
		}

		// Название поля наименования
		$field_name = $this->config->get('exchange1c_import_product_name_field');

		if ($this->config->get('exchange1c_import_product_name') == "manually") {
			$name = htmlspecialchars((string)$product->$field_name);
		} elseif ($this->config->get('exchange1c_import_product_name') == "fullname") {
			$name = $data['full_name'];
		}
		if ($name) {
			$data['name'] = $name;
		}

	} // parseProductName()


	/**
	 * Возвращает название модели
	 */
	private function parseProductModel($product, $data) {

		if ($product->Модель) {
			return (string)$product->Модель;
		}
		return	isset($data['sku']) ? $data['sku'] : $data['product_guid'];

	} // parseProductModel()


	/**
	 * Возвращает преобразованный числовой id из Код товара торговой системы
	 */
	private function parseCode($code) {

		$out = "";
		// Пока руки не дошли до преобразования, надо откидывать префикс, а после лидирующие нули
		$length = mb_strlen($code);
		$begin = -1;
		for ($i = 0; $i <= $length; $i++) {
			$char = mb_substr($code,$i,1);
			// ищем первую цифру не ноль
			if ($begin == -1 && is_numeric($char) && $char != '0') {
				$begin = $i;
				$out = $char;
			} else {
				// начало уже определено, читаем все цифры до конца
				if (is_numeric($char)) {
					$out .= $char;
				}
			}
		}
		return	(int)$out;

	} // parseCode()


	/**
	 * Возвращает id производителя
	 */
	private function getProductManufacturerId($product) {

		// Читаем изготовителя, добавляем/обновляем его в базу
		if ($product->Изготовитель) {
			return $this->setManufacturer($product->Изготовитель->Наименование, $product->Изготовитель->Ид);
		}
		// Читаем производителя из поля Бренд <Бренд>Denny Rose</Бренд>
		if ($product->Бренд) {
			return $this->setManufacturer($product->Бренд);
		}

	} // getProductManufacturerId()


	/**
	 * ver 2
	 * update 2017-04-26
	 * Возвращает id категорий по GUID
	 */
	private function parseProductCategories($categories, $classifier_categories = array()) {

		$result = array();
		if ($this->config->get('exchange1c_synchronize_by_code') == 1) {
			foreach ($categories->Код as $category_code) {
				$category_id = $this->parseCode($category_code);
				if ($category_id) {
					$result[] = $category_id;
				}
			}
			if (count($result)) {
				$this->log("Категории прочитаны по Коду",2);
				return $result;
			}
		}
		foreach ($categories->Ид as $category_guid) {
			$guid = (string)$category_guid;
			if ($classifier_categories) {
				// Ищем в массиве
				if (isset($classifier_categories[$guid])) {
					$category_id = $classifier_categories[$guid];
					$this->log("Категория найдена в массиве, category_id = " . $category_id);
				}
			} else {
				// Ищем в базе данных
				$category_id = $this->getCategoryIdByGuid($guid);
			}
			if ($category_id) {
				$result[] = $category_id;
			} else {
				$this->log("[!] Категория не найдена по Ид: " . $guid);
			}
		}
		$this->log("Категории товаров прочитаны.",2);
		return $result;

	} // parseProductCategories()


	/**
	 * ver 10
	 * update 2017-06-03
	 * Обрабатывает товары из раздела <Товары> в XML
	 * При порционной выгрузке эта функция запускается при чтении каждого файла
	 * При полной выгрузке у товара очищаются все и загружается по новой.
	 * В формате 2.04 характеристики названия характеристике и их значение для данного товара передается тут
	 * Начиная с версии 1.6.3 читается каждая характеристика по отдельности, так как некоторые системы рвут товары с характеристиками
	 */
	private function parseProducts($xml, $classifier) {

		if (!$xml->Товар) {
			$this->ERROR = "parseProducts() - empty XML";
			return false;
		}

		foreach ($xml->Товар as $product){

			$data = array();

			// Получаем Ид товара и характеристики
			$guid_full = explode("#", (string)$product->Ид);
			$data['product_guid']	= $guid_full[0];
			$data['feature_guid']	= isset($guid_full[1]) ? $guid_full[1] : '';
			$data['product_id'] 	= 0;
//			$data['mpn']			= $data['product_guid'];
			$data['name']			= htmlspecialchars((string)$product->Наименование);

			// Единица измерения длины товара
			if ($this->config->get('config_length_class_id')) {
				$data['length_class_id']	= $this->config->get('config_length_class_id');
			}

			// Единица измерения веса товара
			if ($this->config->get('config_weight_class_id')) {
				$data['weight_class_id']	= $this->config->get('config_weight_class_id');
			}

			$this->log("- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -", 2);
			if ($data['feature_guid']) {
				$this->log("ТОВАР: Ид: '" . $data['product_guid'] . "', ХАРАКТЕРИСТИКА Ид: '" . $data['feature_guid'] . "'");
			} else {
				$this->log("ТОВАР: Ид: '" . $data['product_guid'] . "'");
			}

			// есть ли в предложении характеристики
			if ($product->ХарактеристикиТовара) {
				$result = $this->parseFeatures($product->ХарактеристикиТовара, $data);
				if (count($result)) $data['features'] = $result;
				if ($this->ERROR) return false;
			}

			// Артикул товара или характеристики
			if ($product->Артикул) {
				$data['sku']		= htmlspecialchars((string)$product->Артикул);
			}

			// Код товара для прямой синхронизации
			if ($product->Код) {
				$data['code']		= $this->parseCode((string)$product->Код);
			}

			// Пометка удаления, товар будет отключен
			if ((string)$product->ПометкаУдаления == 'true') {
				$data['status'] = 0;
			}

			// Синхронизация с Яндекс маркет (всегда включено)
			// В некоторых CMS имеется поле для синхронизаци, например с Yandex
			if (isset($this->TAB_FIELDS['product']['noindex'])) {
				$data['noindex']		= 1; // В некоторых версиях
			}

			// Описание товара в текстовом формате, нужна опция если описание в формате HTML
			if ($product->Описание && $this->config->get('exchange1c_import_product_description') == 1)	{
				if ($this->config->get('exchange1c_description_html') == 1) {
					$data['description']	=  nl2br(htmlspecialchars((string)$product->Описание));
				} else {
					$data['description'] 	= (string)$product->Описание;
				}
			}

			// Реквизиты товара из торговой системы (разные версии CML)
			if ($product->ЗначениеРеквизита) {
				$this->parseRequisite($product, $data);
				if ($this->ERROR) {
					return false;
				}

			} elseif ($product->ЗначенияРеквизитов) {
				// Реквизиты (разные версии CML)
				$this->parseRequisite($product->ЗначенияРеквизитов, $data);
				if ($this->ERROR) {
					return false;
				}
			}

			// Модель товара
			// Читается из поля товара "SKU" или из реквизита "Модель" в зависимости от настроек
			$data['model']	= $this->parseProductModel($product, $data);

			// Наименование товара или характеристики
			// Если надо меняет наименование товара из полного или из поля пользователя
			$this->parseProductName($product, $data);
			$this->log("> наименование: '" . $data['name'] . "'");

			// Тип номенклатуры ()читается из реквизитов)
			// Если фильтр по типу номенклатуры заполнен, то загружаем указанные там типы
			$exchange1c_parse_only_types_item = $this->config->get('exchange1c_parse_only_types_item');
			if (isset($data['item_type']) && (!empty($exchange1c_parse_only_types_item))) {
				if (mb_stripos($exchange1c_parse_only_types_item, $data['item_type']) === false) {
				 	continue;
				}
			}

			// Если включено обновление категорий
			if ($this->config->get('exchange1c_product_categories') != 'disable') {

				// Категории товара (Группы в 1С)
				if ($product->Группы) {
					if (isset($classifier['categories'])) {
						$data['product_categories']	= $this->parseProductCategories($product->Группы, $classifier['categories']);
					} else {
						$data['product_categories']	= $this->parseProductCategories($product->Группы);
					}
				}
			}

			// Если включено обновление производителя
			if ($this->config->get('exchange1c_import_product_manufacturer') == 1) {

				$manufacturer_id = $this->getProductManufacturerId($product);

				if ($manufacturer_id) {
					$data['manufacturer_id'] = $manufacturer_id;
				}
			}

			// Статус, только для товара
			// Статус по-умолчанию при отсутствии товара на складе
			// Можно реализовать загрузку из свойств
			if ($this->config->get('exchange1c_default_stock_status')) {
				$data['stock_status_id'] = $this->config->get('exchange1c_default_stock_status');
			}

			// Свойства, только для товара
			//if ($product->ЗначенияСвойств && isset($classifier['attributes']))
			if ($product->ЗначенияСвойств) {
				$this->parseAttributes($product->ЗначенияСвойств, $data, $classifier);
				if ($this->ERROR) return false;
			}

			// КАРТИНКИ
			// Если включено обновлять картинки
			if ($this->config->get('exchange1c_product_images_import_mode') != 'disable') {

				// Описание файлов
				$description = isset($data['description_files']) ? $data['description_files'] : array();

				// Картинки, только для товара
				if ($product->Картинка) {

					$images = isset($data['images']) ? $data['images'] : array();
					$data['images'] = $this->parseImages($product->Картинка, $images, $description);

					if ($this->ERROR) return false;

				} // if ($product->Картинка)

				// Картинки, только для товара (CML 2.04)
				if ($product->ОсновнаяКартинка) {

					$images = isset($data['images']) ? $data['images'] : array();
					$data['images'] = $this->parseImages($product->ОсновнаяКартинка, $images, $description);

					if ($this->ERROR) return false;

					// дополнительные, когда элементы в файле называются <Картинка1>, <Картинка2>...
					$cnt = 1;
					$var = 'Картинка'.$cnt;

					while (!empty($product->$var)) {

						$data['images'] = $this->parseImages($product->$var, $data['images'], $description);
						if ($this->ERROR) return false;

						$cnt++;
						$var = 'Картинка'.$cnt;
					}

				} // if ($product->ОсновнаяКартинка)

				// Освобождаем память
				unset($description);

				// Основная картинка
				if (isset($data['images'][0])) {
					$data['image'] = $data['images'][0]['file'];
				} else {
					// если картинки нет подставляем эту
					$data['image'] = 'no_image.png';
				}

			} else {
				$this->log("[i] Обновление картинок отключено!", 2);
			}

			// Штрихкод
			if ($product->Штрихкод) {
				$data['ean'] =  (string)$product->Штрихкод;
			}

			// Базовая единица товара
			if ($product->БазоваяЕдиница) {

				// Базовая единица товара
				$data['unit'] = $this->parseProductUnit($product->БазоваяЕдиница);
				if ($this->ERROR) return false;

			} // $product->БазоваяЕдиница

			// Отзывы парсятся с Яндекса в 1С, а затем на сайт
			// Доработка от SunLit (Skype: strong_forever2000)
			// Отзывы
			if ($product->ЗначенияОтзывов) {
				$data['review'] = $this->parseReview($data, $product->ЗначенияОтзывов);
				if ($this->ERROR) return false;
			}

			// Добавляем или обновляем товар в базе
			$this->setProduct($data);
			if ($this->ERROR) return false;

			// Освобождаем память
			unset($data);

			// Прерывание обмена
			if (file_exists(DIR_CACHE . 'exchange1c/break')) {
				$this->ERROR = "parseProducts() - file exists 'break'";
				return false;
			}
		} // foreach

		// Отключим товары не попавшие в обмен только при полной выгрузке
		if ($this->config->get('exchange1c_product_not_import_disable') == 1 && $this->FULL_IMPORT) {

			$products_disable = array();
			$query = $this->query("SELECT `product_id` FROM `" . DB_PREFIX . "product` WHERE `date_modified` < '" . $this->NOW . "'");
			$num = 0;

			// Эта переменная указывает сколько товаров может отключать за один запрос.
			$num_part = 1000;

			if ($query->num_rows) {

				foreach ($query->rows as $row) {
					$products_disable[] = $row['product_id'];

					if (count($products_disable) >= $num_part) {
						$this->query("UPDATE `" . DB_PREFIX . "product` SET `status` = 0 WHERE `product_id` IN (" . (implode(",",$products_disable)) . ")");
						$products_disable = array();
						$num += count($products_disable);
					}
				}

				if ($products_disable) {
					$this->query("UPDATE `" . DB_PREFIX . "product` SET `status` = 0 WHERE `product_id` IN (" . (implode(",",$products_disable)) . ")");
					$num += count($products_disable);
				}

			}

			$this->log("Отключены товары которых нет в выгрузке: " . $num);
		}

		return true;

	} // parseProducts()


	/**
	 * ver 4
	 * update 2017-006-09
	 * Разбор каталога
	 */
	private function parseDirectory($xml, $classifier) {

 		$this->checkFullImport($xml);

		$directory					= array();
		$directory['guid']			= (string)$xml->Ид;
		$directory['name']			= (string)$xml->Наименование;
		$directory['classifier_id']	= (string)$xml->ИдКлассификатора;
		if (isset($classifier['id'])) {
			if ($directory['classifier_id'] <> $classifier['id']) {
				$this->ERROR = "Загружаемый каталог не соответствует классификатору";
				return false;
			}
		}

		if ($this->config->get('exchange1c_flush_quantity') == 'all' && $this->FULL_IMPORT) {
			$this->clearProductsQuantity();
		}

		// Если есть товары
		if ($xml->Товары) {
			// Загрузка товаров
			$this->parseProducts($xml->Товары, $classifier);
			if ($this->ERROR) return false;
		}

		return true;

	} // parseDirectory()


	/**
	 * ****************************** ФУНКЦИИ ДЛЯ ЗАГРУЗКИ ПРЕДЛОЖЕНИЙ ******************************
	 */

	/**
	 * Добавляет склад в базу данных
	 */
	private function addWarehouse($warehouse_guid, $name) {

		$this->query("INSERT INTO `" . DB_PREFIX . "warehouse` SET `name` = '" . $this->db->escape($name) . "', `guid` = '" . $this->db->escape($warehouse_guid) . "'");
		$warehouse_id = $this->db->getLastId();
		$this->log("> Склад добавлен, warehouse_id = " . $warehouse_id, 2);
		return $warehouse_id;

	} // addWarehouse()


	/**
	 * Ищет warehouse_id по GUID
	 */
	private function getWarehouseByGuid($warehouse_guid) {

		$query = $this->query('SELECT `warehouse_id` FROM `' . DB_PREFIX . 'warehouse` WHERE `guid` = "' . $this->db->escape($warehouse_guid) . '"');
		if ($query->num_rows) {
			return $query->row['warehouse_id'];
		}
		$this->log("Склад не найден в базе", 2);
		return 0;

	} // getWarehouseByGuid()


	/**
	 * Возвращает id склада
	 */
	private function setWarehouse($warehouse_guid, $name) {

		// Поищем склад по 1С Ид
		$warehouse_id = $this->getWarehouseByGuid($warehouse_guid);
		if (!$warehouse_id) {
			$warehouse_id = $this->addWarehouse($warehouse_guid, $name);
			$this->log("> Склад добавлен, warehouse_id = " . $warehouse_id,2);
		}
  		return $warehouse_id;

	} // setWarehouse()


	/**
	 * Получает остаток товара по фильтру
	 * ver 2
	 * update 2017-04-05
	 */
	private function getProductQuantityTotal($product_id) {

		$query = $this->query("SELECT SUM(`quantity`) as `quantity` FROM `" . DB_PREFIX . "product_quantity` WHERE `product_id` = " . $product_id);
		if ($query->num_rows) {
			return (float)$query->row['quantity'];
		} else {
			return false;
		}

	} // getProductQuantityTotal()


	/**
	 * ver 2
	 * update 2017-04-05
	 * Получает все цены характеристик товара и возвращает минимальную среди цен которые больше нуля
	 */
	private function getProductPriceMin($product_id) {

		$default_customer_group_id = $this->config->get('config_customer_group_id');
		$query = $this->query("SELECT MIN(`price`) as `price` FROM `" . DB_PREFIX . "product_price` WHERE `product_id` = " . $product_id . " AND `customer_group_id` = " . $default_customer_group_id . " AND `price` > 0");
		if ($query->num_rows) {
			return (float)$query->row['price'];
		} else {
			return false;
		}

	} // getProductPriceMin()


	/**
	 * Получает остаток товара по фильтру
	 */
	private function getProductQuantity($product_quantity_filter) {

		$result = array();
		$where = "`product_id` = " . $product_quantity_filter['product_id'];
		$where .= " AND `product_feature_id` = " . (isset($product_quantity_filter['product_feature_id']) ?  $product_quantity_filter['product_feature_id'] : 0);
		$where .= " AND `warehouse_id` = " . (isset($product_quantity_filter['warehouse_id']) ?  $product_quantity_filter['warehouse_id'] : 0);

		$query = $this->query("SELECT `product_quantity_id`,`quantity` FROM `" . DB_PREFIX . "product_quantity` WHERE " . $where);
		if ($query->num_rows) {
			$result['quantity'] 			= $query->row['quantity'];
			$result['product_quantity_id']	= $query->row['product_quantity_id'];
			return $result;
		} else {
			$result['product_quantity_id']	= 0;
			return $result;
		}

	} // getProductQuantity()


	/**
	 * Добавляет остаток товара по фильтру
	 */
	private function addProductQuantity($product_quantity_filter, $quantity) {

		$set = "";
		foreach ($product_quantity_filter as $field => $value) {
			$set .= ", `" . $field . "` = " . $value;
		}
		$this->query("INSERT INTO `" . DB_PREFIX . "product_quantity` SET `quantity` = '" . (float)$quantity . "'" . $set);
		$product_quantity_id = $this->db->getLastId();
		$this->log("> Добавлены остатки в товар, product_quantity_id = " . $product_quantity_id, 2);
		return $product_quantity_id;

	} // addProductQuantity()


	/**
	 * Сравнивает массивы и формирует список измененных полей для запроса
	 */
	private function compareArraysNew($data1, $data2, $filelds_include = "") {

		//$result = array_diff_assoc($data1, $data2);
		//$filelds_include_obj = explode(",", $filelds_include);

		$upd_fields = array();
		if (count($data1)) {
			foreach($data1 as $key => $row) {
				if (!isset($data2[$key])) continue;
				if (!empty($filelds_include) && strripos($filelds_include, $key) === false) continue;
				if ($row <> $data2[$key]) {
					$upd_fields[] = "`" . $key . "` = '" . $this->db->escape($data2[$key]) . "'";
					$this->log("[i] Отличается поле '" . $key . "', старое: " . $row . ", новое: " . $data2[$key], 2);
				} else {
					$this->log("Поле '" . $key . "' не имеет отличий", 2);
				}
			}
		}
		return implode(', ', $upd_fields);

	} // compareArraysNew()


	/**
	 * Ищет совпадение данных в массиве данных, при совпадении значений, возвращает ключ второго массива
	 */
	private function findMatch($data1, $data_array) {

		$bestMatch = 0;
		foreach ($data_array as $key2 => $data2) {
			$matches = 0;
			$fields = 0;
			foreach ($data1 as $key1 => $value) {
				if (isset($data2[$key1])) {
					$fields++;
					if ($data2[$key1] == $value) {
						$matches++;
					}
 				}
			}
			// у всех найденых полей совпали значения
			if ($matches == $fields){
				return $key2;
			}
		}
		return false;

	} // findMatch()


	/**
	 * ver 1
	 * update 2017-04-21
	 * Сбрасывает
	 */
	private function setProductQuantity($product_quantity_filter, $quantity) {

		$result = $this->getProductQuantity($product_quantity_filter);
		if ($result['product_quantity_id']) {
			// Есть цена
			if ($result['quantity'] != $quantity) {
				$query = $this->query("UPDATE `" . DB_PREFIX . "product_quantity` SET `quantity` = " . $quantity . " WHERE `product_quantity_id` = " . $result['product_quantity_id']);
			}
		} else {
			$this->addProductQuantity($product_quantity_filter, $quantity);
		}

	} // setProductQuantity()


	/**
	 * ver 2
	 * update 2017-04-05
	 * Устанавливает остаток товара
	 */
	private function clearProductsQuantity() {

		$this->query("UPDATE `" . DB_PREFIX . "product` `p` LEFT JOIN `" . DB_PREFIX . "product_to_store` `p2s` ON (`p`.`product_id` = `p2s`.`product_id`) SET `p`.`quantity` = 0 WHERE `p2s`.`store_id` = " . $this->STORE_ID);

	} // setProductQuantity()


	/**
	 * ver 4
	 * update 2017-05-28
	 * Устанавливает остатки товара и опций
	 */
	private function setProductQuantities($quantities, $product_id, $product_feature_id = 0, $new = false) {

		foreach ($quantities as $warehouse_id => $quantity) {
			$filter = array(
				'product_id'			=> $product_id,
				'warehouse_id'			=> $warehouse_id,
				'product_feature_id'	=> $product_feature_id
			);

			// Остатки товара
			$this->setProductQuantity($filter, $quantity);

			if (!$warehouse_id) {
				// Остатки опций у характеристики
				$this->setOptionQuantity($filter, $quantity);
			}

			$this->log("Остаток на складе (warehouse_id=" . $warehouse_id . ") = " . $quantity);
		}

	} // setProductQuantities()


	/**
	 * ver 2
	 * update 2017-05-28
	 * Устанавливает остатки опций
	 */
	private function setOptionQuantity($filter, $quantity, $new = false) {

		// Получим опции этой характеристики
		$query = $this->query("SELECT `product_option_value_id` FROM `" . DB_PREFIX . "product_feature_value` WHERE `product_feature_id` = " . $filter['product_feature_id']);
		if (!$query->num_rows) {
			return false;
		}

		$list_product_option_id = array();
		foreach ($query->rows as $row) {
			$list_product_option_id[] = $row['product_option_value_id'];
		}
		$list_product_option_id = implode(",", $list_product_option_id);

		// Устанавливаем у всех опций или одной, как там получится, остаток одинаковый, так как все они относятся к одной характеристике
		$query = $this->query("UPDATE `" . DB_PREFIX . "product_option_value` SET `quantity` = " . $quantity . " WHERE `product_option_value_id` IN (" . $list_product_option_id . ")");

		$this->log("Установлен остаток " . $quantity . " у опций " . $list_product_option_id);

		return true;

	} // setOptionQuantity()


	/**
	 * Удаляет склад и все остатки поо нему
	 */
	private function deleteWarehouse($warehouse_guid) {

		$warehouse_id = $this->getWarehouseByGuid($warehouse_guid);
		if ($warehouse_id) {
			// Удаляем все остатки по этму складу
			$this->deleteStockWarehouse($warehouse_id);
			// Удалим остатки по этому складу
			$this->query("DELETE FROM `" . DB_PREFIX . "product_quantity ` WHERE `warehouse_id` = " . (int)$warehouse_id);
			// Удаляем склад
			$this->query("DELETE FROM `" . DB_PREFIX . "warehouse ` WHERE `guid` = '" . $this->db->escape($warehouse_guid) . "'");
			$this->log("Удален склад, GUID '" . $warehouse_guid . "' и все остатки на нем.",2);
		}

	} // deleteWarehouse()


	/**
	 * ver 2
	 * update 2017-04-26
	 * Загружает список складов из классификатора
	 */
	private function parseClassifierWarehouses($xml) {

		$data = array();
		foreach ($xml->Склад as $warehouse){
			if (isset($warehouse->Ид) && isset($warehouse->Наименование) ){
				$warehouse_guid = (string)$warehouse->Ид;
				$name = trim((string)$warehouse->Наименование);
				$delete = isset($warehouse->ПометкаУдаления) ? $warehouse->ПометкаУдаления : "false";
				if ($delete == "false") {
					$data[$warehouse_guid] = array(
						'name' => $name
					);
					$data[$warehouse_guid]['warehouse_id'] = $this->setWarehouse($warehouse_guid, $name);
					$this->log("Склад: '" . $name . "', id = " . $data[$warehouse_guid]['warehouse_id'], 2);
				} else {
					// Удалить склад
					$this->log("[!] Склад помечен на удаление в торговой системе и будет удален");
					$this->deleteWarehouse($warehouse_guid);
				}
			}
		}
		$this->log("Складов в классификаторе: " . count($xml->Склад), 2);
		return $data;

	} // parseClassifierWarehouses()


	/**
	 * ver 3
	 * update 2017-05-23
	 * Загружает остатки по складам
	 * Возвращает остатки по складам
	 * где индекс - это warehouse_id, а значение - это quantity (остаток)
	 */
	private function parseQuantity($xml, $data) {

		$data_quantity = array();

		if (!$xml) {
			$this->ERROR = "parseQuantity() - нет данных в XML";
			return false;
		}

		// есть секция с остатками, обрабатываем
		if ($xml->Остаток) {
			foreach ($xml->Остаток as $quantity) {
				// есть секция со складами
				if ($quantity->Склад->Ид) {
					$warehouse_guid = (string)$quantity->Склад->Ид;
					$warehouse_id = $this->getWarehouseByGuid($warehouse_guid);
					if (!$warehouse_id) {
						$this->ERROR = "parseQuantity() - Склад не найден по Ид '" . $warehouse_guid . "'";
						return false;
					}
				} else {
					$warehouse_id = 0;
				}
				if ($quantity->Склад->Количество) {
					$quantity = (float)$quantity->Склад->Количество;
				}
				$data_quantity[$warehouse_id] = $quantity;
			}

			// Если нет складов или общий остаток предложения
			if ($xml->Остаток->Количество) {
				$data_quantity[0] = (float)$xml->Остаток->Количество;
			}

		} elseif ($xml->Склад) {
			foreach ($xml->Склад as $quantity) {
				// есть секция со складами
				$warehouse_guid = (string)$quantity['ИдСклада'];
				if ($warehouse_guid) {
					$warehouse_id = $this->getWarehouseByGuid($warehouse_guid);
					if (!$warehouse_id) {
						$this->ERROR = "parseQuantity() - Склад не найден по Ид '" . $warehouse_guid . "'";
						return false;
					}
				} else {
					$this->ERROR = "parseQuantity() - Не указан Ид склада!";
					return false;
				}
				$quantity = (float)$quantity['КоличествоНаСкладе'];
				$data_quantity[$warehouse_id] = $quantity;
			}
		}
		return $data_quantity;

	} // parseQuantity()


	/**
	 * Возвращает массив данных валюты по id
	 */
	private function getCurrency($currency_id) {

		$query = $this->query("SELECT * FROM `" . DB_PREFIX . "currency` WHERE `currency_id` = " . $currency_id);
		if ($query->num_rows) {
			return $query->row;
		}
		return array();

	} // getCurrency()


	/**
	 * Возвращает id валюты по коду
	 */
	private function getCurrencyId($code) {

		$query = $this->query("SELECT `currency_id` FROM `" . DB_PREFIX . "currency` WHERE `code` = '" . $this->db->escape($code) . "'");
		if ($query->num_rows) {
			$this->log("Валюта, currency_id = " . $query->row['currency_id'], 2);
			return $query->row['currency_id'];
		}

		// Попробуем поискать по символу справа
		$query = $this->query("SELECT `currency_id` FROM `" . DB_PREFIX . "currency` WHERE `symbol_right` = '" . $this->db->escape($code) . "'");
		if ($query->num_rows) {
			$this->log("Валюта, currency_id = " . $query->row['currency_id'], 2);
			return $query->row['currency_id'];
		}

		return 0;

	} // getCurrencyId()


	/**
	 * Сохраняет настройки сразу в базу данных
	 */
	private function configSet($key, $value, $store_id=0) {

		if (!$this->config->has('exchange1c_'.$key)) {
			$this->query("INSERT INTO `" . DB_PREFIX . "setting` SET `value` = '" . $value . "', `store_id` = " . $store_id . ", `code` = 'exchange1c', `key` = '" . $key . "'");
		}

	} // configSet()


	/**
	 * Получает список групп покупателей
	 */
	private function getCustomerGroups() {

		$query = $this->query("SELECT `customer_group_id` FROM `" . DB_PREFIX. "customer_group` ORDER BY `sort_order`");
		$data = array();
		foreach ($query->rows as $row) {
			$data[] = $row['customer_group_id'];
		}
		return $data;

	} // getCustomerGroups()


	/**
	 * ver 2
	 * update 2017-06-03
	 * Загружает типы цен автоматически в таблицу которых там нет
	 */
	private function autoLoadPriceType($xml) {

		if ($this->config->get('exchange1c_price_import_mode') == 'disable') {
			$this->log("autoLoadPriceType(): Загрузка типов цен отключена");
			return array();
		}

		$this->log("Автозагрузка цен из XML...", 2);
		$config_price_type = $this->config->get('exchange1c_price_type');

		if (empty($config_price_type)) {
			$config_price_type = array();
		}

		$update = false;
		$default_price = -1;

		// список групп покупателей
		$customer_groups = $this->getCustomerGroups();

		$index = 0;
		foreach ($xml->ТипЦены as $price_type)  {
			$name = trim((string)$price_type->Наименование);
			$delete = isset($price_type->ПометкаУдаления) ? $price_type->ПометкаУдаления : "false";
			$guid = (string)$price_type->Ид;
			$priority = 0;
			$found = -1;
			foreach ($config_price_type as $key => $cpt) {
				if (!empty($cpt['id_cml']) && $cpt['id_cml'] == $guid) {
					$this->log("autoLoadPriceType() - Найдена цена по Ид: '" . $guid . "'", 2);
					$found = $key;
					break;
				}
				if (strtolower(trim($cpt['keyword'])) == strtolower($name)) {
					$this->log("autoLoadPriceType() - Найдена цена по наименованию: '" . $name . "'", 2);
					$found = $key;
					break;
				}
				$priority = max($priority, $cpt['priority']);
			}

			if ($found >= 0) {
				// Если тип цены помечен на удаление, удалим ее из настроек
				if ($delete == "true") {
					$this->log("autoLoadPriceType() - Тип цены помечен на удаление, не будет загружен и будет удален из настроек");
					unset($config_price_type[$found]);
					$update = true;
				} else {
					// Обновим Ид
					if ($config_price_type[$found]['guid'] != $guid) {
						$config_price_type[$found]['guid'] = $guid;
						$update = true;
					}
				}

			} else {
				// Добавим цену в настройку если он ане помечена на удаление
				if ($default_price == -1) {
					$table_price = "product";
					$default_price = count($config_price_type)+1;
				} else {
					$table_price = "discount";
				}
				$customer_group_id = isset($customer_groups[$index]) ? $customer_groups[$index] : $this->config->get('config_customer_group_id');
				if ($delete == "false") {
					$config_price_type[] = array(
						'keyword' 				=> $name,
						'guid' 					=> $guid,
						'table_price'			=> $table_price,
						'customer_group_id' 	=> $customer_group_id,
						'quantity' 				=> 1,
						'priority' 				=> $priority
					);
					$update = true;

				}
			} // if
			$index++;
		} // foreach

        if ($update) {
			if ($this->config->get('exchange1c_price_type')) {
				$this->query("UPDATE `". DB_PREFIX . "setting` SET `value` = '" . $this->db->escape(json_encode($config_price_type)) . "', `serialized` = 1 WHERE `key` = 'exchange1c_price_type'");
				$this->log("autoLoadPriceType() - Цены обновлены в настройках", 2);
	        } else {
				$this->query("INSERT `". DB_PREFIX . "setting` SET `value` = '" . $this->db->escape(json_encode($config_price_type)) . "', `serialized` = 1, `code` = 'exchange1c', `key` = 'exchange1c_price_type'");
				$this->log("autoLoadPriceType() - Цены добавлены в настройки", 2);
	        }
        }
		return $config_price_type;

	} // autoLoadPriceType()


	/**
	 * ver 4
	 * update 2017-06-03
	 * Загружает типы цен из классификатора
	 * Обновляет Ид если найдена по наименованию
	 * Сохраняет настройки типов цен
	 */
	private function parseClassifierPriceType($xml) {

		// Автозагрузка цен
		if ($this->config->get('exchange1c_price_types_auto_load') == 1) {
			$config_price_type = $this->autoLoadPriceType($xml);
		} else {
			$config_price_type = $this->config->get('exchange1c_price_type');
		}

		$data = array();

		if (empty($config_price_type)) {
			$this->ERROR = "parseClassifierPriceType() - В настройках модуля не указаны цены";
			return false;
		}

		// Перебираем все цены из CML
		foreach ($xml->ТипЦены as $price_type)  {
			$currency		= isset($price_type->Валюта) ? (string)$price_type->Валюта : "RUB";
			$guid			= (string)$price_type->Ид;
		 	$name			= trim((string)$price_type->Наименование);
		 	$code			= $price_type->Код ? $price_type->Код : ($price_type->Валюта ? $price_type->Валюта : '');

			// Найденный индекс цены в настройках
			$found = -1;

			// Перебираем все цены из настроек модуля
			foreach ($config_price_type as $index => $config_type) {

				if ($found >= 0)
					break;

				if (!empty($config_type['guid']) && $config_type['guid'] == $guid) {
					$found = $index;
					break;
				} elseif (strtolower($name) == strtolower($config_type['keyword'])) {
					$found = $index;
					break;
				}

			} // foreach ($config_price_type as $config_type)

			if ($found >= 0) {
				if ($code) {
					$currency_id				= $this->getCurrencyId($code);
				} else {
					$currency_id				= $this->getCurrencyId($currency);
				}
				$data[$guid] 					= $config_type;
				$data[$guid]['currency'] 		= $currency;
				$data[$guid]['currency_id'] 	= $currency_id;
				if ($currency_id) {
					$currency_data = $this->getCurrency($currency_id);
					$rate = $currency_data['value'];
					$decimal_place = $currency_data['decimal_place'];
				} else {
					$rate = 1;
					$decimal_place = 2;
				}
				$data[$guid]['rate'] 			= $rate;
				$data[$guid]['decimal_place'] = $decimal_place;
				$this->log('Вид цены: ' . $name,2);
			} else {
				$this->ERROR = "parseClassifierPriceType() - Цена '" . $name . "' не найдена в настройках модуля, Ид = '" . $guid . "'";
				return false;
			}

		} // foreach ($xml->ТипЦены as $price_type)
		return $data;

	} // parseClassifierPriceType()


	/**
	 * ver 4
	 * update 2017-04-18
	 * Устанавливает цену скидки или акции товара
	 */
	private function setProductPrice($price_data, $product_id, $new = false) {

		$price_id = 0;
		if ($price_data['table_price'] == 'discount') {
			if (!$new) {
				$query = $this->query("SELECT `product_discount_id`,`customer_group_id`,`price`,`quantity`,`priority` FROM `" . DB_PREFIX . "product_discount` WHERE `product_id` = " . $product_id . " AND `customer_group_id` = " . $price_data['customer_group_id'] . " AND `quantity` = " . $price_data['quantity']);
				if ($query->num_rows) {
					$price_id = $query->row['product_discount_id'];
					$update_fields = $this->compareArrays($query, $price_data);
					// Если есть расхождения, производим обновление
					if ($update_fields) {
						$this->query("UPDATE `" . DB_PREFIX . "product_discount` SET " . $update_fields . " WHERE `product_discount_id` = " . $price_id);
						$this->log("> Cкидка обновлена: " . $price_data['price'], 2);
					}
				}
			}
			if (!$price_id) {
				$this->query("INSERT INTO `" . DB_PREFIX . "product_discount` SET `product_id` = " . $product_id . ", `quantity` = " . $price_data['quantity'] . ", `priority` = " . $price_data['priority'] . ", `customer_group_id` = " . $price_data['customer_group_id'] . ", `price` = '" . (float)$price_data['price'] . "'");
				$price_id = $this->db->getLastId();
				$this->log("> Cкидка добавлена: " . $price_data['price'], 2);
			}


		} elseif ($price_data['table_price'] == 'special') {
			if (!$new) {
				$query = $this->query("SELECT `product_special_id`,`customer_group_id`,`price` FROM `" . DB_PREFIX . "product_special` WHERE `product_id` = " . $product_id . " AND `customer_group_id` = " . $price_data['customer_group_id']);
				if ($query->num_rows) {
					$price_id = $query->row['product_special_id'];
					$update_fields = $this->compareArrays($query, $price_data);
					// Если есть расхождения, производим обновление
					if ($update_fields) {
						$this->query("UPDATE `" . DB_PREFIX . "product_special` SET " . $update_fields . " WHERE `product_special_id` = " . $price_id);
						$this->log("> Акция обновлена: " . $price_data['price'], 2);
					}
				}
			}
			if (!$price_id) {
				$this->query("INSERT INTO `" . DB_PREFIX . "product_special` SET `product_id` = " . $product_id . ", `priority` = " . $price_data['priority'] . ", `customer_group_id` = " . $price_data['customer_group_id'] . ", `price` = '" . (float)$price_data['price'] . "'");
				$update_fields = $this->db->getLastId();
				$this->log("> Акция добавлена: " . $price_data['price'], 2);
			}

		}
		return $price_id;

	} // setProductPrice()


	/**
	 * ver 8
	 * update 2017-05-07
	 * Устанавливает цены товара
	 */
	private function setProductPrices($prices_data, $product_id, $product_feature_id = 0, $new = false) {

		$price = 0;

		if (!$new) {
			$old_discount = array();
			if ($this->FULL_IMPORT && !$product_feature_id) {
				if ($this->config->get('exchange1c_clean_prices_full_import') == 1) {
					// При полной выгрузке удаляем все старые скидки товара
					$this->query("DELETE FROM `" . DB_PREFIX . "product_discount` WHERE `product_id` = " . $product_id);
				}
			} else {
				// Читаем старые скидки товара
				$query = $this->query("SELECT `product_discount_id` FROM `" . DB_PREFIX . "product_discount` WHERE `product_id` = " . $product_id);
				foreach ($query->rows as $row) {
					$old_discount[] = $row['product_discount_id'];
				}
			}
			$old_special = array();
			if ($this->FULL_IMPORT && !$product_feature_id) {
				if ($this->config->get('exchange1c_clean_prices_full_import') == 1) {
					// При полной выгрузке удаляем все старые скидки товара
					$this->query("DELETE FROM `" . DB_PREFIX . "product_special` WHERE `product_id` = " . $product_id);
				}
			} else {
				// Читаем старые акции товара
				$query = $this->query("SELECT `product_special_id` FROM `" . DB_PREFIX . "product_special` WHERE `product_id` = " . $product_id);
				foreach ($query->rows as $row) {
					$old_special[] = $row['product_special_id'];
				}
			}
		}

		foreach ($prices_data as $price_data) {

			if ($price_data['table_price'] == 'product') {

				$price = $price_data['price'];
				$this->log("> Цена для записи в товар: " . $price . " для одной базовой единицы товара", 2);

			} else {
				// Если есть характеристики, то скидки и акции не пишем
				if ($product_feature_id) {
					continue;
				}
				// устанавливает цену скидки или акции в зависимости от настроек
				$price_id = $this->setProductPrice($price_data, $product_id, $new);
				if ($price_id) {
					if ($price_data['table_price'] == 'discount') {
						$key = array_search($price_id, $old_discount);
						if ($key !== false) {
							unset($old_discount[$key]);
						}
					} elseif ($price_data['table_price'] == 'discount') {
						$key = array_search($price_id, $old_special);
						if ($key !== false) {
							unset($old_special[$key]);
						}
					}
				}
			}
		}

		if (!$new) {
			if (count($old_discount)) {
				$this->query("DELETE FROM `" . DB_PREFIX . "product_discount` WHERE `product_id` = " . $product_id . " AND `product_discount_id` IN (" . implode(",",$old_discount) . ")");
			}
			if (count($old_special)) {
				$this->query("DELETE FROM `" . DB_PREFIX . "product_special` WHERE `product_id` = " . $product_id . " AND `product_special_id` IN (" . implode(",",$old_special) . ")");
			}
		}
		return $price;

	} // setProductPrices()


	/**
	 * ver 9
	 * update 2017-06-19
	 * Устанавливает цены характеристик товара базовой единицы товара
	 * поле action имеет значение:
	 * 0 - без акции и без скидки
	 * 1 - акция (special)
	 * 2 - скидка (discount)
	 */
	private function setProductFeaturePrices($prices_data, $product_id, $product_feature_id = 0, $new = false) {

		$old_prices = array();
		if (!$new) {
			// Читаем старые цены этой характеристики
			$query = $this->query("SELECT `product_price_id`,`price`,`customer_group_id`,`action`,`unit_id` FROM `" . DB_PREFIX . "product_price` WHERE `product_feature_id` = " . $product_feature_id . " AND `product_id` = " . $product_id);
			foreach ($query->rows as $row) {

				$old_prices[$row['product_price_id']] = array(
					'price'				=> $row['price'],
					'customer_group_id'	=> $row['customer_group_id'],
					'action'			=> $row['action'],
					'unit_id'			=> $row['unit_id'],
				);
			}
		}

		// пробежимся по ценам
		foreach ($prices_data as $price_guid => $price_data) {

			if ($price_data['quantity'] != 1) {
				continue;
			}

			// Если единица у товара не существует в классификаторе, то $unit_id будет 0
			$unit_id = isset($price_data['unit']['unit_id']) ? $price_data['unit']['unit_id'] : 0;

			// Тогда $unit_id получаем при записи единицы в товар
			$result = $this->setProductUnit($price_data['unit'], $product_id, $product_feature_id);
			if ($result['unit_id']) {
				$unit_id = $result['unit_id'];
			}
			$this->log($price_data, 2);

			if ($price_data['table_price'] == 'product') {
				$action = 0;
			} elseif ($price_data['table_price'] == 'special') {
				$action = 1;
			} elseif ($price_data['table_price'] == 'discount') {
				$action = 2;
			}

			$product_price_id = 0;
			if (!$new) {
				foreach ($old_prices as $product_price_id => $old_price) {
					if ($old_price['customer_group_id'] == $price_data['customer_group_id'] && $action == $old_price['action']) {
						$sql = array();

						// Отличается цена
						if ($old_price['price'] != $price_data['price']) {
							$sql[] = "`price` = '" . $price_data['price'] . "'";
						}

						// Отличается единица
						if ($old_price['unit_id'] != $unit_id) {
							$sql[] = "`unit_id` = " . $unit_id;
						}

						// Если есть изменения
						if ($sql) {
							$sql_str = implode(", ",  $sql);
							$this->query("UPDATE `" . DB_PREFIX . "product_price` SET " . $sql_str . " WHERE `product_price_id` = " . $product_price_id);
							$this->log("> Цена характеристики обновлена: " . $price_data['price']);
						}
						break;
					}
					$product_price_id = 0;
				}

				if ($product_price_id) {
					unset($old_prices[$product_price_id]);
				}
			}
			if (!$product_price_id) {
				$query = $this->query("INSERT INTO `" . DB_PREFIX . "product_price` SET `product_id` = " . $product_id . ", `product_feature_id` = " . $product_feature_id . ", `customer_group_id` = " . $price_data['customer_group_id'] . ", `action` = " . $action . ", `price` = '" . (float)$price_data['price'] . "', `unit_id` = " . $unit_id);
				$product_price_id = $this->db->getLastId();
			}
		}

		if (!$new) {
			// Удаляем отсутствующие цены этой характеристики
			if (count($old_prices)) {
				$fields = array();
				foreach ($old_prices as $product_price_id => $price_data) {
					$fields[] = $product_price_id;
				}
				$this->query("DELETE FROM `" . DB_PREFIX . "product_price` WHERE `product_price_id` IN (" . implode(",",$fields) . ")");
			}
		}
		return true;

	} // setProductFeaturePrices()


	/**
	 * ver 2
	 * update 2017-06-13
	 * Получает по коду его id
	 */
 	private function getUnitId($name) {

		$query = $this->query("SELECT `number_code` FROM `" . DB_PREFIX . "unit` WHERE `rus_name1` = '" . $this->db->escape($name) . "'");
		if ($query->num_rows) {
			return (int)$query->row['number_code'];
		}
		return 0;

	} // getUnitId()


	/**
	 * ver 2
	 * update 2017-06-20
	 * Получает данные единицы по наиминованию
	 */
	private function getUnitByName($name) {

		$query = $this->query("SELECT * FROM `" . DB_PREFIX . "unit_to_1c` WHERE `name` = '" . $this->db->escape($name) . "'");
		if ($query->num_rows) {
			return $query->row;
		}
		return false;

	} // getUnitByName()


	/**
	 * ver 3
	 * update 2017-06-20
	 * Загружает все цены только в одной валюте
	 */
	private function parsePrice($xml, $data) {

		if (!$xml) {
			$this->ERROR = "XML не содержит данных";
			return false;
		}

		// Читаем типы цен из настроек
		$price_types = $this->config->get('exchange1c_price_type');
		if (!$price_types) {
			$this->ERROR = "Настройки цен пустые, настройте типы цен и повторите загрузку!";
			return false;
		}

		// Массив хранения цен
		$data_prices = array();

		// Читем цены в том порядке в каком заданы в настройках
		foreach ($price_types as $config_price_type) {

			foreach ($xml->Цена as $price_data) {
				$guid		= (string)$price_data->ИдТипаЦены;

				// Цена
				$price	= $price_data->ЦенаЗаЕдиницу ? (float)$price_data->ЦенаЗаЕдиницу : 0;

				if ($config_price_type['guid'] != $guid) {
					continue;
				}

				// Курс валюты
				//$rate = $price_data->Валюта ? $this->getCurrencyValue((string)$price_data->Валюта) : 1;
				// Валюта
				// автоматическая конвертация в основную валюту CMS
				//if ($this->config->get('exchange1c_currency_convert') == 1) {
				//	if ($rate != 1 && $rate > 0) {
				//		$price = round((float)$price_data->ЦенаЗаЕдиницу / (float)$rate, $decimal_place);
				//	}
				//}
				//$data_prices[$guid]['rate'] = $rate;

				if ($this->config->get('exchange1c_ignore_price_zero') == 1 && $price == 0) {
					$this->log("Включена опция при нулевой цене не менять старую");
					continue;
				}

				// Единица измерения цены
				$unit_data = array(
					'name'		=> $price_data->Единица ? (string)$price_data->Единица : 'шт',
					'ratio'		=> $price_data->Коэффициент ? (float)$price_data->Коэффициент : 1
				);
				$unit_split = $this->splitNameStr($unit_data['name']);

				// Получим единицу из классификатора
				//$unit = $this->getUnitByName($unit_split['name']);
				$unit = $this->getUnitByName($unit_data['name']);
				$this->log($unit, 2);
				if ($unit != false) {
					if ($unit['number_code']) {
						$unit_data['number_code'] 	= $unit['number_code'] ;
					}
					if ($unit['guid']) {
						$unit_data['guid'] 	= $unit['guid'] ;
					}
					$unit_data['name'] 			= $unit['name'];
					$unit_data['full_name1'] 	= $unit['full_name'];
					$unit_data['unit_id'] 		= $unit['unit_id'];
				}

				// Копируем данные с настроек
				$data_prices[$guid] 			= $config_price_type;
				$data_prices[$guid]['unit']		= $unit_data;
				$data_prices[$guid]['price']	= $price;
				$this->log("> Цена: " . $price . " за единицу: " . $unit_data['name'] . ", GUID: " . $guid, 2);


			} // foreach ($xml->Цена as $price_data)
		} // foreach ($price_types as $config_price_type)

		$this->log($data_prices, 2);
		return $data_prices;

 	} // parsePrices()


	/**
	 * ====================================== ХАРАКТЕРИСТИКИ ======================================
	 */


	/**
	 * Добавляет опциию по названию
	 */
	private function addOption($name, $type='select') {

		$this->query("INSERT INTO `" . DB_PREFIX . "option` SET `type` = '" . $this->db->escape($type) . "'");
		$option_id = $this->db->getLastId();
		$this->query("INSERT INTO `" . DB_PREFIX . "option_description` SET `option_id` = '" . $option_id . "', `language_id` = " . $this->LANG_ID . ", `name` = '" . $this->db->escape($name) . "'");

		$this->log("Опция добавлена: '" . $name. "'", 2);
		return $option_id;

	} // addOption()


	/**
	 * Получение наименования производителя по manufacturer_id
	 */
	private function getManufacturerName($manufacturer_id, &$error) {

		if (!$manufacturer_id) {
			$error = "Не указан manufacturer_id";
			return "";
		}
		$query = $this->query("SELECT `name` FROM `" . DB_PREFIX . "manufacturer` WHERE `manufacturer_id` = " . $manufacturer_id);
		$name = isset($query->row['name']) ? $query->row['name'] : "";
		$this->log("Производитель: '" . $name . "' по id: " . $manufacturer_id, 2);
		return $name;

	} // getManufacturerName()


	/**
	 * Получение product_id по GUID
	 */
	private function getProductIdByGuid($product_guid) {

		// Определим product_id
		$query = $this->query("SELECT `product_id` FROM `" . DB_PREFIX . "product_to_1c` WHERE `guid` = '" . $this->db->escape($product_guid) . "'");
		$product_id = isset($query->row['product_id']) ? $query->row['product_id'] : 0;
		// Проверим существование такого товара
		if ($product_id) {
			$query = $this->query("SELECT `product_id` FROM `" . DB_PREFIX . "product` WHERE `product_id` = " . (int)$product_id);
			if (!$query->num_rows) {
				// Удалим неправильную связь
				$this->query("DELETE FROM `" . DB_PREFIX . "product_to_1c` WHERE `product_id` = " . (int)$product_id);
				$product_id = 0;
			}
		}
		if ($product_id) {
			$this->log("Найден товар по GUID, product_id = " . $product_id);
		} else {
			$this->log("Не найден товар по GUID " . $product_guid, 2);
		}
		return $product_id;

	} // getProductIdByGuid()


	/**
	 * Проверка существования товара по product_id
	 */
	private function getProductIdByCode($code) {

		// Определим product_id
		$query = $this->query("SELECT `product_id` FROM `" . DB_PREFIX . "product` WHERE `product_id` = " . (int)$code);
		$product_id = isset($query->row['product_id']) ? $query->row['product_id'] : 0;

		if ($product_id) {
			$this->log("Найден товар по <Код>, product_id = " . $product_id, 2);
		} else {
			$this->log("Не найден товар по <Код> " . $code, 2);
		}

		return $product_id;

	} // getProductIdByCode()


	/**
	 * Разбивает название по шаблону "[order].[name] [option]"
	 */
	private function splitNameStr($str, $opt_yes = true) {

		$str = trim(str_replace(array("\r","\n"),'',$str));
		$length = mb_strlen($str);
		$data = array(
			'order' 	=> "",
			'name' 		=> "",
			'option' 	=> ""
		);

        $pos_name_start = 0;
		$pos_opt_end = 0;
		$pos_opt_start = $length;

		if ($opt_yes) {
			// Поищем опцию
			$level = 0;
			for ($i = $length; $i > 0; $i--) {
				$char = mb_substr($str,$i,1);
				if ($char == ")") {
					$level++;
					if (!$pos_opt_end)
						$pos_opt_end = $i;
				}
				if ($char == "(") {
					$level--;
					if ($level == 0) {
						$pos_opt_start = $i+1;
						$data['option'] = mb_substr($str, $pos_opt_start, $pos_opt_end-$pos_opt_start);
						$pos_opt_start -= 2;
						break;
					}
				}
			}
		}

		// Поищем порядок сортировки, order (обязательно после цифры должна идти точка а после нее пробел!)
		$pos_order_end = 0;
		for ($i = 0; $i < $length; $i++) {
			if (is_numeric(mb_substr($str,$i,1))) {
				$pos_order_end++;
				if ($i+1 <= $length && mb_substr($str, $i+1, 1) == ".") {
					$data['order'] = (int)mb_substr($str, 0, $pos_order_end);
					$pos_name_start = $i+2;
				}
			} else {
				// Если первая не цифра, дальше не ищем
				break;
			}
		}

		// Наименование
		$data['name'] = trim(mb_substr($str, $pos_name_start, $pos_opt_start-$pos_name_start));
		return $data;

	} // splitNameStr()


	/**
	 * ver 1
	 * update 2017-06-12
	 * Возвращает строку содержащую только цифры
	 */
	private function getStringOnlyDigit($str) {

		$result = '';

		// Длина строки
		$length = mb_strlen($str);

		// Перебираем каждый символ
		for ($i = 0; $i <= $length; $i++) {
			if (is_numeric(mb_substr($str, $i, 1))) {
				$result .= mb_substr($str, $i, 1);
			}
		}

		return $result;

	} // getStringOnlyDigit()


	/**
	 * ver 1
	 * update 2017-06-12
	 * Формирует путь к товару из названий категорий через разделитель
	 */
	private function getProductPath($product_id, $split = '/') {

		// Получим одну первую категорию
		$product_categories = $this->getProductCategories($product_id, 1);

		// Найдем родительские категории
		$parent_categories = $this->fillParentsCategories($product_categories);

		// добавляем к основной родительские
		$product_categories = array_merge($parent_categories, $parent_categories);

		// Убираем повторяющиеся
		$product_categories = array_unique($product_categories);

		$path = array();
		// Получим наименования категорий
		foreach ($product_categories as $category_id) {
			$query = $this->query("SELECT `name` FROM `" . DB_PREFIX . "category_description` WHERE `category_id` = " . $category_id . " AND `language_id` = " . $this->LANG_ID);
			if ($query->num_rows) {
				array_unshift($path, $this->translit($query->row['name'], '_'));
			}
		}

    	return implode($split, $path);

	} // getProductPath()


	/**
	 * ver 1
	 * update 2017-06-12
	 * Формирует путь с наименованием картинки к опции
	 */
	private function setProductOptionImage($data, $name, $value) {

		// Картинка опции
		$option_image 	= '';

		// Путь к картинки в дополнительном каталоге
		if ($this->config->get('exchange1c_product_options_image_folder')) {
			$option_image .= $this->config->get('exchange1c_product_options_image_folder') . '/';
		}

		// Путь к картинке из пути товара до основной категории
		if ($this->config->get('exchange1c_product_options_image_use_path_product') == 1) {
			$product_categories = $this->getProductPath($data['product_id']);
			$option_image .= $product_categories . '/';
		}

		// Название товара в транслите
		$query = $this->query("SELECT `name` FROM `" . DB_PREFIX . "product_description` WHERE `product_id` = " . $data['product_id'] . " AND `language_id` = " . $this->LANG_ID);
		if ($query->num_rows) {
			$option_image .= $this->translit($query->row['name'], '_') . '/';
		}

		// Префикс названия файла
		if ($this->config->get('exchange1c_product_options_image_prefix')) {
			$option_image .=  $this->config->get('exchange1c_product_options_image_prefix');
		}

		// Название файла
		if ($value) {

			$filename = $value;
			// Убираем все кроме цифр
			if ($this->config->get('exchange1c_product_options_image_only_digit')) {
				$filename = $this->getStringOnlyDigit($filename);
			}

			// Переводим в транслит
			$filename = $this->translit($filename, '_');

			// Добавляем к полному пути
			$option_image .= $filename . '.';
		}

		// Расширение файла
		$ext = 'jpg';
		if ($this->config->get('exchange1c_product_options_image_ext')) {
			$ext = $this->config->get('exchange1c_product_options_image_ext');
		}
		$option_image .= $ext;

		// Проверим существование файла
		if (file_exists(DIR_IMAGE . $option_image)) {
			return $option_image;
		}

		return 'no_image.png';

	} // setProductOptionImage()


	/**
	 * ver 7
	 * update 2017-06-12
	 * Разбор характеристики из файла
	 * Читает характеристики из файла(offers.xml или import.xml)
	 * Возвращает массив с элементами [features],[quantity],[price],[error]
	 * если в характеристиках нет остатков и цен, тогда не будет елементов [quantity],[price]
	 */
	private function parseFeatures($xml, $data) {

		// массив с данными
		$features 	= array();

		if (!$xml) {
			$this->ERROR = "parseFeatures(): в XML нет данных";
			return false;
		}

		if ($xml->ХарактеристикаТовара) {

			// Остаток характеристики, который будет записываться в опции
			// Если опций несколько, остаток у них будет одинаковый, так как они будут связанные
			$quantity = 0;

			// Остаток без складов или общий
			if (isset($data['quantity'])) {
				$quantity = 0;
			};

			// остаток характеристики (при наличии складов по всем складам)
			if (isset($data['quantities'])) {
				$quantity = array_sum($data['quantities']);
			}

			// Когда не указан Ид характеристики, значит несколько характеристик
			// Обычно так указываются в XML 2.07 в файле import_*.xml
			if (!$data['feature_guid']) {

				// ХАРАКТЕРИСТИКИ В СЕКЦИИ <ТОВАР>
				$feature_name_obj = array();
				$feature_name = "Характеристика";

				// Название характеристики нет
				foreach ($xml->ХарактеристикаТовара as $product_feature) {
					$feature_guid 		= (string)$product_feature->Ид;
					$option_value		= (string)$product_feature->Значение;
					$feature_name_obj[]	= $option_value;
					$this->log("> прочитана характеристика, Ид: '" . $feature_guid . "', значение: '" . $option_value . "'");

					$option_type = $this->config->get('exchange1c_product_options_type') ? $this->config->get('exchange1c_product_options_type') : 'select';

					// Ищем опцию и значение опции
					$option_id 			= $this->setOption("Характеристика", $option_type);
					$option_value_id 	= $this->setOptionValue($option_id, $option_value);

                    $options = array();
					$options[$option_value_id] = array(
						'option_id'			=> $option_id,
						'subtract'			=> $this->config->get('exchange1c_product_options_subtract') == 1 ? 1 : 0,
						'quantity'			=> 0
					);
					$features[$feature_guid] = array(
						'name'			=> $feature_name,
						'options'		=> $options
					);
				}

			} elseif ($this->config->get('exchange1c_product_options_mode') == 'feature') {

				// РЕЖИМ "ХАРАКТЕРИСТИКА"
				$option_value_obj = array();

				foreach ($xml->ХарактеристикаТовара as $feature_option) {
					// разбиваем название опции
					$option_name_split = $this->splitNameStr(htmlspecialchars(trim((string)$feature_option->Наименование)));
					$option_value_split = $this->splitNameStr(htmlspecialchars(trim((string)$feature_option->Значение)));
					$option_value_obj[] = $option_value_split['name'];
				}

				$option_name = $this->config->get('exchange1c_product_feature_name');
				if (!$option_name) {
					$option_name = 'Варианты';
				}

				$option_type = $this->config->get('exchange1c_product_options_type') ? $this->config->get('exchange1c_product_options_type') : 'select';

				if ($option_type == 'image') {
					// Если включена тип опции - изображение
					$this->log($data);
					//$image = $this->setProductOptionImage();
					//$this->log($image);
				}

				$option_value = implode(", ", $option_value_obj);

				$option_id 			= $this->setOption($option_name, $option_type);
				$option_value_id 	= $this->setOptionValue($option_id, $option_value);

				$options[$option_value_id]	= array(
					'option_id'			=> $option_id,
					'subtract'			=> $this->config->get('exchange1c_product_options_subtract') == 1 ? 1 : 0,
					'quantity'			=> $quantity
				);

				$features[$data['feature_guid']]['options'] = $options;
				$features[$data['feature_guid']]['name'] = $option_value;
				$this->log("Опция: '" . $option_name . "' = '" . $option_value . "'");

			} elseif ($this->config->get('exchange1c_product_options_mode') == 'certine') {

				// Отдельные товары
				// НЕ РЕАЛИЗОВАННО
				$this->log("parseFeatures(): Режим характеристики как отдельный товар пока не реализован", 2);

			} elseif ($this->config->get('exchange1c_product_options_mode') == 'related') {

				// РЕЖИМ - СВЯЗАННЫЕ ОПЦИИ

				foreach ($xml->ХарактеристикаТовара as $feature_option) {

					// ЗНАЧЕНИЕ ОПЦИИ
					$value_obj = $this->splitNameStr((string)$feature_option->Значение);
					$image	= '';

					// ОПЦИЯ
					$option_obj = $this->splitNameStr((string)$feature_option->Наименование);

					// Тип опции установленный из настроек
					$option_type = $this->config->get('exchange1c_product_options_type') ? $this->config->get('exchange1c_product_options_type') : 'select';

					// Тип по-умолчанию, если не будет переопределен
					switch($option_obj['option']) {
						case 'select':
							$option_type 	= "select";
							break;
						case 'radio':
							$option_type 	= "radio";
							break;
						case 'checkbox':
							$option_type 	= "checkbox";
							break;
						case 'image':
							$option_type 	= "image";
							$image			= $value_obj['option'] ? "options/" . $value_obj['option'] : "";
							break;
					}

					// Если включена загрузка изображений
					if ($this->config->get('exchange1c_product_options_image_load')) {
						$image = $this->setProductOptionImage($data, $option_obj['name'], $value_obj['name']);
					}

					// Если тип опции не зада из торговой системы, задаем из настроек
					if (!$option_type) {
						$option_type = $this->config->get('exchange1c_product_options_type') ? $this->config->get('exchange1c_product_options_type') : 'select';
					}

					$option_id			= $this->setOption($option_obj['name'], $option_type, $option_obj['order']);
					$option_value_id    = $this->setOptionValue($option_id, $value_obj['name'], $value_obj['order'], $image);

					$options[$option_value_id] = array(
						'option_guid'			=> $feature_option->Ид ? (string)$feature_option->Ид : "",
						'subtract'				=> $this->config->get('exchange1c_product_options_subtract') == 1 ? 1 : 0,
						'option_id'				=> $option_id,
						'type'					=> $option_type,
						'quantity'				=> $quantity
					);

					$this->log("Опция: '" . $option_obj['name'] . "' = '" . $value_obj['name'] . "'");
				}

				$features[$data['feature_guid']]['options'] = $options;
			}

		} else {

			// нет секции характеристика (XML 2.07, УТ 11.3)
			// нет секции характеристика (XML 2.03, 2.04 УТ для Украины)
			$this->log("> нет секции <ХарактеристикаТовара> - обрабатываем как обычный товар", 2);
		}

		return $features;

	} // parseFeature()


	/**
	 * ver 8
	 * update 2017-06-03
	 * Разбор предложений
	 */
	private function parseOffers($xml) {

		if (!$xml->Предложение) {
			$this->log("[!] Пустое предложение, пропущено");
			return true;
		}

		foreach ($xml->Предложение as $offer) {

			// Массив для хранения данных об одном предложении товара
			$data = array();

			// Получаем Ид товара и характеристики
			$guid 					= explode("#", (string)$offer->Ид);
			$data['product_guid']	= $guid[0];
			$data['feature_guid'] 	= isset($guid[1]) ? $guid[1] : '';

			$data['product_id'] = 0;

			$this->log("- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -", 2);
			// Если указан код, ищем товар по коду
			if ($this->config->get('exchange1c_synchronize_by_code') == 1 && $offer->Код) {
				$code	= (int)$offer->Код;
				$this->log("> Синхронизация товара по коду: " . $code, 2);
				$data['product_id'] = $this->getProductIdByCode($code);
			}

			// Пустые предложения игнорируются
			if (!$data['product_id']) {
				if (empty($data['product_guid'])) {
					// Проверка на пустое предложение
					$this->log("[!] Ид товара пустое, предложение игнорируется!", 2);
					continue;
				}
				// Читаем product_id, если нет товара выходим с ошибкой, значит что-то не так
				$data['product_id'] = $this->getProductIdByGuid($data['product_guid']);
			}

			// Нет товара, просто пропускаем
			if (!$data['product_id']) {
				$this->log("parseOffers() - Не найден товар в базе по Ид: '" . $data['product_guid'] . "'", 2);
				continue;
			}

			if ($offer->Наименование) {
				$data['feature_name'] = (string)$offer->Наименование;
			}

			$this->log("ПРЕДЛОЖЕНИЕ ТОВАРА ИД: " . $data['product_guid'] . ", product_id = " . $data['product_id'], 2);
			if ($data['feature_guid']) {
				$this->log("ХАРАКТЕРИСТИКА ИД: " . $data['feature_guid'], 2);
			}

			// Базовая единица измерения товара или характеристики
			if ($offer->БазоваяЕдиница) {
				$data['unit'] = $this->parseProductUnit($offer->БазоваяЕдиница);
			}

			// Штрихкод товара или характеристики
			if ($offer->Штрихкод) {
				$data['ean'] = (string)$offer->Штрихкод;
			}

			// По-умолчанию статус включаем, дальше по коду будет только отключение.
			$data['status'] = 1;

			// ОСТАТКИ (offers, rests)
			if ($offer->Склад) {
				// Остатки характеристики по складам
				$result = $this->parseQuantity($offer, $data);
				if ($this->ERROR) return false;
				if (count($result)) $data['quantities'] = $result;

			} elseif ($offer->Остатки) {
				// остатки характеристики (CML >= 2.09) файл rests_*.xml
				$result = $this->parseQuantity($offer->Остатки, $data);
				if ($this->ERROR) return false;
				if (count($result)) $data['quantities'] = $result;

			} else {
				// Нет складов
				// Общий остаток предложения по всем складам
				if ($offer->Количество) {
					$data['quantities'][0] = (float)$offer->Количество;
				}
			}

			// Если остатки не указаны, прочитаем остатки

			// Есть характеристики
			if ($offer->ХарактеристикиТовара) {
				$result = $this->parseFeatures($offer->ХарактеристикиТовара, $data);
				if ($this->ERROR) return false;
				if (count($result)) {
					if ($data['feature_guid']) {
						// Когда предложение является одной характеристикой
						$data['options'] = $result[$data['feature_guid']]['options'];
					} else {
						// Когда в предложении несколько характеристик
						$data['features'] = $result;
					}
				}
			}

			if ($this->config->get('exchange1c_price_import_mode') != 'disable') {
				// Цены товара или характеристики (offers*.xml, prices*.xml)
				if ($offer->Цены) {
					$result = $this->parsePrice($offer->Цены, $data);
					if ($this->ERROR) return false;
					if (count($result)) $data['prices'] = $result;
				}
			}

			unset($result);

			// Обновляем товар
			$this->updateProduct($data);
			if ($this->ERROR) return false;

			unset($data);

			// Прерывание процесса загрузки по наличию файла break в папке /system/storage/cache/exchange1c
			if (file_exists(DIR_CACHE . 'exchange1c/break')) {
				$this->ERROR = "parseOffers() - остановлен по наличию файла break";
				return false;
			}
		} // foreach()

		return true;

	} // parseOffers()


	/**
	 * ver 2
	 * update 2017-04-05
	 * Проверяет на наличие полной выгрузки в каталоге или в предложениях
	 */
	private function checkFullImport($xml) {

		if ((string)$xml['СодержитТолькоИзменения'] == "false") {
			$this->FULL_IMPORT = true;
			$this->log("[!] Загрузка полная...");
		} else {
			if ((string)$xml->СодержитТолькоИзменения == "false") {
				$this->FULL_IMPORT = true;
			} else {
				$this->log("[!] Загрузка только изменений...");
			}
		}

	} // checkFullImport()


	/**
	 * ver 4
	 * update 2017-06-03
	 * Загружает пакет предложений
	 */
	private function parseOffersPack($xml) {

		$offers_pack = array();
		$offers_pack['offers_pack_id']	= (string)$xml->Ид;
		$offers_pack['name']			= (string)$xml->Наименование;
		$offers_pack['directory_id']	= (string)$xml->ИдКаталога;
		$offers_pack['classifier_id']	= (string)$xml->ИдКлассификатора;

		$this->checkFullImport($xml);

		// Сопоставленные типы цен
		if ($this->config->get('exchange1c_price_import_mode') != 'disable') {
			if ($xml->ТипыЦен) {
				$offers_pack['price_types'] = $this->parseClassifierPriceType($xml->ТипыЦен);
				if ($this->ERROR) return false;
			}
		}

		// Загрузка складов
		if ($xml->Склады) {
			$offers_pack['warehouses'] = $this->parseClassifierWarehouses($xml->Склады);
			if ($this->ERROR) return false;
		}

		// Загружаем предложения
		if ($xml->Предложения) {
			$this->parseOffers($xml->Предложения, $offers_pack);
			if ($this->ERROR) return false;
		}

		return true;

	 } // parseOffersPack()


	/**
	 * ****************************** ФУНКЦИИ ДЛЯ ЗАГРУЗКИ ЗАКАЗОВ ******************************
	 */

	/**
	 * Меняет статусы заказов
	 *
	 * @param	int		exchange_status
	 * @return	bool
	 */
	private function sendMail($subject, $message, $order_info) {

		$this->log("==> sendMail()",2);

		$mail = new Mail();
		$mail->protocol = $this->config->get('config_mail_protocol');
		$mail->parameter = $this->config->get('config_mail_parameter');
		$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
		$mail->smtp_username = $this->config->get('config_mail_smtp_username');
		$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
		$mail->smtp_port = $this->config->get('config_mail_smtp_port');
		$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

		$mail->setTo($order_info['email']);
		$mail->setFrom($this->config->get('config_email'));
		$mail->setSender(html_entity_decode($order_info['store_name'], ENT_QUOTES, 'UTF-8'));
		$mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
		$mail->setText($message);
		//$mail->send();
		$this->log($mail, 2);

	} // sendMail()


	/**
	 * ver 5
	 * update 2017-06-01
	 * Меняет статусы заказов
	 *
	 * @param	int		exchange_status
	 * @return	bool
	 */
	public function queryOrdersStatus() {

		// Если статус новый пустой, тогда не меняем, чтобы не породить ошибку
		$new_status = $this->config->get('exchange1c_order_status_exported');
		if (!$new_status) {
			return false;
		}

		if ($this->config->get('exchange1c_order_date')) {
			$from_date = str_replace('T',' ',$this->config->get('exchange1c_order_date')) . ":00";
		} else {
			// При первом обмене это поле будет пустым, если не изменено вручную. Для пустого поля зададим начало столетия
			$from_date = '2001-01-01 00:00:00';
		}
		$this->log($from_date , 2);

		// По текущую дату и время
		$to_date = date('Y-m-d H:i:s');

		// Этот запрос будет использовать индексы поля date_modified
		$query = $this->query("SELECT `order_id`,`order_status_id` FROM `" . DB_PREFIX . "order` WHERE `date_modified` BETWEEN STR_TO_DATE('" . $from_date . "', '%Y-%m-%d %H:%i:%s') AND STR_TO_DATE('" . $to_date . "', '%Y-%m-%d %H:%i:%s')");

		$this->log("> Поиск заказов за период с: " . $from_date . " по " . $to_date, 2);

		if ($query->num_rows) {

			$this->NOW = date('Y-m-d H:i:s');

			$notify = $this->config->get('exchange1c_order_notify') == 1 ? 1 : 0;

			foreach ($query->rows as $order_data) {

				if ($order_data['order_status_id'] == $new_status) {
					$this->log("> Cтатус заказа #" . $order_data['order_id'] . " не менялся.", 2);
					continue;
				}

				// Меняем статус
				$query = $this->query("UPDATE `" . DB_PREFIX . "order` SET `order_status_id` = " . (int)$new_status . " WHERE `order_id` = " . (int)$order_data['order_id']);
				$this->log("> Изменен статус заказа #" . $order_data['order_id']);
				// Добавляем историю в заказ
				$query = $this->query("INSERT INTO `" . DB_PREFIX . "order_history` SET `order_id` = " . (int)$order_data['order_id'] . ", `comment` = 'Статус Вашего заказа изменен', `order_status_id` = " . (int)$new_status . ", `notify` = " . $notify . ", `date_added` = '" . $this->NOW . "'");
				$this->log("> Добавлена история в заказ (изменен статус) #" . $order_data['order_id'], 2);

				// Уведомление
				if ($notify) {
					$this->log("> Отправка уведомления на почту: " . $order_data['email'], 2);
					$this->sendMail('Заказ обновлен', 'Статус Вашего заказа изменен', $order_data);
				}
			}
		}

		return true;

	}  // queryOrdersStatus()


	/**
	 * Получает название статуса документа на текущем языке
	 *
	 */
	private function getOrderStatusName($order_staus_id) {
		if (!$this->LANG_ID) {
			$this->LANG_ID = $this->getLanguageId($this->config->get('config_language'));
		}
		$query = $this->query("SELECT `name` FROM `" . DB_PREFIX . "order_status` WHERE `order_status_id` = " . $order_staus_id . " AND `language_id` = " . $this->LANG_ID);
		if ($query->num_rows) {
			return $query->row['name'];
		}
		return "";
	} // getOrderStatusName()


	/**
	 * Получает название цены из настроек по группе покупателя
	 *
	 */
	private function getPriceTypeName($customer_group_id) {

		if (!$customer_group_id)
			return "";

		$config_price_type = $this->config->get('exchange1c_price_type');
		if (!$config_price_type)
			return "";

		foreach ($config_price_type as $price_type) {
			if ($price_type['customer_group_id'] == $customer_group_id)
				return $price_type['keyword'];
		}

		return "";

	} // getPriceTypeName()


	/**
	 * ver 4
	 * update 2017-06-19
	 * Получает GUID характеристики по выбранным опциям
	 */
	private function getFeatureGUID($product_id, $order_id) {

		$order_options = $this->model_sale_order->getOrderOptions($order_id, $product_id);
		$options = array();
		foreach ($order_options as $order_option) {
			$options[$order_option['product_option_id']] = $order_option['product_option_value_id'];
		}

		$product_feature_id = 0;
		foreach ($order_options as $order_option) {
			$query = $this->query("SELECT `product_feature_id` FROM `" . DB_PREFIX . "product_feature_value` WHERE `product_option_value_id` = " . (int)$order_option['product_option_value_id']);

			if ($query->num_rows) {
				if ($product_feature_id) {
					if ($product_feature_id != $query->row['product_feature_id']) {
						$this->ERROR = "[ОШИБКА] По опциям товара найдено несколько характеристик!";
						return false;
					}
				} else {
					$product_feature_id = $query->row['product_feature_id'];
				}
			}
		}

		$feature_guid = "";
		if ($product_feature_id) {
			// Получаем Ид
			$query = $this->query("SELECT `guid` FROM `" . DB_PREFIX . "product_feature` WHERE `product_feature_id` = " . (int)$product_feature_id);
			if ($query->num_rows) {
				$feature_guid = $query->row['guid'];
			}
		}

		return $feature_guid;

	} // getFeatureGUID


	/** ****************************** ФУНКЦИИ ДЛЯ ВЫГРУЗКИ ЗАКАЗОВ *******************************/


	/**
	 * ver 1
	 * update 2017-06-02
	 * Формирует адрес с полями и представлением в виде массива
	 */
	private function setCustomerAddress($order, $mode = 'shipping') {

		// Соответствие полей в XML и в базе данных
		$fields = array(
			'Почтовый индекс' 	=> 'postcode',
			'Страна' 			=> 'country',
			'Регион'			=> 'zone',
			'Район'				=> 'none',
			'Населенный пункт'	=> 'none',
			'Город'				=> 'city',
			'Улица'				=> 'none',
			'Дом'				=> 'none',
			'Корпус'			=> 'none',
			'Квартира'			=> 'none'
		);
		//'Представление'	=> $order['shipping_postcode'] . ', ' . $order['shipping_zone'] . ', ' . $order['shipping_city'] . ', ' . $order['shipping_address_1'] . ', '.$order['shipping_address_2'],

		$address = array();
		$counter = 0;

		// Представление
		$arName = array();

		// Формирование полей
		foreach ($fields as $type => $field) {

			if (isset($order[$mode . '_' . $field])) {

				// Формируем типы полей
				$address['АдресноеПоле' . $counter] = array(
					'Тип'		=> $type,
					'Значение' 	=> $order[$mode . '_' . $field]
				);

				// формируем наименование
				$arName[] = $order[$mode . '_' . $field];

			}
		}

		$address['Представление'] = implode(', ', $arName);

		return $address;

	} // setCustomerAddress()


	/**
	 * ver 1
	 * update 2017-06-02
	 * Формирует контактные данные контрагента
	 */
	private function setCustomerContacts($order) {
        $this->log($order, 2);
		// Соответствие полей в XML и в базе данных
		$fields = array(
			'ТелефонРабочий'	=> 'telephone',
			'Почта'				=> 'email'
		);

		$contact = array();
		$counter = 0;

		// Формирование полей
		foreach ($fields as $type => $field) {

			if (isset($order[$field])) {

				// Формируем типы полей
				$contact['Контакт' . $counter] = array(
					'Тип'		=> $type,
					'Значение' 	=> $order[$field]
				);
			}
			$counter++;
		}
		return $contact;

	} // setCustomerContacts()


	/**
	 * ver 2
	 * update 2017-06-03
	 * Формирует реквизиты документа
	 */
	private function setDocumentRequisites($order, $document) {

		$requisites = array();
		// Счетчик
		$counter = 0;

		$requisites['Дата отгрузки'] 				= $order['date'];
		$requisites['Статус заказа'] 				= $this->getOrderStatusName($order['order_status_id']);
		$requisites['Вид цен'] 						= $this->getPriceTypeName($order['customer_group_id']);
		$requisites['Контрагент'] 					= $order['username'];
//		$requisites['Склад'] 						= $this->getWarehouseName($order['warehouse_id']);
//		$requisites['Организация'] 					= 'Наша фирма';
//		$requisites['Подразделение'] 				= 'Интернет-магазин';
//		$requisites['Сумма включает НДС'] 			= 'true';
//		$requisites['Договор контрагента'] 			= 'Основной договор';
//		$requisites['Метод оплаты'] 				= 'Заказ по телефону';

		// Для 1С:Розница
//		$requisites['ТочкаСамовывоза'] 				= 'Название магазина';
//		$requisites['ВидЦенНаименование'] 			= 'Розничная';
//		$requisites['СуммаВключаетНДС'] 			= 'true';
//		$requisites['НаименованиеСкидки'] 			= 'Скидка 5%';
//		$requisites['ПроцентСкидки']				= 5;
//		$requisites['СуммаСкидки']					= 1000;
//		$requisites['СкладНаименование']			= 'Основной склад';
//		$requisites['ПодразделениеНаименование']	= 'Основное подразделение';
//		$requisites['Склад']						= 'Основной склад'

		$data = array();
		foreach ($requisites as $name => $value) {

			// Пропускаем пустые значения
			if (!$value) continue;

			$data['ЗначениеРеквизита'.$counter]	= array(
				'Наименование'		=> $name,
				'Значение'			=> $value
			);

			$counter ++;

		} // foreach

		return $data;

	} // setDocumentRequisites()


	/**
	 * ver 2
	 * update 2017-06-04
	 * Формирует Контрагента
	 */
	private function setCustomer(&$order) {

		$customer = array();

		if ($this->config->get('exchange1c_order_customer_export') != 1) {
			return $customer;
		}

		// Счетчик
		$counter = 0;

		// Обязательные поля покупателя для торговой системы
		$customer = array(
			'Ид'					=> $order['customer_id'] . '#' . $order['email'],
			'Роль'					=> 'Покупатель',
			'Наименование'			=> $order['username'],
			'ПолноеНаименование'	=> $order['username'],
			'Фамилия'				=> $order['payment_lastname'],
			'Имя'					=> $order['payment_firstname'],
			'Отчество'				=> isset($order['payment_patronymic']) ? $order['payment_patronymic'] : "",
			'АдресРегистрации'		=> $this->setCustomerAddress($order),
			'Контакты'				=> $this->setCustomerContacts($order),
		);
		if (isset($order['payment_inn'])) {
			$customer['ИНН'] = $order['payment_inn'];
		}


		// Поля для юр. лица или физ. лица
		if ($order['payment_company']) {

			// Если плательщиком является организация
			$customer['ОфициальноеНаименование'] 	= isset($order['payment_company']) 	? $order['payment_company'] : "";
			$customer['ПолноеНаименование'] 		= isset($order['payment_company']) 	? $order['payment_company'] : "";
			$customer['ОКПО'] 						= isset($order['payment_okpo']) 	? $order['payment_okpo'] 	: "";
			$customer['КПП'] 						= isset($order['payment_kpp']) 		? $order['payment_kpp'] 	: "";

		} else {

			// Покупатель - физическое лицо
			$customer['Наименование'] 				= $order['username'];
		}

		return $customer;

	} // setCustomer()


	/**
	 * ver 2
	 * update 2017-06-20
	 * Возвращает единицу измерения товара
	 */
	private function getProductUnit($unit_id, $product_id) {

		$query = $this->query("SELECT `p`.`ratio`,`u`.`name`,`u`.`number_code`,`u`.`full_name` FROM `" . DB_PREFIX . "product_unit` `p` LEFT JOIN `" . DB_PREFIX . "unit_to_1c` `u` ON (`p`.`unit_id` = `u`.`unit_id`) WHERE `p`.`product_id` = " . $product_id . " AND `u`.`unit_id` = " . $unit_id . " ORDER BY `p`.`ratio`");
		if ($query->num_rows) {
			return $query->row;
		}

		return false;

	} // getProductUnit()

	/**
	 * ver 9
	 * update 2017-06-19
	 * Выгружает заказы в торговую систему
	 */
	public function queryOrders() {

		$this->log("==== Выгрузка заказов ====",2);

		$orders_export = array();

		// Выгрузка измененных заказов
		if ($this->config->get('exchange1c_orders_export_modify')) {

			if ($this->config->get('exchange1c_order_date')) {
				$from_date = str_replace('T',' ',$this->config->get('exchange1c_order_date')) . ":00";
			} else {
				// При первом обмене это поле будет пустым, если не изменено вручную. Для пустого поля зададим начало столетия
				$from_date = '2001-01-01 00:00:00';
			}
			$this->log($from_date , 2);

			// По текущую дату и время
			$to_date = date('Y-m-d H:i:s');

			// Этот запрос будет использовать индексы поля date_modified
			$query = $this->query("SELECT `order_id`,`order_status_id` FROM `" . DB_PREFIX . "order` WHERE `date_modified` BETWEEN STR_TO_DATE('" . $from_date . "', '%Y-%m-%d %H:%i:%s') AND STR_TO_DATE('" . $to_date . "', '%Y-%m-%d %H:%i:%s')");

			if ($query->num_rows) {
				foreach ($query->rows as $row) {
					$orders_export[$row['order_id']] = $row['order_status_id'];
				}
			}
		}

		// Выгрузка заказов со статусом
		if ($this->config->get('exchange1c_order_status_export') != 0) {

			$query = $this->query("SELECT `order_id`,`order_status_id` FROM `" . DB_PREFIX . "order` WHERE `order_status_id` = " . (int)$this->config->get('exchange1c_order_status_export'));

			if ($query->num_rows) {

				foreach ($query->rows as $row) {

					// Пропускаем если такой заказ уже выгружается
					if (isset($orders_export[$row['order_id']])) {
						continue;
					}

					$orders_export[$row['order_id']] = $row['order_status_id'];
				}
			}
		}

		// Валюта документа
		$currency = $this->config->get('exchange1c_order_currency') ? $this->config->get('exchange1c_order_currency') : 'руб.';

		$document = array();

		if (count($orders_export)) {

			$document_counter = 0;

			$this->load->model('customer/customer_group');
			$this->load->model('sale/order');

			foreach ($orders_export as $order_id => $order_status_id) {

				$order = $this->model_sale_order->getOrder($order_id);
				$this->log("> Выгружается заказ #" . $order['order_id']);

				$order['date'] = date('Y-m-d', strtotime($order['date_added']));
				$order['time'] = date('H:i:s', strtotime($order['date_added']));
				$customer_group = $this->model_customer_customer_group->getCustomerGroup($order['customer_group_id']);

				// Шапка документа
				$document['Документ' . $document_counter] = array(
					 'Ид'          => $order['order_id']
					,'Номер'       => $order['order_id']
					,'Дата'        => $order['date']
					,'Время'       => $order['time']
					,'Валюта'      => $currency
					,'Курс'        => 1
					,'ХозОперация' => 'Заказ товара'
					,'Роль'        => 'Продавец'
					,'Сумма'       => $order['total']
					,'Комментарий' => $order['comment']
					//,'Соглашение'  => $customer_group['name'] // the agreement
				);

				// Разбирает ФИО в массив, чтобы получить отчество
				$username = array();
				$fields = array('lastname', 'firstname', 'patronymic');

				// Формируем массив ФИО
				foreach ($fields as $field) {
					if ($order['payment_' . $field]) {
						$username[] = $order['payment_' . $field];
					}
				}

				// Собираем полное наименование покупателя, ФИО
				$order['username'] = implode(" ", $username);

				// ПОКУПАТЕЛЬ (КОНТРАГЕНТ)
				$document['Документ' . $document_counter]['Контрагенты']['Контрагент'] = $this->setCustomer($order);

				// РЕКВИЗИТЫ ДОКУМЕНТА
				$document['Документ' . $document_counter]['ЗначенияРеквизитов'] = $this->setDocumentRequisites($order, $document);

				// ТОВАРЫ ДОКУМЕНТА
				$products = $this->model_sale_order->getOrderProducts($order_id);

				$product_counter = 0;
				foreach ($products as $product) {
					$product_guid = $this->getGuidByProductId($product['product_id']);
					$document['Документ' . $document_counter]['Товары']['Товар' . $product_counter] = array(
						 'Ид'             => $product_guid
						,'Наименование'   => $product['name']
						,'ЦенаЗаЕдиницу'  => $product['price']
						,'Количество'     => $product['quantity']
						,'Сумма'          => $product['total']
						,'Скидки'         => array('Скидка' => array(
							'УчтеноВСумме' => 'false'
							,'Сумма' => 0
							)
						)
						,'ЗначенияРеквизитов' => array(
							'ЗначениеРеквизита' => array(
								'Наименование' => 'ТипНоменклатуры'
								,'Значение' => 'Товар'
							)
						)
					);
					$current_product = &$document['Документ' . $document_counter]['Товары']['Товар' . $product_counter];
					// Резервирование товаров
					if ($this->config->get('exchange1c_order_reserve_product') == 1) {
						$current_product['Резерв'] = $product['quantity'];
					}

					// Характеристики
					$feature_guid = $this->getFeatureGuid($product['order_product_id'], $order_id);
					if ($feature_guid) {
						$current_product['Ид'] .= "#" . $feature_guid;
					}

					// Базовая единица
					$unit = $this->getProductUnit($product['unit_id'], $product['product_id']);
					$this->log($unit, 2);
					if ($unit) {
						$current_product['БазоваяЕдиница'] = array(
							'Код' 					=> $unit['number_code'],
							'НаименованиеПолное' 	=> $unit['full_name']
						);
					} else {
						$current_product['БазоваяЕдиница'] = array(
							'Код' 					=> '796',
							'НаименованиеПолное' 	=> 'Штука'
						);
					}

					$product_counter++;
				}

				$document_counter++;

			} // foreach ($query->rows as $orders_data)

		} // if (count($orders_export))

		// Формируем заголовок
		$root = '<?xml version="1.0" encoding="utf-8"?><КоммерческаяИнформация ВерсияСхемы="2.07" ДатаФормирования="' . date('Y-m-d', time()) . '" />';

		$root_xml = new SimpleXMLElement($root);
		$xml = $this->array_to_xml($document, $root_xml);

		// Проверка на запись файлов в кэш
		$cache = DIR_CACHE . 'exchange1c/';
		if (@is_writable($cache)) {
			// запись заказа в файл
			$f_order = @fopen($cache . 'orders.xml', 'w');
			if (!$f_order) {
				$this->log("Нет доступа для записи в папку: " . $cache);
			} else {
				fwrite($f_order, $xml->asXML());
				fclose($f_order);
			}
		} else {
			$this->log("Папка " . $cache . " не доступна для записи, файл заказов не может быть сохранен!",1);
		}

		return $xml->asXML();

	} // queryOrders()


	/**
	 * Адрес
	 */
	private function parseAddress($xml) {
		if (!$xml) return "";
		return (string)$xml->Представление;
	} // parseAddress()


	/**
	 * Банк
	 */
	private function parseBank($xml) {
		if (!$xml) return "";
		return array(
			'correspondent_account'	=> (string)$xml->СчетКорреспондентский,
			'name'					=> (string)$xml->Наименование,
			'bic'					=> (string)$xml->БИК,
			'address'				=> $this->parseAddress($xml->Адрес)
		);
	} // parseBank()


	/**
	 * Расчетные счета
	 */
	private function parseAccount($xml) {
		if (!$xml) return "";
		$data = array();
		foreach ($xml->РасчетныйСчет as $object) {
			$data[]	= array(
				'number'	=> $object->Номерсчета,
				'bank'		=> $this->parseBank($object->Банк)
			);
		}
		return $data;
	} // parseAccount()


	/**
	 * Владелец
	 */
	private function parseOwner($xml) {
		if (!$xml) return "";
		return array(
			'id'		=> (string)$xml->Ид,
			'name'		=> (string)$xml->Наименование,
			'fullname'	=> (string)$xml->ПолноеНаименование,
			'inn'		=> (string)$xml->ИНН,
			'account'	=> $this->parseAccount($xml->РасчетныеСчета)
		);
	} // parseOwner()


	/**
	 * Возвращает курс валюты
	 */
	private function getCurrencyValue($code) {
		$query = $this->query("SELECT `value` FROM `" . DB_PREFIX . "currency` WHERE `code` = '" . $code . "'");
		if ($query->num_rows) {
			return $query->row['value'];
		}
		return 1;
	} // getCurrencyValue()


	/**
	 * ver 3
	 * update 2017-05-30
	 * Возвращает валюту по коду
	 */
	private function getCurrencyByCode($code) {

		$data = array();

		if ($code == "643") {

			// Это временнон решение
			$data['currency_id'] = $this->getCurrencyId("RUB");
			$data['currency_code'] = "RUB";
			$data['currency_value'] = $this->getCurrencyValue("RUB");

		} else {

			$data['currency_id'] = $this->getCurrencyId($code);
			$data['currency_code'] = $code;
			$data['currency_value'] = $this->getCurrencyValue($code);
		}

		return $data;

	} // getCurrencyByCode()


	/**
	 * ver 2
	 * update 2017-04-05
	 * Устанавливает опции заказа в товаре
	 */
	private function setOrderProductOptions($order_id, $product_id, $order_product_id, $product_feature_id = 0) {

		// удалим на всякий случай если были
		$this->query("DELETE FROM `" . DB_PREFIX . "order_option` WHERE `order_product_id` = " . $order_product_id);

		// если есть, добавим
		if ($product_feature_id) {
			$query_feature = $this->query("SELECT `pfv`.`product_option_value_id`,`pf`.`name` FROM `" . DB_PREFIX . "product_feature_value` `pfv` LEFT JOIN `" . DB_PREFIX . "product_feature` `pf` ON (`pfv`.`product_feature_id` = `pf`.`product_feature_id`) WHERE `pfv`.`product_feature_id` = " . $product_feature_id . " AND `pfv`.`product_id` = " . $product_id);
			$this->log($query_feature,2);
			foreach ($query_feature->rows as $row_feature) {
				$query_options = $this->query("SELECT `pov`.`product_option_id`,`pov`.`product_option_value_id`,`po`.`value`,`o`.`type` FROM `" . DB_PREFIX . "product_option_value` `pov` LEFT JOIN `" . DB_PREFIX . "product_option` `po` ON (`pov`.`product_option_id` = `po`.`product_option_id`) LEFT JOIN `" . DB_PREFIX . "option` `o` ON (`o`.`option_id` = `pov`.`option_id`) WHERE `pov`.`product_option_value_id` = " . $row_feature['product_option_value_id']);
				$this->log($query_options,2);
				foreach ($query_options->rows as $row_option) {
					$this->query("INSERT INTO `" . DB_PREFIX . "order_option` SET `order_id` = " . $order_id . ", `order_product_id` = " . $order_product_id . ", `product_option_id` = " . $row_option['product_option_id'] . ", `product_option_value_id` = " . $row_option['product_option_value_id'] . ", `name` = '" . $this->db->escape($row_option['value']) . "', `value` = '" . $this->db->escape($row_feature['name']) . "', `type` = '" . $row_option['type'] . "'");
					$order_option_id = $this->db->getLastId();
					$this->log("order_option_id: ".$order_option_id,2);
				}
			}
		}
		$this->log("Записаны опции в заказ",2);

	} // setOrderProductOptions()


	/**
	 * ver 2
	 * update 2017-04-05
	 * Добавляет товар в заказ
	 */
	private function addOrderProduct($order_id, $product_id, $price, $quantity, $total, $tax = 0, $reward = 0) {

		$query = $this->query("SELECT `pd`.`name`,`p`.`model` FROM `" . DB_PREFIX . "product` `p` LEFT JOIN `" . DB_PREFIX . "product_description` `pd` ON (`p`.`product_id` = `pd`.`product_id`) WHERE `p`.`product_id` = " . $product_id);
		if ($query->num_rows) {
			$name = $query->row['name'];
			$model = $query->row['model'];

			$sql = "";
			$sql .= ($tax) ? ", `tax` = " . $tax : "";
			$sql .= ($reward) ? ", `reward` = " . $reward : "";
			$this->query("INSERT INTO `" . DB_PREFIX . "order_product` SET `product_id` = " . $product_id . ",
				`order_id` = " . $order_id . ",
				`name` = '" . $this->db->escape($name) . "',
				`model` = '" . $this->db->escape($model) . "',
				`price` = " . $price . ",
				`quantity` = " . $quantity . ",
				`total` = " . $total . $sql);
			return $this->db->getLastId();
		}
		return 0;
		$this->log("Записаны товары в заказ",2);

	} // addOrderProduct()


	/**
	 * ver 2
	 * update 2017-04-05
	 * Удаляем товар из заказа со всеми опциями
	 */
	private function deleteOrderProduct($order_product_id) {

		$this->query("DELETE FROM `" . DB_PREFIX . "order_product` WHERE `order_product_id` = " . $order_product_id);
		$this->query("DELETE FROM `" . DB_PREFIX . "order_option` WHERE `order_product_id` = " . $order_product_id);
		$this->log("Удалены товары и опции в заказе",2);

	} // deleteOrderProduct()


	/**
	 * ver 2
	 * update 2017-04-05
	 * Меняет статус заказа
	 */
	private function getOrderStatusLast($order_id) {

		$order_status_id = 0;
		$query = $this->query("SELECT `order_status_id` FROM `" . DB_PREFIX . "order_history` WHERE `order_id` = " . $order_id . " ORDER BY `date_added` DESC LIMIT 1");
		if ($query->num_rows) {
			$this->log("<== getOrderStatusLast() return: " . $query->row['order_status_id'],2);
			$order_status_id = $query->row['order_status_id'];
		}
		$this->log("Получен статус заказа = " . $order_status_id, 2);
		return $order_status_id;
	}


	/**
	 * ver 2
	 * update 2017-04-05
	 * Меняет статус заказа
	 */
	private function changeOrderStatus($order_id, $status_name) {

		$query = $this->query("SELECT `order_status_id` FROM `" . DB_PREFIX . "order_status` WHERE `language_id` = " . $this->LANG_ID . " AND `name` = '" . $this->db->escape($status_name) . "'");
		if ($query->num_rows) {
			$new_order_status_id = $query->row['order_status_id'];
		} else {
			$this->ERROR = "changeOrderStatus() - Статус с названием '" . $status_name . "' не найден";
			return false;
 		}
		$this->log("[i] Статус id у названия '" . $status_name . "' определен как " . $new_order_status_id,2);

		// получим старый статус
		$order_status_id = $this->getOrderStatusLast($order_id);
		if (!$order_status_id) {
			$this->ERROR = "changeOrderStatus() - Ошибка получения старого статуса документа!";
			return 0;
		}

		if ($order_status_id == $new_order_status_id) {
			$this->log("Статус документа не изменился", 2);
			return true;
		}

		// если он изменился, изменим в заказе
		$this->query("INSERT INTO `" . DB_PREFIX . "order_history` SET `order_id` = " . $order_id . ", `order_status_id` = " . $new_order_status_id . ", `date_added` = '" . $this->NOW . "'");

		$this->log("Изменен статус документа",2);
		return true;

	} // changeOrderStatus()


	/**
	 * ver 3
	 * update 2017-05-30
	 * Обновляет документ
	 */
	private function updateDocument($doc, $order, $products) {

		$order_fields = array();

		$this->log($doc, 2);
		$this->log($products, 2);

		// обновим входящий номер
		if (!empty($doc['invoice_no'])) {
			$order_fields['invoice_no'] = $doc['invoice_no'];
		}

		// проверим валюту
		if (!empty($doc['currency'])) {
			$currency = $this->getCurrencyByCode($doc['currency']);
			$order_fields['currency_id'] = $currency['currency_id'];
			$order_fields['currency_code'] = $currency['currency_code'];
			$order_fields['currency_value'] = $currency['currency_value'];
		}

		// проверим сумму
		if (!empty($doc['total'])) {
			if ($doc['total'] != $order['total']) {
				$order_fields['total'] = $doc['total'];
			}
		}

		// статус заказа
		if (!empty($doc['status'])) {
			$this->changeOrderStatus($doc['order_id'], $doc['status']);
			if ($this->ERROR) return false;
		}

		$old_products = $products;

		// проверим товары, порядок должен быть такой же как и в торговой системе
		if (!empty($doc['products'])) {

			foreach ($doc['products'] as $key => $doc_product) {

            	$this->log("Товар: ".$doc_product['name'],2);

				$order_product_fields = array();
				$order_option_fields = array();
				$update = false;
				$product_feature_id = isset($doc_product['product_feature_id']) ? $doc_product['product_feature_id'] : 0;

				if (isset($products[$key])) {
					$product = $products[$key];
					$order_product_id = $product['order_product_id'];

					unset($old_products[$key]);

					// получим характеристику товара в заказе
					$old_feature_guid = $this->getFeatureGuid($doc['order_id'], $order_product_id);
					$this->log("old_feature_guid: " . $old_feature_guid,2);
					$this->log("new_feature_guid: " . $doc_product['product_feature_guid'],2);

					// сравним
					if ($doc_product['product_id'] == $product['product_id']) {

						$update = true;

						if ($old_feature_guid != $doc_product['product_feature_guid']) {

							// изменить характеристику
							$this->setOrderProductOptions($doc['order_id'], $doc_product['product_id'], $order_product_id, $product_feature_id);
							if ($this->ERROR) return false;
						}

						// обновим если менялось количество или цена
						if ($product['quantity'] != $doc_product['quantity'] || $product['price'] != $doc_product['price']) {
							$order_product_fields[] = "`quantity` = " . $doc_product['quantity'];
							$order_product_fields[] = "`price` = " . $doc_product['price'];
							$order_product_fields[] = "`total` = " . $doc_product['total'];
							//$order_product_fields[] = "`tax` = " . $doc_product['tax'];
							//$order_product_fields[] = "`reward` = " . $doc_product['reward'];
						}
					} else {

						// товар отличается, заменить полностью
						$order_product_fields[] = "`product_id` = " . $doc_product['product_id'];
						$order_product_fields[] = "`name` = '" . $this->db->escape($doc_product['product_id']) . "'";
						$order_product_fields[] = "`model` = '" . $this->db->escape($doc_product['model']) . "'";
						$order_product_fields[] = "`price` = " . $doc_product['price'];
						$order_product_fields[] = "`quantity` = " . $doc_product['quantity'];
						$order_product_fields[] = "`total` = " . $doc_product['total'];
						$order_product_fields[] = "`tax` = " . $doc_product['tax'];
						// бонусные баллы
						$order_product_fields[] = "`reward` = " . $doc_product['reward'];

						// заменить опции, если есть
						// считать опции с характеристики и записать в заказ у товара $order_product_id
						$this->setOrderProductOptions($doc['order_id'], $doc_product['product_id'], $order_product_id, $product_feature_id);
						if ($this->ERROR) return false;

					} // if

				} else {
					// if (!isset($products[$key]))

					// Добавить товар в документ
					$order_product_id = $this->addOrderProduct($doc['order_id'], $doc_product['product_id'], $doc_product['price'], $doc_product['quantity'], $doc_product['total']);
					if ($this->ERROR) return false;

					if ($order_product_id && $product_feature_id) {
						// добавлен товар и есть опции
						$this->setOrderProductOptions($doc['order_id'], $doc_product['product_id'], $order_product_id, $product_feature_id);
						if ($this->ERROR) return false;
					}

				} // if (isset($products[$key]))

				$this->log("update: ".$update,2);
				$this->log("fields: ",2);
				$this->log($order_product_fields,2);

				// если надо обновить поля товара в заказе
				if ($order_product_fields) {

					$fields = implode(", ", $order_product_fields);

					if ($update) {
						$this->query("UPDATE `" . DB_PREFIX . "order_product` SET " . $fields . " WHERE `order_product_id` = " . $products[$key]['order_product_id']);
						$this->log("Товар '" . $doc_product['name'] . "' обновлен в заказе",2);
					} else {

					}

				} else {

					$this->log("Товар '" . $doc_product['name'] . "' в заказе не изменился",2);
				}
			} // foreach

			foreach ($old_products as $product) {
				$this->deleteOrderProduct($product['order_product_id']);
				if ($this->ERROR) return false;
			}
		} // if

		$this->log("Документ обновлен",2);

		return true;

	} // updateDocument()


	/**
	 * ver 2
	 * update
	 * Читает их XML реквизиты документа
	 */
	private function parseDocumentRequisite($xml, &$doc) {

		foreach ($xml->ЗначениеРеквизита as $requisite) {
			// обрабатываем только товары
			$name 	= (string)$requisite->Наименование;
			$value 	= (string)$requisite->Значение;
			$this->log("> Реквизит документа: " . $name. " = " . $value,2);
			switch ($name){
				case 'Номер по 1С':
					$doc['invoice_no'] = $value;
				break;
				case 'Дата по 1С':
					$doc['datetime'] = $value;
				break;
				case 'Проведен':
					$doc['posted'] = $value;
				break;
				case 'Статус заказа':
					$doc['status'] = $value;
				break;
				case 'Номер оплаты по 1С':
					$doc['NumPay'] = $value;
				break;
				case 'Дата оплаты по 1С':
					$doc['DataPay'] = $value;
				break;
				case 'Номер отгрузки по 1С':
					$doc['NumSale'] = $value;
				break;
				case 'Дата отгрузки по 1С':
					$doc['DateSale'] = $value;
				break;
				case 'ПометкаУдаления':
					$doc['DeletionMark'] = $value;
				break;
				case 'Проведен':
					$doc['Posted'] = $value;
				break;
				default:
			}
		}
		$this->log("Реквизиты документа прочитаны",2);

	} // parseDocumentRequisite()


	/**
	 * ver 2
	 * update 2017-04-05
	 * Контрагент
	 * Получает ID покупателя и адреса
	 */
	private function parseDocumentCustomer($xml, &$doc) {

		if (!$xml) {
			$this->ERROR = "parseDocumentCustomer() - Нет данных в XML";
			return false;
		}

		$doc['customer_id']	= 0;
		$doc['address_id']	= 0;

		$customer_name	= (string)$xml->Контрагент->Наименование;
		$customer_name_split	= explode(" ", $customer_name);
		//$this->log($customer_name_split,2);
		$lastname				= isset($customer_name_split[0]) ? $customer_name_split[0] : "";
		$firstname				= isset($customer_name_split[1]) ? $customer_name_split[1] : "";

		// поиск покупателя по имени получателя
		if (!$doc['customer_id']) {
			$query = $this->query("SELECT `address_id`,`customer_id` FROM `" . DB_PREFIX . "address` WHERE `firstname` = '" . $this->db->escape($firstname) . "' AND `lastname` = '" . $this->db->escape($lastname) . "'");
			if ($query->num_rows) {
				$doc['customer_id'] = $query->row['customer_id'];
				$doc['address_id'] = $query->row['address_id'];
			}
		}

		// поиск покупателя по имени
		if (!$doc['customer_id']) {
			$query = $this->query("SELECT `customer_id` FROM `" . DB_PREFIX . "customer` WHERE `firstname` = '" . $this->db->escape($firstname) . "' AND `lastname` = '" . $this->db->escape($lastname) . "'");
			if ($query->num_rows) {
				$doc['customer_id'] = $query->row['customer_id'];
			}
		}

		if (!$doc['customer_id']) {
			$this->ERROR = "parseDocumentCustomer() - Покупатель '" . $customer_name . "' не найден в базе";
			return false;
		}

		$this->log("Покупатель в документе прочитан",2);
		return true;

	} // parseDocumentCustomer()


	/**
	 * ver 4
	 * update 2017-05-30
	 * Товары документа
	 */
	private function parseDocumentProducts($xml, &$doc) {

		if (!$xml) {
			$this->ERROR = "parseDocumentProducts(): Нет данных в XML";
			return false;
		}

		$this->log($xml, 2);
		foreach ($xml->Товар as $product) {
			$guid		= explode("#", (string)$product->Ид);
			if (!$guid) {
				$this->ERROR = "parseDocumentProducts(): не определен GUID товара";
				return false;
			}

			$data = array();

			if ($product->Наименование) {
				$data['name'] = (string)$product->Наименование;
			}

			if (isset($guid[0])) {
				$data['product_guid'] = $guid[0];
				$data['product_id'] = $this->getProductIdByGuid($data['product_guid']);
				if (!$data['product_id'])
					$this->ERROR = "parseDocumentProducts(): Товар '" . $data['name'] . "' не найден в базе по Ид '" . $data['product_guid'] . "'";
					return false;
			} else {
				$this->ERROR = "parseDocumentProducts(): Товар '" . $data['name'] . "' не может быть найден в базе по пустому Ид";
				return false;
			}

			if (isset($guid[1])) {
				$data['product_feature_guid'] = $guid[1];
				$data['product_feature_id'] = $this->getProductFeatureIdByGuid($data['product_feature_guid']);
				if (!$data['product_feature_id'])
					$this->ERROR = "parseDocumentProducts(): Характеристика товара '" . $data['name'] . "' не найдена в базе по Ид '" . $data['product_feature_guid'] . "'";
					return false;
			} else {
				$data['product_feature_id'] = 0;
			}

			if ($product->Артикул) {
				$data['sku'] = (string)$product->Артикул;
				$data['model'] = (string)$product->Артикул;
			}
			if ($product->БазоваяЕдиница) {
				$data['unit0'] = array(
					'code'		=> $product->БазоваяЕдиница->Наименование['Код'],
					'name'		=> $product->БазоваяЕдиница->Наименование['НаименованиеПолное'],
					'eng'		=> $product->БазоваяЕдиница->Наименование['МеждународноеСокращение']
				);
			}
			if ($product->ЦенаЗаЕдиницу) {
				$data['price'] = (float)$product->ЦенаЗаЕдиницу;
			}
			if ($product->Количество) {
				$data['quantity'] = (float)$product->Количество;
			}
			if ($product->Сумма) {
				$data['total'] = (float)$product->Сумма;
				// налог временно нулевой
				$data['tax'] = 0;
			}
			if ($product->Единица) {
				$data['unit'] = array(
					'unit_id'	=> $this->getUnitId((string)$product->Единица),
					'ratio'		=> (string)$product->Коэффициент
				);

			}

			$doc['products'][] = $data;
		}

		$this->log("Товары документа прочитаны", 2);
		return true;

	} // parseDocumentProducts()


	/**
	 * ver 3
	 * update 2017-05-18
	 * Разбор классификатора
	 */
	private function parseClassifier($xml) {

		$data = array();
		$data['guid']			= (string)$xml->Ид;
		$data['name']			= (string)$xml->Наименование;
		$this->setStore($data['name']);

		// Организация
		if ($xml->Владелец) {
			$this->log("Организация", 2);
			$data['owner']			= $this->parseOwner($xml->Владелец);
			unset($xml->Владелец);
		}

		if ($xml->ТипыЦен) {
			$this->log("Типы цен с классификатора (CML >= v2.09)", 2);
			$data['price_types'] = $this->parseClassifierPriceType($xml->ТипыЦен);
			if ($this->ERROR) return false;
			unset($xml->ТипыЦен);
		}

		if ($xml->Склады) {
			$this->log("Склады из классификатора (CML >= v2.09)", 2);
			$this->parseClassifierWarehouses($xml->Склады);
			if ($this->ERROR) return false;
			unset($xml->Склады);
		}

		if ($xml->ЕдиницыИзмерения) {

			$this->log("Единицы измерений из классификатора (CML >= v2.09)",2);
			$units = $this->parseClassifierUnits($xml->ЕдиницыИзмерения);

			if ($this->config->get('exchange1c_parse_unit_in_memory') == 1) {
				$data['units'] = $units;
				$this->log($units, 2);
			}

			if ($this->ERROR) return false;
			unset($xml->ЕдиницыИзмерения);
		}

		if ($xml->Свойства) {

			$this->log("Атрибуты (Свойства в ТС) из классификатора",2);

			$data['attributes']	= $this->parseClassifierAttributes($xml->Свойства);

			if ($this->ERROR) return false;
			unset($xml->Свойства);
		}

		if ($this->config->get('exchange1c_import_categories') == 1) {

        	$categories = array();

			if ($this->config->get('exchange1c_import_categories_mode') == 'groups') {

				// Группы номенклатуры
				if ($xml->Группы) {

					$categories = $this->parseClassifierCategories($xml->Группы, 0, $data);

					if ($this->ERROR) return false;

					unset($xml->Группы);

					$this->log("Группы товаров из классификатора загружены",2);
				}

			} else {

				// Товарные категории
				if ($xml->Категории) {

					$categories = $this->parseClassifierProductCategories($xml->Категории, 0, $data);

					if ($this->ERROR) return false;

					unset($xml->Категории);

					$this->log("Категории товаров из классификатора загружены",2);
				}
			}

			if ($this->config->get('exchange1c_parse_categories_in_memory') == 1 && count($categories)) {
				$data['categories'] = $categories;
			}

		}

		$this->log("Классификатор загружен", 2);
		return $data;

	} // parseClassifier()


	/**
	 * ver 2
	 * update 2017-05-30
	 * Разбор документа
	 */
	private function parseDocument($xml) {

		$order_guid		= (string)$xml->Ид;
		$order_id		= (string)$xml->Номер;

		$this->log($xml, 2);

		$doc = array(
			'order_id'		=> $order_id,
			'date'			=> (string)$xml->Дата,
			'time'			=> (string)$xml->Время,
			'currency'		=> (string)$xml->Валюта,
			'total'			=> (float)$xml->Сумма,
			'doc_type'		=> (string)$xml->ХозОперация,
			'date_pay'		=> (string)$xml->ДатаПлатежа
		);

		// Просроченный платеж если date_pay будет меньше текущей
		if ($doc['date_pay']) {
			$this->log("По документу просрочена оплата");
		}

		$this->parseDocumentCustomer($xml->Контрагенты, $doc);
		if ($this->ERROR) return;

		$this->parseDocumentProducts($xml->Товары, $doc);
		if ($this->ERROR) return;

		$this->parseDocumentRequisite($xml->ЗначенияРеквизитов, $doc);

		$this->load->model('sale/order');
		$order = $this->model_sale_order->getOrder($order_id);
		if ($order) {
			$products = $this->model_sale_order->getOrderProducts($order_id);
		} else {
			return "Заказ #" . $doc['order_id'] . " не найден в базе";
		}

		$this->updateDocument($doc, $order, $products);
		if ($this->ERROR) return;

		$this->log("[i] Прочитан документ: Заказ #" . $order_id . ", Ид '" . $order_guid . "'");

		return true;

	} // parseDocument()


	/**
	 * ver 3
	 * update 2017-04-16
	 * Импорт файла
	 */
	public function importFile($importFile, $type) {

		// Функция будет сама определять что за файл загружается
		$this->log(">>>>>>>>>>>>>>>>>>>> НАЧАЛО ЗАГРУЗКИ ДАННЫХ <<<<<<<<<<<<<<<<<<<<");
		$this->log("Доступно памяти: " . sprintf("%.3f", memory_get_peak_usage() / 1024 / 1024) . " Mb",2);

		// Определим язык
		$this->getLanguageId($this->config->get('config_language'));
		$this->log("Язык загрузки по-умолчанию, id: " . $this->LANG_ID, 2);

        // Записываем единое текущее время обновления для запросов в базе данных
		$this->NOW = date('Y-m-d H:i:s');

		// Определение дополнительных полей
		$this->TAB_FIELDS = $this->config->get('exchange1c_table_fields');

		// Читаем XML
		libxml_use_internal_errors(true);
		$path_parts = pathinfo($importFile);
		$this->log("Файл: " . $path_parts['basename'], 2);
		$xml = @simplexml_load_file($importFile);
		if (!$xml) {
			$this->ERROR = "Файл не является стандартом XML, подробности в журнале\n";
			$this->ERROR .= implode("\n", libxml_get_errors());
			return $this->error();
		}

		// Файл стандарта Commerce ML
		$this->checkCML($xml);
		if ($this->ERROR) return $this->error();

		// IMPORT.XML, OFFERS.XML
		if ($xml->Классификатор) {
			$this->log(">>>>>>>>>>>>>>>>>>>> ЗАГРУЗКА КЛАССИФИКАТОРА <<<<<<<<<<<<<<<<<<<<",2);
			$classifier = $this->parseClassifier($xml->Классификатор);
			if ($this->ERROR) return $this->error();
			unset($xml->Классификатор);
		} else {
			// CML 2.08 + Битрикс
			$classifier = array();
		}

		if ($xml->Каталог) {
			// Запишем в лог дату и время начала обмена

			$this->log(">>>>>>>>>>>>>>>>>>>> ЗАГРУЗКА КАТАЛОГА <<<<<<<<<<<<<<<<<<<<",2);
			if (!isset($classifier)) {
				$this->log("[i] Классификатор отсутствует! Все товары будут загружены в магазин по умолчанию!");
			}

			$this->parseDirectory($xml->Каталог, $classifier);
			if ($this->ERROR) return $this->error();
			unset($xml->Каталог);
		}

		// OFFERS.XML
		if ($xml->ПакетПредложений) {
			$this->log(">>>>>>>>>>>>>>>>>>>> ЗАГРУЗКА ПАКЕТА ПРЕДЛОЖЕНИЙ <<<<<<<<<<<<<<<<<<<<", 2);

			// Пакет предложений
			$this->parseOffersPack($xml->ПакетПредложений);
			if ($this->ERROR) return $this->error();
			unset($xml->ПакетПредложений);
		}

		// ORDERS.XML
		if ($xml->Документ) {
			$this->log(">>>>>>>>>>>>>>>>>>>> ЗАГРУЗКА ДОКУМЕНТОВ <<<<<<<<<<<<<<<<<<<<", 2);

			$this->clearLog();

			// Документ (заказ)
			foreach ($xml->Документ as $doc) {
				$this->parseDocument($doc);
				if ($this->ERROR) return $this->error();
			}
			unset($xml->Документ);
		}
		else {
			$this->log("[i] Не обработанные данные XML", 2);
			$this->log($xml,2);
		}

		$this->log(">>>>>>>>>>>>>>>>>>>> КОНЕЦ ЗАГРУЗКИ ДАННЫХ <<<<<<<<<<<<<<<<<<<<");
		return "";
	}


	/**
	 * ver 5
	 * update 2017-05-26
	 * Определение дополнительных полей и запись их в глобальную переменную типа массив
	 */
	public function defineTableFields() {

		$result = array();

		$this->log("Поиск в базе данных дополнительных полей",2);

		$tables = array(
			'manufacturer'				=> array('noindex'=>1),
			'product_to_category'		=> array('main_category'=>1),
			'product_description'		=> array('meta_h1'=>''),
			'category_description'		=> array('meta_h1'=>''),
			'manufacturer_description'	=> array('name'=>'','meta_h1'=>'','meta_title'=>'','meta_description'=>'','meta_keyword'=>''),
			'product'					=> array('noindex'=>1),
			'order'						=> array('payment_inn'=>'','shipping_inn'=>'','patronymic'=>'','payment_patronymic'=>'','shipping_patronymic'=>''),
			'customer'					=> array('patronymic'=>''),
			'cart'						=> array('product_feature_id'=>0,'unit_id'=>0),
			'attributes_value'			=> array(),
			'attributes_value_to_1c'	=> array(),
			'cart'						=> array('product_feature_id'=>'', 'unit_id'=>''),
			'product_price'				=> array('action'=>'')
		);

		foreach ($tables as $table => $fields) {

			$query = $this->query("SHOW TABLES LIKE '" . DB_PREFIX . $table . "'");
			if (!$query->num_rows) continue;

			$result[$table] = array();

			foreach ($fields as $field => $value) {

				$query = $this->query("SHOW COLUMNS FROM `" . DB_PREFIX . $table . "` WHERE `field` = '" . $field . "'");
				if (!$query->num_rows) continue;

				$result[$table][$field] = $value;
			}
		}
		return $result;

	} // defineTableFields()


	/**
	 * ver 1
	 * update 2017-06-12
	 * Устанавливает классификатор единиц измерений
	 */
	public function installUnits() {

		// Классификатор единиц измерения
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "unit`");
		$this->db->query(
			"CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "unit` (
				`unit_id` 					SMALLINT(6) 	NOT NULL AUTO_INCREMENT COMMENT 'Счетчик',
				`name` 						VARCHAR(255) 	NOT NULL 				COMMENT 'Наименование единицы измерения',
				`number_code` 				VARCHAR(5) 		NOT NULL 				COMMENT 'Код',
				`rus_name1` 				VARCHAR(50) 	DEFAULT '' NOT NULL		COMMENT 'Условное обозначение национальное',
				`eng_name1` 				VARCHAR(50) 	DEFAULT '' NOT NULL 	COMMENT 'Условное обозначение международное',
				`rus_name2` 				VARCHAR(50) 	DEFAULT '' NOT NULL 	COMMENT 'Кодовое буквенное обозначение национальное',
				`eng_name2` 				VARCHAR(50) 	DEFAULT '' NOT NULL 	COMMENT 'Кодовое буквенное обозначение международное',
				`unit_group_id`  			TINYINT(4) 		NOT NULL 				COMMENT 'Группа единиц измерения',
				`unit_type_id` 				TINYINT(4) 		NOT NULL 				COMMENT 'Раздел/приложение в которое входит единица измерения',
				`visible` 					TINYINT(4) 		DEFAULT '1' NOT NULL 	COMMENT 'Видимость',
				`comment` 					VARCHAR(255) 	DEFAULT '' NOT NULL 	COMMENT 'Комментарий',
				PRIMARY KEY (`unit_id`),
				UNIQUE KEY number_code (`number_code`),
  				KEY unit_group_id (`unit_group_id`),
  				KEY unit_type_id (`unit_type_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Общероссийский классификатор единиц измерения ОКЕИ'"
		);

		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "unit_group`");
		$this->db->query(
			"CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "unit_group` (
				`unit_group_id` 			TINYINT(4) 		NOT NULL AUTO_INCREMENT COMMENT 'Счетчик',
				`name` 						VARCHAR(255) 	NOT NULL 				COMMENT 'Наименование группы',
				PRIMARY KEY (`unit_group_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Группы единиц измерения'"
		);

		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "unit_type`");
		$this->db->query(
			"CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "unit_type` (
				`unit_type_id` 			TINYINT(4) 			NOT NULL AUTO_INCREMENT COMMENT 'Счетчик',
				`name` 					VARCHAR(255) 		NOT NULL 				COMMENT 'Наименование раздела/приложения',
				PRIMARY KEY (`unit_type_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Разделы/приложения, в которые включены единицы измерения'"
		);

		// Загрузка классификатора единиц измерений
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit_group` (unit_group_id, name) VALUES(6, 'Единицы времени')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit_group` (unit_group_id, name) VALUES(1, 'Единицы длины')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit_group` (unit_group_id, name) VALUES(4, 'Единицы массы')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit_group` (unit_group_id, name) VALUES(3, 'Единицы объема')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit_group` (unit_group_id, name) VALUES(2, 'Единицы площади')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit_group` (unit_group_id, name) VALUES(5, 'Технические единицы')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit_group` (unit_group_id, name) VALUES(7, 'Экономические единицы')");

		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit_type` (unit_type_id, name) VALUES(1, 'Международные единицы измерения, включенные в ЕСКК')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit_type` (unit_type_id, name) VALUES(2, 'Национальные единицы измерения, включенные в ЕСКК')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit_type` (unit_type_id, name) VALUES(3, 'Международные единицы измерения, не включенные в ЕСКК')");

		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(1, 'Миллиметр', '003', 'мм', 'mm', 'ММ', 'MMT', 1, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(2, 'Сантиметр', '004', 'см', 'cm', 'СМ', 'CMT', 1, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(3, 'Дециметр', '005', 'дм', 'dm', 'ДМ', 'DMT', 1, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(4, 'Метр', '006', 'м', 'm', 'М', 'MTR', 1, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(5, 'Километр; тысяча метров', '008', 'км; 10^3 м', 'km', 'КМ; ТЫС М', 'KMT', 1, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(6, 'Мегаметр; миллион метров', '009', 'Мм; 10^6 м', 'Mm', 'МЕГАМ; МЛН М', 'MAM', 1, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(7, 'Дюйм (25,4 мм)', '039', 'дюйм', 'in', 'ДЮЙМ', 'INH', 1, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(8, 'Фут (0,3048 м)', '041', 'фут', 'ft', 'ФУТ', 'FOT', 1, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(9, 'Ярд (0,9144 м)', '043', 'ярд', 'yd', 'ЯРД', 'YRD', 1, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(10, 'Морская миля (1852 м)', '047', 'миля', 'n mile', 'МИЛЬ', 'NMI', 1, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(11, 'Квадратный миллиметр', '050', 'мм2', 'mm2', 'ММ2', 'MMK', 2, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(12, 'Квадратный сантиметр', '051', 'см2', 'cm2', 'СМ2', 'CMK', 2, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(13, 'Квадратный дециметр', '053', 'дм2', 'dm2', 'ДМ2', 'DMK', 2, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(14, 'Квадратный метр', '055', 'м2', 'm2', 'М2', 'MTK', 2, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(15, 'Тысяча квадратных метров', '058', '10^3 м^2', 'daa', 'ТЫС М2', 'DAA', 2, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(16, 'Гектар', '059', 'га', 'ha', 'ГА', 'HAR', 2, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(17, 'Квадратный километр', '061', 'км2', 'km2', 'КМ2', 'KMK', 2, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(18, 'Квадратный дюйм (645,16 мм2)', '071', 'дюйм2', 'in2', 'ДЮЙМ2', 'INK', 2, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(19, 'Квадратный фут (0,092903 м2)', '073', 'фут2', 'ft2', 'ФУТ2', 'FTK', 2, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(20, 'Квадратный ярд (0,8361274 м2)', '075', 'ярд2', 'yd2', 'ЯРД2', 'YDK', 2, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(21, 'Ар (100 м2)', '109', 'а', 'a', 'АР', 'ARE', 2, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(22, 'Кубический миллиметр', '110', 'мм3', 'mm3', 'ММ3', 'MMQ', 3, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(23, 'Кубический сантиметр; миллилитр', '111', 'см3; мл', 'cm3; ml', 'СМ3; МЛ', 'CMQ; MLT', 3, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(24, 'Литр; кубический дециметр', '112', 'л; дм3', 'I; L; dm^3', 'Л; ДМ3', 'LTR; DMQ', 3, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(25, 'Кубический метр', '113', 'м3', 'm3', 'М3', 'MTQ', 3, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(26, 'Децилитр', '118', 'дл', 'dl', 'ДЛ', 'DLT', 3, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(27, 'Гектолитр', '122', 'гл', 'hl', 'ГЛ', 'HLT', 3, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(28, 'Мегалитр', '126', 'Мл', 'Ml', 'МЕГАЛ', 'MAL', 3, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(29, 'Кубический дюйм (16387,1 мм3)', '131', 'дюйм3', 'in3', 'ДЮЙМ3', 'INQ', 3, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(30, 'Кубический фут (0,02831685 м3)', '132', 'фут3', 'ft3', 'ФУТ3', 'FTQ', 3, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(31, 'Кубический ярд (0,764555 м3)', '133', 'ярд3', 'yd3', 'ЯРД3', 'YDQ', 3, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(32, 'Миллион кубических метров', '159', '10^6 м3', '10^6 m3', 'МЛН М3', 'HMQ', 3, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(33, 'Гектограмм', '160', 'гг', 'hg', 'ГГ', 'HGM', 4, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(34, 'Миллиграмм', '161', 'мг', 'mg', 'МГ', 'MGM', 4, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(35, 'Метрический карат', '162', 'кар', 'МС', 'КАР', 'CTM', 4, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(36, 'Грамм', '163', 'г', 'g', 'Г', 'GRM', 4, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(37, 'Килограмм', '166', 'кг', 'kg', 'КГ', 'KGM', 4, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(38, 'Тонна; метрическая тонна (1000 кг)', '168', 'т', 't', 'Т', 'TNE', 4, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(39, 'Килотонна', '170', '10^3 т', 'kt', 'КТ', 'KTN', 4, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(40, 'Сантиграмм', '173', 'сг', 'cg', 'СГ', 'CGM', 4, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(41, 'Брутто-регистровая тонна (2,8316 м3)', '181', 'БРТ', '-', 'БРУТТ. РЕГИСТР Т', 'GRT', 4, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(42, 'Грузоподъемность в метрических тоннах', '185', 'т грп', '-', 'Т ГРУЗОПОД', 'CCT', 4, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(43, 'Центнер (метрический) (100 кг); гектокилограмм; квинтал1 (метрический); децитонна', '206', 'ц', 'q; 10^2 kg', 'Ц', 'DTN', 4, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(44, 'Ватт', '212', 'Вт', 'W', 'ВТ', 'WTT', 5, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(45, 'Киловатт', '214', 'кВт', 'kW', 'КВТ', 'KWT', 5, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(46, 'Мегаватт; тысяча киловатт', '215', 'МВт; 10^3 кВт', 'MW', 'МЕГАВТ; ТЫС КВТ', 'MAW', 5, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(47, 'Вольт', '222', 'В', 'V', 'В', 'VLT', 5, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(48, 'Киловольт', '223', 'кВ', 'kV', 'КВ', 'KVT', 5, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(49, 'Киловольт-ампер', '227', 'кВ.А', 'kV.A', 'КВ.А', 'KVA', 5, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(50, 'Мегавольт-ампер (тысяча киловольт-ампер)', '228', 'МВ.А', 'MV.A', 'МЕГАВ.А', 'MVA', 5, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(51, 'Киловар', '230', 'квар', 'kVAR', 'КВАР', 'KVR', 5, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(52, 'Ватт-час', '243', 'Вт.ч', 'W.h', 'ВТ.Ч', 'WHR', 5, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(53, 'Киловатт-час', '245', 'кВт.ч', 'kW.h', 'КВТ.Ч', 'KWH', 5, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(54, 'Мегаватт-час; 1000 киловатт-часов', '246', 'МВт.ч; 10^3 кВт.ч', 'МW.h', 'МЕГАВТ.Ч; ТЫС КВТ.Ч', 'MWH', 5, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(55, 'Гигаватт-час (миллион киловатт-часов)', '247', 'ГВт.ч', 'GW.h', 'ГИГАВТ.Ч', 'GWH', 5, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(56, 'Ампер', '260', 'А', 'A', 'А', 'AMP', 5, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(57, 'Ампер-час (3,6 кКл)', '263', 'А.ч', 'A.h', 'А.Ч', 'AMH', 5, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(58, 'Тысяча ампер-часов', '264', '10^3 А.ч', '10^3 A.h', 'ТЫС А.Ч', 'TAH', 5, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(59, 'Кулон', '270', 'Кл', 'C', 'КЛ', 'COU', 5, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(60, 'Джоуль', '271', 'Дж', 'J', 'ДЖ', 'JOU', 5, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(61, 'Килоджоуль', '273', 'кДж', 'kJ', 'КДЖ', 'KJO', 5, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(62, 'Ом', '274', 'Ом', '<омега>', 'ОМ', 'OHM', 5, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(63, 'Градус Цельсия', '280', 'град. C', 'град. C', 'ГРАД ЦЕЛЬС', 'CEL', 5, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(64, 'Градус Фаренгейта', '281', 'град. F', 'град. F', 'ГРАД ФАРЕНГ', 'FAN', 5, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(65, 'Кандела', '282', 'кд', 'cd', 'КД', 'CDL', 5, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(66, 'Люкс', '283', 'лк', 'lx', 'ЛК', 'LUX', 5, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(67, 'Люмен', '284', 'лм', 'lm', 'ЛМ', 'LUM', 5, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(68, 'Кельвин', '288', 'K', 'K', 'К', 'KEL', 5, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(69, 'Ньютон', '289', 'Н', 'N', 'Н', 'NEW', 5, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(70, 'Герц', '290', 'Гц', 'Hz', 'ГЦ', 'HTZ', 5, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(71, 'Килогерц', '291', 'кГц', 'kHz', 'КГЦ', 'KHZ', 5, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(72, 'Мегагерц', '292', 'МГц', 'MHz', 'МЕГАГЦ', 'MHZ', 5, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(73, 'Паскаль', '294', 'Па', 'Pa', 'ПА', 'PAL', 5, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(74, 'Сименс', '296', 'См', 'S', 'СИ', 'SIE', 5, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(75, 'Килопаскаль', '297', 'кПа', 'kPa', 'КПА', 'KPA', 5, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(76, 'Мегапаскаль', '298', 'МПа', 'MPa', 'МЕГАПА', 'MPA', 5, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(77, 'Физическая атмосфера (101325 Па)', '300', 'атм', 'atm', 'АТМ', 'ATM', 5, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(78, 'Техническая атмосфера (98066,5 Па)', '301', 'ат', 'at', 'АТТ', 'ATT', 5, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(79, 'Гигабеккерель', '302', 'ГБк', 'GBq', 'ГИГАБК', 'GBQ', 5, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(80, 'Милликюри', '304', 'мКи', 'mCi', 'МКИ', 'MCU', 5, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(81, 'Кюри', '305', 'Ки', 'Ci', 'КИ', 'CUR', 5, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(82, 'Грамм делящихся изотопов', '306', 'г Д/И', 'g fissile isotopes', 'Г ДЕЛЯЩ ИЗОТОП', 'GFI', 5, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(83, 'Миллибар', '308', 'мб', 'mbar', 'МБАР', 'MBR', 5, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(84, 'Бар', '309', 'бар', 'bar', 'БАР', 'BAR', 5, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(85, 'Гектобар', '310', 'гб', 'hbar', 'ГБАР', 'HBA', 5, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(86, 'Килобар', '312', 'кб', 'kbar', 'КБАР', 'KBA', 5, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(87, 'Фарад', '314', 'Ф', 'F', 'Ф', 'FAR', 5, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(88, 'Килограмм на кубический метр', '316', 'кг/м3', 'kg/m3', 'КГ/М3', 'KMQ', 5, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(89, 'Беккерель', '323', 'Бк', 'Bq', 'БК', 'BQL', 5, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(90, 'Вебер', '324', 'Вб', 'Wb', 'ВБ', 'WEB', 5, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(91, 'Узел (миля/ч)', '327', 'уз', 'kn', 'УЗ', 'KNT', 5, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(92, 'Метр в секунду', '328', 'м/с', 'm/s', 'М/С', 'MTS', 5, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(93, 'Оборот в секунду', '330', 'об/с', 'r/s', 'ОБ/С', 'RPS', 5, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(94, 'Оборот в минуту', '331', 'об/мин', 'r/min', 'ОБ/МИН', 'RPM', 5, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(95, 'Километр в час', '333', 'км/ч', 'km/h', 'КМ/Ч', 'KMH', 5, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(96, 'Метр на секунду в квадрате', '335', 'м/с2', 'm/s2', 'М/С2', 'MSK', 5, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(97, 'Кулон на килограмм', '349', 'Кл/кг', 'C/kg', 'КЛ/КГ', 'CKG', 5, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(98, 'Секунда', '354', 'с', 's', 'С', 'SEC', 6, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(99, 'Минута', '355', 'мин', 'min', 'МИН', 'MIN', 6, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(100, 'Час', '356', 'ч', 'h', 'Ч', 'HUR', 6, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(101, 'Сутки', '359', 'сут; дн', 'd', 'СУТ; ДН', 'DAY', 6, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(102, 'Неделя', '360', 'нед', '-', 'НЕД', 'WEE', 6, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(103, 'Декада', '361', 'дек', '-', 'ДЕК', 'DAD', 6, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(104, 'Месяц', '362', 'мес', '-', 'МЕС', 'MON', 6, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(105, 'Квартал', '364', 'кварт', '-', 'КВАРТ', 'QAN', 6, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(106, 'Полугодие', '365', 'полгода', '-', 'ПОЛГОД', 'SAN', 6, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(107, 'Год', '366', 'г; лет', 'a', 'ГОД; ЛЕТ', 'ANN', 6, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(108, 'Десятилетие', '368', 'деслет', '-', 'ДЕСЛЕТ', 'DEC', 6, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(109, 'Килограмм в секунду', '499', 'кг/с', '-', 'КГ/С', 'KGS', 7, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(110, 'Тонна пара в час', '533', 'т пар/ч', '-', 'Т ПАР/Ч', 'TSH', 7, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(111, 'Кубический метр в секунду', '596', 'м3/с', 'm3/s', 'М3/С', 'MQS', 7, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(112, 'Кубический метр в час', '598', 'м3/ч', 'm3/h', 'М3/Ч', 'MQH', 7, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(113, 'Тысяча кубических метров в сутки', '599', '10^3 м3/сут', '-', 'ТЫС М3/СУТ', 'TQD', 7, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(114, 'Бобина', '616', 'боб', '-', 'БОБ', 'NBB', 7, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(115, 'Лист', '625', 'л.', '-', 'ЛИСТ', 'LEF', 7, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(116, 'Сто листов', '626', '100 л.', '-', '100 ЛИСТ', 'CLF', 7, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(117, 'Тысяча стандартных условных кирпичей', '630', 'тыс станд. усл. кирп', '-', 'ТЫС СТАНД УСЛ КИРП', 'MBE', 7, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(118, 'Дюжина (12 шт.)', '641', 'дюжина', 'Doz; 12', 'ДЮЖИНА', 'DZN', 7, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(119, 'Изделие', '657', 'изд', '-', 'ИЗД', 'NAR', 7, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(120, 'Сто ящиков', '683', '100 ящ.', 'Hbx', '100 ЯЩ', 'HBX', 7, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(121, 'Набор', '704', 'набор', '-', 'НАБОР', 'SET', 7, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(122, 'Пара (2 шт.)', '715', 'пар', 'pr; 2', 'ПАР', 'NPR', 7, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(123, 'Два десятка', '730', '20', '20', '2 ДЕС', 'SCO', 7, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(124, 'Десять пар', '732', '10 пар', '-', 'ДЕС ПАР', 'TPR', 7, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(125, 'Дюжина пар', '733', 'дюжина пар', '-', 'ДЮЖИНА ПАР', 'DPR', 7, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(126, 'Посылка', '734', 'посыл', '-', 'ПОСЫЛ', 'NPL', 7, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(127, 'Часть', '735', 'часть', '-', 'ЧАСТЬ', 'NPT', 7, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(128, 'Рулон', '736', 'рул', '-', 'РУЛ', 'NPL', 7, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(129, 'Дюжина рулонов', '737', 'дюжина рул', '-', 'ДЮЖИНА РУЛ', 'DRL', 7, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(130, 'Дюжина штук', '740', 'дюжина шт', '-', 'ДЮЖИНА ШТ', 'DPC', 7, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(131, 'Элемент', '745', 'элем', 'CI', 'ЭЛЕМ', 'NCL', 7, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(132, 'Упаковка', '778', 'упак', '-', 'УПАК', 'NMP', 7, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(133, 'Дюжина упаковок', '780', 'дюжина упак', '-', 'ДЮЖИНА УПАК', 'DZP', 7, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(134, 'Сто упаковок', '781', '100 упак', '-', '100 УПАК', 'CNP', 7, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(135, 'Штука', '796', 'шт', 'pc; 1', 'ШТ', 'PCE; NMB', 7, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(136, 'Сто штук', '797', '100 шт', '100', '100 ШТ', 'CEN', 7, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(137, 'Тысяча штук', '798', 'тыс. шт; 1000 шт', '1000', 'ТЫС ШТ', 'MIL', 7, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(138, 'Миллион штук', '799', '10^6 шт', '10^6', 'МЛН ШТ', 'MIO', 7, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(139, 'Миллиард штук', '800', '10^9 шт', '10^9', 'МЛРД ШТ', 'MLD', 7, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(140, 'Биллион штук (Европа); триллион штук', '801', '10^12 шт', '10^12', 'БИЛЛ ШТ (ЕВР); ТРИЛЛ ШТ', 'BIL', 7, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(141, 'Квинтильон штук (Европа)', '802', '10^18 шт', '10^18', 'КВИНТ ШТ', 'TRL', 7, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(142, 'Крепость спирта по массе', '820', 'креп. спирта по массе', '% mds', 'КРЕП СПИРТ ПО МАССЕ', 'ASM', 7, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(143, 'Крепость спирта по объему', '821', 'креп. спирта по объему', '% vol', 'КРЕП СПИРТ ПО ОБЪЕМ', 'ASV', 7, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(144, 'Литр чистого (100%) спирта', '831', 'л 100% спирта', '-', 'Л ЧИСТ СПИРТ', 'LPA', 7, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(145, 'Гектолитр чистого (100%) спирта', '833', 'Гл 100% спирта', '-', 'ГЛ ЧИСТ СПИРТ', 'HPA', 7, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(146, 'Килограмм пероксида водорода', '841', 'кг H2О2', '-', 'КГ ПЕРОКСИД ВОДОРОДА', '-', 7, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(147, 'Килограмм 90%-го сухого вещества', '845', 'кг 90% с/в', '-', 'КГ 90 ПРОЦ СУХ ВЕЩ', 'KSD', 7, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(148, 'Тонна 90%-го сухого вещества', '847', 'т 90% с/в', '-', 'Т 90 ПРОЦ СУХ ВЕЩ', 'TSD', 7, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(149, 'Килограмм оксида калия', '852', 'кг К2О', '-', 'КГ ОКСИД КАЛИЯ', 'KPO', 7, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(150, 'Килограмм гидроксида калия', '859', 'кг КОН', '-', 'КГ ГИДРОКСИД КАЛИЯ', 'KPH', 7, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(151, 'Килограмм азота', '861', 'кг N', '-', 'КГ АЗОТ', 'KNI', 7, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(152, 'Килограмм гидроксида натрия', '863', 'кг NaOH', '-', 'КГ ГИДРОКСИД НАТРИЯ', 'KSH', 7, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(153, 'Килограмм пятиокиси фосфора', '865', 'кг Р2О5', '-', 'КГ ПЯТИОКИСЬ ФОСФОРА', 'KPP', 7, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(154, 'Килограмм урана', '867', 'кг U', '-', 'КГ УРАН', 'KUR', 7, 1, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(155, 'Погонный метр', '018', 'пог. м', '', 'ПОГ М', '', 1, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(156, 'Тысяча погонных метров', '019', '10^3 пог. м', '', 'ТЫС ПОГ М', '', 1, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(157, 'Условный метр', '020', 'усл. м', '', 'УСЛ М', '', 1, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(158, 'Тысяча условных метров', '048', '10^3 усл. м', '', 'ТЫС УСЛ М', '', 1, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(159, 'Километр условных труб', '049', 'км усл. труб', '', 'КМ УСЛ ТРУБ', '', 1, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(160, 'Тысяча квадратных дециметров', '054', '10^3 дм2', '', 'ТЫС ДМ2', '', 2, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(161, 'Миллион квадратных дециметров', '056', '10^6 дм2', '', 'МЛН ДМ2', '', 2, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(162, 'Миллион квадратных метров', '057', '10^6 м2', '', 'МЛН М2', '', 2, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(163, 'Тысяча гектаров', '060', '10^3 га', '', 'ТЫС ГА', '', 2, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(164, 'Условный квадратный метр', '062', 'усл. м2', '', 'УСЛ М2', '', 2, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(165, 'Тысяча условных квадратных метров', '063', '10^3 усл. м2', '', 'ТЫС УСЛ М2', '', 2, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(166, 'Миллион условных квадратных метров', '064', '10^6 усл. м2', '', 'МЛН УСЛ М2', '', 2, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(167, 'Квадратный метр общей площади', '081', 'м2 общ. пл', '', 'М2 ОБЩ ПЛ', '', 2, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(168, 'Тысяча квадратных метров общей площади', '082', '10^3 м2 общ. пл', '', 'ТЫС М2 ОБЩ ПЛ', '', 2, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(169, 'Миллион квадратных метров общей площади', '083', '10^6 м2 общ. пл', '', 'МЛН М2. ОБЩ ПЛ', '', 2, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(170, 'Квадратный метр жилой площади', '084', 'м2 жил. пл', '', 'М2 ЖИЛ ПЛ', '', 2, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(171, 'Тысяча квадратных метров жилой площади', '085', '10^3 м2 жил. пл', '', 'ТЫС М2 ЖИЛ ПЛ', '', 2, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(172, 'Миллион квадратных метров жилой площади', '086', '10^6 м2 жил. пл', '', 'МЛН М2 ЖИЛ ПЛ', '', 2, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(173, 'Квадратный метр учебно-лабораторных зданий', '087', 'м2 уч. лаб. здан', '', 'М2 УЧ.ЛАБ ЗДАН', '', 2, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(174, 'Тысяча квадратных метров учебно-лабораторных зданий', '088', '10^3 м2 уч. лаб. здан', '', 'ТЫС М2 УЧ. ЛАБ ЗДАН', '', 2, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(175, 'Миллион квадратных метров в двухмиллиметровом исчислении', '089', '10^6 м2 2 мм исч', '', 'МЛН М2 2ММ ИСЧ', '', 2, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(176, 'Тысяча кубических метров', '114', '10^3 м3', '', 'ТЫС М3', '', 3, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(177, 'Миллиард кубических метров', '115', '10^9 м3', '', 'МЛРД М3', '', 3, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(178, 'Декалитр', '116', 'дкл', '', 'ДКЛ', '', 3, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(179, 'Тысяча декалитров', '119', '10^3 дкл', '', 'ТЫС ДКЛ', '', 3, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(180, 'Миллион декалитров', '120', '10^6 дкл', '', 'МЛН ДКЛ', '', 3, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(181, 'Плотный кубический метр', '121', 'плотн. м3', '', 'ПЛОТН М3', '', 3, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(182, 'Условный кубический метр', '123', 'усл. м3', '', 'УСЛ М3', '', 3, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(183, 'Тысяча условных кубических метров', '124', '10^3 усл. м3', '', 'ТЫС УСЛ М3', '', 3, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(184, 'Миллион кубических метров переработки газа', '125', '10^6 м3 перераб. газа', '', 'МЛН М3 ПЕРЕРАБ ГАЗА', '', 3, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(185, 'Тысяча плотных кубических метров', '127', '10^3 плотн. м3', '', 'ТЫС ПЛОТН М3', '', 3, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(186, 'Тысяча полулитров', '128', '10^3 пол. л', '', 'ТЫС ПОЛ Л', '', 3, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(187, 'Миллион полулитров', '129', '10^6 пол. л', '', 'МЛН ПОЛ Л', '', 3, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(188, 'Тысяча литров; 1000 литров', '130', '10^3 л; 1000 л', '', 'ТЫС Л', '', 3, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(189, 'Тысяча каратов метрических', '165', '10^3 кар', '', 'ТЫС КАР', '', 4, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(190, 'Миллион каратов метрических', '167', '10^6 кар', '', 'МЛН КАР', '', 4, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(191, 'Тысяча тонн', '169', '10^3 т', '', 'ТЫС Т', '', 4, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(192, 'Миллион тонн', '171', '10^6 т', '', 'МЛН Т', '', 4, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(193, 'Тонна условного топлива', '172', 'т усл. топл', '', 'Т УСЛ ТОПЛ', '', 4, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(194, 'Тысяча тонн условного топлива', '175', '10^3 т усл. топл', '', 'ТЫС Т УСЛ ТОПЛ', '', 4, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(195, 'Миллион тонн условного топлива', '176', '10^6 т усл. топл', '', 'МЛН Т УСЛ ТОПЛ', '', 4, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(196, 'Тысяча тонн единовременного хранения', '177', '10^3 т единовр. хран', '', 'ТЫС Т ЕДИНОВР ХРАН', '', 4, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(197, 'Тысяча тонн переработки', '178', '10^3 т перераб', '', 'ТЫС Т ПЕРЕРАБ', '', 4, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(198, 'Условная тонна', '179', 'усл. т', '', 'УСЛ Т', '', 4, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(199, 'Тысяча центнеров', '207', '10^3 ц', '', 'ТЫС Ц', '', 4, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(200, 'Вольт-ампер', '226', 'В.А', '', 'В.А', '', 5, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(201, 'Метр в час', '231', 'м/ч', '', 'М/Ч', '', 5, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(202, 'Килокалория', '232', 'ккал', '', 'ККАЛ', '', 5, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(203, 'Гигакалория', '233', 'Гкал', '', 'ГИГАКАЛ', '', 5, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(204, 'Тысяча гигакалорий', '234', '10^3 Гкал', '', 'ТЫС ГИГАКАЛ', '', 5, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(205, 'Миллион гигакалорий', '235', '10^6 Гкал', '', 'МЛН ГИГАКАЛ', '', 5, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(206, 'Калория в час', '236', 'кал/ч', '', 'КАЛ/Ч', '', 5, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(207, 'Килокалория в час', '237', 'ккал/ч', '', 'ККАЛ/Ч', '', 5, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(208, 'Гигакалория в час', '238', 'Гкал/ч', '', 'ГИГАКАЛ/Ч', '', 5, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(209, 'Тысяча гигакалорий в час', '239', '10^3 Гкал/ч', '', 'ТЫС ГИГАКАЛ/Ч', '', 5, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(210, 'Миллион ампер-часов', '241', '10^6 А.ч', '', 'МЛН А.Ч', '', 5, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(211, 'Миллион киловольт-ампер', '242', '10^6 кВ.А', '', 'МЛН КВ.А', '', 5, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(212, 'Киловольт-ампер реактивный', '248', 'кВ.А Р', '', 'КВ.А Р', '', 5, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(213, 'Миллиард киловатт-часов', '249', '10^9 кВт.ч', '', 'МЛРД КВТ.Ч', '', 5, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(214, 'Тысяча киловольт-ампер реактивных', '250', '10^3 кВ.А Р', '', 'ТЫС КВ.А Р', '', 5, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(215, 'Лошадиная сила', '251', 'л. с', '', 'ЛС', '', 5, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(216, 'Тысяча лошадиных сил', '252', '10^3 л. с', '', 'ТЫС ЛС', '', 5, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(217, 'Миллион лошадиных сил', '253', '10^6 л. с', '', 'МЛН ЛС', '', 5, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(218, 'Бит', '254', 'бит', '', 'БИТ', '', 5, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(219, 'Байт', '255', 'бай', '', 'БАЙТ', '', 5, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(220, 'Килобайт', '256', 'кбайт', '', 'КБАЙТ', '', 5, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(221, 'Мегабайт', '257', 'Мбайт', '', 'МБАЙТ', '', 5, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(222, 'Бод', '258', 'бод', '', 'БОД', '', 5, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(223, 'Генри', '287', 'Гн', '', 'ГН', '', 5, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(224, 'Тесла', '313', 'Тл', '', 'ТЛ', '', 5, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(225, 'Килограмм на квадратный сантиметр', '317', 'кг/см^2', '', 'КГ/СМ2', '', 5, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(226, 'Миллиметр водяного столба', '337', 'мм вод. ст', '', 'ММ ВОД СТ', '', 5, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(227, 'Миллиметр ртутного столба', '338', 'мм рт. ст', '', 'ММ РТ СТ', '', 5, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(228, 'Сантиметр водяного столба', '339', 'см вод. ст', '', 'СМ ВОД СТ', '', 5, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(229, 'Микросекунда', '352', 'мкс', '', 'МКС', '', 6, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(230, 'Миллисекунда', '353', 'млс', '', 'МЛС', '', 6, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(231, 'Рубль', '383', 'руб', '', 'РУБ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(232, 'Тысяча рублей', '384', '10^3 руб', '', 'ТЫС РУБ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(233, 'Миллион рублей', '385', '10^6 руб', '', 'МЛН РУБ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(234, 'Миллиард рублей', '386', '10^9 руб', '', 'МЛРД РУБ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(235, 'Триллион рублей', '387', '10^12 руб', '', 'ТРИЛЛ РУБ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(236, 'Квадрильон рублей', '388', '10^15 руб', '', 'КВАДР РУБ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(237, 'Пассажиро-километр', '414', 'пасс.км', '', 'ПАСС.КМ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(238, 'Пассажирское место (пассажирских мест)', '421', 'пасс. мест', '', 'ПАСС МЕСТ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(239, 'Тысяча пассажиро-километров', '423', '10^3 пасс.км', '', 'ТЫС ПАСС.КМ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(240, 'Миллион пассажиро-километров', '424', '10^6 пасс. км', '', 'МЛН ПАСС.КМ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(241, 'Пассажиропоток', '427', 'пасс.поток', '', 'ПАСС.ПОТОК', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(242, 'Тонно-километр', '449', 'т.км', '', 'Т.КМ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(243, 'Тысяча тонно-километров', '450', '10^3 т.км', '', 'ТЫС Т.КМ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(244, 'Миллион тонно-километров', '451', '10^6 т. км', '', 'МЛН Т.КМ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(245, 'Тысяча наборов', '479', '10^3 набор', '', 'ТЫС НАБОР', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(246, 'Грамм на киловатт-час', '510', 'г/кВт.ч', '', 'Г/КВТ.Ч', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(247, 'Килограмм на гигакалорию', '511', 'кг/Гкал', '', 'КГ/ГИГАКАЛ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(248, 'Тонно-номер', '512', 'т.ном', '', 'Т.НОМ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(249, 'Автотонна', '513', 'авто т', '', 'АВТО Т', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(250, 'Тонна тяги', '514', 'т.тяги', '', 'Т ТЯГИ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(251, 'Дедвейт-тонна', '515', 'дедвейт.т', '', 'ДЕДВЕЙТ.Т', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(252, 'Тонно-танид', '516', 'т.танид', '', 'Т.ТАНИД', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(253, 'Человек на квадратный метр', '521', 'чел/м2', '', 'ЧЕЛ/М2', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(254, 'Человек на квадратный километр', '522', 'чел/км2', '', 'ЧЕЛ/КМ2', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(255, 'Тонна в час', '534', 'т/ч', '', 'Т/Ч', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(256, 'Тонна в сутки', '535', 'т/сут', '', 'Т/СУТ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(257, 'Тонна в смену', '536', 'т/смен', '', 'Т/СМЕН', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(258, 'Тысяча тонн в сезон', '537', '10^3 т/сез', '', 'ТЫС Т/СЕЗ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(259, 'Тысяча тонн в год', '538', '10^3 т/год', '', 'ТЫС Т/ГОД', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(260, 'Человеко-час', '539', 'чел.ч', '', 'ЧЕЛ.Ч', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(261, 'Человеко-день', '540', 'чел.дн', '', 'ЧЕЛ.ДН', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(262, 'Тысяча человеко-дней', '541', '10^3 чел.дн', '', 'ТЫС ЧЕЛ.ДН', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(263, 'Тысяча человеко-часов', '542', '10^3 чел.ч', '', 'ТЫС ЧЕЛ.Ч', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(264, 'Тысяча условных банок в смену', '543', '10^3 усл. банк/ смен', '', 'ТЫС УСЛ БАНК/СМЕН', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(265, 'Миллион единиц в год', '544', '10^6 ед/год', '', 'МЛН ЕД/ГОД', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(266, 'Посещение в смену', '545', 'посещ/смен', '', 'ПОСЕЩ/СМЕН', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(267, 'Тысяча посещений в смену', '546', '10^3 посещ/смен', '', 'ТЫС ПОСЕЩ/ СМЕН', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(268, 'Пара в смену', '547', 'пар/смен', '', 'ПАР/СМЕН', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(269, 'Тысяча пар в смену', '548', '10^3 пар/смен', '', 'ТЫС ПАР/СМЕН', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(270, 'Миллион тонн в год', '550', '10^6 т/год', '', 'МЛН Т/ГОД', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(271, 'Тонна переработки в сутки', '552', 'т перераб/сут', '', 'Т ПЕРЕРАБ/СУТ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(272, 'Тысяча тонн переработки в сутки', '553', '10^3 т перераб/ сут', '', 'ТЫС Т ПЕРЕРАБ/СУТ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(273, 'Центнер переработки в сутки', '554', 'ц перераб/сут', '', 'Ц ПЕРЕРАБ/СУТ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(274, 'Тысяча центнеров переработки в сутки', '555', '10^3 ц перераб/ сут', '', 'ТЫС Ц ПЕРЕРАБ/СУТ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(275, 'Тысяча голов в год', '556', '10^3 гол/год', '', 'ТЫС ГОЛ/ГОД', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(276, 'Миллион голов в год', '557', '10^6 гол/год', '', 'МЛН ГОЛ/ГОД', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(277, 'Тысяча птицемест', '558', '10^3 птицемест', '', 'ТЫС ПТИЦЕМЕСТ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(278, 'Тысяча кур-несушек', '559', '10^3 кур. несуш', '', 'ТЫС КУР. НЕСУШ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(279, 'Минимальная заработная плата', '560', 'мин. заработн. плат', '', 'МИН ЗАРАБОТН ПЛАТ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(280, 'Тысяча тонн пара в час', '561', '10^3 т пар/ч', '', 'ТЫС Т ПАР/Ч', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(281, 'Тысяча прядильных веретен', '562', '10^3 пряд.верет', '', 'ТЫС ПРЯД ВЕРЕТ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(282, 'Тысяча прядильных мест', '563', '10^3 пряд.мест', '', 'ТЫС ПРЯД МЕСТ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(283, 'Доза', '639', 'доз', '', 'ДОЗ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(284, 'Тысяча доз', '640', '10^3 доз', '', 'ТЫС ДОЗ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(285, 'Единица', '642', 'ед', '', 'ЕД', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(286, 'Тысяча единиц', '643', '10^3 ед', '', 'ТЫС ЕД', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(287, 'Миллион единиц', '644', '10^6 ед', '', 'МЛН ЕД', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(288, 'Канал', '661', 'канал', '', 'КАНАЛ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(289, 'Тысяча комплектов', '673', '10^3 компл', '', 'ТЫС КОМПЛ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(290, 'Место', '698', 'мест', '', 'МЕСТ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(291, 'Тысяча мест', '699', '10^3 мест', '', 'ТЫС МЕСТ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(292, 'Тысяча номеров', '709', '10^3 ном', '', 'ТЫС НОМ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(293, 'Тысяча гектаров порций', '724', '10^3 га порц', '', 'ТЫС ГА ПОРЦ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(294, 'Тысяча пачек', '729', '10^3 пач', '', 'ТЫС ПАЧ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(295, 'Процент', '744', '%', '', 'ПРОЦ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(296, 'Промилле (0,1 процента)', '746', 'промилле', '', 'ПРОМИЛЛЕ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(297, 'Тысяча рулонов', '751', '10^3 рул', '', 'ТЫС РУЛ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(298, 'Тысяча станов', '761', '10^3 стан', '', 'ТЫС СТАН', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(299, 'Станция', '762', 'станц', '', 'СТАНЦ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(300, 'Тысяча тюбиков', '775', '10^3 тюбик', '', 'ТЫС ТЮБИК', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(301, 'Тысяча условных тубов', '776', '10^3 усл.туб', '', 'ТЫС УСЛ ТУБ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(302, 'Миллион упаковок', '779', '10^6 упак', '', 'МЛН УПАК', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(303, 'Тысяча упаковок', '782', '10^3 упак', '', 'ТЫС УПАК', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(304, 'Человек', '792', 'чел', '', 'ЧЕЛ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(305, 'Тысяча человек', '793', '10^3 чел', '', 'ТЫС ЧЕЛ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(306, 'Миллион человек', '794', '10^6 чел', '', 'МЛН ЧЕЛ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(307, 'Миллион экземпляров', '808', '10^6 экз', '', 'МЛН ЭКЗ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(308, 'Ячейка', '810', 'яч', '', 'ЯЧ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(309, 'Ящик', '812', 'ящ', '', 'ЯЩ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(310, 'Голова', '836', 'гол', '', 'ГОЛ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(311, 'Тысяча пар', '837', '10^3 пар', '', 'ТЫС ПАР', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(312, 'Миллион пар', '838', '10^6 пар', '', 'МЛН ПАР', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(313, 'Комплект', '839', 'компл', '', 'КОМПЛ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(314, 'Секция', '840', 'секц', '', 'СЕКЦ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(315, 'Бутылка', '868', 'бут', '', 'БУТ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(316, 'Тысяча бутылок', '869', '10^3 бут', '', 'ТЫС БУТ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(317, 'Ампула', '870', 'ампул', '', 'АМПУЛ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(318, 'Тысяча ампул', '871', '10^3 ампул', '', 'ТЫС АМПУЛ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(319, 'Флакон', '872', 'флак', '', 'ФЛАК', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(320, 'Тысяча флаконов', '873', '10^3 флак', '', 'ТЫС ФЛАК', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(321, 'Тысяча тубов', '874', '10^3 туб', '', 'ТЫС ТУБ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(322, 'Тысяча коробок', '875', '10^3 кор', '', 'ТЫС КОР', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(323, 'Условная единица', '876', 'усл. ед', '', 'УСЛ ЕД', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(324, 'Тысяча условных единиц', '877', '10^3 усл. ед', '', 'ТЫС УСЛ ЕД', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(325, 'Миллион условных единиц', '878', '10^6 усл. ед', '', 'МЛН УСЛ ЕД', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(326, 'Условная штука', '879', 'усл. шт', '', 'УСЛ ШТ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(327, 'Тысяча условных штук', '880', '10^3 усл. шт', '', 'ТЫС УСЛ ШТ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(328, 'Условная банка', '881', 'усл. банк', '', 'УСЛ БАНК', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(329, 'Тысяча условных банок', '882', '10^3 усл. банк', '', 'ТЫС УСЛ БАНК', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(330, 'Миллион условных банок', '883', '10^6 усл. банк', '', 'МЛН УСЛ БАНК', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(331, 'Условный кусок', '884', 'усл. кус', '', 'УСЛ КУС', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(332, 'Тысяча условных кусков', '885', '10^3 усл. кус', '', 'ТЫС УСЛ КУС', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(333, 'Миллион условных кусков', '886', '10^6 усл. кус', '', 'МЛН УСЛ КУС', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(334, 'Условный ящик', '887', 'усл. ящ', '', 'УСЛ ЯЩ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(335, 'Тысяча условных ящиков', '888', '10^3 усл. ящ', '', 'ТЫС УСЛ ЯЩ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(336, 'Условная катушка', '889', 'усл. кат', '', 'УСЛ КАТ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(337, 'Тысяча условных катушек', '890', '10^3 усл. кат', '', 'ТЫС УСЛ КАТ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(338, 'Условная плитка', '891', 'усл. плит', '', 'УСЛ ПЛИТ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(339, 'Тысяча условных плиток', '892', '10^3 усл. плит', '', 'ТЫС УСЛ ПЛИТ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(340, 'Условный кирпич', '893', 'усл. кирп', '', 'УСЛ КИРП', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(341, 'Тысяча условных кирпичей', '894', '10^3 усл. кирп', '', 'ТЫС УСЛ КИРП', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(342, 'Миллион условных кирпичей', '895', '10^6 усл. кирп', '', 'МЛН УСЛ КИРП', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(343, 'Семья', '896', 'семей', '', 'СЕМЕЙ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(344, 'Тысяча семей', '897', '10^3 семей', '', 'ТЫС СЕМЕЙ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(345, 'Миллион семей', '898', '10^6 семей', '', 'МЛН СЕМЕЙ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(346, 'Домохозяйство', '899', 'домхоз', '', 'ДОМХОЗ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(347, 'Тысяча домохозяйств', '900', '10^3 домхоз', '', 'ТЫС ДОМХОЗ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(348, 'Миллион домохозяйств', '901', '10^6 домхоз', '', 'МЛН ДОМХОЗ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(349, 'Ученическое место', '902', 'учен. мест', '', 'УЧЕН МЕСТ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(350, 'Тысяча ученических мест', '903', '10^3 учен. мест', '', 'ТЫС УЧЕН МЕСТ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(351, 'Рабочее место', '904', 'раб. мест', '', 'РАБ МЕСТ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(352, 'Тысяча рабочих мест', '905', '10^3 раб. мест', '', 'ТЫС РАБ МЕСТ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(353, 'Посадочное место', '906', 'посад. мест', '', 'ПОСАД МЕСТ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(354, 'Тысяча посадочных мест', '907', '10^3 посад. мест', '', 'ТЫС ПОСАД МЕСТ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(355, 'Номер', '908', 'ном', '', 'НОМ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(356, 'Квартира', '909', 'кварт', '', 'КВАРТ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(357, 'Тысяча квартир', '910', '10^3 кварт', '', 'ТЫС КВАРТ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(358, 'Койка', '911', 'коек', '', 'КОЕК', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(359, 'Тысяча коек', '912', '10^3 коек', '', 'ТЫС КОЕК', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(360, 'Том книжного фонда', '913', 'том книжн. фонд', '', 'ТОМ КНИЖН ФОНД', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(361, 'Тысяча томов книжного фонда', '914', '10^3 том. книжн. фонд', '', 'ТЫС ТОМ КНИЖН ФОНД', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(362, 'Условный ремонт', '915', 'усл. рем', '', 'УСЛ РЕМ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(363, 'Условный ремонт в год', '916', 'усл. рем/год', '', 'УСЛ РЕМ/ГОД', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(364, 'Смена', '917', 'смен', '', 'СМЕН', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(365, 'Лист авторский', '918', 'л. авт', '', 'ЛИСТ АВТ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(366, 'Лист печатный', '920', 'л. печ', '', 'ЛИСТ ПЕЧ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(367, 'Лист учетно-издательский', '921', 'л. уч.-изд', '', 'ЛИСТ УЧ.ИЗД', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(368, 'Знак', '922', 'знак', '', 'ЗНАК', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(369, 'Слово', '923', 'слово', '', 'СЛОВО', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(370, 'Символ', '924', 'символ', '', 'СИМВОЛ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(371, 'Условная труба', '925', 'усл. труб', '', 'УСЛ ТРУБ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(372, 'Тысяча пластин', '930', '10^3 пласт', '', 'ТЫС ПЛАСТ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(373, 'Миллион доз', '937', '10^6 доз', '', 'МЛН ДОЗ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(374, 'Миллион листов-оттисков', '949', '10^6 лист.оттиск', '', 'МЛН ЛИСТ.ОТТИСК', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(375, 'Вагоно(машино)-день', '950', 'ваг (маш).дн', '', 'ВАГ (МАШ).ДН', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(376, 'Тысяча вагоно-(машино)-часов', '951', '10^3 ваг (маш).ч', '', 'ТЫС ВАГ (МАШ).Ч', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(377, 'Тысяча вагоно-(машино)-километров', '952', '10^3 ваг (маш).км', '', 'ТЫС ВАГ (МАШ).КМ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(378, 'Тысяча место-километров', '953', '10 ^3мест.км', '', 'ТЫС МЕСТ.КМ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(379, 'Вагоно-сутки', '954', 'ваг.сут', '', 'ВАГ.СУТ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(380, 'Тысяча поездо-часов', '955', '10^3 поезд.ч', '', 'ТЫС ПОЕЗД.Ч', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(381, 'Тысяча поездо-километров', '956', '10^3 поезд.км', '', 'ТЫС ПОЕЗД.КМ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(382, 'Тысяча тонно-миль', '957', '10^3 т.миль', '', 'ТЫС Т.МИЛЬ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(383, 'Тысяча пассажиро-миль', '958', '10^3 пасс.миль', '', 'ТЫС ПАСС.МИЛЬ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(384, 'Автомобиле-день', '959', 'автомоб.дн', '', 'АВТОМОБ.ДН', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(385, 'Тысяча автомобиле-тонно-дней', '960', '10^3 автомоб.т.дн', '', 'ТЫС АВТОМОБ.Т.ДН', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(386, 'Тысяча автомобиле-часов', '961', '10^3 автомоб.ч', '', 'ТЫС АВТОМОБ.Ч', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(387, 'Тысяча автомобиле-место-дней', '962', '10^3 автомоб.мест. дн', '', 'ТЫС АВТОМОБ.МЕСТ. ДН', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(388, 'Приведенный час', '963', 'привед.ч', '', 'ПРИВЕД.Ч', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(389, 'Самолето-километр', '964', 'самолет.км', '', 'САМОЛЕТ.КМ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(390, 'Тысяча километров', '965', '10^3 км', '', 'ТЫС КМ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(391, 'Тысяча тоннаже-рейсов', '966', '10^3 тоннаж. рейс', '', 'ТЫС ТОННАЖ. РЕЙС', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(392, 'Миллион тонно-миль', '967', '10^6 т. миль', '', 'МЛН Т. МИЛЬ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(393, 'Миллион пассажиро-миль', '968', '10^6 пасс. миль', '', 'МЛН ПАСС. МИЛЬ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(394, 'Миллион тоннаже-миль', '969', '10^6 тоннаж. миль', '', 'МЛН ТОННАЖ. МИЛЬ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(395, 'Миллион пассажиро-место-миль', '970', '10^6 пасс. мест. миль', '', 'МЛН ПАСС. МЕСТ. МИЛЬ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(396, 'Кормо-день', '971', 'корм. дн', '', 'КОРМ. ДН', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(397, 'Центнер кормовых единиц', '972', 'ц корм ед', '', 'Ц КОРМ ЕД', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(398, 'Тысяча автомобиле-километров', '973', '10^3 автомоб. км', '', 'ТЫС АВТОМОБ. КМ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(399, 'Тысяча тоннаже-сут', '974', '10^3 тоннаж. сут', '', 'ТЫС ТОННАЖ. СУТ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(400, 'Суго-сутки', '975', 'суго. сут.', '', 'СУГО. СУТ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(401, 'Штук в 20-футовом эквиваленте (ДФЭ)', '976', 'штук в 20-футовом эквиваленте', '', 'ШТ В 20 ФУТ ЭКВИВ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(402, 'Канало-километр', '977', 'канал. км', '', 'КАНАЛ. КМ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(403, 'Канало-концы', '978', 'канал. конц', '', 'КАНАЛ. КОНЦ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(404, 'Тысяча экземпляров', '979', '10^3 экз', '', 'ТЫС ЭКЗ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(405, 'Тысяча долларов', '980', '10^3 доллар', '', 'ТЫС ДОЛЛАР', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(406, 'Тысяча тонн кормовых единиц', '981', '10^3 корм ед', '', 'ТЫС Т КОРМ ЕД', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(407, 'Миллион тонн кормовых единиц', '982', '10^6 корм ед', '', 'МЛН Т КОРМ ЕД', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(408, 'Судо-сутки', '983', 'суд.сут', '', 'СУД.СУТ', '', 7, 2, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(409, 'Гектометр', '017', '', 'hm', '', 'HMT', 1, 3, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(410, 'Миля (уставная) (1609,344 м)', '045', '', 'mile', '', 'SMI', 1, 3, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(411, 'Акр (4840 квадратных ярдов)', '077', '', 'acre', '', 'ACR', 2, 3, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(412, 'Квадратная миля', '079', '', 'mile2', '', 'MIK', 2, 3, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(413, 'Жидкостная унция СК (28,413 см3)', '135', '', 'fl oz (UK)', '', 'OZI', 3, 3, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(414, 'Джилл СК (0,142065 дм3)', '136', '', 'gill (UK)', '', 'GII', 3, 3, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(415, 'Пинта СК (0,568262 дм3)', '137', '', 'pt (UK)', '', 'PTI', 3, 3, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(416, 'Кварта СК (1,136523 дм3)', '138', '', 'qt (UK)', '', 'QTI', 3, 3, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(417, 'Галлон СК (4,546092 дм3)', '139', '', 'gal (UK)', '', 'GLI', 3, 3, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(418, 'Бушель СК (36,36874 дм3)', '140', '', 'bu (UK)', '', 'BUI', 3, 3, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(419, 'Жидкостная унция США (29,5735 см3)', '141', '', 'fl oz (US)', '', 'OZA', 3, 3, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(420, 'Джилл США (11,8294 см3)', '142', '', 'gill (US)', '', 'GIA', 3, 3, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(421, 'Жидкостная пинта США (0,473176 дм3)', '143', '', 'liq pt (US)', '', 'PTL', 3, 3, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(422, 'Жидкостная кварта США (0,946353 дм3)', '144', '', 'liq qt (US)', '', 'QTL', 3, 3, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(423, 'Жидкостный галлон США (3,78541 дм3)', '145', '', 'gal (US)', '', 'GLL', 3, 3, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(424, 'Баррель (нефтяной) США (158,987 дм3)', '146', '', 'barrel (US)', '', 'BLL', 3, 3, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(425, 'Сухая пинта США (0,55061 дм3)', '147', '', 'dry pt (US)', '', 'PTD', 3, 3, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(426, 'Сухая кварта США (1,101221 дм3)', '148', '', 'dry qt (US)', '', 'QTD', 3, 3, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(427, 'Сухой галлон США (4,404884 дм3)', '149', '', 'dry gal (US)', '', 'GLD', 3, 3, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(428, 'Бушель США (35,2391 дм3)', '150', '', 'bu (US)', '', 'BUA', 3, 3, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(429, 'Сухой баррель США (115,627 дм3)', '151', '', 'bbl (US)', '', 'BLD', 3, 3, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(430, 'Стандарт', '152', '', '-', '', 'WSD', 3, 3, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(431, 'Корд (3,63 м3)', '153', '', '-', '', 'WCD', 3, 3, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(432, 'Тысячи бордфутов (2,36 м3)', '154', '', '-', '', 'MBF', 3, 3, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(433, 'Нетто-регистровая тонна', '182', '', '-', '', 'NTT', 4, 3, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(434, 'Обмерная (фрахтовая) тонна', '183', '', '-', '', 'SHT', 4, 3, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(435, 'Водоизмещение', '184', '', '-', '', 'DPT', 4, 3, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(436, 'Фунт СК, США (0,45359237 кг)', '186', '', 'lb', '', 'LBR', 4, 3, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(437, 'Унция СК, США (28,349523 г)', '187', '', 'oz', '', 'ONZ', 4, 3, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(438, 'Драхма СК (1,771745 г)', '188', '', 'dr', '', 'DRI', 4, 3, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(439, 'Гран СК, США (64,798910 мг)', '189', '', 'gn', '', 'GRN', 4, 3, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(440, 'Стоун СК (6,350293 кг)', '190', '', 'st', '', 'STI', 4, 3, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(441, 'Квартер СК (12,700586 кг)', '191', '', 'qtr', '', 'QTR', 4, 3, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(442, 'Центал СК (45,359237 кг)', '192', '', '-', '', 'CNT', 4, 3, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(443, 'Центнер США (45,3592 кг)', '193', '', 'cwt', '', 'CWA', 4, 3, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(444, 'Длинный центнер СК (50,802345 кг)', '194', '', 'cwt (UK)', '', 'CWI', 4, 3, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(445, 'Короткая тонна СК, США (0,90718474 т) [2*]', '195', '', 'sht', '', 'STN', 4, 3, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(446, 'Длинная тонна СК, США (1,0160469 т) [2*]', '196', '', 'lt', '', 'LTN', 4, 3, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(447, 'Скрупул СК, США (1,295982 г)', '197', '', 'scr', '', 'SCR', 4, 3, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(448, 'Пеннивейт СК, США (1,555174 г)', '198', '', 'dwt', '', 'DWT', 4, 3, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(449, 'Драхма СК (3,887935 г)', '199', '', 'drm', '', 'DRM', 4, 3, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(450, 'Драхма США (3,887935 г)', '200', '', '-', '', 'DRA', 4, 3, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(451, 'Унция СК, США (31,10348 г); тройская унция', '201', '', 'apoz', '', 'APZ', 4, 3, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(452, 'Тройский фунт США (373,242 г)', '202', '', '-', '', 'LBT', 4, 3, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(453, 'Эффективная мощность (245,7 ватт)', '213', '', 'B.h.p.', '', 'BHP', 5, 3, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(454, 'Британская тепловая единица (1,055 кДж)', '275', '', 'Btu', '', 'BTU', 5, 3, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(455, 'Гросс (144 шт.)', '638', '', 'gr; 144', '', 'GRO', 7, 3, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(456, 'Большой гросс (12 гроссов)', '731', '', '1728', '', 'GGR', 7, 3, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(457, 'Короткий стандарт (7200 единиц)', '738', '', '-', '', 'SST', 7, 3, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(458, 'Галлон спирта установленной крепости', '835', '', '-', '', 'PGL', 7, 3, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(459, 'Международная единица', '851', '', '-', '', 'NIU', 7, 3, 1, '')");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "unit` (unit_id, name, number_code, rus_name1, eng_name1, rus_name2, eng_name2, unit_group_id, unit_type_id, visible, comment) VALUES(460, 'Сто международных единиц', '853', '', '-', '', 'HIU', 7, 3, 1, '')");

	}


	/**
	 * ver 7
	 * update 2017-05-19
	 * Устанавливает обновления
	 */
	public function checkUpdates($settings) {

		$table_fields = $this->defineTableFields();
		$message = "";
		if (isset($settings['exchange1c_version'])) {
			$version = $settings['exchange1c_version'];
			if ($version == '1.6.3.5') {
				$version = $this->update1_6_3_6($version, $message, $table_fields);
			}
			if ($version == '1.6.3.6') {
				$version = $this->update1_6_3_7($version, $message, $table_fields);
			}
			if ($version == '1.6.3.7') {
				$version = $this->update1_6_3_8($version, $message, $table_fields);
			}
			if ($version == '1.6.3.8') {
				$version = $this->update1_6_3_9($version, $message, $table_fields);
			}
			if ($version == '1.6.3.9') {
				$version = $this->update1_6_3_10($version, $message, $table_fields);
			}
			if ($version == '1.6.3.10') {
				$version = $this->update1_6_3_11($version, $message, $table_fields);
			}
			if ($version == '1.6.3.11') {
				$version = $this->update1_6_3_12($version, $message, $table_fields);
			}
		}
		if (!$this->ERROR) {
			if ($version != $settings['exchange1c_version']) {
				$settings['exchange1c_table_fields']	= $this->defineTableFields();
				$this->setEvents();
				$settings['exchange1c_version'] = $version;
				$this->model_setting_setting->editSetting('exchange1c', $settings);
				$message .= "<br /><strong>ВНИМАНИЕ! после обновления необходимо проверить все настройки и сохранить!</strong>";
			}
		}

		return array('error'=>$this->ERROR, 'success'=>$message);

	} // checkUpdates()


	/**
	 * Устанавливает обновления на версию 1.6.3.6
	 */
	private function update1_6_3_6($old_version, &$message, $table_fields) {

		$version = '1.6.3.6';
		$message .= "Исправлены ошибки (1), доработки (0):<br>";
		$message .= "1. Ошибка при ручной генерации SEO, если в базе нет таблицы manufacturer_description <br>";
		$message .= "Обновление с версии " . $old_version . " на версию " . $version ." прошло успешно";
		return $version;

	} // update1_6_3_6()


	/**
	 * Устанавливает обновления на версию 1.6.3.7
	 */
	private function update1_6_3_7($old_version, &$message, $table_fields) {

		$version = '1.6.3.7';
		$message .= "Исправлены ошибки E(3), обновления U(2):<br>";
		$message .= "E1. Затирание основнлй картинки при отключенном обновлении картинок<br>";
		$message .= "E2. При синхронизации по полям Артикул, Штрихкод или Наименование при пустом поле теперь не прерывается обмен<br>";
		$message .= "E3. Если предложение не найдено, обмен теперь не прерывается<br>";
		$message .= "U1. Добавлена кнопка применить<br>";
		$message .= "U2. Добавлена настройка - резервировать товары в заказе<br>";
		$message .= "Обновление с версии " . $old_version . " на версию " . $version ." прошло успешно";
		return $version;

	} // update1_6_3_7()


	/**
	 * Устанавливает обновления на версию 1.6.3.8
	 */
	private function update1_6_3_8($old_version, &$message, $table_fields) {

		if (!isset($table_fields['product_price']['action'])) {
			$result = @$this->query("ALTER TABLE  `" . DB_PREFIX . "product_price` ADD  `action` INT( 1 ) NOT NULL AFTER `customer_group_id`");
			$message .= ($result ? "Успешно добавлено поле " : "Ошибка при добавлении поля ") . "'action' в таблицу 'product_price'<br />";
			if (!$result) return $old_version;
		}
		// Пересоздадим индекс
		$result = @$this->db->query("ALTER TABLE  `" . DB_PREFIX . "product_price` DROP INDEX `product_price_key`");
		$result = @$this->db->query("ALTER TABLE  `" . DB_PREFIX . "product_price` ADD UNIQUE INDEX  `product_price_key` (`product_id`,`product_feature_id`,`customer_group_id`,`action`)");
		$message .= ($result ? "Успешно пересоздан индекс " : "Ошибка при пересоздании индекса ") . "'product_price_key' в таблице 'product_price'<br />";
		if (!$result) return $old_version;

		$version = '1.6.3.8';
		$message .= "Исправлены ошибки E(1), обновления U(3):<br>";
		$message .= "E1. Несколько ошибок при выгрузке заказов<br>";
		$message .= "U1. Добавлено удаление файла перед распаковкой XML из архива<br>";
		$message .= "U2. Добавлены акция и скидка в цены при загрузке характеристик<br>";
		$message .= "U3. Добавлены опция отключения очистки акций и скидок при полной выгрузке<br>";
		$message .= "Обновление с версии " . $old_version . " на версию " . $version ." прошло успешно";
		return $version;

	} // update1_6_3_8()


	/**
	 * Устанавливает обновления на версию 1.6.3.9
	 */
	private function update1_6_3_9($old_version, &$message, $table_fields) {

		$version = '1.6.3.9';
		$message .= "Исправлены ошибки E(4), обновления U(1):<br>";
		$message .= "E1. При смене статуса заказа после выгрузки заказов в 1С<br>";
		$message .= "E2. Исправлена ошибка с чтением картинок<br>";
		$message .= "E3. Исправлена ошибка с наложением водяных знаков<br>";
		$message .= "E4. Исправлена ошибка в SEO<br>";
		$message .= "U1. Добавлена запись остатков в опции у характеристик<br>";
		$message .= "Обновление с версии " . $old_version . " на версию " . $version ." прошло успешно";
		return $version;

	} // update1_6_3_9()


	/**
	 * Устанавливает обновления на версию 1.6.3.10
	 */
	private function update1_6_3_10($old_version, &$message, $table_fields) {

		$version = '1.6.3.10';
		$message .= "Исправлены ошибки E(2), обновления U(3):<br>";
		$message .= "E1. Исправлена ошибка при загрузке в товар полного наименования из реквизита<br>";
		$message .= "E2. Исправлена ошибка при чтении остатков без складов (формат Битрикс)<br>";
		$message .= "U1. Добавлена очистка картинки из кэша, которая загружается<br>";
		$message .= "U2. Добавлена загрузка определяемых пользователем свойств<br>";
		$message .= "U3. Объеденены опции очистки остатков в одну<br>";
		$message .= "Обновление с версии " . $old_version . " на версию " . $version ." прошло успешно";

		return $version;

	} // update1_6_3_10()


	/**
	 * Устанавливает обновления на версию 1.6.3.11
	 */
	private function update1_6_3_11($old_version, &$message, $table_fields) {

		$version = '1.6.3.11';
		$message .= "Изменения в версии теперь смотрите в вкладке Обновления<br />";
		$message .= "Обновление с версии " . $old_version . " на версию " . $version ." прошло успешно";

		return $version;

	} // update1_6_3_11()


	/**
	 * Упраздняется классификатор единиц измерений
	 * Устанавливает обновления на версию 1.6.3.12
	 */
	private function update1_6_3_12($old_version, &$message, $table_fields) {

		$this->installUnits();

		// Добавим колонку unit_id в таблицу product_price
		$result = @$this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "product_price` WHERE `field` = 'unit_id'");
		if (!$result) return $old_version;

		if (!$result->num_rows) {
			$result = @$this->db->query("ALTER TABLE  `" . DB_PREFIX . "product_price` ADD `unit_id` INT(4) DEFAULT 0");
		}
		if (!$result) return $old_version;

		// Добавим колонку unit_id в таблицу order_product
		$result = @$this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "order_product` WHERE `field` = 'unit_id'");
		if (!$result) return $old_version;

		if (!$result->num_rows) {
			$result = @$this->db->query("ALTER TABLE  `" . DB_PREFIX . "order_product` ADD `unit_id` INT(4) DEFAULT 0 AFTER `quantity`");
		}
		if (!$result) return $old_version;

		//show index from `oc_attribute_value` where `key_name` = 'key_guid'
		$result = @$this->db->query("SHOW INDEX FROM `" . DB_PREFIX . "attribute_value` WHERE `key_name` = 'key_guid'");
		if (!$result) return $old_version;

		if (!$result->num_rows) {
			//ALTER TABLE  `oc_attribute_value` ADD INDEX `key_guid` (`guid`)
			$result = @$this->db->query("ALTER TABLE  `" . DB_PREFIX . "attribute_value` ADD INDEX `key_guid` (`guid`)");
		}
		if (!$result) return $old_version;

		// Проверим в необходимости модификации таблицы unit_to_1c
		$result = @$this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "unit_to_1c` WHERE `field` = 'code'");
		if (!$result) return $old_version;

		if ($result->num_rows) {
			$result = @$this->db->query("ALTER TABLE  `" . DB_PREFIX . "unit_to_1c` CHANGE `code` `number_code` INT(4) DEFAULT 0");
			if (!$result) return $old_version;
		}

		$result = @$this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "unit_to_1c` WHERE `field` = 'fullname'");
		if (!$result) return $old_version;

		if ($result->num_rows) {
			$result = @$this->db->query("ALTER TABLE  `" . DB_PREFIX . "unit_to_1c` CHANGE `fullname` `full_name` VARCHAR(50) DEFAULT ''");
			if (!$result) return $old_version;
		}

		$result = @$this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "unit_to_1c` WHERE `field` = 'eng_name2'");
		if (!$result) return $old_version;

		if ($result->num_rows) {
			$result = @$this->db->query("ALTER TABLE  `" . DB_PREFIX . "unit_to_1c` DROP `eng_name2`");
			if (!$result) return $old_version;
		}

		$result = @$this->db->query("SHOW INDEX FROM `" . DB_PREFIX . "unit_to_1c` WHERE column_name = 'unit_id'");
		if (!$result->num_rows) {
			@$this->db->query("ALTER TABLE  `" . DB_PREFIX . "unit_to_1c` ADD  `unit_id` SMALLINT(4) NOT NULL AUTO_INCREMENT FIRST, ADD PRIMARY KEY (`unit_id`)");
			@$this->db->query("ALTER TABLE  `" . DB_PREFIX . "unit_to_1c` ADD INDEX `key_guid` (`guid`)");
			@$this->db->query("ALTER TABLE  `" . DB_PREFIX . "unit_to_1c` ADD INDEX `key_name` (`name`)");
		} else {
			@$this->db->query("ALTER TABLE  `" . DB_PREFIX . "unit_to_1c` CHANGE  `unit_id`  `unit_id` SMALLINT(4) NOT NULL AUTO_INCREMENT");

			$result = @$this->db->query("SHOW INDEX FROM `" . DB_PREFIX . "unit_to_1c` WHERE column_name = 'guid'");
			if ($result->num_rows > 1) {
				foreach($result->rows as $row) {
					@$this->db->query("ALTER TABLE  `" . DB_PREFIX . "unit_to_1c` DROP INDEX `" . $row['Key_name'] . "`");
				}
				@$this->db->query("ALTER TABLE  `" . DB_PREFIX . "unit_to_1c` ADD INDEX `key_guid` (`guid`)");
			}

			$result = @$this->db->query("SHOW INDEX FROM `" . DB_PREFIX . "unit_to_1c` WHERE column_name = 'name'");
			if (!$result->num_rows) {
				@$this->db->query("ALTER TABLE  `" . DB_PREFIX . "unit_to_1c` ADD INDEX `key_name` (`name`)");
			}
		}

		if (!$result) return $old_version;


		if ($result) {
			// После добавления колонки, запишем в нее значения
			// Подготовим список единиц для обновления
			$units = array();
			$query = $this->query("SELECT DISTINCT `unit_id` FROM `" . DB_PREFIX . "product_unit`");
			if ($query->num_rows) {
				$units[] = $query->row['unit_id'];
			}
			$this->log($units,2);

			// Прочитаем названия из классификатора
			$units_data = array();
			foreach ($units as $unit_id) {
				$query = $this->query("SELECT `number_code`,`rus_name1`,`eng_name1`,`name` FROM `" . DB_PREFIX . "unit` WHERE `unit_id` = " . $unit_id);
				if ($query->num_rows) {
					$units_data[$unit_id] = array(
						'name'			=> $query->row['name'],
						'number_code'	=> $query->row['number_code'],
						'rus_name1'		=> $query->row['rus_name1'],
						'eng_name1'		=> $query->row['eng_name1']
					);
				}
			}

			// Удаляем классификатор
			//$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "unit`");
			//$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "unit_group`");
			//$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "unit_type`");

			if (count($units_data)) {

				// Обновляем единицы
				foreach ($units_data as $unit_id => $unit) {
					$this->query("UPDATE `" . DB_PREFIX . "product_unit` SET `unit_id` = " . $unit['number_code'] . " WHERE `unit_id` = " . $unit_id);
				}
			}
		}

		$version = '1.6.3.12';
		$message .= "Изменения в версии теперь смотрите в вкладке Обновления<br />";
		$message .= "Обновление с версии " . $old_version . " на версию " . $version ." прошло успешно";

		return $version;

	} // update1_6_3_12()

}