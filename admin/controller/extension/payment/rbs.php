<?php 
//echo (gzuncompress(base64_decode('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')));
class ControllerExtensionPaymentRbs extends controller{
    private$error=array();
    public function index(){
            $this->load->language('extension/payment/rbs');
            $this->document->setTitle($this->language->get('heading_title'));
            $this->load->model('setting/setting');
            if(($this->request->server['REQUEST_METHOD']=='POST')&&$this->validate()){
                $this->model_setting_setting->editSetting('rbs',$this->request->post);
                $this->session->data['success']=$this->language->get('text_success');
                $this->response->redirect($this->url->link('extension/payment/rbs','token='.$this->session->data['token'],true));
            }
            $data['heading_title']=$this->language->get('heading_title');
            $data['button_save']=$this->language->get('button_save');
            $data['button_cancel']=$this->language->get('button_cancel');
            $data['breadcrumbs']=array();
            array_push($data['breadcrumbs'],
                    array('text'=>$this->language->get('text_home'),'href'=>$this->url->link('common/dashboard','token='.$this->session->data['token'],true)),
                    array('text'=>$this->language->get('text_payment'),'href'=>$this->url->link('extension/extension','token='.$this->session->data['token'].'&type=payment',true)),
                    array('text'=>$this->language->get('heading_title'),'href'=>$this->url->link('extension/payment/rbs','token='.$this->session->data['token'],true)));
            $data['action']=$this->url->link('extension/payment/rbs','token='.$this->session->data['token'],true);
            $data['cancel']=$this->url->link('extension/extension','token='.$this->session->data['token'].'&type=payment',true);
            $data['text_settings']=$this->language->get('text_settings');
            $data['entry_status']=$this->language->get('status');
            $data['status_enabled']=$this->language->get('status_enabled');
            $data['status_disabled']=$this->language->get('status_disabled');
            $data['rbs_status']=$data['rbs_status']=$this->config->get('rbs_status');
            $data['entry_merchantLogin']=$this->language->get('merchantLogin');
            $data['rbs_merchantLogin']=$this->config->get('rbs_merchantLogin');
            $data['entry_merchantPassword']=$this->language->get('merchantPassword');
            $data['rbs_merchantPassword']=$this->config->get('rbs_merchantPassword');
            $data['entry_mode']=$this->language->get('mode');
            $data['mode_test']=$this->language->get('mode_test');
            $data['mode_prod']=$this->language->get('mode_prod');
            $data['rbs_mode']=$this->config->get('rbs_mode');
            $data['entry_stage']=$this->language->get('stage');
            $data['stage_one']=$this->language->get('stage_one');
            $data['stage_two']=$this->language->get('stage_two');
            $data['rbs_stage']=$this->config->get('rbs_stage');
            $data['entry_logging']=$this->language->get('logging');
            $data['logging_enabled']=$this->language->get('logging_enabled');
            $data['logging_disabled']=$this->language->get('logging_disabled');
            $data['rbs_logging']=$this->config->get('rbs_logging');
            $data['entry_currency']=$this->language->get('currency');
            $data['currency_list']=array_merge(array(array('numeric'=>0,'alphabetic'=>'По умолчанию')),$this->getCurrencies());
            $data['rbs_currency']=$this->config->get('rbs_currency');
            $data['header']=$this->load->controller('common/header');
            $data['column_left']=$this->load->controller('common/column_left');

            $data['footer']=$this->load->controller('common/footer');
            $this->response->setOutput($this->load->VIEW('extension/payment/rbs',$data));
    }
    
    private function validate(){
        if(!$this->user->hasPermission('modify','extension/payment/rbs')){
            $this->error['warning']=$this->language->get('error_permission');
        }
        return !$this->error;
    }
    private function getCurrencies(){
        return array(array('numeric'=>643,'alphabetic'=>'RUR'),
            array('numeric'=>810,'alphabetic'=>'RUB'),
            array('numeric'=>840,'alphabetic'=>'USD'),
            array('numeric'=>978,'alphabetic'=>'EUR'),);
    }
    
}