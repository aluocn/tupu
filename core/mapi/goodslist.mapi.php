<?php
class goodslistMapi
{
	public function run()
	{
		global $_FANWE;	
		
		$root = array();
		$root['return'] = 0;
		
		$uid = (int)$_FANWE['requestData']['uid'];
		if($uid > 0)
		{
			if(!FS('User')->getUserExists($uid))
				$uid = 0;
		}

		if($uid == 0)
		{
			$uid = $_FANWE['uid'];
			$root['home_user'] = $_FANWE['user'];
		}

		if($uid == 0)
		{
			$root['info'] = "请选择要查看的会员";
			m_display($root);
		}

		if(!isset($root['home_user']))
		{
			$root['home_user'] = FS("User")->getUserById($uid);
			unset($root['home_user']['user_name_match'],$root['home_user']['password'],$root['home_user']['active_hash'],$root['home_user']['reset_hash']);
			$root['home_user']['user_avatar'] = avatar($uid,'m',$root['home_user']['server_code'],1,true);
		}

		$page = (int)$_FANWE['requestData']['page'];
		$page = max(1,$page);
		$is_spare_flow = (int)$_FANWE['requestData']['is_spare_flow'];
		$img_size = 200;
		$scale = 2;
		if($is_spare_flow == 1)
		{
			$img_size = 100;
			$scale = 1;
		}

		$total = FDB::resultFirst('SELECT COUNT(goods_id) FROM '.FDB::table('share_goods').' WHERE uid = '.$uid);
		$page_size = PAGE_SIZE;
		$page_total = max(1,ceil($total/$page_size));
		if($page > $page_total)
			$page = $page_total;
		$limit = (($page - 1) * $page_size).",".$page_size;
		
		$goods_list = array();
		$res = FDB::query('SELECT goods_id,share_id,img,name 
			FROM '.FDB::table('share_goods').' 
			WHERE uid = '.$uid.' ORDER BY goods_id DESC LIMIT '.$limit);
		while($goods = FDB::fetch($res))
		{
			$goods['img'] = getImgName($goods['img'],$img_size,$img_size,1,true);
			$goods['height'] = round($img_size / $scale);
			$goods['url'] = FU('note/g',array('sid'=>$goods['share_id'],'id'=>$goods['goods_id']),true);
			$goods_list[] = $goods;
		}

		$root['return'] = 1;
		$root['item'] = $goods_list;
		$root['page'] = array("page"=>$page,"page_total"=>$page_total);
		m_display($root);
	}
}
?>