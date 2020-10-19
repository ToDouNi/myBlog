<?php  
namespace app\index\controller;
use app\common\controller\Base;
class Video extends Base
{
	public function index(){
		return $this->view->fetch('video');
	}
		
}
?>