<?  
// Init 
	ini_set('display_errors', 1);
	ini_set('error_reporting', E_ALL);
	ini_set('memory_limit', '512M');
	ini_set('max_execution_time', '300');

	Header("Content-Type: text/html;charset=UTF-8");

	include("opencart_inc.php");

	if($_GET['argv']) { $argv[1]=$_GET['argv']; }
	else { exit; }
// ---


// 1 - New categories
if($argv[1]=='1'){
	$CHECK_MS=true;

	$page=0;
	while($CHECK_MS){
		$limit=100;
		$offset=$limit*$page;
		$link="https://online.moysklad.ru/api/remap/1.1/entity/productfolder?limit=$limit&offset=$offset";
		$json=ms_query($link);
		
		foreach($json['rows'] as $k=>$v){
			//$res=mysql_query("select category_id from oc_category_description where name='".addslashes($v['name'])."'");
			//list($cat_id)=mysql_fetch_row($res);
			$res=mysql_query("select site_id,id from ms_cats where ms_id='{$v['id']}'");
			list($cat_id,$chid)=mysql_fetch_row($res);
			$tmpx=explode("/",$v['productFolder']['meta']['href']);
			$tmp_ms_par_cat=$tmpx[8];
			$respr=mysql_query("select site_id from ms_cats where ms_id='$tmp_ms_par_cat'");
			list($par_cat_id)=mysql_fetch_row($respr);

			if((!$chid)&&($cat_id)){
				//mysql_query("insert into ms_cats set  ms_id='{$v['id']}',ms_name='".addslashes($v['name'])."',site_id='$cat_id'");
			}elseif((!$chid)&&(!$cat_id)){
				mysql_query("insert into oc_category set parent_id='$par_cat_id',status='1'");
				$cat_id=mysql_insert_id();
				mysql_query("insert into oc_category_description set category_id='$cat_id',meta_title='".addslashes($v['name'])."', language_id='1',name='".addslashes($v['name'])."'");	
				mysql_query("delete from oc_url_alias where query='category_id=$cat_id'");
				mysql_query("insert into oc_url_alias set query='category_id=$cat_id',keyword='".slugify($v['name'])."'");
				mysql_query("insert into oc_category_to_store set  category_id='$cat_id',store_id='0'");
					
				if($par_cat_id){
					$par_cat_id2=get_parent_id ($par_cat_id);
				}
				
				if($par_cat_id&&$par_cat_id2){
			        mysql_query("insert into oc_category_path set category_id='$cat_id',path_id='$cat_id',level='2'");
			        mysql_query("insert into oc_category_path set category_id='$cat_id',path_id='$par_cat_id',level='1'");
			        mysql_query("insert into oc_category_path set category_id='$cat_id',path_id='$par_cat_id2',level='0'");
				}
				
				if($par_cat_id&&!$par_cat_id2){
			        mysql_query("insert into oc_category_path set category_id='$cat_id',path_id='$cat_id',level='1'");
			        mysql_query("insert into oc_category_path set category_id='$cat_id',path_id='$par_cat_id',level='0'");
				}

				if(!$par_cat_id&&!$par_cat_id2){
			        mysql_query("insert into oc_category_path set category_id='$cat_id',path_id='$cat_id',level='0'");
				}
				
			    mysql_query("insert into ms_cats set  ms_id='{$v['id']}',ms_name='".addslashes($v['name'])."',site_id='$cat_id'");
			}elseif($chid){
				//$res=mysql_query("select category_id from oc_category_description where name='".addslashes($v['pathName'])."'");
				//list($par_cat_id)=mysql_fetch_row($res);
				mysql_query("delete from oc_category_path where category_id='$cat_id'");

				if($par_cat_id){
					$par_cat_id2=get_parent_id ($par_cat_id);
				}
					
				if($par_cat_id&&$par_cat_id2){
			        mysql_query("insert into oc_category_path set category_id='$cat_id',path_id='$cat_id',level='2'");
			        mysql_query("insert into oc_category_path set category_id='$cat_id',path_id='$par_cat_id',level='1'");
			        mysql_query("insert into oc_category_path set category_id='$cat_id',path_id='$par_cat_id2',level='0'");
				}
				
				if($par_cat_id&&!$par_cat_id2){
			        mysql_query("insert into oc_category_path set category_id='$cat_id',path_id='$cat_id',level='1'");
			        mysql_query("insert into oc_category_path set category_id='$cat_id',path_id='$par_cat_id',level='0'");
				}

				if(!$par_cat_id&&!$par_cat_id2){
			        mysql_query("insert into oc_category_path set category_id='$cat_id',path_id='$cat_id',level='0'");
				}

				mysql_query("update oc_category_description set name='".addslashes($v['name'])."' where category_id='$cat_id' and language_id='1' ");	
				mysql_query("update oc_category set parent_id='$par_cat_id' where category_id='$cat_id'");
			}

		}//foreach

		if(!count($json['rows'])) { $CHECK_MS=false; echo("###GOODS END###\n");} 
		$page++;
	}
}
// END 1

// 2 - Update products data
if($argv[1]=='2'){

	// Get UOM/MS reference catalog
		$CHECK_MS=true;
		$page=0;
		$limit=100;

		while($CHECK_MS){
			$offset=$limit*$page;
			$link="https://online.moysklad.ru/api/remap/1.1/entity/uom/?limit=$limit&offset=$offset";
			$json=ms_query($link);
			
			foreach($json['rows'] as $k=>$v){		
				if($v['name']=="л") {
					$v['description']="Литр";
				} //костыль, т.к. не знаю как исправить в МС этот справочник	
		 	
				$EDIZM[$v['meta']['href']]=array('name'=>$v['name'], 'description'=>$v['description']);
			}
		
			if(!count($json['rows'])) { $CHECK_MS=false; } 	$page++;
		}
	// ---


	// Get countries as manufacturers
		$CHECK_MS=true;
		$page=0;
		$limit=100;
		
		while($CHECK_MS){
			$offset=$limit*$page;
			$link="https://online.moysklad.ru/api/remap/1.1/entity/country/?limit=$limit&offset=$offset";
			$json=ms_query($link);
				
			foreach($json['rows'] as $k=>$v){
				if ( $qCountries = mysql_query("SELECT `manufacturer_id` FROM `oc_manufacturer` WHERE `name`='".$v['name']."';") ) $nCountries = mysql_num_rows($qCountries);
				else $nCountries = 0;

				if( $nCountries>0 ){
					$rowCountry = mysql_fetch_assoc($qCountries);
					$manid = $rowCountry['manufacturer_id'];
				}
				else{
					mysql_query("insert into oc_manufacturer  set name='".$v['name']."'");
					$manid=mysql_insert_id();
				}
					
				$res=mysql_query("select manufacturer_id from oc_manufacturer_to_store where manufacturer_id='".$manid."'");
				list($manid2)=mysql_fetch_row($res);
				
				if(!$manid2) {
					mysql_query("insert into oc_manufacturer_to_store  set manufacturer_id='".$manid."',store_id='0'");
					$manid2=mysql_insert_id();
				}

				$CNTRS[$v['meta']['href']]=$manid;
			}
			
			if(!count($json['rows'])) { 
				$CHECK_MS=false; 
			} 
			$page++;
		}
	// ---

	// Get products
		$CHECK_MS=true;
		$page=0;
		$limit=100;
		
		while($CHECK_MS){
			$offset=$limit*$page;
			$link="https://online.moysklad.ru/api/remap/1.1/entity/product?limit=$limit&offset=$offset";
			$json=ms_query($link);

			foreach($json['rows'] as $k=>$v){
				// ---
					$NDEL_METKA[$v['id']]=$v['id'];
					
					if(isset($v['externalCode'])){			
						$res=mysql_query("select product_id from ms_products where ms_id='".$v['id']."'");
						list($product_id)=mysql_fetch_row($res);
						$res=mysql_query("select name from oc_product_description where product_id='$product_id'");
						list($prname)=mysql_fetch_row($res);

						if(!$prname) echo("<br><font color=red>Новый продукт - $product_id - {$v['name']} - {$v['id']}</font><br>");
						if(!$product_id) {
							$rj=mysql_query("select product_id from oc_product where sku='".$v['externalCode']."'");
							list($product_id)=mysql_fetch_row($rj);
						}

						$saleprice=$v['salePrices'][0]['value']/100;
						if( isset($v['weighed']) && $v['weighed'] ) $minimum=0;
						else $minimum=1;

						foreach($v['salePrices'] as $kch=>$vch){
							if($vch['priceType']=='Цена продажи') $price=$vch['value']/100;
						}		

						$res_gr=ms_query($v['productFolder']['meta']['href']);
						$resx=mysql_query("select site_id from ms_cats where ms_id='{$res_gr['id']}'");
						list($site_id)=mysql_fetch_row($resx);

						// Get weight class id
							$res=mysql_query("select weight_class_id from oc_weight_class_description where title='".$EDIZM[$v['uom']['meta']['href']]['description']."'");
							list($uomid)=mysql_fetch_row($res);
							if(!$uomid) {
								mysql_query("insert into oc_weight_class_description set title='".$EDIZM[$v['uom']['meta']['href']]['name']."', description='".$EDIZM[$v['uom']['meta']['href']]['description']."'");
								$uomid=mysql_insert_id();
							}
						// ---

						if($product_id){
							echo '<br><b>UPDATE PRODUCT</b><br>';

							// Update existed product
								if(isset($v['image']['meta']['href'])) {
									$image_url=$v['image']['meta']['href'];
									$topath=$_SERVER['DOCUMENT_ROOT'].'/image/catalog/';
									$file_path=$topath.$v['image']['filename'];
									
									if(!file_exists($file_path)||(filesize($file_path)!=$v['image']['size'])) {
										$rescurl=ms_query_image($image_url);
										$fp = fopen($file_path,	 'w');
						  		        fwrite($fp, $rescurl);
										fclose($fp);						
										echo($v['name']."  - ".$file_path." - ".filesize($file_path)." - ".$v['image']['size']."<BR>");
										$tmp['tmp_name']=$file_path;

										if(file_exists($file_path)) {
											resizeImage($tmp,$_SERVER['DOCUMENT_ROOT'].'/image/catalog/',$v['image']['title']."-200x200.jpg",200,200,100);
										}
									
										if(file_exists($file_path)) resizeImage($tmp,$_SERVER['DOCUMENT_ROOT'].'/image/cache/catalog/',$v['image']['title']."-228x228-product_thumb.jpg",228,228,100);
										if(file_exists($file_path)) resizeImage($tmp,$_SERVER['DOCUMENT_ROOT'].'/image/cache/catalog/',$v['image']['title']."-40x40.jpg",40,40,100);
										if(file_exists($file_path)) resizeImage($tmp,$_SERVER['DOCUMENT_ROOT'].'/image/cache/catalog/',$v['image']['title']."-500x500.jpg",500,500,100);
										if(file_exists($file_path)) resizeImage($tmp,$_SERVER['DOCUMENT_ROOT'].'/image/cache/catalog/',$v['image']['title']."-74x74-product_thumb.jpg",74,74,100);

										mysql_query("update oc_product set  image= '".'catalog/'.$v['image']['filename']."' where product_id='$product_id'");
									}

								}

								mysql_query("delete from oc_url_alias where query='product_id=$product_id'");
								mysql_query("insert into oc_url_alias set query='product_id=$product_id',keyword='".slugify($v['name'])."'");
								mysql_query("delete from   oc_product_to_category where product_id='$product_id'");	
						        mysql_query("insert into oc_product_to_category set category_id='$site_id' , product_id='$product_id'");
								
								$CHCAT=true;
								$ch_site_id=$site_id;
								while($CHCAT){
									$gp_site_id=get_parent_id($ch_site_id);
									$ch_site_id=$gp_site_id;
									
									if(!$gp_site_id) $CHCAT=false;
									else mysql_query("insert into oc_product_to_category set category_id='$gp_site_id' , product_id='$product_id'");
								}
			 

								$res_ms=mysql_query("select id from ms_products where product_id='$product_id'");
								list($chid)=mysql_fetch_row($res_ms);
								
								if($chid){
			                		mysql_query("update ms_products set del='0' where product_id='$product_id'");
								}else{
									mysql_query("insert into ms_products set xmlId='".$v['externalCode']."', product_id='$product_id',ms_id='".$v['id']."',del='0'");
								}

								mysql_query("
									UPDATE `oc_product_description` SET 
									`name`='".$v['name']."' 
									WHERE `product_id`='".$product_id."' AND `language_id`='1'");

								mysql_query("
									UPDATE `oc_product` SET 
									`date_modified`=NOW(),
									`weight`='".$v['weight']."',
									`minimum`='$minimum',
									`weight_class_id`='$uomid',
									`price`='".$price."',
									`model`='".$v['name']."' 
									WHERE product_id='$product_id'
								");

								if( isset($v['country']) ){
									mysql_query("
										UPDATE `oc_product` SET 
										`manufacturer_id`='".$CNTRS[$v['country']['meta']['href']]."',
										WHERE product_id='$product_id'
									");
								}
							// ---
						}
						else{
							echo '<br><b>ADD PRODUCT</b><br>';

							// Add new product
								if( !isset($v['weight']) ) { $v['weight'] = 0; }
								if( !isset($v['weighed']) ) { $v['weighed'] = 0; }
								if( !isset($v['name']) ) { $v['name'] = ''; }
								if( !isset($v['description']) ) { $v['description'] = ''; }
								if( !isset($v['image']) ) {
									$v['image'] = array(
										'meta' => array('href' => '','mediaType' => 'application/octet-stream'),
										'title' => '',
										'filename' => '',
										'size' => 0,
										'updated' => '0000-00-00 00:00:00',
										'miniature' => array('href' => '','mediaType' => 'image/png'),
										'tiny' => array('href' => '','mediaType' => 'image/png')
									);
								}
								if( !isset($v['country']) ) {
									$v['country'] = array(
										'meta' => array(
											'href' => '',
											'metadataHref' => '',
											'type' => 'country',
											'mediaType' => 'application/json'
										)
									);
								}


								if(isset($v['image']['meta']['href']) && $v['image']['meta']['href']!='') {
									$image_url=$v['image']['meta']['href'];
									$topath=$_SERVER['DOCUMENT_ROOT'].'/image/catalog/';
									$file_path=$topath.$v['image']['filename'];

									$rescurl=ms_query_image($image_url);
									$fp = fopen($file_path, 'w');
					  		        fwrite($fp, $rescurl);
									fclose($fp);

									$tmp['tmp_name']=$file_path;

									if(file_exists($file_path)) {
										resizeImage($tmp,$_SERVER['DOCUMENT_ROOT'].'/image/catalog/',$v['image']['title']."-200x200.jpg",200,200,100);
									}
									
									if(file_exists($file_path)) resizeImage($tmp,$_SERVER['DOCUMENT_ROOT'].'/image/cache/catalog/',$v['image']['title']."-228x228-product_thumb.jpg",228,228,100);
									if(file_exists($file_path)) resizeImage($tmp,$_SERVER['DOCUMENT_ROOT'].'/image/cache/catalog/',$v['image']['title']."-40x40.jpg",40,40,100);
									if(file_exists($file_path)) resizeImage($tmp,$_SERVER['DOCUMENT_ROOT'].'/image/cache/catalog/',$v['image']['title']."-500x500.jpg",500,500,100);
									if(file_exists($file_path)) resizeImage($tmp,$_SERVER['DOCUMENT_ROOT'].'/image/cache/catalog/',$v['image']['title']."-74x74-product_thumb.jpg",74,74,100);
								}

								$qInsert = mysql_query("
									INSERT IGNORE INTO oc_product SET 
									model = '" . addslashes($v['name']) . "',
									is_weighted = '0',
									composite_price = '0',
									sku = '" . $v['externalCode'] . "',
									weight_variants = '',
									shelf_life = '',
									available_in_time = '',
									special_price = '0',
									profitable_offer = '0',
									available = '0',
									upc = '',
									ean = '',
									jan = '',
									isbn = '',
									mpn = '',
									location = '',
									quantity = '0',
									minimum = '" . (int)$minimum . "',
									subtract = '1',
									stock_status_id = '5',
									date_available = '0000-00-00',
									manufacturer_id = '" . (int)$CNTRS[$v['country']['meta']['href']] . "',
									shipping = '1',
									price = '" . (float)$price . "',
									points = '0',
									weight = '" . (float)$v['weight'] . "',
									weight_class_id = '" . (int)$uomid . "',
									length = '0',
									width = '0',
									height = '0',
									length_class_id = '0',
									status = '1',
									tax_class_id = '0',
									sort_order = '0',
									date_added = NOW(),
									image= '".'catalog/'.$v['image']['filename']."';
								");

								$product_id=mysql_insert_id();
								echo $product_id." oc_product error:".mysql_error();
								echo '<br>';

								$qInsert = mysql_query("
									INSERT IGNORE INTO oc_product_description SET 
									name='".addslashes($v['name'])."',
									meta_title='".addslashes($v['name'])."',
									product_id='".$product_id."',
									language_id='1',
									description='".$v['description']."',
									description_short='';
								");
								echo "oc_product_description error: ".mysql_error();
								echo '<br><br>';


								mysql_query("delete from oc_url_alias where query='product_id=$product_id'");
								
								mysql_query("insert into oc_url_alias set query='product_id=$product_id',keyword='".slugify($v['name'])."'");
					
						        mysql_query("insert into oc_product_to_category set category_id='$site_id' , product_id='$product_id'");

						        mysql_query("insert into oc_product_to_store set  product_id='$product_id',store_id='0'");

								mysql_query("insert into ms_products set xmlId='".$v['externalCode']."', product_id='$product_id', ms_id='".$v['id']."',del='0',purchaseprice='0'");
							// ---
						}

					}

					echo "<br> --- <br>";
				// ---
			}
			if(!count($json['rows'])) { $CHECK_MS=false; } 
			$page++;
		}
	// ---


	// Get variants
		$CHECK_MS=true;
		$limit=100;
		$page=0;
		
		while($CHECK_MS){
			
			$offset=$limit*$page;
			$link="https://online.moysklad.ru/api/remap/1.1/entity/variant?limit=$limit&offset=$offset";
			$json=ms_query($link);
			
			foreach($json['rows'] as $k=>$v){
				$tmp=1;		$ch_gr=NULL; $ch_attr=NULL;

				$product_ms_arr=explode("/",$v['product']['meta']['href']);
				$product_ms_id=$product_ms_arr[8];

				$res=mysql_query("select product_id from ms_products where ms_id='$product_ms_id'");
				list($product_id)=mysql_fetch_row($res);

				$msvlid_arr=$msvlid_arr2=array();
				
				foreach($v['characteristics'] as $kch=>$vch){
					
					$res=mysql_query("select option_id from oc_option_description where name='".$vch['name']."'");
					list($option_id)=mysql_fetch_row($res);
					if(!$option_id){
						mysql_query("insert into oc_option set type='select'");
						$option_id=mysql_insert_id();
						mysql_query("insert into oc_option_description set option_id='$option_id',name='".$vch['name']."',language_id='1'");
					}
					
					$vopt=$vch['value'];
					echo($vch['name']." - ".$vopt." - ".$v['externalCode']."<BR>");
					$res=mysql_query("select 	option_value_id from oc_option_value_description where name='".addslashes($vopt)."' and option_id='$option_id'");
					list($option_value_id)=mysql_fetch_row($res);
					if(!$option_value_id){
						mysql_query("insert into oc_option_value  set  option_id='$option_id'");
						$option_value_id=mysql_insert_id();
						mysql_query("insert into oc_option_value_description  set option_value_id='$option_value_id',language_id='1', name='".addslashes($vopt)."' , option_id='$option_id'");
					}

					$search_char_arr[]='characteristics.'.$vch['name'].'='.$vopt;
					
					if($product_id){
						$res=mysql_query("select product_option_id from oc_product_option where product_id='".$product_id."' and option_id='$option_id'");
						list($msid)=mysql_fetch_row($res);
						if(!$msid){
							mysql_query("insert into oc_product_option set product_id='".$product_id."' , option_id='$option_id', required='1'");
							$msid=mysql_insert_id();
						}
						$res=mysql_query("select product_option_value_id from oc_product_option_value where product_id='".$product_id."' and option_id='$option_id' and option_value_id='$option_value_id' and product_option_id='$msid'");
						list($msvlid)=mysql_fetch_row($res);
						
						if(!$msvlid){
							mysql_query("insert into oc_product_option_value set product_option_id='$msid', option_value_id='$option_value_id', product_id='".$product_id."' , option_id='$option_id'");
							$msvlid=mysql_insert_id();
						}else{
						}

						$msvlid_arr2[]="$msid"."-$option_value_id";
						$msvlid_arr[]=$msvlid;

					}
				}

	            $msvlidx=implode(",",$msvlid_arr);
	            $msvlidx2=implode("_",$msvlid_arr2);
				$res_ms=mysql_query("select id from ms_variants where product_option_value_id='$msvlidx'");
				list($chid)=mysql_fetch_row($res_ms);
				if(!$chid){
					mysql_query("insert into ms_variants set  product_option_value_id='$msvlidx',ms_id='".$v['id']."'");
				}
			}
			
			if(!count($json['rows'])) { $CHECK_MS=false; echo("###");} 
			$page++;
		}
	// ---
}
// END 2



// 3 - Update products stock
if($argv[1]=='3'){
	// ---
		$PARENTS=NULL;
		$CHECK_MS=true;
		$page=0;		

		while($CHECK_MS){
		
			$limit=100;
			$offset=$limit*$page;
			$link="https://online.moysklad.ru/api/remap/1.1/report/stock/all?stockMode=all&limit=$limit&offset=$offset";//&updatedFrom=".$updfrom;
			//store.id
			$json=ms_query($link);

			foreach($json['rows'] as $k=>$v){
				$url=parse_url($v['meta']['href']);

				// Get id product
					$idsArr=explode("/",$url['path']);
					$id=$idsArr[6];
				// ---

				$qty=$v['quantity'];
				
				if($v['meta']['type']=='variant') {

					$res=mysql_query("select product_option_value_id from ms_variants where ms_id='$id'");
					list($sub_post_id)=mysql_fetch_row($res);
					echo("$sub_post_id - $qty<BR>");
					
					if($sub_post_id ){
						$res2=mysql_query("select product_id from oc_product_option_value where  product_option_value_id='$sub_post_id'");
						list($chpr_id)=mysql_fetch_row($res2);
						
						if($chpr_id){
							$QTS[$chpr_id]=$chpr_id; 

							mysql_query("
								UPDATE IGNORE `oc_product` SET 
								`quantity`=$qty, `date_available`='0000-00-00', `status`=1 
								WHERE `product_id`='$chpr_id';
							");
						}
						mysql_query("update oc_product_option_value set quantity='$qty' where product_option_value_id='$sub_post_id'");

					}
				}elseif($v['meta']['type']=='product') {
					// ---
						if ( $qProduct = mysql_query("SELECT `product_id` FROM `ms_products` WHERE ms_id='$id';") ) $nProduct = mysql_num_rows($qProduct);
						else $nProduct = 0;

						if( $nProduct>0 ){
							$rowMSProduct = mysql_fetch_assoc($qProduct);
							$product_id=$rowMSProduct['product_id'];

							if($qty>0) { $QTS[$product_id]=$product_id; $AVA=" and AVAILABLE='Y'"; } 

							mysql_query("
								UPDATE IGNORE `oc_product` SET 
								`quantity`=$qty, `date_available`='0000-00-00', `status`=1 
								WHERE `product_id`=".$product_id.";
							");
						}
					// ---
				}
			}
			
			if(!count($json['rows'])) { $CHECK_MS=false; echo("###STOCK END###\n");} 
			$page++;
			sleep(3);
		}

		
		//$res=mysql_query("select product_id from oc_product ");
		//while(list($CHID)=mysql_fetch_row($res)){
			//$res2=mysql_query("select del from ms_products where product_id='$CHID'");
			//list($del)=mysql_fetch_row($res2);
			//$UPDST='';
			//if(!in_array($CHID,$QTS)){
			//if($del==1) $UPDST="status='0',"; else $UPDST='';			
			//mysql_query("update oc_product set  $UPDST quantity='0',stock_status_id='5' where product_id='$CHID'");
		//}

	// ---
}
// END 3