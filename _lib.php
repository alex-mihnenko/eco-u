<?php
// Init
	error_reporting(E_ALL);
	ini_set('display_errors', '1');

	include("config.php");

	$db = new mysqli(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

	if ($db->connect_error) {
	    die("Connection failed to db: " . $db->connect_error);
	}

	$db->set_charset("utf8");

	$log = [];


	define('MS_AUTH', 'admin@mail195:b41fd841edc5');
	define('RCRM_KEY', 'AuNf4IgJFHTmZQu7PwTKuPNQch5v03to');

	// Telphin
		define( 'TELPHIN_MY_APP_KEY', 'eeb41c58b7954678971824d23133fbc6' );
		define( 'TELPHIN_MY_APP_SECRET', '9dac551ddb5945859c0d42aa12e47200' );
		define( 'TELPHIN_EXTERNAL_FIRST', true );
		define( 'TELPHIN_EXTENSION_ID', '154126' );
		define( 'TELPHIN_LOCAL_NUMBER', '74951081876' );
		define( 'TELPHIN_SERVER_NAME', "apiproxy.telphin.ru" );

		define( 'TELPHIN_MAX_COUNT_IP', 10000 );	// максимальное кол-во попыток звонка с одного ip
		define( 'TELPHIN_MAX_COUNT_TEL', 1000 );	// максимальное кол-во попыток звонка на один номер
		define( 'TELPHIN_BLOCK_PERIOD', 86400 );	// в течение какого временного периода действует ограничение на кол-во звонков. По умолчанию, 24 часа

		function get_token() {
	        $res = gen_token();
	        return $res['access_token'];
	    }

	    function gen_token() {
	        $url = 'https://' . TELPHIN_SERVER_NAME . '/oauth/token';

	        $post_data = array(
			'client_id'       => TELPHIN_MY_APP_KEY,
			'client_secret'   => TELPHIN_MY_APP_SECRET,
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

	    function telphinRequest($url, $qdata, $request) {

	        $data = json_encode( $qdata );
	        $token = get_token();

	        $req = curl_init();
	        curl_setopt( $req, CURLOPT_URL, $url );
	        curl_setopt( $req, CURLOPT_RETURNTRANSFER, true );
	        curl_setopt( $req, CURLOPT_FOLLOWLOCATION, true );
	        curl_setopt( $req, CURLOPT_POST, true );
	        curl_setopt( $req, CURLOPT_CUSTOMREQUEST, $request);  
	        curl_setopt( $req, CURLOPT_POSTFIELDS, $data );
	        curl_setopt( $req, CURLOPT_SSL_VERIFYPEER, false );
	        curl_setopt( $req, CURLOPT_HTTPHEADER, array(
	                'Content-type: application/json',
	                "Authorization: Bearer " . $token
	            )
	        );
	        curl_setopt( $req, CURLOPT_USERAGENT, 'TelphinWebCall-RingМeScript' );
	        $res = json_decode( curl_exec( $req ), true );

			if(!curl_errno($req)) {
				if (curl_getinfo($req, CURLINFO_HTTP_CODE) == 401) {
	                    curl_setopt( $req, CURLOPT_HTTPHEADER, array(
	                            'Content-type: application/json',
	                            "Authorization: Bearer " . $token
	                        )
	                    );
	                    $res = json_decode( curl_exec( $req ), true );
				}
			}
			
	        
			if(isset($res) && isset($res["message"]) && $res["message"] == "Unauthorized" ){
				unset($res);
			}
			
	        if ( ! $res )
	            return array( 'error' => 'Error make call' );
	        elseif ( isset( $res['error'] ) )
	            return array( 'error' => $res['error'] );
	        else
	            return $res;
	    }
	// ---


	function connectPostAPI($url, $qdata, $auth='', $cookie='') {
		// ---
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
		// ---
	}

	function connectGetAPI($url, $qdata, $auth='') {
		// ---
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
		// ---
	}


	function connectMSAPI($url, $qdata, $request, $auth='', $cookie='') {
		// ---
			$data = $qdata;

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,$url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $request);  
			curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			if( !empty($auth) ){
				curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
				curl_setopt($ch, CURLOPT_USERPWD, $auth);
			}
			curl_setopt($ch, CURLOPT_COOKIE, $cookie);
			$headers = ['Content-Type: application/json'];
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
		// ---
	}

	// Processors
		function addressCrmToOc($address, $office = false){
			// ---
				$obj = array();
				$text = '';

				// ---
					if( isset($address->region) ){
						$obj['region'] = $address->region;
						$text .= $address->region . ', '; // Область
					}
					if( isset($address->regionId) ){
						$obj['regionId'] = $address->regionId;
						//$text .= $address->regionId; // Идентификатор области в geohelper
					}
					
					if( isset($address->city) && isset($address->cityType) ){
						$obj['city'] = $address->city;
						$obj['cityType'] = $address->cityType;
						$text .= $address->cityType . ' ' . $address->city . ', ' ; // Город
					}
					else if( isset($address->city) && !isset($address->cityType) ){
						$obj['city'] = $address->city;
						$text .= $address->city . ', '; // Город
					}

					if( isset($address->cityId) ){
						$obj['cityId'] = $address->cityId;
						//$text .= $address->cityId . ''; // Идентификатор города в geohelper
					}
					if( isset($address->street) && isset($address->streetType) ){
						$obj['street'] = $address->street;
						$obj['streetType'] = $address->streetType;
						$text .= $address->streetType . ' ' . $address->street . ', '; // Улица
					}
					if( isset($address->streetId) ){
						$obj['streetId'] = $address->streetId;
						//$text .= '' . $address->streetId . ''; // Идентификатор улицы в geohelper
					}
					if( isset($address->building) ){
						$obj['building'] = $address->building;
						$text .= 'д. ' . $address->building . ', '; // Номер дома
					}
					if( isset($address->flat) ){
						$obj['flat'] = $address->flat;
						$text .= 'кв./офис ' . $address->flat . ', '; // Номер квартиры или офиса
					}
					if( isset($address->intercomCode) ){
						$obj['intercomCode'] = $address->intercomCode;
						$text .= 'код домофона ' . $address->intercomCode . ', '; // Код домофона
					}
					if( isset($address->floor) ){
						$obj['floor'] = $address->floor;
						$text .= 'эт. ' . $address->floor . ', '; // Этаж
					}
					if( isset($address->block) ){
						$obj['block'] = $address->block;
						$text .= 'под. ' . $address->block . ', '; // Подъезд
					}
					if( isset($address->house) ){
						$obj['house'] = $address->house;
						$text .= 'стр./корпус ' . $address->house . ', '; // Строение/корпус
					}
					if( isset($address->metro) ){
						$obj['metro'] = $address->metro;
						$text .= 'метро ' . $address->metro . ', '; // Метро
					}

					// Fix
					$text = mb_substr($text,0,mb_strlen($text)-2);


					if( isset($office) && $office !== false ){
						$obj['office'] = true;
					}
				// ---

				return array('obj' => $obj, 'text' => $text);
			// ---
		}

		function isCyrilicLetter($char) {
	        $alphabet = array(
	            'а','б','в',
	            'г','д','е',
	            'ё','ж','з',
	            'и','й','к',
	            'л','м','н',
	            'о','п','р',
	            'с','т','у',
	            'ф','х','ц',
	            'ч','ш','щ',
	            'ь','ы','ъ',
	            'э','ю','я',
	            'А','Б','В',
	            'Г','Д','Е',
	            'Ё','Ж','З',
	            'И','Й','К',
	            'Л','М','Н',
	            'О','П','Р',
	            'С','Т','У',
	            'Ф','Х','Ц',
	            'Ч','Ш','Щ',
	            'Ь','Ы','Ъ',
	            'Э','Ю','Я'
	       	);

	        if( in_array($char,$alphabet, true) ){ return true; }
	        else { return false; }
	    }
    // ---
// ---