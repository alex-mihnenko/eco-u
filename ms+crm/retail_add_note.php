<?php
include("opencart_inc.php");

if($_POST['order_id']){
	$link="https://eco-u.retailcrm.ru/api/v5/orders/".$_POST['order_id']."?by=id&apiKey=".$retail_key;
	$res=crm_query($link);
	
	$customer_id = 0;	
	$customer_id = $res['order']['customer']['id'];	
	
	$msg_text = $_POST['msg_text'];
	
	$link = "https://eco-u.retailcrm.ru/api/v5/customers/notes/create";

//	$customer_arr = Array();
//	$customer_arr['id']=$customer_id;
//	$customer = (object) $customer_arr;
	
	$note_arr = Array();
	$note_arr['text'] = $msg_text;
	//$note_arr['customer']['id'] = $customer_id;
	//$note = $note_arr;
	
	$data = Array();
	$customer = (object) array("id" => $customer_id);	
	$note = (object) array("text" => $msg_text, "customer" => $customer);
	$data['apiKey'] = $retail_key;
	$data['note'] = json_encode($note);
	
	//$new_note=crm_query($link);
	
	$new_note=crm_query_send($link,$data);
	
	echo "<pre>";
	print_r($new_note);
	print_r($data);
	echo "</pre>";	
}
