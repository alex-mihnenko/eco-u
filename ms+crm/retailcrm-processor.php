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

	define('AUTH_DATA', 'admin@mail195:b41fd841edc5');
	define('RETAILCRM_KEY', 'AuNf4IgJFHTmZQu7PwTKuPNQch5v03to');


	$config = json_decode(file_get_contents('retailcrm-processor-config.json'));


	$log = [];
// ---

//79165890241
// Set query
	$url = 'https://eco-u.retailcrm.ru/api/v5/customers';
	
	$qdata = array(
		'apiKey' => RETAILCRM_KEY,
		'limit' => 100,
		'page' => (int)$config->page,
		'filter' => array()
	);

	if( $config->page == 0 ) { exit; }
// ---

// Request
	$results = connectGetAPI($url,$qdata);
	$log['Customers'] = count($results->customers);

	if( count($results->customers) > 0 ){
		$config->page = $config->page + 1;
	}
	else { $config->page = 0; }
	
	file_put_contents('retailcrm-processor-config.json',json_encode($config));
// ---

// Processing
	foreach ($results->customers as $key => $customer) {
		// ---
			// Init
				$c_id = 0;
				$c_externalId = '';
				$c_firstName = '';
				$c_email = '';
				$c_createdAt = '';

				if( !empty($customer->id) ) $c_id = $customer->id;
				if( !empty($customer->externalId) ) $c_externalId = $customer->externalId;
				if( !empty($customer->firstName) ) $c_firstName = $customer->firstName;
				if( !empty($customer->email) ) $c_email = $customer->email;
				if( !empty($customer->createdAt) ) $c_createdAt = $customer->createdAt;
			// ---

			// Check db customer
	            $q = "SELECT * FROM `retailCRM_customers` WHERE `id_external`='".$c_externalId."' AND `email`='".$c_email."';";
	            
	            $dbcustomer = $db->query($q);

	            $processingFlag = false;

	            if ( $dbcustomer->num_rows == 0 ) { $processingFlag = true; }
			// ---

			if( $processingFlag == true ){
				// Get customer
					$url = 'https://eco-u.retailcrm.ru/api/v5/customers';
		
					$qdata = array(
						'apiKey' => RETAILCRM_KEY,
						'limit' => 20,
						'page' => 1,
						'filter' => array(
							'email'=>$c_email
						)
					);

					$dublicates = connectGetAPI($url,$qdata);

					if( count($dublicates->customers) < 2 ) { $dbdublicates = 0; }
					else { $dbdublicates = count($dublicates->customers); }
				// ---

				// Save customer
					$q = "
						INSERT INTO `retailCRM_customers`
						(`id_internal`, `id_external`, `firstname`, `email`, `dublicates`, `created`)
						values
						(
							".$c_id.",
							'".$c_externalId."',
							'".$c_firstName."',
							'".$c_email."',
							".$dbdublicates.",
							'".$c_createdAt."'
						);
					";
					
					if ($db->query($q) === TRUE) {
					    $log[$c_id] = "Success insert";
					} else {
						$log[$c_id] = "Error insert: ".$db->error;
					}
				// ---
			}		
		// ---
	}
// ---


$res['log'] = json_encode($log);
$res['mess']='OK';
echo json_encode($res); exit;




function connectPostAPI($url, $qdata, $cookie='') {

	$data = http_build_query($qdata);

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");  
	curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
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

function connectGetAPI($url, $qdata) {

	$data = http_build_query($qdata);

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
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