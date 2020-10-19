<?php  
namespace app\index\controller;
use app\common\controller\Base;
class Test extends Base{
	
    public function index(){
		$html = $this->get('http://dfcdm.com/tv/51347/');
		preg_match('/<ul class="urlli"><div id="stab_1_71"><ul>(.*?) <\/ul><\/div><\/ul>/mis', $html, $match);
        $area = $match[1];
        preg_match_all('/<li><a href="(.*?)">(.*?)<\/a><\/li>/s',$area, $list);
        $result = [];
        for($i = 0; $i< sizeof($list[2]);$i++){
        	$result[$i]['title'] = $list[2][$i];
        	$result[$i]['url'] = $list[1][$i];
        }
        $this->assign('list',$result);
        return $this->view->fetch();
	}
	public function detail(){
		$html = $this->get('https://weibo.com/');
		halt($html);
		preg_match('/<video .*?><\/video>/mis', $html, $match);
		halt($match);
	}
}
?>