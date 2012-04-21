﻿<?php
$URL_PREFIX="../p";
$URL_SUFFIX=".html";
$CMT_SUFFIX=".xml";
$LIST_SIZE=15;//how many lists per page
$mysqlpw="";

function seg($tag, $val){
	return "<".$tag.">".$val."</".$tag.">";
}

function mkdirs($dir)  
{  
	if(!is_dir($dir)){  
		if(!mkdirs(dirname($dir))){  
			return false;  
		}  
		if(!mkdir($dir,0777)){
			return false;
		}
	}
	return true;  
}
function randomkeys($length)
{
	$pattern='1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLOMNOPQRSTUVWXYZ';
	for($i=0;$i<$length;$i++)
	{
 		$key .= $pattern{mt_rand(0,35)};
	}
	return $key;
}

/* @param string $string 原文或者密文
* @param string $operation 操作(ENCODE | DECODE), 默认为 DECODE
* @param string $key 密钥
* @param int $expiry 密文有效期, 加密时候有效， 单位 秒，0 为永久有效
* @return string 处理后的 原文或者 经过 base64_encode 处理后的密文
* 可以在指定时间内加密还原字符串，超时无法还原 可做单点登录的token加密传输，临时密码等
* @example
*
* $a = authcode('abc', 'ENCODE', 'key');
* $b = authcode($a, 'DECODE', 'key');  // $b(abc)
*
* $a = authcode('abc', 'ENCODE', 'key', 3600);
* $b = authcode('abc', 'DECODE', 'key'); // 在一个小时内，$b(abc)，否则 $b 为空
*/
function authcode($string, $operation = 'DECODE', $key = '', $expiry = 3600) { 
	$ckey_length = 4;   
	// 随机密钥长度 取值 0-32;
	// 加入随机密钥，可以令密文无任何规律，即便是原文和密钥完全相同，加密结果也会每次不同，增大破解难度。
	// 取值越大，密文变动规律越大，密文变化 = 16 的 $ckey_length 次方
	// 当此值为 0 时，则不产生随机密钥
	
	$key = md5($key ? $key : 'default_key'); //这里可以填写默认key值
	$keya = md5(substr($key, 0, 16));
	$keyb = md5(substr($key, 16, 16));
	$keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';
	
	$cryptkey = $keya.md5($keya.$keyc);
	$key_length = strlen($cryptkey);
	
	$string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
	$string_length = strlen($string);
	
	$result = '';
	$box = range(0, 255);
	
	$rndkey = array();
	for($i = 0; $i <= 255; $i++) {
		$rndkey[$i] = ord($cryptkey[$i % $key_length]);
	}

	for($j = $i = 0; $i < 256; $i++) {
		$j = ($j + $box[$i] + $rndkey[$i]) % 256;
		$tmp = $box[$i];
		$box[$i] = $box[$j];
		$box[$j] = $tmp;
	}

	for($a = $j = $i = 0; $i < $string_length; $i++) {
		$a = ($a + 1) % 256;
		$j = ($j + $box[$a]) % 256;
		$tmp = $box[$a];
		$box[$a] = $box[$j];
		$box[$j] = $tmp;
		$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
	}

	if($operation == 'DECODE') {
		if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
			return substr($result, 26);
		} else {
			return '';
		}
	} else {
		return $keyc.str_replace('=', '', base64_encode($result));
	}

}

/*
 * Add score for a user
 */
function addScore($email,$incre){
	$sql="select * from userscores where user_email=\"$email\" limit 1";
	$query=mysql_query($sql);
	if($myrow=mysql_fetch_array($query)){
		$score=$myrow['score'];
		$sql="update userscores set score=($score+$incre) where user_email='$email'";//each post makes 5 scores
		$query=mysql_query($sql);
	}
}
?>
