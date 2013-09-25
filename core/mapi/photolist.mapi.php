<?php
class photolistMapi
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
			$root['home_user']['user_avatar'] = avatar_url($uid,'m',$root['home_user']['server_code'],1,true);
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

		$total = FDB::resultFirst('SELECT COUNT(photo_id) FROM '.FDB::table('share_photo').' WHERE uid = '.$uid);
		$page_size = 20;//PAGE_SIZE;
		$page_total = max(1,ceil($total/$page_size));
		if($page > $page_total)
			$page = $page_total;
		$limit = (($page - 1) * $page_size).",".$page_size;
		
		$photo_list = array();
		$res = FDB::query('SELECT photo_id,share_id,img 
			FROM '.FDB::table('share_photo').' 
			WHERE uid = '.$uid.' and img !="" ORDER BY photo_id DESC LIMIT '.$limit);
		while($photo = FDB::fetch($res))
		{
			$photo['img'] =  FS("Image")->getImageUrl(getImgName($photo['img'],$img_size,$img_size,1,$photo['server_code']),2);
			$photo['height'] = round($img_size / $scale);
			$photo['url'] = FU('note/m',array('sid'=>$photo['share_id'],'id'=>$photo['photo_id']),true);
			if(!empty($photo['img'])){
				$photo_list[] = $photo;
			}
			
		}

		$root['return'] = 1;
		$root['item'] = $photo_list;
		$root['page'] = array("page"=>$page,"page_total"=>$page_total);
		m_display($root);
	}
}
?>