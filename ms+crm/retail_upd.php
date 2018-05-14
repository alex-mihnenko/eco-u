<?php

include("opencart_inc.php");



$link="https://eco-u.retailcrm.ru/api/v5/reference/statuses?apiKey=".$retail_key;
$res=crm_query($link);


foreach($res['statuses'] as $k=>$v){
 $STAT[$k]=$v['name'];
}

if($_GET['type']){

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
		 if($vms['name']=="IM".$num) { //Заказы в retailCRM с префиксом и потому название отличается от id заказа
				 $VMS=$vms;
	}}
	
	/*
	$fp = fopen("retail_upd.log", 'a+');
	fwrite($fp, "num=".$num."\n\n\ resx".json_encode($resx, JSON_PRETTY_PRINT)."\n\n VMS".json_encode($VMS, JSON_PRETTY_PRINT));
	fclose($fp);
	*/


	//Проверяем если что-то в заказе добавилось добавляем в order_product если удалилось, то удаляем, если изменилось, то изменяем
	$ndel=array();			
	foreach($res['order']['items'] as $kg=>$vg){
	        $resx=mysql_query("select order_product_id from oc_order_product where order_id='$num' and product_id='{$vg['offer']['externalId']}'");
		list($opid)=mysql_fetch_row($resx);

		$totalg=$vg['initialPrice']*$vg['quantity'];
		if(!$opid) {
			mysql_query("insert into   oc_order_product set order_id='$num', product_id='{$vg['offer']['externalId']}',name='{$vg['offer']['name']}',
				quantity='{$vg['quantity']}',price='{$vg['initialPrice']}',total ='$totalg'");
			$opid=mysql_insert_id();
		}
		else mysql_query("update   oc_order_product set quantity='{$vg['quantity']}',price='{$vg['initialPrice']}',total ='$totalg' where order_product_id='$opid'");
		$ndel[]=$opid;
	}    

        $resx=mysql_query("select order_product_id from oc_order_product where order_id='$num' ");
	while(list($opid)=mysql_fetch_row($resx)){
		 if(!in_array($opid,$ndel)) mysql_query("delete from  oc_order_product where  order_product_id='$opid'");
	}


	//Обновляем данные по заказу в oc_order и в oc_order_total
	mysql_query("update  oc_order set total='{$res['order']['totalSumm']}', order_status_id='$order_status_id' where  order_id='{$res['order']['externalId']}'");
	mysql_query("update  oc_order_total set value='{$res['order']['totalSumm']}'  where  order_id='{$res['order']['externalId']}' and code='total'");
	mysql_query("update  oc_order_total set value='{$res['order']['summ']}'  where  order_id='{$res['order']['externalId']}' and code='sub_total'");
	mysql_query("update  oc_order_total set value='{$res['order']['cost']}'  where  order_id='{$res['order']['externalId']}' and code='shipping'");

	//Пишем логи по апдейту
	//$fp = fopen("retail_upd.log", 'a+');
    //    fwrite($fp, "\n$link \n status - ".$res['order']['status']."\nres 0:".json_encode($res, JSON_PRETTY_PRINT)."\n\n ORDER".json_encode($order, JSON_PRETTY_PRINT));
	//fclose($fp);

	//Ищем ID заказа в МоёмCкладе
	$resx=mysql_query("select ms_id,demand_id from ms_leads where retailcrm_id='$num'");
	list($ms_lead_id,$ms_demand_id)=mysql_fetch_row($resx);

	$fp = fopen("retail_upd.log", 'a+');
	fwrite($fp, "\n Отработал. Статус заказа: ".$res['order']['status']." \n");
	fclose($fp);
	//sleep(rand(3,7));
		//Если статус заказа собран, то обновляем дату сборки в retailCRM и обновляем данные по заказу в МС
		if($res['order']['status']=='delivering'){	
			  		
			if(!$ms_lead_id){
					
				$order=null;
				$shipmentDate=date("Y-m-d",time());
				$order['shipmentDate']=$shipmentDate;
				$senddata['order']=json_encode($order);
				$link='https://eco-u.retailcrm.ru/api/v5/orders/'.$num.'/edit?apiKey='.$retail_key;
				$json=crm_query_send($link,$senddata);							

				$link='https://online.moysklad.ru/api/remap/1.1/entity/customerorder/?filter=name='.$res['order']['number'];
				$json = ms_query($link);
                $ms_lead_id_meta=$json['rows'][0]['meta']['href'];

//				$fp = fopen("retail_upd.log", 'a+');
//			    fwrite($fp, "\n retailzzz $link -ms log: ".json_encode($json)."\n");
//				fclose($fp);



                $ms_lead_id=$VMS['id'];
				$ms_data = $json = NULL;
				$ms_data['customerOrder']["meta"] = array(
									"href" => $ms_lead_id_meta,//'https://online.moysklad.ru/api/remap/1.1/entity/customerorder/'.$ms_lead_id,
									"type" => 'customerorder',
									"mediaType" => 'application/json'
				);


				$fp = fopen("retail_upd.log", 'a+');
			        fwrite($fp, "\n retail -ms log: ".json_encode($ms_data)."\n");
				fclose($fp);
				//Задержка перед созданием
				//sleep(rand(3,7));
					
				$link2='https://online.moysklad.ru/api/remap/1.1/entity/customerorder?search='.$num;
				$json2 = ms_query($link2);
					$fp = fopen("retail_upd.log", 'a+');
			       fwrite($fp, "\n timeffff: ".date("d.m.Y H:i:s").json_encode($json2)."count=".count($json2['rows'][0]['demands']).'-'.$num."\n");
				fclose($fp);
				
				
				$resx2=mysql_query("select count(*) as kol from ms_test where numb='$num' ");
$mass2=mysql_fetch_row($resx2);

				if($mass2[0]==0)
				{
				//Проверям есть ли уже отгрузку если есть, то не создаем
				//if(count($json2['rows'][0]['demands'])==0)
				//{
				
				
				//sleep(rand(3,7));
				$json=null;
				$link = "https://online.moysklad.ru/api/remap/1.1/entity/demand/new";
				$json = ms_query_send($link, $ms_data, 'PUT');
				//sleep(rand(1,2));
				mysql_query("insert into   ms_test set numb='$num'");
				/*Убираем резерв*/
				
				if("IM".$krt['order']['externalId']==$krt['order']['number'])
				{
				$link3='https://online.moysklad.ru/api/remap/1.1/entity/customerorder/?filter=name=IM'.$num;
				}
				else 
				{
					$link3='https://online.moysklad.ru/api/remap/1.1/entity/customerorder/?filter=name='.$krt['order']['number'];
				}
$json3 = ms_query($link3);

$ms_lead_id3=$json3['rows'][0]['id'];
$POS3=ms_query($json3['rows'][0]['positions']['meta']['href']);
//print_r($POS);
foreach($POS3['rows'] as $kp3=>$vp3){
					$vp3['reserve']=0;
					//$vp['shipped']=0;
					$ms_data3['positions'][]=$vp3;	
				}
				
				$link3='https://online.moysklad.ru/api/remap/1.1/entity/customerorder/'.$ms_lead_id3;
				$json3 = ms_query_send($link3, $ms_data3, 'PUT');
				
				
				
				/**/
				
				
				
				$fp = fopen("retail_upd.log", 'a+');
			       fwrite($fp, "\n time: ".date("d.m.Y H:i:s").json_encode($json2)."count=".count($json2['rows'][0]['demands']).'-'.$num."\n");
				fclose($fp);
				
				
				$fp = fopen("retail_upd.log", 'a+');
			       fwrite($fp, "\n query demand: ".json_encode($json)."\n");
				fclose($fp);

//				$json['attributes'][]=array('id'=>'a4cad357-cd07-11e6-7a69-971100100909','value'=>$DOST[$sposob_dost]);
//				if(!isset($json['store'])) $json['store']['meta']=array('href'=>'https://online.moysklad.ru/api/remap/1.1/entity/store/8cef7721-9e45-418a-a146-955b2bf50c11',
//					'type'=>'store');
				$link = "https://online.moysklad.ru/api/remap/1.1/entity/demand";
						
				$json = ms_query_send($link, $json, 'POST');
				
				//$fp = fopen("retail_upd.log", 'a+');
			    //    fwrite($fp, "moysklad: ".json_encode($json, JSON_PRETTY_PRINT)."\n".$json['errors'][0]['error']."\n");
				//fclose($fp);
				
				if($json['id']) mysql_query("insert into ms_leads set retailcrm_id='$num', ms_id ='$ms_lead_id',demand_id='".$json['id']."'");
				//}
			}
			}


		//Если статус заказа отменён, то изменяем данные по заказу в МС
		}elseif($res['order']['status']=='cancel-other'){
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

		}


}


