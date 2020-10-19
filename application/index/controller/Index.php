<?php
namespace app\index\controller;
use app\common\controller\Base;
use think\facade\Request;
use app\index\model\ChatRoom;
use think\facade\Session;
class Index extends Base
{
    public function index()
    {
        $this->assign('articleList',$this->article());
        $this->assign('chatRoomList',$this->chatRoom());
        return $this->fetch('index');
    }
    public function chatRoom(){
        $chat_room = ChatRoom::alias('a')
                            ->join('b_user b','a.owner = b.id','left')
                            ->field('a.*,b.name,b.avatar')
                            ->order('create_time asc')
                            ->all();
        return $chat_room;
    }
    public function insertChat(){
        $data = Request::post();
        $data['owner'] = Session::get('user_id');
        if($data = ChatRoom::create($data)){
           return ['code'=>1,'msg'=>'插入成功','data'=>$data]; 
        }else{
           return ['code'=>0,'msg'=>'插入失败'];
        }
        
    }
    public function searchChat(){
        $id = Request::get('id');
        if(isset($id)){
            $result = ChatRoom::where('id','>',$id)->order('create_time asc')->select();
            if($result){
                return ['code'=>1,'msg'=>'获取消息列表成功','data'=>$result];
            }else{
                return ['code'=>0,'msg'=>'获取消息列表失败'];
            }
        }
        
    }
    public function article(){
        $html = $this->get('http://www.todayhot.com.cn/');
        if($html == ""){
            $article = [];
            return $article;
        }
        //获取主页列表
        preg_match('/<ul id="alist" class="xlmm-sx">(.*?)<\/ul>/mis', $html, $match);
        // halt($html);
        $area = $match[0];
        preg_match_all('/<li class="item">(.*?)<\/li>/s',$area, $find);
        // halt($find);
        for ($n = 0; $n < sizeof($find[0]); $n++) {
            //获取作者
            preg_match_all('/<a.*?class="lbtn source" target="_blank">&nbsp;(.*?)&nbsp;<\/a>/', $area, $author);
            if(($author[1][$n] == "广告君")){
                continue;
            }else{
                $article[$n]['author'] = isset($author[1][$n])?$author[1][$n]:'';
            }
            //获取标题和url
            preg_match_all('/<a href="(.*?)" target="_blank" class="link">(.*?)<\/a>/', $area, $utitle);
            $article[$n]['url'] = isset($utitle[1][$n])?trim(strrchr($utitle[1][$n], '/'),'/'):'';
            $article[$n]['title'] = isset($utitle[2][$n])?$utitle[2][$n]:'';
            //获取时间
            preg_match_all('/<span class="lbtn">&nbsp;(.*?)<\/span>/', $area, $time);
            $article[$n]['time'] = isset($time[1][$n])?$time[1][$n]:'';
            //获取标签
            preg_match_all('/<a.*?class="lbtn tag tag-bg-other">(.*?)<\/a>/', $area, $tag);
            $article[$n]['tag'] = isset($tag[1][$n])?$tag[1][$n]:'';
            //取出图片
             preg_match_all('/<img.*?src="(data\/.*?)" class="xlmmlazy">/is', $find[0][$n], $image);
            $article[$n]['image'] = $image[1];
            for($i=0;$i<sizeof($article[$n]['image']);$i++){
                $article[$n]['image'][$i] = 'http://www.todayhot.com.cn/'. $article[$n]['image'][$i];
            }
        }
        // halt($article);
        return $article;
    }
    public function detail(){
        $url = Request::param('url');
        if(isset($url)){
            $html = $this->get('http://www.todayhot.com.cn/info/'.$url.'.html');
            // halt($html);
            //获取标题
            preg_match('/<h1 class="ph">(.*?)<\/h1>/mis',$html,$title);
            $this->assign('title',$title[1]);
            //获取摘要
            preg_match('/<p class="ellipsis">(.*?)<\/p>/mis',$html,$zy);
            $this->assign('zy',$zy[1]);
            //获取日期和作者
            preg_match('/<p class="xg1">(.*?)<span.*?<a.*?>(.*?)<\/a>/mis',$html,$towdata);
            $this->assign('date',isset($towdata[1])?$towdata[1]:'');
            $this->assign('author',isset($towdata[2])?$towdata[2]:'');
            //内容校验
            $preUrl = 'http://www.todayhot.com.cn';
            preg_match('/<table cellpadding="0" cellspacing="0" class="vwtb">(.*?)<\/table>/mis', $html, $content);
            $content = preg_replace('#src="data/([^"]+?)"#','src="'.$preUrl.'/data/$1"',$content[0]);
            $content = preg_replace('#<a href="data/([^"]+?)"#','<a href="'.$preUrl.'/data/$1"',$content);
            $this->assign('content',$content);
            return $this->fetch('detail');
        }else{
            return $this->error('没有获取到url','index/index/index');
        }
    }
    
}
