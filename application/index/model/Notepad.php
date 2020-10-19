<?php  
namespace app\index\model;
use think\Model;
class Notepad extends Model{
	protected $pk = "id";
	protected $table= "b_notepad";
	protected $autoWriteTimestamp = true; //自动时间戳
	protected $createTime = 'create_time';//声明字段名
	protected $updateTime = 'update_time';//声明字段名	
	protected $dateFormat = 'Y-m-d H:i';//设置时间查询出来的格式


	//开启自动完成
	protected $auto = []; //无论是新增或者更新都要设置的字段
	//仅仅是新增的时候有效
	protected $insert = ['create_time','status'=>1];
	//仅更新的时候设置
	protected $update = ['update_time'];	
}
?>