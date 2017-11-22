<?php
class ModelSmsConfirmation extends Model {
	public function addCode($code, $timestamp) {
		if(!empty($code) && !empty($timestamp))
                {
                    $mysql_timestamp = date('Y-m-d H:i:s', $timestamp);
                    $query = "INSERT INTO `" . DB_PREFIX . "sms_confirmation`(`code`, `timeout`) VALUES('{$code}', '{$mysql_timestamp}')";
                    $this->db->query($query);   
                }
	}
        
        public function sendSMS($phoneFormat, $message) {
            $api = '4f383bd0d416c';
            $arParams = Array(
                'project' => 'ecou',
                'recipients' => $phoneFormat,
                'sender' => 'eco-u.ru',
                'test' => 0,
                'message' => $message
            );
            natsort($arParams);
            $sign = '';
            foreach($arParams as $param)
            {
                $sign .= $param.';';
            }
            $sign .= $api;
            $arParams['sign'] = md5(sha1($sign));
            
            // Отправка sms
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,"https://mainsms.ru/api/mainsms/message/send");
            curl_setopt($ch, CURLOPT_POST, 1);
            $query = http_build_query($arParams);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            
            $server_output = curl_exec ($ch);

            curl_close($ch);
            return $server_output;
        }
        
        public function clearOldCodes() {
            $mysql_timestamp = date('Y-m-d H:i:s', time());
            $query = "DELETE FROM `" . DB_PREFIX . "sms_confirmation` WHERE `timeout` < '{$mysql_timestamp}'";
            $this->db->query($query);
        }
        public function validateCode($code) {
            $query = "SELECT `sms_id` FROM `" . DB_PREFIX ."sms_confirmation` WHERE `code` = '" . $this->db->escape($code) ."'";
            $result = $this->db->query($query);
            if($result->num_rows > 0) {
                $row = $result->row;
                return $row;
            }
            else
            {
                return 0;
            }
        }
}
?>