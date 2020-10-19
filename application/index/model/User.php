<?php  
namespace app\index\model;
use think\Model;
class User extends Model{
	protected $pk = 'id';
	protected $table = 'b_user';
	protected $autoWriteTimestamp = true;
	protected $createTime = 'create_time';//声明字段名

	//开启自动完成
	protected $auto = []; //无论是新增或者更新都要设置的字段
	//仅仅是新增的时候有效
	protected $insert = ['create_time','status'=>1,'is_admin'=>0];

	public function getStatusAttr($value){
		$status = ['1'=>'启用','0'=>'禁用'];
		return $status[$value];
	}

	//修改器-----将用户传入的密码进行加密 sha1加密
	public function setPasswordAttr($value){
		return sha1($value);
	}


}
?>