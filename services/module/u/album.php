<?php
	global $_FANWE;
	
	$is_root = false;
	
	$img_width = $_FANWE['request']['width'];
	$home_id = $_FANWE['request']['home_id'];
	
	$page = intval($_REQUEST['p']);
	$page_num = intval($_FANWE['setting']['share_self_page'])?intval($_FANWE['setting']['share_self_page']):30;
	
	$limit = (($page - 1)*$page_num).","."$page_num";
	$next_limit = ($page *$page_num).","."1";
	
	
	
	$sql = 'SELECT *
			FROM '.FDB::table("album")." where uid =".$home_id." 
			ORDER BY id DESC LIMIT ".$limit;
	
	
	$next_sql = 'SELECT *
			FROM '.FDB::table("album").$share_condition." where uid =".$home_id." 
			ORDER BY id DESC LIMIT ".$next_limit;
	
	$hasNextPage = FDB::fetchAll($next_sql);
	if($hasNextPage)
	{
		$is_next = 1;
	}
	else 
	{
		$is_next = 0;
	}
	$album_list = FDB::fetchAll($sql);
	
	if($album_list)
	{
		$list = array();
		$current_user = array();
		$current_user['u_url'] = FU('u/index',array('uid'=>$_FANWE['uid']));
		$current_user['avt'] = avatar($_FANWE['uid'], 's', $is_src = 1);
		
		foreach($album_list as $k => $v)
		{
                $album_list[$k]['imgs'] = array();
				$img_sql = "select sp.img as img,sp.server_code from ".FDB::table("share_photo")." as sp left join ".FDB::table("album_share")." as als on als.share_id = sp.share_id left join ".FDB::table("album")." as a on a.id = als.album_id where a.id = ".$v['id'];
						
				$res_img = FDB::query($img_sql);
				$photo_data = array();
				while($img_data = FDB::fetch($res_img))
				{
					if($img_data['server_code']){
						if($img_data['animate']){
							$photo_data[]=FS('Image')->getImgUrl($img_data['img']);
						}else{
							$photo_data[]=getImgName($img_data['img'],60,60,1,$img_data['server_code']);
						}
					}else{
						if($img_data['is_animate']){
							$photo_data[]=$img_data['img'];
						}else{
							$photo_data[]=getImgName($img_data['img'],60,60,1,$img_data['server_code']);
						}
					}
				}
				$album_list[$k]['url'] = FU('album/show',array('id'=>$v['id'])); 
				$args = array(
					'album_imgs'=>&$photo_data,
					'album_url'	=>&$album_list[$k]['url'],
				);
				$album_list[$k]['isMe'] = $_FANWE['uid'] == $v['uid'] ? 1:0;
				
				$album_list[$k]['imgs'] = tplFetch('services/album/album_img',$args);
				   
		}
		outputJson(array('result'=>$album_list,'current_user'=>$current_user,'status'=>1,'hasNextPage'=>$is_next));
	}
	else {
		outputJson(array('status'=>0,'hasNextPage'=>$is_next));
	}
	
?>