<?php
class ControllerApiSms extends Controller {
	public function send() {
		// Init
        	$phone = preg_replace("/[^0-9,.]/", "", $this->request->post['phone']);
        	$message = $this->request->post['message'];

        	$response = new stdClass();
      	// ---

        // Send
        	$this->load->model('sms/confirmation');
            $this->model_sms_confirmation->sendSMS($phone, $message);
        // ---

        // Response
		$response->status = 'success';
		$response->result = true;


		echo json_encode($response);
		exit;
	}
}
