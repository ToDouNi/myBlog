<?php
namespace app\index\controller;
use app\common\controller\Base;
use think\facade\Request;
use app\index\model\ChatRoom as ChatRoomModel;
use think\facade\Session;
class Chatroom extends Base
{
    public function index()
    {
        // $this->master = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        // socket_set_option($this->master,SOL_SOCKET,SO_REUSEADDR,1);
        // socket_bind($this->master,$host,$port);
        $this->assign('chatRoomList',$this->chatRoom());
        return $this->fetch('chatroom');
    }
    public function chatRoom(){
        $chat_room = ChatRoomModel::alias('a')
                            ->join('b_user b','a.owner = b.id','left')
                            ->field('a.*,b.name,b.avatar')
                            ->order('create_time asc')
                            ->all();
        return $chat_room;
    }
    public function insertChat(){
        $data = Request::post();
        $data['owner'] = Session::get('user_id');
        if($data = ChatRoomModel::create($data)){
           return ['code'=>1,'msg'=>'插入成功','data'=>$data]; 
        }else{
           return ['code'=>0,'msg'=>'插入失败'];
        }
        
    }
    public function searchChat(){
        $id = Request::get('id');
        if(isset($id)){
            $result = ChatRoomModel::where('id','>',$id)->order('create_time asc')->select();
            if($result){
                return ['code'=>1,'msg'=>'获取消息列表成功','data'=>$result];
            }else{
                return ['code'=>0,'msg'=>'获取消息列表失败'];
            }
        }
        
    }
}
