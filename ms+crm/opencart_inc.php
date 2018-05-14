<?php


include(__DIR__."/../config.php");

    $db = mysql_connect(DB_HOSTNAME,DB_USERNAME,DB_PASSWORD);
    mysql_select_db(DB_DATABASE,$db);

    mysql_query("SET NAMES 'utf8'");

    $AUTH_DATA='admin@mail195:134679';

    $retail_key='AuNf4IgJFHTmZQu7PwTKuPNQch5v03to';



function ms_query($link){

	global $AUTH_DATA;

	$curl=curl_init(); 
	curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
	curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client/1.0');
	curl_setopt($curl,CURLOPT_URL,$link);
	curl_setopt($curl, CURLOPT_HTTPHEADER, array(    'Content-Type: application/json'));
	curl_setopt($curl,CURLOPT_POST,0);
	curl_setopt($curl,CURLOPT_USERPWD,$AUTH_DATA);
	curl_setopt($curl,CURLOPT_HEADER,false);
//	curl_setopt($curl,CURLOPT_COOKIEFILE,dirname(__FILE__).'/cookie2.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
//	curl_setopt($curl,CURLOPT_COOKIEJAR,dirname(__FILE__).'/cookie2.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
	curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
	curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);
	$out=curl_exec($curl); #Èíèöèèðóåì çàïðîñ ê API è ñîõðàíÿåì îòâåò â ïåðåìåííóþ
	curl_close($curl); #Çàâåðøàåì ñåàíñ cURL
	$json=json_decode($out, JSON_UNESCAPED_UNICODE);
//var_dump($out);
	return $json;
}






function ms_query_send($link,$data,$request){

	global $AUTH_DATA;
	$send_data=json_encode($data);

	$curl=curl_init(); 
	curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
	curl_setopt($curl,CURLOPT_URL,$link);
	curl_setopt($curl,CURLOPT_POST,0);
	curl_setopt($curl,CURLOPT_USERPWD,"$AUTH_DATA");
	curl_setopt($curl,CURLOPT_HEADER,false);
	curl_setopt($curl, CURLOPT_HTTPHEADER, array(
	    'Content-Type: application/json'
	));
	curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "$request");  
	curl_setopt($curl, CURLOPT_POSTFIELDS,            $send_data);
	
	curl_setopt($curl,CURLOPT_COOKIEFILE,dirname(__FILE__).'/cookie2.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
	curl_setopt($curl,CURLOPT_COOKIEJAR,dirname(__FILE__).'/cookie2.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
	curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
	curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);
	$out=curl_exec($curl); 
	curl_close($curl); 
	$json=json_decode($out, JSON_UNESCAPED_UNICODE);

	return $json;
}




function ms_query_image($link){

	global $AUTH_DATA;

	$curl=curl_init(); #Ñîõðàíÿåì äåñêðèïòîð ñåàíñà cURL
	curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
	curl_setopt($curl,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.7.3) Gecko/20041001 Firefox/0.10.1');
	curl_setopt($curl,CURLOPT_URL,$link);
	curl_setopt($curl, CURLOPT_HTTPHEADER, array(    'Content-Type: application/json'));
	curl_setopt($curl,CURLOPT_POST,0);
	curl_setopt($curl,CURLOPT_USERPWD,$AUTH_DATA);
	curl_setopt($curl,CURLOPT_HEADER,false);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

	curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
	curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);
	$content=curl_exec($curl); #Èíèöèèðóåì çàïðîñ ê API è ñîõðàíÿåì îòâåò â ïåðåìåííóþ

	$info = curl_getinfo($curl); 
	$cerrorno = curl_errno($curl); 
	$cerrorinfo = curl_error($curl); 

	curl_close($curl); #Çàâåðøàåì ñåàíñ cURL
	$response = $info;

if ($response['http_code'] == 301 || $response['http_code'] == 302)
{
//var_dump($response);
    ini_set("user_agent", "Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.7.3) Gecko/20041001 Firefox/0.10.1");
    $headers = get_headers($response['url']);
    $headers = get_headers($response['redirect_url']);

    $location = "";
    foreach( $headers as $value )
    {
//            		$out= ms_query_image( $response['redirect_url'] );
//			var_dump($out);

            		$out= file_get_contents( $response['redirect_url'] );
			return($out);

    }
}

    return $content; 

}



function resizeImage($imgObject, $savePath, $imgName, $imgMaxWidth, $imgMaxHeight, $imgQuality)
{

    $source = imagecreatefromjpeg($imgObject['tmp_name']);
    list($imgWidth, $imgHeight) = getimagesize($imgObject['tmp_name']);

  

    $imgAspectRatio = $imgWidth / $imgHeight;
    if ($imgMaxWidth / $imgMaxHeight > $imgAspectRatio)
    {
        $imgMaxWidth = $imgMaxHeight * $imgAspectRatio;
    }
    else
    {
        $imgMaxHeight = $imgMaxWidth / $imgAspectRatio;
    }
    $image_p = imagecreatetruecolor($imgMaxWidth, $imgMaxHeight);
    $image = imagecreatefromjpeg($imgObject['tmp_name']);
    imagecopyresampled($image_p, $source, 0, 0, 0, 0, $imgMaxWidth, $imgMaxHeight, $imgWidth, $imgHeight);
    imagejpeg($image_p, $savePath . $imgName, $imgQuality);
    unset($imgObject);
    unset($source);
    unset($image_p);
    unset($image);
//exit();
}




function crm_query($link){

	$curl=curl_init();
	curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
	curl_setopt($curl,CURLOPT_URL,$link);
	curl_setopt($curl, CURLOPT_HTTPHEADER, array(    'Content-Type: application/json'));
	curl_setopt($curl,CURLOPT_HEADER,false);
	curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
	curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);
	$out=curl_exec($curl);


	$info = curl_getinfo($curl); 
	$cerrorno = curl_errno($curl); 
	$cerrorinfo = curl_error($curl); 
	curl_close($curl);
	$json=json_decode($out, JSON_UNESCAPED_UNICODE);

	return $json;
}



function crm_query_send($link,$data){

	$send_data=http_build_query($data);
	$curl=curl_init(); #Ñîõðàíÿåì äåñêðèïòîð ñåàíñà cURL
	curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);

	curl_setopt($curl,CURLOPT_URL,$link);

	curl_setopt($curl,CURLOPT_POST,1);
	curl_setopt($curl, CURLOPT_HTTPHEADER, array(
    		'Content-Type: application/x-www-form-urlencoded'
	));
	curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");  
	curl_setopt($curl, CURLOPT_POSTFIELDS,       $send_data);

	curl_setopt($curl,CURLOPT_HEADER,false);
	$out=curl_exec($curl); #Èíèöèèðóåì çàïðîñ ê API è ñîõðàíÿåì îòâåò â ïåðåìåííóþ
	curl_close($curl);
   	$json=json_decode($out, JSON_UNESCAPED_UNICODE);

	return $json;
}



function get_parent_id($catid){
	
	$res=mysql_query("select parent_id from oc_category where category_id='$catid'");
	list($parent_id)=mysql_Fetch_row($res);

	return $parent_id;
}



function slugify ($text) {

    $replace = [
        '"'=>'', '.'=>'', '&lt;' => '', '&gt;' => '', '&#039;' => '', '&amp;' => '',
        '&quot;' => '', 'Ã€' => 'A', 'Ã' => 'A', 'Ã‚' => 'A', 'Ãƒ' => 'A', 'Ã„'=> 'Ae',
        '&Auml;' => 'A', 'Ã…' => 'A', 'Ä€' => 'A', 'Ä„' => 'A', 'Ä‚' => 'A', 'Ã†' => 'Ae',
        'Ã‡' => 'C', 'Ä†' => 'C', 'ÄŒ' => 'C', 'Äˆ' => 'C', 'ÄŠ' => 'C', 'ÄŽ' => 'D', 'Ä' => 'D',
        'Ã' => 'D', 'Ãˆ' => 'E', 'Ã‰' => 'E', 'ÃŠ' => 'E', 'Ã‹' => 'E', 'Ä’' => 'E',
        'Ä˜' => 'E', 'Äš' => 'E', 'Ä”' => 'E', 'Ä–' => 'E', 'Äœ' => 'G', 'Äž' => 'G',
        'Ä ' => 'G', 'Ä¢' => 'G', 'Ä¤' => 'H', 'Ä¦' => 'H', 'ÃŒ' => 'I', 'Ã' => 'I',
        'ÃŽ' => 'I', 'Ã' => 'I', 'Äª' => 'I', 'Ä¨' => 'I', 'Ä¬' => 'I', 'Ä®' => 'I',
        'Ä°' => 'I', 'Ä²' => 'IJ', 'Ä´' => 'J', 'Ä¶' => 'K', 'Å' => 'K', 'Ä½' => 'K',
        'Ä¹' => 'K', 'Ä»' => 'K', 'Ä¿' => 'K', 'Ã‘' => 'N', 'Åƒ' => 'N', 'Å‡' => 'N',
        'Å…' => 'N', 'ÅŠ' => 'N', 'Ã’' => 'O', 'Ã“' => 'O', 'Ã”' => 'O', 'Ã•' => 'O',
        'Ã–' => 'Oe', '&Ouml;' => 'Oe', 'Ã˜' => 'O', 'ÅŒ' => 'O', 'Å' => 'O', 'ÅŽ' => 'O',
        'Å’' => 'OE', 'Å”' => 'R', 'Å˜' => 'R', 'Å–' => 'R', 'Åš' => 'S', 'Å ' => 'S',
        'Åž' => 'S', 'Åœ' => 'S', 'È˜' => 'S', 'Å¤' => 'T', 'Å¢' => 'T', 'Å¦' => 'T',
        'Èš' => 'T', 'Ã™' => 'U', 'Ãš' => 'U', 'Ã›' => 'U', 'Ãœ' => 'Ue', 'Åª' => 'U',
        '&Uuml;' => 'Ue', 'Å®' => 'U', 'Å°' => 'U', 'Å¬' => 'U', 'Å¨' => 'U', 'Å²' => 'U',
        'Å´' => 'W', 'Ã' => 'Y', 'Å¶' => 'Y', 'Å¸' => 'Y', 'Å¹' => 'Z', 'Å½' => 'Z',
        'Å»' => 'Z', 'Ãž' => 'T', 'Ã ' => 'a', 'Ã¡' => 'a', 'Ã¢' => 'a', 'Ã£' => 'a',
        'Ã¤' => 'ae', '&auml;' => 'ae', 'Ã¥' => 'a', 'Ä' => 'a', 'Ä…' => 'a', 'Äƒ' => 'a',
        'Ã¦' => 'ae', 'Ã§' => 'c', 'Ä‡' => 'c', 'Ä' => 'c', 'Ä‰' => 'c', 'Ä‹' => 'c',
        'Ä' => 'd', 'Ä‘' => 'd', 'Ã°' => 'd', 'Ã¨' => 'e', 'Ã©' => 'e', 'Ãª' => 'e',
        'Ã«' => 'e', 'Ä“' => 'e', 'Ä™' => 'e', 'Ä›' => 'e', 'Ä•' => 'e', 'Ä—' => 'e',
        'Æ’' => 'f', 'Ä' => 'g', 'ÄŸ' => 'g', 'Ä¡' => 'g', 'Ä£' => 'g', 'Ä¥' => 'h',
        'Ä§' => 'h', 'Ã¬' => 'i', 'Ã­' => 'i', 'Ã®' => 'i', 'Ã¯' => 'i', 'Ä«' => 'i',
        'Ä©' => 'i', 'Ä­' => 'i', 'Ä¯' => 'i', 'Ä±' => 'i', 'Ä³' => 'ij', 'Äµ' => 'j',
        'Ä·' => 'k', 'Ä¸' => 'k', 'Å‚' => 'l', 'Ä¾' => 'l', 'Äº' => 'l', 'Ä¼' => 'l',
        'Å€' => 'l', 'Ã±' => 'n', 'Å„' => 'n', 'Åˆ' => 'n', 'Å†' => 'n', 'Å‰' => 'n',
        'Å‹' => 'n', 'Ã²' => 'o', 'Ã³' => 'o', 'Ã´' => 'o', 'Ãµ' => 'o', 'Ã¶' => 'oe',
        '&ouml;' => 'oe', 'Ã¸' => 'o', 'Å' => 'o', 'Å‘' => 'o', 'Å' => 'o', 'Å“' => 'oe',
        'Å•' => 'r', 'Å™' => 'r', 'Å—' => 'r', 'Å¡' => 's', 'Ã¹' => 'u', 'Ãº' => 'u',
        'Ã»' => 'u', 'Ã¼' => 'ue', 'Å«' => 'u', '&uuml;' => 'ue', 'Å¯' => 'u', 'Å±' => 'u',
        'Å­' => 'u', 'Å©' => 'u', 'Å³' => 'u', 'Åµ' => 'w', 'Ã½' => 'y', 'Ã¿' => 'y',
        'Å·' => 'y', 'Å¾' => 'z', 'Å¼' => 'z', 'Åº' => 'z', 'Ã¾' => 't', 'ÃŸ' => 'ss',
        'Å¿' => 'ss', 'Ñ‹Ð¹' => 'iy', 'Ð' => 'A', 'Ð‘' => 'B', 'Ð’' => 'V', 'Ð“' => 'G',
        'Ð”' => 'D', 'Ð•' => 'E', 'Ð' => 'YO', 'Ð–' => 'ZH', 'Ð—' => 'Z', 'Ð˜' => 'I',
        'Ð™' => 'Y', 'Ðš' => 'K', 'Ð›' => 'L', 'Ðœ' => 'M', 'Ð' => 'N', 'Ðž' => 'O',
        'ÐŸ' => 'P', 'Ð ' => 'R', 'Ð¡' => 'S', 'Ð¢' => 'T', 'Ð£' => 'U', 'Ð¤' => 'F',
        'Ð¥' => 'H', 'Ð¦' => 'C', 'Ð§' => 'CH', 'Ð¨' => 'SH', 'Ð©' => 'SCH', 'Ðª' => '',
        'Ð«' => 'Y', 'Ð¬' => '', 'Ð­' => 'E', 'Ð®' => 'YU', 'Ð¯' => 'YA', 'Ð°' => 'a',
        'Ð±' => 'b', 'Ð²' => 'v', 'Ð³' => 'g', 'Ð´' => 'd', 'Ðµ' => 'e', 'Ñ‘' => 'yo',
        'Ð¶' => 'zh', 'Ð·' => 'z', 'Ð¸' => 'i', 'Ð¹' => 'y', 'Ðº' => 'k', 'Ð»' => 'l',
        'Ð¼' => 'm', 'Ð½' => 'n', 'Ð¾' => 'o', 'Ð¿' => 'p', 'Ñ€' => 'r', 'Ñ' => 's',
        'Ñ‚' => 't', 'Ñƒ' => 'u', 'Ñ„' => 'f', 'Ñ…' => 'h', 'Ñ†' => 'c', 'Ñ‡' => 'ch',
        'Ñˆ' => 'sh', 'Ñ‰' => 'sch', 'ÑŠ' => '', 'Ñ‹' => 'y', 'ÑŒ' => '', 'Ñ' => 'e',
        'ÑŽ' => 'yu', 'Ñ' => 'ya'
    ];

    // make a human readable string
    $text = strtr($text, $replace);

    // replace non letter or digits by -
    $text = preg_replace('~[^\\pL\d.]+~u', '-', $text);

    // trim
    $text = trim($text, '-');

    // remove unwanted characters
    $text = preg_replace('~[^-\w.]+~', '', $text);

    $text = strtolower($text);

    return $text;
}
