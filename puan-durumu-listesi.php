<?php

 $sonuc=puan_durumu_getir_icerik();
 header('Content-Type: application/json; charset=utf-8');
 echo $sonuc;
 function puan_durumu_file_update(){
	 $urls=array(
		'https://www.tff.org/default.aspx?pageID=198'=>array(
			'title'=>'Süper Lig',
			'key'=>'super_lig',
		),
		'https://www.tff.org/default.aspx?pageID=142'=>array(
			'title'=>'Trendyol 1. Lig',
			'key'=>'birinci_lig',
		),
	 );
	 if($urls){
		 $puan_tablosu=array();
		 foreach($urls as $url=>$val){
			 $content=file_get_contents($url);
			 $content = mb_convert_encoding($content, "UTF-8", "ISO-8859-9");
			 $kn=$content;
			 preg_match_all('@<table class="s-table">(.*?)></table>@si',$kn,$tablo);
			 if(isset($tablo[0][0])){
				$_tablo=$tablo[0][0];
				preg_match_all('@<tr>(.*?)</tr>@si',$_tablo,$_liste);
				if(isset($_liste[0][0])){
					foreach($_liste[0] as $satir){
						preg_match_all('@<td(.*?)>(.*?)</td>@si',$satir,$sütunlar);
						$_temp=array();
						if(isset($sütunlar[2][0])){
							if(count($sütunlar[2])>4){
								foreach($sütunlar[2] as $_stn){
									$_temp[]=puan_durumu_data_clear(strip_tags($_stn));
								}
							}
						}
						if($_temp){
							$puan_tablosu[$val['key']][]=$_temp;
						}
					}
				}
			 }

		 }
	 }
	 if($puan_tablosu){
		 $file='puan-durumlari.json';
		 $_datas=json_encode($puan_tablosu);
		 if($_datas){
			file_put_contents($file,$_datas );		
		 }		 
	 }	 
 }


 function puan_durumu_data_clear($des){
	 // Strip HTML Tags
$clear = strip_tags($des);
// Clean up things like &amp;
$clear = html_entity_decode($clear);
// Strip out any url-encoded stuff
$clear = urldecode($clear);
// Replace non-AlNum characters with space
//$clear = preg_replace('/[^A-Za-z0-9]/', ' ', $clear);
// Replace Multiple spaces with single space
$clear = preg_replace('/ +/', ' ', $clear);
// Trim the string of leading/trailing space
$clear = trim($clear);
return $clear;
 }
function puan_durumu_getir_icerik(){
     $file='puan-durumlari.json';
     if (file_exists($file)){
		 $dateXML = date("d-m-Y H:i:s", filemtime($file));
		 $todayEk = date('d-m-Y H:i:s', strtotime(' - 60 minutes '));
		 if (strtotime($todayEk) > strtotime($dateXML)){
			puan_durumu_file_update();
		 }else{
			 
		 }
     }else{
		 puan_durumu_file_update();
	 }	
	 $_datas=file_get_contents($file); 
     return $_datas;	 
}

function urlCekPuan ( $url ) {
   $curl = curl_init( $url );
   curl_setopt( $curl, CURLOPT_TIMEOUT, "50" );
   curl_setopt( $curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/534.30 (KHTML, like Gecko) Chrome/12.0.742.122 Safari/534.30" );
   curl_setopt( $curl, CURLOPT_RETURNTRANSFER, TRUE );
   curl_setopt( $curl, CURLOPT_HEADER, 0 );
   curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, FALSE );
   $curlResult = curl_exec($curl);
   curl_close($curl);
   return str_replace (array("\n","\t","\r"), null, $curlResult );
}