<?php

Class TelphinCall {
    private $APP_KEY;
    private $APP_SECRET;
    private $API_SERVER;
	private $EXTENSION_ID;
    const MAX_COUNT_IP = 10000;        // максимальное кол-во попыток звонка с одного ip
    const MAX_COUNT_TEL = 1000;        // максимальное кол-во попыток звонка на один номер
    const BLOCK_PERIOD  = 86400;    // в течение какого временного периода действует ограничение на кол-во звонков. По умолчанию, 24 часа

    // конструктор класса
    function __construct( $app_key, $app_secret, $server_name, $extension_id) {
        $this->APP_KEY    = $app_key;
        $this->APP_SECRET = $app_secret;
        $this->API_SERVER = $server_name;
		$this->EXTENSION_ID = $extension_id;
    }

    // get_token -- получение токена
    // если локально не сохранён или протух, то получаем новый
    public function get_token() {
        $file_data = $this->get_file_data();

        if ( $file_data ) {
            if ( isset( $file_data['access_token'] ) && isset( $file_data['expires_in'] ) ) {
                if ( $file_data['expires_in'] > time() ) {
                    return $file_data['access_token'];
                }
            }
        }

        $res = $this->gen_token();
        $this->save_token( $res['access_token'], $res['expires_in'] );
        return $res['access_token'];
    }

    // gen_token -- сгенерировать токен
    private function gen_token() {
        $url = 'https://' . $this->API_SERVER . '/oauth/token';

        $post_data = array(
		'client_id'       => $this->APP_KEY,
		'client_secret'   => $this->APP_SECRET,
        'grant_type'      => 'client_credentials',
        );

        $req = curl_init();
        curl_setopt( $req, CURLOPT_URL, $url );
        curl_setopt( $req, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $req, CURLOPT_FOLLOWLOCATION, true );
        curl_setopt( $req, CURLOPT_POST, true );
        curl_setopt( $req, CURLOPT_POSTFIELDS, http_build_query( $post_data, '', '&' ) );
        curl_setopt( $req, CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $req, CURLOPT_HTTPHEADER, array( 'Content-type: application/x-www-form-urlencoded' ) );
        curl_setopt( $req, CURLOPT_USERAGENT, 'TelphinWebCall-RingMeScript' );
        $res = json_decode( curl_exec( $req ), true );

        if ( ! $res )
            return array( 'error' => 'Error get token' );
        elseif ( isset( $res['error'] ) )
            return array( 'error' => $res['error'] );
        else
            return $res;
    }

    // save_token -- сохранить токен на диск
    private function save_token( $access_token, $expires_in ) {
        $expires_in += time();

        $token_data = array(
            'access_token'  => $access_token,
            'expires_in'    => $expires_in,
        );

        return $this->save_data( $token_data );
    }

    // save_data -- сохраняет данные на диск в виде JSON
    private function save_data( $data ) {
        if ( $data ) {
            $file_data = $this->get_file_data();
            foreach ( $data as $data_key => $data_value ) {
                if ( $data_key == 'access_token' || $data_key == 'expires_in' )
                    $file_data[ $data_key ] = $data_value;
                elseif ( $data_key == 'ips' || $data_key == 'tels' ) {
                    $field_key = $data_value[0];
                    $ncount = ( isset( $data_value[1] ) ) ? $data_value[1] : 1;
                    $file_data[ $data_key ][ $field_key ]['timestamp'] = time();
                    $file_data[ $data_key ][ $field_key ]['ncount']    = $ncount;
                }
            }

            $json = json_encode( $file_data );
            $file = fopen( $this->get_filename(), 'w' );
            fwrite( $file, $json . "\n" );
            fclose( $file );
            return true;
        }

        return false;
    }

    // prepare_count_field -- подготавливает и возвращает ncount запрашиваемого значения
    // сбрасывает счётчик попыток если со времени последней попытки прошло более BLOCK_PERIOD секунд
    private function prepare_count_field( $field_type, $field_name ) {
        if ( ! in_array( $field_type, array( 'ips', 'tels' ) ) ) {
            return 0;
        }

        $file_data = $this->get_file_data();
        $field_ncount;
        if ( isset( $file_data[ $field_type ][ $field_name ] ) ) {
            $field_timestamp = $file_data[ $field_type ][ $field_name ]['timestamp'];
            $field_ncount    = $file_data[ $field_type ][ $field_name ]['ncount'];
            if ( $field_timestamp + self::BLOCK_PERIOD <= time() ) // прошёл период блокировки, сбрасываем счётчик
                $field_ncount = 0;
        }
        else
            $field_ncount = 0;
        $this->save_data( array( $field_type => array( $field_name, $field_ncount + 1 ) ) );
        return $field_ncount;
    }

    private function get_count_ips( $ip ) {
        return $this->prepare_count_field( 'ips', 'ip_' . $ip );
    }

    private function get_count_tels( $tel ) {
        return $this->prepare_count_field( 'tels', 'tel_' . $tel );
    }

    // get_file_data -- получение данных с диска в виде именованного массива
    private function get_file_data() {
        $filename = $this->get_filename();
        if ( file_exists( $filename ) ) {
            $file_data = file_get_contents( $filename );
            return json_decode( $file_data, true );
        }
        else {
            return false;
        }
    }

    // файл на диске, куда записывается информация о токене и посетителях
    private function get_filename() {
        return '._ttmc_' . md5( $this->APP_KEY . $this->APP_SECRET );
    }

    // check_permit -- проверяем, можем ли совершить вызов на телефонный номер
    private function check_permit( $source ) {
        $ip;
        if ( isset( $_SERVER['REMOTE_ADDR'] ) )
            $ip = $_SERVER['REMOTE_ADDR'];
        else
            $ip = '127.0.0.1';

        if ( $ip == '127.0.0.1' )   // для локальной отладки не смотрим на лимиты
            return array( 'result' => 'SUCCESS' );
        else {
            $count_ip = $this->get_count_ips( $ip );
            if ( $count_ip >= self::MAX_COUNT_IP )
                return array( 'result' => 'FAIL', 'error' => 'limit exceeded requests from the ip-address ' . $ip );

            $count_tel = $this->get_count_tels( $source );
            if ( $count_tel >= self::MAX_COUNT_TEL )
                return array( 'result' => 'FAIL', 'error' => 'exceeded the limit of calls to a phone number ' . $source );
        }

        return array( 'result' => 'SUCCESS' );
    }

    // make_call( $extension, $source, $destination, $caller_id, $call_type ) -- совершение вызова на указанные номера
    /********* params *********
        $extension   -- абонент PBX, для совершения вызова( по данному номеру система проверяет, можно ли сделать вызов )
        $source      -- первый абонент, которому идёт звонок(А-плечо). Для типа звонка 'callback' подходит только внешний номер
        $destination -- абонент, которму направляется вызов, если первый абонент(source) ответил на звонок(Б-плечо).
                        Для типа звонка 'callback' подходит только внешний номер.
        $caller_id   -- имя и номер, которые высветятся у абонента-А на телефоне. Формат: Имя <Номер>, например, TestCall <111111>
        $call_type   -- Тип звонка. Допустимые значения: 'callback', 'simple'. Необязательный параметр, по умолчанию, используется тип 'simple'
    ***************************/
    public function make_call($source, $destination, $phonenumber ) {

        $check_permit = $this->check_permit( $source );
        if ( $check_permit['result'] == 'FAIL' )
            return array( 'error' => $check_permit['error'] );

        $url = 'https://' . $this->API_SERVER . '/api/ver1.0/extension/'.EXTENSION_ID.'/callback/';

        $post_data_array = array(
            'dst_num' => $destination,
            'caller_id_name' => 'Web Call '.$phonenumber,
            'caller_id_number' => $phonenumber,
            'call_duration' => '3600',			
            'src_num' => array($source)
        );

        $post_data = json_encode( $post_data_array );

        $req = curl_init();
        curl_setopt( $req, CURLOPT_URL, $url );
        curl_setopt( $req, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $req, CURLOPT_FOLLOWLOCATION, true );
        curl_setopt( $req, CURLOPT_POST, true );
        curl_setopt( $req, CURLOPT_POSTFIELDS, $post_data );
        curl_setopt( $req, CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $req, CURLOPT_HTTPHEADER, array(
                'Content-type: application/json',
                "Authorization: Bearer " . $this->get_token()
            )
        );
        curl_setopt( $req, CURLOPT_USERAGENT, 'TelphinWebCall-RingМeScript' );
        $res = json_decode( curl_exec( $req ), true );

		if(!curl_errno($req)) {
			if (curl_getinfo($req, CURLINFO_HTTP_CODE) == 401) {
					$un_filename = $this->get_filename();
					unlink($un_filename);
                    curl_setopt( $req, CURLOPT_HTTPHEADER, array(
                            'Content-type: application/json',
                            "Authorization: Bearer " . $this->get_token()
                        )
                    );
                    $res = json_decode( curl_exec( $req ), true );
			}
		}
		
		if(isset($res) && $res["message"] == "Unauthorized" ){
			unset($res);
		}
		
        if ( ! $res )
            return array( 'error' => 'Error make call' );
        elseif ( isset( $res['error'] ) )
            return array( 'error' => $res['error'] );
        else
            return $res;
    }
}

/****************************************
 *
 * Дальше идут функции обработки
 * пользовательских данных и HTML-форма
 *
*****************************************/

define( 'MY_APP_KEY', 'eeb41c58b7954678971824d23133fbc6' );
define( 'MY_APP_SECRET', '9dac551ddb5945859c0d42aa12e47200' );
define( 'EXTERNAL_FIRST', true );
define( 'EXTENSION_ID', '154126' );
define( 'LOCAL_NUMBER', '74951081876' );
define( 'SERVER_NAME', "apiproxy.telphin.ru" );

// проверяем валидность телефонного номера
function check_phone( $phonenumber ) {
    $phone_str = "$phonenumber";
    $phone_len = strlen( $phone_str );

    // проверяем длину номера
    if ( $phone_str[0] == '7' ) {
        if ( $phone_len > 11 )
            return false;
    }
    elseif ( $phone_str[0] == '+' && $phone_str[1] == '7' ) {
        if ( $phone_len > 12 )
            return false;
    }
    else {
        if ( $phone_len < 9 || $phone_len > 15 )
            return false;
    }

    if ( ! preg_match( '/^\+?\d+$/', $phonenumber ) )
        return false;

    return true;
}

// нормализуем телефонный номер
function clear_phone( $phonenumber ) {
    $phonenumber = str_replace( "-", "", $phonenumber );
    $phonenumber = trim( $phonenumber );
    return $phonenumber;
}

// совершаем вызов
function call_proccessing( $phonenumber ) {
    $tel_call = new TelphinCall( MY_APP_KEY, MY_APP_SECRET, SERVER_NAME, EXTENSION_ID);
    $source;
    $destination;
    if ( EXTERNAL_FIRST ) {
        $source = $phonenumber;
        $destination = LOCAL_NUMBER;
    }
    else {
        $source = LOCAL_NUMBER;
        $destination = $phonenumber;
    }
	return $tel_call->make_call($source, $destination, $phonenumber);
}

function get_phone() {
    if ( isset( $_REQUEST['phonenumber'] ) ) {
        return $_REQUEST['phonenumber'];
    }
    else {
        return '';
    }
}

// обрабатываем ошибку
function get_error( $error_code ) {
    $error_data = array( 'res' => 'ERROR' );
    switch ( $error_code ) {
        case "invalid_phone":
            $error_data['msg'] = '<div style="font-size:12px; color:#c30404;padding-top:4px;">Некорректный номер</div>';
            break;
        default:
            $error_data['msg'] = '<div style="font-size:18px;color:#c30404;">Извините, сервис временно недоступен.</div>';
            break;
    }

    return $error_data;
}

// проверяем входные данные и вызываем функцию совершения вызова
function process_data() {
    $empty_data   = array();
    $success_data = array( 'res' => 'SUCCESS' );

    if ( $_SERVER['REQUEST_METHOD'] != 'POST' )
        return $empty_data;
    else { // POST
        $phonenumber = get_phone();
        if ( isset( $phonenumber ) ) {
            if ( ! check_phone( $phonenumber ) )
                return get_error( 'invalid_phone' );

            $phonenumber = clear_phone( $phonenumber );
            $call_res    = call_proccessing( $phonenumber );
            if ( isset( $call_res['error'] ) )
                return get_error( $call_res['error'] );
            else {
                $success_data['msg'] = '<div style="font-size:18px;color:#1e9516;">Ожидайте звонка на введенный номер.</div>';
                return $success_data;
            }
        }
        else {
            return $empty_data;
        }
    }
}