<?php  
namespace app\index\controller;
use app\common\controller\Base;
class Music extends Base{
	function index(){
		halt($this->musicList());
		return $this->fetch('music');
	}
	public function musicList(){
        $html = $this->get('https://music.163.com/#/song?id=1456730');
        return $html;
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
}
?>