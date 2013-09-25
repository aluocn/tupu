<?php
	global $_FANWE;
	
	$page_num = intval($_FANWE['setting']['share_search_page'])?intval($_FANWE['setting']['share_search_page']):30;
	$is_root = false;
	
	$img_width = $_FANWE['request']['width'];
	$kwy_word = $_FANWE['request']['kwy_word'];
	
	if(!empty($kwy_word))
	{
		$match_key = segmentToUnicode($kwy_word,'+');
		$share_condition = " WHERE match(album_title_match) against('".$match_key."' IN BOOLEAN MODE) ";
	}
	
	$page = intval($_REQUEST['p']);
	$limit = (($page - 1)*$page_num).","."$page_num";
	$next_limit = ($page *$page_num).","."1";
	
	
	
	$sql = 'SELECT *
			FROM '.FDB::table("album").$share_condition."
			ORDER BY id DESC LIMIT ".$limit;
	
	
	$next_sql = 'SELECT *
			FROM '.FDB::table("album").$share_condition."
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
				$img_sql = "select sp.img as img from ".FDB::table("share_photo")." as sp left join ".FDB::table("album_share")." as als on als.share_id = sp.share_id left join ".FDB::table("album")." as a on a.id = als.album_id where a.id = ".$v['id'];
						
				$res_img = FDB::query($img_sql);
				$photo_data = array();
				while($img_data = FDB::fetch($res_img))
				{
					$photo_data[] = $img_data['img'];
				}
				$album_list[$k]['url'] = FU('album/show',array('id'=>$v['id'])); 
				$album_list[$k]['is_follow_album'] = FS('album')->getIsFollow($v['id'],$_FANWE['uid']);
				$album_list[$k]['isMe'] = $v['uid'] == $_FANWE['uid'] ? 1:0;
				$args = array(
					'album_imgs'=>&$photo_data,
					'album_url'	=>&$album_list[$k]['url'],
				);
				
				$album_list[$k]['imgs'] = tplFetch('services/album/album_img',$args);
				   
		}
		
		outputJson(array('result'=>$album_list,'current_user'=>$current_user,'status'=>1,'hasNextPage'=>$is_next));
	}
	else {
		outputJson(array('status'=>0,'hasNextPage'=>$is_next));
	}
	
?>