<?php  
namespace app\common\controller;
use think\Controller;
use think\facade\Session;
/**
 * 
 */
class Base extends Controller
{   
	public function checkLogin(){
		if(!Session::has('user_id')){
			return false;
		}else{
			return true;
		}
	}
	public function get($url)
    {
        //初使化curl
        $ch = curl_init();
        //请求的url，由形参传入
        curl_setopt($ch, CURLOPT_URL, $url);
        //将得到的数据返回
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //不验证证书下同
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        //解决冲定向  
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);   
        //不处理头信息
        curl_setopt($ch, CURLOPT_HEADER, 0);
        //连接超过10秒超时
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        //执行curl
        $output = curl_exec($ch);
        //关闭资源
        curl_close($ch);
        $res=mb_convert_encoding($output, 'UTF-8', 'UTF-8,GBK,GB2312,BIG5');
        //返回内容
        return $res;
    }
}	
?>