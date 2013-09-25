<?php
class IndexModule
{
	public function index()
	{
		global $_FANWE;
		
		if($_REQUEST['hot']){
			$id = intval($_REQUEST['hot']);
			$sql = "select * from ".FDB::table('album_category')." where id =".$id;
			$nav = FDB::fetchFirst($sql);
			$_FANWE['nav_title'] = $nav['seo_title'];
			$_FANWE['seo_keywords'] = $nav['seo_keywords'];
			$_FANWE['seo_description'] = $nav['seo_desc'];
		}
		$sql = "select * from ".FDB::table('collect_cate');
		$collect_list = FDB::fetchAll($sql);
		$uname = $_FANWE['request']['sy'];
		if($uname && $uname != 'all'){
			$sql = "select * from ".FDB::table('collect_cate')." where uname ="."'$uname'";
			$list = FDB::fetchFirst($sql);
			$_FANWE['nav_title'] = $list['seo_title'];
			$_FANWE['seo_keywords'] = $list['seo_keywords'];
			$_FANWE['seo_description'] = $list['seo_desc'];
		}
		$login_modules = getLoginModuleList();
		$tqq = $login_modules['tqq'];
		$qq = $login_modules['qq'];
		$weibo = $login_modules['sina'];
		$taobao = $login_modules['taobao'];
		
		switch($_FANWE['request']['sy']){
			case 'hot':
			$_FANWE['request']['sort']='hot';
			break;
			case 'video':
			$_FANWE['request']['is_video']=1;
			break;
			case 'goods':
			$_FANWE['request']['is_video']=2;
			break;
			case 'pic':
			$_FANWE['request']['is_video']="pic";
			break;
			case 'paper':
			$_FANWE['request']['is_video']=3;
			break;
		}
		include template('page/index_index');
		display();
	}
}
?>