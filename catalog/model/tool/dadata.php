<?php
class ModelToolDadata extends Model {
    const apiKey = "47b01516e1cd602eeb8cb6bf084be7583450dbb6";
    const secretKey = "8893102fc1ee73d209ce76902414050261948256";

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
             'Authorization: Token ' . self::apiKey,
             'X-Secret: ' . self::secretKey,
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

// $dadata = new Dadata(self::apiKey, self::secretKey);


// *** Стандартизация одного значения конкретного типа ***
// $result = $dadata->clean('name', 'Сергей Владимерович Иванов');
// print_r($result);

// *** Стандартизация нескольких значений одного типа ***
// *** Допускается не более 1 ФИО, 3 адресов, 3 телефонов, 3 email ***
// $structure = array("PHONE", "PHONE", "PHONE");
// $record = array("8 916 823 3454", "495 663-12-53", "457 07 25");
// $result = $dadata->cleanRecord($structure, $record);
// print_r($result);