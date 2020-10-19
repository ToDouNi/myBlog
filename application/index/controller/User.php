<?php  
namespace app\index\controller;
use app\common\controller\Base;
use think\facade\Request;
use app\index\model\User as UserModel;
use think\facade\Session;
use think\Db;
class User extends Base{
	public function register(){
		return $this->view->fetch();
	}
	public function login(){
		return $this->view->fetch();
	}
	public function loginSave(){
		if(Request::isAjax()){
			$data = Request::post();
			$rule = [
				'mobile|手机号'=>'require|mobile',
				'password|密码'=>'require|alphaNum'
			];
			$res = $this->validate($data,$rule);
			if(true !== $res){
				return ['code'=> 0,'msg'=>$res];
			}else{
				$result = UserModel::get(function($qurey) use ($data){
					$qurey->where('password',sha1($data['password']))
						  ->where('mobile',$data['mobile']);
				});
				if(null == $result){
					return ['code'=>0,'msg'=>'登录失败,手机号或者密码不正确'];
				}else{
					Session::set('user_id',$result->id);
					Session::set('user_name',$result->name);
					Session::set('user_avatar',$result->avatar);
					return ['code'=>1,'msg'=>'登录成功'];
				}
			}
		}else{
			return ['code'=>0,'msg'=>'请求类型错误'];
		}
	}
	public function unLogin(){
		Session::clear();
		$this->success('退出登录成功','index/index/index');
	}
	public function registerSave(){
		if(Request::isAjax()){
			$data = Request::post();
			$rule = 'app\common\validate\User';
			$res = $this->validate($data,$rule);
			if(true !== $res){
				return ['code'=>0,'msg'=>$res];
			}else{
				if($user = UserModel::create($data)){
					Session::set('user_id',$user['id']);
					Session::set('user_name',$user['name']);
					return ['code'=>1,'msg'=>'注册成功'];
				}else{
					return ['code'=>0,'msg'=>'注册失败'];
				}
			}
		}else{
			return ['code'=>0,'msg'=>'请求类型错误'];
		}
	}
	public function uploadAvatar(){
		$data = Request::post();
		if(empty($data['user_id'])){
			return ['code'=>0,'msg'=>'用户未登录!'];
		}
		if($data['src']){
			$oldImg = UserModel::field('avatar')->where('id',$data['user_id'])->find();
			$base64_image = str_replace(' ', '+', $data['src']);
		    if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image, $result)){
		        $image_name = uniqid().'.'.$result[2];
		        $image_file = "./uploads/avatar/{$image_name}";
		        if (file_put_contents($image_file, base64_decode(str_replace($result[1], '', $base64_image)))){
		        	if(UserModel::update(['avatar'=>"/uploads/avatar/{$image_name}",'id'=>$data['user_id']])){
						unlink('.'.$oldImg['avatar']);
		        		Session::set('user_avatar',"/uploads/avatar/{$image_name}");
		            	return ['code'=>1,'msg'=>'头像上传成功','data'=>$image_file];
					}
		        }else{
		            return ['code'=>0,'msg'=>'获取文件名失败'];
		        }
		    }else{
		        return ['code'=>0,'msg'=>'校验错误'];
		    }
		}else{
			return ['code'=>0,'msg'=>'获取文件路径失败'];
		}
	}
	public function userSave(){
		$data = Request::post();
		if(Request::ispost()){
			if($data){
				if(UserModel::update($data)){
					$this->success('保存成功');
				}else{
					$this->error('信息更新失败,请联系管理员');
				}
			}else{
				$this->error('数据异常');
			}
		}
	}
	public function personal(){
		$type = Request::param('type');
		if(isset($type)){
			switch ($type) {
				case 1:
					$userinfo = UserModel::where('id',Session::get('user_id'))
										->field('id,name,email,avatar,sign,mobile,create_time,label,medal,skill')
										->find();
					$label = Db::table('b_label')->field('color,text')->where('id','in',$userinfo->label)->select();
					$medal = Db::table('b_medal')->field('code,desc')->where('id','in',$userinfo->medal)->select();
					$skill = Db::table('b_skill')->field('code,desc')->where('id','in',$userinfo->skill)->select();
					$this->view->assign('userinfo',$userinfo);
					$this->view->assign('label',$label);
					$this->view->assign('medal',$medal);
					$this->view->assign('skill',$skill);
					break;
				case 2:
					$userinfo = UserModel::where('id',Session::get('user_id'))
										->field('name,avatar,sign,medal')
										->find();
					$emptyMedal = Db::table('b_medal')->field('code,desc')->where('id','not in',$userinfo->medal)->select();
					$hasMedal = Db::table('b_medal')->field('code,desc')->where('id','in',$userinfo->medal)->select();	
					$this->view->assign('userinfo',$userinfo);
					$this->view->assign('emptyMedal',$emptyMedal);
					$this->view->assign('hasMedal',$hasMedal);
					break;
				case 3:
					# code...
					break;	
				default:
					# code...
					break;
			}
			return $this->view->fetch('personal-'.$type);
		}
	}
}
?>