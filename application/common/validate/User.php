<?php  
namespace app\common\validate;
use think\Validate;
class User extends Validate{
	protected $rule = [
		'name|用户名'=>'require|length:1,20',
		'mobile|手机号'=>'require|mobile|unique:b_user',
		'password|密码'=>'require|length:6,20|alphaNum|confirm',
	];
}
?>