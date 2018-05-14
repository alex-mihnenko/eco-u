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
	$out=curl_exec($curl); #���������� ������ � API � ��������� ����� � ����������
	curl_close($curl); #��������� ����� cURL
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

	$curl=curl_init(); #��������� ���������� ������ cURL
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
	$content=curl_exec($curl); #���������� ������ � API � ��������� ����� � ����������

	$info = curl_getinfo($curl); 
	$cerrorno = curl_errno($curl); 
	$cerrorinfo = curl_error($curl); 

	curl_close($curl); #��������� ����� cURL
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
	$curl=curl_init(); #��������� ���������� ������ cURL
	curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);

	curl_setopt($curl,CURLOPT_URL,$link);

	curl_setopt($curl,CURLOPT_POST,1);
	curl_setopt($curl, CURLOPT_HTTPHEADER, array(
    		'Content-Type: application/x-www-form-urlencoded'
	));
	curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");  
	curl_setopt($curl, CURLOPT_POSTFIELDS,       $send_data);

	curl_setopt($curl,CURLOPT_HEADER,false);
	$out=curl_exec($curl); #���������� ������ � API � ��������� ����� � ����������
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
        '&quot;' => '', 'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä'=> 'Ae',
        '&Auml;' => 'A', 'Å' => 'A', 'Ā' => 'A', 'Ą' => 'A', 'Ă' => 'A', 'Æ' => 'Ae',
        'Ç' => 'C', 'Ć' => 'C', 'Č' => 'C', 'Ĉ' => 'C', 'Ċ' => 'C', 'Ď' => 'D', 'Đ' => 'D',
        'Ð' => 'D', 'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ē' => 'E',
        'Ę' => 'E', 'Ě' => 'E', 'Ĕ' => 'E', 'Ė' => 'E', 'Ĝ' => 'G', 'Ğ' => 'G',
        'Ġ' => 'G', 'Ģ' => 'G', 'Ĥ' => 'H', 'Ħ' => 'H', 'Ì' => 'I', 'Í' => 'I',
        'Î' => 'I', 'Ï' => 'I', 'Ī' => 'I', 'Ĩ' => 'I', 'Ĭ' => 'I', 'Į' => 'I',
        'İ' => 'I', 'Ĳ' => 'IJ', 'Ĵ' => 'J', 'Ķ' => 'K', 'Ł' => 'K', 'Ľ' => 'K',
        'Ĺ' => 'K', 'Ļ' => 'K', 'Ŀ' => 'K', 'Ñ' => 'N', 'Ń' => 'N', 'Ň' => 'N',
        'Ņ' => 'N', 'Ŋ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O',
        'Ö' => 'Oe', '&Ouml;' => 'Oe', 'Ø' => 'O', 'Ō' => 'O', 'Ő' => 'O', 'Ŏ' => 'O',
        'Œ' => 'OE', 'Ŕ' => 'R', 'Ř' => 'R', 'Ŗ' => 'R', 'Ś' => 'S', 'Š' => 'S',
        'Ş' => 'S', 'Ŝ' => 'S', 'Ș' => 'S', 'Ť' => 'T', 'Ţ' => 'T', 'Ŧ' => 'T',
        'Ț' => 'T', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'Ue', 'Ū' => 'U',
        '&Uuml;' => 'Ue', 'Ů' => 'U', 'Ű' => 'U', 'Ŭ' => 'U', 'Ũ' => 'U', 'Ų' => 'U',
        'Ŵ' => 'W', 'Ý' => 'Y', 'Ŷ' => 'Y', 'Ÿ' => 'Y', 'Ź' => 'Z', 'Ž' => 'Z',
        'Ż' => 'Z', 'Þ' => 'T', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a',
        'ä' => 'ae', '&auml;' => 'ae', 'å' => 'a', 'ā' => 'a', 'ą' => 'a', 'ă' => 'a',
        'æ' => 'ae', 'ç' => 'c', 'ć' => 'c', 'č' => 'c', 'ĉ' => 'c', 'ċ' => 'c',
        'ď' => 'd', 'đ' => 'd', 'ð' => 'd', 'è' => 'e', 'é' => 'e', 'ê' => 'e',
        'ë' => 'e', 'ē' => 'e', 'ę' => 'e', 'ě' => 'e', 'ĕ' => 'e', 'ė' => 'e',
        'ƒ' => 'f', 'ĝ' => 'g', 'ğ' => 'g', 'ġ' => 'g', 'ģ' => 'g', 'ĥ' => 'h',
        'ħ' => 'h', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ī' => 'i',
        'ĩ' => 'i', 'ĭ' => 'i', 'į' => 'i', 'ı' => 'i', 'ĳ' => 'ij', 'ĵ' => 'j',
        'ķ' => 'k', 'ĸ' => 'k', 'ł' => 'l', 'ľ' => 'l', 'ĺ' => 'l', 'ļ' => 'l',
        'ŀ' => 'l', 'ñ' => 'n', 'ń' => 'n', 'ň' => 'n', 'ņ' => 'n', 'ŉ' => 'n',
        'ŋ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'oe',
        '&ouml;' => 'oe', 'ø' => 'o', 'ō' => 'o', 'ő' => 'o', 'ŏ' => 'o', 'œ' => 'oe',
        'ŕ' => 'r', 'ř' => 'r', 'ŗ' => 'r', 'š' => 's', 'ù' => 'u', 'ú' => 'u',
        'û' => 'u', 'ü' => 'ue', 'ū' => 'u', '&uuml;' => 'ue', 'ů' => 'u', 'ű' => 'u',
        'ŭ' => 'u', 'ũ' => 'u', 'ų' => 'u', 'ŵ' => 'w', 'ý' => 'y', 'ÿ' => 'y',
        'ŷ' => 'y', 'ž' => 'z', 'ż' => 'z', 'ź' => 'z', 'þ' => 't', 'ß' => 'ss',
        'ſ' => 'ss', 'ый' => 'iy', 'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G',
        'Д' => 'D', 'Е' => 'E', 'Ё' => 'YO', 'Ж' => 'ZH', 'З' => 'Z', 'И' => 'I',
        'Й' => 'Y', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N', 'О' => 'O',
        'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T', 'У' => 'U', 'Ф' => 'F',
        'Х' => 'H', 'Ц' => 'C', 'Ч' => 'CH', 'Ш' => 'SH', 'Щ' => 'SCH', 'Ъ' => '',
        'Ы' => 'Y', 'Ь' => '', 'Э' => 'E', 'Ю' => 'YU', 'Я' => 'YA', 'а' => 'a',
        'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'yo',
        'ж' => 'zh', 'з' => 'z', 'и' => 'i', 'й' => 'y', 'к' => 'k', 'л' => 'l',
        'м' => 'm', 'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's',
        'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c', 'ч' => 'ch',
        'ш' => 'sh', 'щ' => 'sch', 'ъ' => '', 'ы' => 'y', 'ь' => '', 'э' => 'e',
        'ю' => 'yu', 'я' => 'ya'
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
