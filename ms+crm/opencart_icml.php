<?php 
//    ini_set('display_errors', 1);
//    ini_set('error_reporting', E_ALL);
	ini_set('memory_limit', '512M');
	ini_set('max_execution_time', '300');

header("Content-Type: text/xml");
header("Expires: Thu, 19 Feb 1998 13:24:18 GMT");
header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Cache-Control: post-check=0,pre-check=0");
header("Cache-Control: max-age=0");
header("Pragma: no-cache");

include("opencart_inc.php");



echo("<yml_catalog date=\"2016-12-10 22:27:15\">
<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<shop>
<name>Eco-u</name>
<company>Eco-u</company>
<categories>");
	$res=mysql_query("select category_id,name from oc_category_description");
	while(list($cid,$cname)=mysql_fetch_Row($res)){

		$res2=mysql_query("select parent_id from oc_category where category_id='$cid'");
		list($parent_id)=mysql_fetch_Row($res2);

		if($parent_id) $parentid=" parentId=\"$parent_id\" ";
		else $parentid='';
	
		echo("<category id=\"$cid\" $parentid>$cname</category>");
	}


echo("</categories><offers>");

	$res=mysql_query("select product_id,model,sku,image,price,special_price,weight_class_id,quantity,ultra_fresh from oc_product");// and CP.TIMESTAMP_X>'$updfrom' //NAME 
	while(list($EL_ID,$model,$sku,$image,$price,$special_price,$wclid,$quantity,$ultra_fresh)=mysql_fetch_row($res)){


		$res2=mysql_query("select title ,unit from oc_weight_class_description where weight_class_id='$wclid'");
		list($wtitle,$wunit)=mysql_fetch_row($res2);

		$res2=mysql_query("select name from oc_product_description where product_id='$EL_ID'");
		list($name)=mysql_fetch_Row($res2);

		$res5=mysql_query("select xmlId,purchaseprice from ms_products where product_id='$EL_ID'");
		list($xmlId,$purchaseprice)=mysql_fetch_row($res5);


		echo("<offer id=\"$EL_ID\" productId=\"$EL_ID\" quantity=\"$quantity\">
		<xmlId>$xmlId</xmlId>");


		echo("<param name=\"Артикул\" code=\"article\">$sku</param>");
		if( $ultra_fresh == 1 ) {
			echo("<param name=\"UltraFresh\" code=\"ultrafresh\">1</param>");
		}

		$resp=mysql_query("select category_id  from oc_product_to_category  where product_id='$EL_ID'");
		while(list($pcatid)=mysql_fetch_row($resp)){
			echo("<categoryId>$pcatid</categoryId>");
		}
		$img="http://eco-u.ru/image/$image";
		if($purchaseprice) echo("<purchasePrice>$purchaseprice</purchasePrice>");
		echo("<picture>$img</picture>");
		$resp=mysql_query("select keyword from oc_url_alias where query='product_id=$EL_ID'");
		list($keyword)=mysql_fetch_row($resp);
		//$keyword=str_replace($keyword);
		echo("<url>http://eco-u.ru/eda/$keyword</url>");


		// Price
			if( $special_price == 0 ){
	 			echo("<price>".round($price)."</price>");
			}
			else{ 
	 			echo("<price>".round($special_price)."</price>");
			}
	 	// ---

		switch ($wunit) {
			case 'кг':
				$wunit_eng='kg';
			break;
			case 'шт.':
				$wunit_eng='pc';
			break;
			case 'л':
				$wunit_eng='l';
			break;
			case 'мл':
				$wunit_eng='ml';
			break;
			case 'г':
				$wunit_eng='g';
			break;
			default:
				$wunit_eng='';
		}
		
		if($wunit) echo("<unit code=\"$wunit_eng\" name=\"$wtitle\" sym=\"$wunit\" />");
		echo("<name>$name</name>");
		echo("<productName>$name</productName>");
		echo("</offer>");



		$res5=mysql_query("select id, product_option_value_id,xmlId from ms_variants where product_id='$EL_ID'");

		while(list($msid,$ms_povi,$xmlId2)=mysql_fetch_row($res5)){

			echo("<offer id=\"$EL_ID"."#$ms_povi\" productId=\"$EL_ID\" quantity=\"0\">");

			echo("<productName>$name</productName>");
		 	echo("<price>$price</price>");
			echo("<picture>$img</picture>");
			echo("<xmlId>$xmlId#$xmlId2</xmlId>");
			echo("<url>http://eco-u.ru/$keyword</url>");
			echo("<param name=\"Артикул\" code=\"article\">$sku</param>");
		
			$ms_povi_arr=explode(",",$ms_povi);
			$tmpchar=null;
			foreach($ms_povi_arr as $kp=>$vp){
				$resx=mysql_query("select  product_option_id,option_id,option_value_id from oc_product_option_value where product_id='$EL_ID' and product_option_value_id='$vp'");
				list($poi,$oi,$ovi)=mysql_fetch_row($resx);
				$resx=mysql_query("select name from oc_option_description where option_id='$oi'");
				list($oiname)=mysql_fetch_row($resx);
				$oiname2=slugify($oiname);

				$resx=mysql_query("select name from oc_option_value_description where 	option_value_id ='$ovi'");
				list($oviname)=mysql_fetch_row($resx);

				echo("<param name=\"$oiname\" code=\"$oiname2\">$oviname</param>");
				$tmpchar[]=$oviname;
	                
			}
			if(count($tmpchar)>0) echo("<name>$name (".implode(",",$tmpchar).")</name>");
			else echo("<name>$name</name>");

        		echo("</offer>");

		}
	
	}



echo("</offers></shop> 
</yml_catalog>");




