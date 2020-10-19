<?php  
namespace app\index\controller;
use app\common\controller\Base;
use think\facade\Request;
use app\index\model\Forum as ForumModel;
use think\facade\Session;
use think\Db;
class Forum extends Base{
	public function index(){
		$bqb = Db::table('b_bqb')->select();
		$forum = ForumModel::alias('a')
							->join('b_user b','a.user_id = b.id','left')
							->field('a.*,b.name,b.avatar')
							->order('create_time desc')
							->all();
		for($i =0; $i<sizeof($forum); $i++){
			$left = preg_replace('/\[/mis','<img style="width:20px;height:20px;margin:0 2px;" src="/static/images/bqb/',$forum[$i]['content']);
			$forum[$i]['content'] = preg_replace('/\]/mis','.png" />',$left);
			if($forum[$i]['type'] == 2){
				$forum[$i]['url'] = explode(",",$forum[$i]['url']);
			}
		}
		$this->assign('bqb',$bqb);
		$this->assign('forum',$forum);
		return $this->view->fetch('forum');
	}
	public function insert(){
		if($this->checkLogin()){
			$data = Request::post();
			$file = Request::file();
			if(empty($file) && $data['content'] == ""){
				$this->error('不能为空!');
			}
			$paths = "";
			if(!empty($file)){
				if(array_keys($file)[0] == 'video'){
					//键名为video时进行视频上传
					$data['type'] = 3;
					$info = $file[array_keys($file)[0]]->validate([
			                'size'=>'52428800',
			                'ext'=>'ogg,mpeg4,webm,mp4',
			            ])->move('uploads/forum/video');
					if(!$info){
						$this->error($file[array_keys($file)[0]]->getError());
					}
			        $paths = '/uploads/forum/video/'.$info->getSaveName().',';
			        $data['url'] = substr($paths,0,strlen($paths)-1);
				}else{
					//否则为图片上传
					$data['type'] = 2;
					foreach ($file as $key => $value) {
						$info = $file[$key]->validate([
			                    'size'=>'2000000',
			                    'ext'=>'jpeg,jpg,png,gif,jfif',
			                ])->move('uploads/forum/images');
						if(!$info){
							 $this->error($file[$key]->getError());
						}
			        	$paths .= '/uploads/forum/images/'.$info->getSaveName().',';
			        	$data['url'] = substr($paths,0,strlen($paths)-1);
					};
				}
				
			}
			if(ForumModel::create($data)){
				$this->success('发布成功!');
			}else{
				$this->error('发送失败!');
			}
		}else{
			$this->error('客官您请先登录!');
		}
	}
}
?>