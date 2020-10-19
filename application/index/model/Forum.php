<?php  
namespace app\index\model;
use think\Model;
class Forum extends Model{
	protected $pk = 'id';
	protected $table = 'b_forum';
	protected $autoWriteTimestamp = true; //自动时间戳
	protected $createTime = 'create_time';//声明字段名
	protected $dateFormat = 'Y-m-d H:i:s';//设置时间查询出来的格式
	protected $insert = ['create_time','status'=>1];
}
?>