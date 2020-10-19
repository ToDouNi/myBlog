<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

/**
* 去除所有html标签
* @param string $content 要解析的内容
* return string
*/
if(!function_exists('unHtml')){//
	function unHtml($content){
		$search = array ("'<script[^>]*?>.*?</script>'si", // 去掉 javascript
					"'<[\/\!]*?[^<>]*?>'si", // 去掉 HTML 标记
					"'([\r\n])[\s]+'", // 去掉空白字符
					"'&(quot|#34);'i", // 替换 HTML 实体
					"'&(amp|#38);'i",
					"'&(lt|#60);'i",
					"'&(gt|#62);'i",
					"'&(nbsp|#160);'i"
					);
		$replace = array ("","","\\1","\"","&","<",">"," ");
		$content = preg_replace($search, $replace, $content);
		return $content;
	}
}

/**
* 系统加密方法
* @param string $data 要加密的字符串
* @param string $key 加密密钥
* @param int $expire 过期时间 单位 秒
* return string
*/

if(!function_exists('encrypt')){
	function encrypt($data, $key = '', $expire = 0) {
		$key = md5(empty($key) ? config('DATA_AUTH_KEY') : $key);
		$data = base64_encode($data);
		$x = 0;
		$len = strlen($data);
		$l = strlen($key);
		$char = '';
		for ($i = 0; $i < $len; $i++) {
		if ($x == $l) $x = 0;
		$char .= substr($key, $x, 1);
		$x++;
		}
		$str = sprintf('%010d', $expire ? $expire + time():0);
		for ($i = 0; $i < $len; $i++) {
		$str .= chr(ord(substr($data, $i, 1)) + (ord(substr($char, $i, 1)))%256);
		}
		return str_replace(array('+','/','='),array('-','_',''),base64_encode($str));
	}
}

/**
* 时间描述
* @param string $date Y-m-d H:i:s 格式
* return string
*/
if(!function_exists('date_desc')){
	function date_desc($date){
		$Y = substr($date,0,4);
		$m = substr($date,5,2);
		$d = substr($date,8,2);
		$H = substr($date,11,2);
		$i = substr($date,14,2);
		$s = substr($date,17,2);
		if(date("Y")>(int)$Y){
			return $date;
		}else if(date("m")>(int)$m){
			return (date("m") - (int)$m).'月前'; 
		}else if(date("d")>(int)$d){
			return (date("d") - (int)$d).'天前'; 
		}else if(date("H")>(int)$H){
			return (date("H") - (int)$H).'小时前'; 
		}else if(date("i")>(int)$i){
			return (date("i") - (int)$i).'分钟前';
		}else{
			return '刚刚';
		}
	}
}
