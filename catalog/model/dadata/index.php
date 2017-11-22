<?php
class ModelDadataIndex extends Model {
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
             'Authorization: Token ' . "a4ad0e938bf22c2ffbf205a4935ef651fc92ed52",
             'X-Secret: ' . "b92170b29956c66785a193c112c5a5131756e96a"
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

?>