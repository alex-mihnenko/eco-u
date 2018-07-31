<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

include("opencart_inc.php");
$log = [];

$link="https://eco-u.retailcrm.ru/api/v5/reference/statuses?apiKey=".$retail_key;
$res=crm_query($link);


foreach($res['statuses'] as $k=>$v){
 $STAT[$k]=$v['name'];
}

if($_GET['type']){
// ---

	//Получаем из retailCRM данные по заказу
	$link="https://eco-u.retailcrm.ru/api/v5/orders/{$_GET['id']}?by=id&apiKey=".$retail_key;
	$krt=$res=crm_query($link);

	//Уточняем id статуса, проверив соответствие статусов по названию
	$resj=mysql_query("select order_status_id from oc_order_status where  name='".$STAT[$res['order']['status']]."'");
	list($order_status_id)=mysql_fetch_row($resj);

	//Берём данные по заказу из МС
	$num=$res['order']['externalId'];
	$VMS=null;
	$link='https://online.moysklad.ru/api/remap/1.1/entity/customerorder?search='.$num;
	$resx=ms_query($link);

	foreach($resx['rows'] as $kms=>$vms){
		if($vms['name']=="IM".$num) {
			//Заказы в retailCRM с префиксом и потому название отличается от id заказа
			$VMS=$vms;
		}
	}


	// Edit order products
		$ndel=array();
		
		foreach($res['order']['items'] as $kg=>$vg){
			// ---
				if ( $qOrderProduct = mysql_query("SELECT `order_product_id` FROM `oc_order_product` WHERE `order_id`=".$num." AND `product_id`='{$vg['offer']['externalId']}';") ) $nOrderProduct = mysql_num_rows($qOrderProduct);
				else $nOrderProduct = 0;

				$totalg=$vg['initialPrice']*$vg['quantity'];

				if( $nOrderProduct==0 ){
					// ---
						// Get product
							if ( $qProduct = mysql_query("SELECT * FROM `oc_product` WHERE `product_id`='{$vg['offer']['externalId']}';") ) $nProduct = mysql_num_rows($qProduct);
							else $nProduct = 0;
						// ---

						if( $nProduct>0 ){
							// ---
								$rowProduct = mysql_fetch_assoc($qOrderProduct);

								$qInsert = mysql_query("
									INSERT INTO `oc_order_product` SET 
									`order_id`='$num',
									`product_id`='{$vg['offer']['externalId']}',
									`name`='{$vg['offer']['name']}',
									`model`='".$rowProduct['model']."',
									`quantity`='{$vg['quantity']}',
									`amount`='1',
									`variant`='1',
									`price`='{$vg['initialPrice']}',
									`total` ='$totalg',
									`tax`='0',
									`reward`='0'
								");

								$opid=mysql_insert_id();

								$log[] = "New product in order: [".$opid."] SQL Error Insert: [".mysql_error()."]";
							// ---
						}

					// ---
				}
				else {
					// ---
						$rowProduct = mysql_fetch_assoc($qOrderProduct);
						$opid=$rowProduct['order_product_id'];

						$qUpdate = mysql_query("
							UPDATE `oc_order_product` SET 
							quantity='{$vg['quantity']}',
							price='{$vg['initialPrice']}',
							total ='$totalg' 
							WHERE `order_product_id`='$opid';
						");

						$log[] = "Update product in order: [".$opid."] SQL Error Update: [".mysql_error()."]";
					// ---
				}
			
				$ndel[]=$opid;
			// ---
		}
	// ---

	// Clear products
		if ( $qOrderProduct = mysql_query("SELECT `order_product_id` FROM `oc_order_product` WHERE `order_id`=".$num.";") ) $nOrderProduct = mysql_num_rows($qOrderProduct);
		else $nOrderProduct = 0;

		if( $nOrderProduct>0 ){
			// ---
				while ($row = mysql_fetch_assoc($qOrderProduct)) {
				    if(!in_array($row['order_product_id'],$ndel)) {
				    	$qDelete = mysql_query("DELETE FROM `oc_order_product` WHERE `order_product_id`='".$row['order_product_id']."';");
				    }
				}
			// ---
		}
	// ---

	// Update order and totals
		$qUpdate = mysql_query("UPDATE `oc_order` SET `total`='{$res['order']['totalSumm']}', `order_status_id`='$order_status_id', `rcrm_status`='edited' WHERE `order_id`='{$res['order']['externalId']}'");
		$qUpdate = mysql_query("UPDATE `oc_order_total` SET `value`='{$res['order']['totalSumm']}'  WHERE `order_id`='{$res['order']['externalId']}' AND code='total'");
		$qUpdate = mysql_query("UPDATE `oc_order_total` SET `value`='{$res['order']['summ']}'  WHERE `order_id`='{$res['order']['externalId']}' AND code='sub_total'");
		$qUpdate = mysql_query("UPDATE `oc_order_total` SET `value`='{$res['order']['delivery']['cost']}'  WHERE `order_id`='{$res['order']['externalId']}' AND code='shipping'");
	// ---

	//Ищем ID заказа в МоёмCкладе
	$resx=mysql_query("select ms_id,demand_id from ms_leads where retailcrm_id='$num'");
	list($ms_lead_id,$ms_demand_id)=mysql_fetch_row($resx);
	
	//Если статус заказа собран, то обновляем дату сборки в retailCRM и обновляем данные по заказу в МС
	if($res['order']['status']=='assembling-complete'){
			//if(!$ms_lead_id){
					
				$order=null;
				$shipmentDate=date("Y-m-d",time());
				$order['shipmentDate']=$shipmentDate;
				$senddata['order']=json_encode($order);
				$link='https://eco-u.retailcrm.ru/api/v5/orders/'.$num.'/edit?apiKey='.$retail_key;
				$json=crm_query_send($link,$senddata);							

				$link='https://online.moysklad.ru/api/remap/1.1/entity/customerorder/?filter=name='.$res['order']['number'];
				$json = ms_query($link);
	            $ms_lead_id_meta=$json['rows'][0]['meta']['href'];

	            $ms_lead_id=$VMS['id'];
				$ms_data = $json = NULL;
				$ms_data['customerOrder']["meta"] = array(
					"href" => $ms_lead_id_meta,
					"type" => 'customerorder',
					"mediaType" => 'application/json'
				);


				// Create demand task
					$qInsertDemand = mysql_query("
						INSERT INTO ms_demand SET 
						ms_demand_id='',
						ms_customer_order_id='".$ms_lead_id."',
						order_id='".$num."',
						customer_order_data='".json_encode($ms_data)."',
						date_added='".time()."',
						deleted=0,
						completed=0
					;");

					file_put_contents('log-ms-demand.txt', $num." : mysql error : ".mysql_error()."\n\n", FILE_APPEND | LOCK_EX);
				// ---

				if("IM".$krt['order']['externalId']==$krt['order']['number']) {
					$link3='https://online.moysklad.ru/api/remap/1.1/entity/customerorder/?filter=name=IM'.$num;
				}
				else {
					$link3='https://online.moysklad.ru/api/remap/1.1/entity/customerorder/?filter=name='.$krt['order']['number'];
				}

				$json3 = ms_query($link3);

				$ms_lead_id3=$json3['rows'][0]['id'];
				$POS3=ms_query($json3['rows'][0]['positions']['meta']['href']);

				foreach($POS3['rows'] as $kp3=>$vp3){
					$vp3['reserve']=0;
					$ms_data3['positions'][]=$vp3;
				}
				
				$link3='https://online.moysklad.ru/api/remap/1.1/entity/customerorder/'.$ms_lead_id3;
				$json3 = ms_query_send($link3, $ms_data3, 'PUT');


				//$link2='https://online.moysklad.ru/api/remap/1.1/entity/customerorder?search='.$num;
				//$json2 = ms_query($link2);

			
				//$resx2=mysql_query("select count(*) as kol from ms_test where numb='$num' ");
				//$mass2=mysql_fetch_row($resx2);

				/*
				if($mass2[0]==0) {
					$json=null;
					$link = "https://online.moysklad.ru/api/remap/1.1/entity/demand/new";
					$json = ms_query_send($link, $ms_data, 'PUT');
					mysql_query("insert into ms_test set numb='$num'");

					file_put_contents('log-ms-demand.txt', $num." : entity/demand/new : ".json_encode($json)."\n", FILE_APPEND | LOCK_EX);
				
					if("IM".$krt['order']['externalId']==$krt['order']['number']) {
						$link3='https://online.moysklad.ru/api/remap/1.1/entity/customerorder/?filter=name=IM'.$num;
					}
					else {
						$link3='https://online.moysklad.ru/api/remap/1.1/entity/customerorder/?filter=name='.$krt['order']['number'];
					}

					$json3 = ms_query($link3);

					$ms_lead_id3=$json3['rows'][0]['id'];
					$POS3=ms_query($json3['rows'][0]['positions']['meta']['href']);

					foreach($POS3['rows'] as $kp3=>$vp3){
						$vp3['reserve']=0;
						$ms_data3['positions'][]=$vp3;
					}
					
					$link3='https://online.moysklad.ru/api/remap/1.1/entity/customerorder/'.$ms_lead_id3;
					$json3 = ms_query_send($link3, $ms_data3, 'PUT');
				
			

					$link = "https://online.moysklad.ru/api/remap/1.1/entity/demand";
							
					$json = ms_query_send($link, $json, 'POST');

					file_put_contents('log-ms-demand.txt', $num." : entity/demand : ".json_encode($json)."\n\n", FILE_APPEND | LOCK_EX);
					
					if( isset($json['id']) ) {
						$qInsertDemand = mysql_query("INSERT INTO ms_leads SET shop_id=0, ms_id='".$ms_lead_id."', demand_id='".$json['id']."', retailcrm_id='".$num."';");
					}

					file_put_contents('log-ms-demand.txt', $num." : mysql error : ".mysql_error()."\n\n", FILE_APPEND | LOCK_EX);
				} */
			//}
		// ---
	}
	else if($res['order']['status']=='cancel-other'){
		// Если статус заказа отменён, то изменяем данные по заказу в МС
			$fp = fopen("retail_upd.log", 'a+');
			
			if(!$ms_lead_id) $ms_lead_id=$VMS['id'];
		
			fwrite($fp, "ms_lead_id=".$ms_lead_id."\n\n"."VMS=".json_encode($VMS, JSON_PRETTY_PRINT)."\n\n");
			
			$ms_data=null;
			$POS=ms_query($VMS['positions']['meta']['href']);

			fwrite($fp, "POS=".json_encode($POS, JSON_PRETTY_PRINT)."\n\n");

			foreach($POS['rows'] as $kp=>$vp){
				$vp['reserve']=0;
				$vp['shipped']=0;
				$ms_data['positions'][]=$vp;	
			}
			
			$link='https://online.moysklad.ru/api/remap/1.1/entity/customerorder/'.$ms_lead_id;
			$json = ms_query_send($link, $ms_data, 'PUT');

			
			fwrite($fp, "RESERVE 0:\n\n MS_DATA".json_encode($ms_data, JSON_PRETTY_PRINT)."\n\n JSON".json_encode($json, JSON_PRETTY_PRINT)."\n".$link);
			fclose($fp);

			if($ms_demand_id){					
				$link = "https://online.moysklad.ru/api/remap/1.1/entity/demand/$ms_demand_id";
				$json = ms_query_send($link, '', 'DELETE');
				mysql_query("delete from ms_leads where demand_id='$ms_demand_id'");
			}
		// ---
	}
	else if($res['order']['status']=='complete'){
		// ---
			if(!$ms_lead_id) $ms_lead_id=$VMS['id'];
	
			$ms_data=null;
			$POS=ms_query($VMS['positions']['meta']['href']);

			foreach($POS['rows'] as $kp=>$vp){
				$vp['reserve']=0;
				//$vp['shipped']=0;
				$ms_data['positions'][]=$vp;	
			}
			
			$link='https://online.moysklad.ru/api/remap/1.1/entity/customerorder/'.$ms_lead_id;
			$json = ms_query_send($link, $ms_data, 'PUT');
		// ---
	}


	// Show log
	print_r($log);
// ---
}
