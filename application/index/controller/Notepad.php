<?php  
namespace app\index\controller;
use app\common\controller\Base;
use think\facade\Request;
use app\index\model\Notepad as NotepadModel;
use think\facade\Session;
/**
 * 
 */
class Notepad extends Base
{
	public function index(){
		$notepadList = [];
		if(Session::has('user_id')){
			$notepadList = NotepadModel::order('update_time desc')
					   ->where('owner',Session::get('user_id'))
					   ->select();	
		}
		return $this->view->fetch('notepad',['notepadList'=>$notepadList]);
	}	
	public function detail(){
		$id = Request::post('id');
		$notepadDetail = NotepadModel::where('id',$id)->find();
		if(isset($notepadDetail)){
			return ['code'=> 1 ,'msg'=>'查询成功' ,'data'=>$notepadDetail];
		}else{
			return ['code'=> 0 ,'msg'=>'未查询到数据'];
		}
	}
	public function save(){
		if(!$this->checkLogin()){
			return ['code'=> -1,'msg'=> '客官还没登录呀！'];
			break;
		}
		$data = Request::post();
		$data['owner'] = Session::get('user_id');
		if($data['id']){
			$result = NotepadModel::update($data);
			if($result){
				return ['code'=> 1,'msg'=> '更新成功'];
			}else{
				return ['code'=> 0,'msg'=> '更新失败'];
			}
		}else{
			$result = NotepadModel::create($data);
			if($result){
				return ['code'=> 1,'msg'=> '新增成功'];
			}else{
				return ['code'=> 0,'msg'=> '新增失败'];
			}
		}
	}
	public function delete(){
		$id = Request::post('id');
		if(NotepadModel::destroy($id)){
			return ['code'=> 1,'msg'=> '删除成功'];
		}else{
			return ['code'=> 0,'msg'=> '删除失败'];
		}
	}
}
?>