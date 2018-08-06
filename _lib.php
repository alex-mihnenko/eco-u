<?php
// Init
	error_reporting(E_ALL);
	ini_set('display_errors', '1');

	include("../config.php");

	$db = new mysqli(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

	if ($db->connect_error) {
	    die("Connection failed to db: " . $db->connect_error);
	}

	$db->set_charset("utf8");

	$log = [];


	define('MS_AUTH', 'admin@mail195:b41fd841edc5');
	define('RCRM_KEY', 'AuNf4IgJFHTmZQu7PwTKuPNQch5v03to');


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