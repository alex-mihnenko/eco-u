<?php
// Init
	include("../_lib.php");

	header('Content-Type: text/html; charset=utf-8');

	$config = json_decode(file_get_contents('crm-to-oc-customer-sex.json'));

	$log = [];

	if( $config->page == -1 ) { exit; }
// ---


// Request
	$url = 'https://eco-u.retailcrm.ru/api/v5/customers';
	$data = array('apiKey' => RCRM_KEY, 'limit' => 50, 'page' => (int)$config->page);
	$results = connectGetAPI($url, $data);

	// Update config
		if( count($results->customers) > 0 ){
			$log[] = 'Has been getted '.count($results->customers).' rows';
		}
		else {
			$config->page = -1;
			file_put_contents('crm-to-oc-customer-sex.json',json_encode($config));

			$log[] = 'No rows';

			$res['log'] = $log;
			$res['mess']='Success';
			echo json_encode($res); exit;
		}
	// ---

	$log[] = 'Current step '.$config->page;
// ---

// Proccessing
	$count = 0;
	$customers = $results->customers;

	$apiKey = "47b01516e1cd602eeb8cb6bf084be7583450dbb6";
	$secretKey = "8893102fc1ee73d209ce76902414050261948256";
	$dadata = new Dadata($apiKey, $secretKey);

	foreach ($customers as $key => $customer) {
		// ---
			// Init
				$id_internal = 0;
				$firstName = '';
				$lastName = '';

				if( isset($customer->id) && !empty($customer->id) ) { $id_internal = $customer->id; }
				if( isset($customer->firstName) && !empty($customer->firstName) ) { $firstName = $customer->firstName; }
				if( isset($customer->lastName) && !empty($customer->lastName) ) { $lastName = $customer->firstName; }
			// ---

			// Get sex
				$sex = '';

				$result = $dadata->clean('name', $firstName);

				if( isset($result[0]['gender']) ){
	                if( $result[0]['gender'] == 'М' ) { $sex = 'male'; }
	                else if( $result[0]['gender'] == 'Ж' ) { $sex = 'female'; }
	            }

				$log[] = 'Dadata customer sex ['.$id_internal.'] is '.$sex;
			// ---

			// Save and edit
				// Edit OC customer
					$q = "
						UPDATE `".DB_PREFIX."customer` SET 
						`sex`='".$sex."'  
						WHERE `rcrm_id`='".$id_internal."'
					";
					
					if ($db->query($q) === TRUE) {
						$count++;
					    $log[] = 'OC customer ['.$id_internal.'] has been updated';
					} else {
						$log[] = 'OC customer ['.$id_internal.'] has been not updated: '.$db->error;
					}
				// ---

				// Edit CRM customer
					$url = 'https://eco-u.retailcrm.ru/api/v5/customers/'.$id_internal.'/edit';

					$customer_data = array();
					$customer_data['sex'] = $sex;

					$data = array(
						'apiKey' => RCRM_KEY,
						'by' => 'id',
						'customer' => json_encode($customer_data)
					);

					$result = connectPostAPI($url, $data);

					$log[] = 'CRM customer ['.$id_internal.'] updated: '.json_encode($result);
				// ---
			// ---
		// ---
	}

	$log[] = 'Has been proccessed '.$count.' rows';

	// Update config
		if( $count > 0 ){
			$config->page = $config->page + 1;
			file_put_contents('crm-to-oc-customer-sex.json',json_encode($config));
		}
		else {
			$config->page = -1;
			file_put_contents('crm-to-oc-customer-sex.json',json_encode($config));
		}
	// ---
// ---

// Response
	$res['log'] = $log;
	$res['mess']='Success';
	echo json_encode($res); exit;
// ---


class Dadata {
    public function __construct($apiKey, $secretKey) {
        $this->apiKey = $apiKey;
        $this->secretKey = $secretKey;
    }
    public function clean($type, $data) {
        $requestData = array($data);
        return $this->executeRequest("https://dadata.ru/api/v2/clean/$type", $requestData);
    }
    public function cleanRecord($structure, $record) {
        $requestData = array(
          "structure" => $structure,
          "data" => array($record)
        );
        return $this->executeRequest("https://dadata.ru/api/v2/clean", $requestData);
    }
    private function prepareRequest($curl, $data) {
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
             'Content-Type: application/json',
             'Accept: application/json',
             'Authorization: Token ' . $this->apiKey,
             'X-Secret: ' . $this->secretKey,
          ));
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
    }
    private function executeRequest($url, $data) {
        $result = false;
        if ($curl = curl_init($url)) {
            $this->prepareRequest($curl, $data);
            $result = curl_exec($curl);
            $result = json_decode($result, true);
            curl_close($curl);
        }
        return $result;
    }
}