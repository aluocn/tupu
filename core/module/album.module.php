<?php
class AlbumModule
{
	public function index()
	{
		global $_FANWE;
		
		//专辑flash
		$flash_album = FS("Album")->getFlashAlbums(3);
		
		//推荐专辑
		$best_album = FS("Album")->getBestAlbums(6);
		
		//最新专辑作者
		$new_users = FS("Album")->getNewUsers(6);
		
		//最热专辑作者
		$hot_users = FS("Album")->getHotUsers(6);
		
		$page_args = array();
		$sort = $_FANWE['request']['sort'];
		
		switch($sort)
		{
			case 'new':
				$page_args['sort'] = 'new';
				$order = " ORDER BY share_id DESC";
			break;
			default:
				$sort = 'hot';
				$page_args['sort'] = 'hot';
				$order = " ORDER BY collect_count DESC";
			break;
		}
		
		$sql = 'SELECT COUNT(DISTINCT share_id) FROM '.FDB::table('album_share');
		$count = FDB::resultFirst($sql);
		$share_list = array();
		$share_display = array();
		
		if($count > 0)
		{
			$pager = buildPage('album/'.ACTION_NAME,$page_args,$count,$_FANWE['page'],40);
			$share_list = FDB::fetchAll('SELECT * FROM '.FDB::table('share').'  
				WHERE type = \'album_item\''.$order.' LIMIT '.$pager['limit']);
			$share_list = FS('Share')->getShareDetailList($share_list,false,false,false,true,2);
						
			$col = 4;
			$index = 0;
			$share_display = array();
			foreach($share_list as $share)
			{
				$share['empty_content'] = sprintf(lang('album','rel_album_empty_content'),$share['title']);
				$mod = $index % $col;
				$share_display['col'.$mod][] = $share;
				$index++;
			}
		}
		
		include template('page/album/album_index');
		display();
	}
	
	public function show()
	{
		global $_FANWE;
		$id = (int)$_FANWE['request']['id'];
		if(!$id)
			exit;
		$album_url = FU('album/show',array('id'=>$id)); 
		
		$album = FS("Album")->getAlbumById($id);
		$_FANWE['nav_title'] = $album['title'];
		$is_follow_album = FS('album')->getIsFollow($id,$_FANWE['uid']);
		
		if(empty($album))
			fHeader("location: ".FU('album'));
			
		$home_uid = $album['uid'];
		$home_user = FS("User")->getUserById($home_uid);
		
		$is_show_follow = false;
		if($home_uid != $_FANWE['uid'])
		{
			if(!FS('User')->getIsFollowUId($home_uid))
				$is_show_follow = true;
		}
		
		
		$is_manage_album = false;
		if($_FANWE['uid'] == $album['uid'])
			$is_manage_album = true;
			
		$sql = 'SELECT * FROM '.FDB::table('album').' 
						WHERE uid = '.$home_uid.' and id <> '.$id.' ORDER BY id DESC ';
					
		$res = FDB::query($sql);
		while($data = FDB::fetch($res))
		{
			$data['imgs'] = array();
			if(!empty($data['cache_data']))
			{
				$cache_data = unserialize($data['cache_data']);
				$data['imgs'] = $cache_data['imgs'];
				unset($data['cache_data']);
			}
			$data['img_count'] = count($data['imgs']);
			$data['url'] = FU('album/show',array('id'=>$data['id']));
			$album_list[] = $data;
		}
		
	
	$page_num = intval($_FANWE['setting']['share_self_page'])?intval($_FANWE['setting']['share_self_page']):30;
	
	$is_root = false;
	
	$img_width = 190;
	
	
	$limit = $page_num;
	
	
	$share_ids = array();
	
	$res = FDB::query('SELECT share_id FROM '.FDB::table('album_share').' 
				WHERE album_id = '.$id.' ORDER BY share_id DESC ');
	
	while($data = FDB::fetch($res))
	{
		$share_ids[] = $data['share_id'];
	}
	$share_ids = implode(',',$share_ids);
	if($share_ids)
	{
		$sql = 'SELECT *
			FROM '.FDB::table("share").'
			WHERE share_data <> '." 'default' and share_id IN (".$share_ids.") 
			ORDER BY share_id DESC LIMIT ".$limit;
	
		$share_list = FDB::fetchAll($sql);
	}
	
	if($share_list)
	{
		$share_list = FS('Share')->getShareDetailList($share_list,false,false,false,true,2);
		$list = array();
		$current_user = array();
		$current_user['u_url'] = FU('u/index',array('uid'=>$_FANWE['uid']));
		$current_user['avt'] = avatar($_FANWE['uid'], 's', $is_src = 1);
		$i = 0;
		
		foreach($share_list as $k => $v)
		{
		
		$img_info = FDB::fetchFirst("select text,type,img,img_width,img_height,is_animate,video,server_code from ".FDB::table("share_photo")." where share_id = ".$v['share_id']);
		if($img_info['img']){
			if($img_info['is_animate']==1){
				$img=$img_info['img'];
			}
			else{
				$img=getImgName($img_info['img'],$img_width,999,0,$img_info['server_code']);	
			}
			$is_video=0;
			$vedio_url='';
			if(!empty($img_info['video'])){
				$is_video=1;
				$vedio_url=$img_info['video'];
			}
			$list[$i] = $v;
			$list[$i]['is_video'] =$is_video;
			$list[$i]['video'] =$vedio_url;
			
			
			if($img_width < $img_info['img_width']){
						$list[$i]['height'] = $img_info['img_height'] * ($img_width / $img_info['img_width']);	
			}else{
				$list[$i]['height'] = $img_info['img_height'];
			}
			
			/*获取play图标的位置*/
			$list[$i]['video_style_top']=($list[$i]['height']-33)/2;
			$list[$i]['video_style_right']=80;
			
			$list[$i]['share_img_org'] = $img_info['img'];
			if($img_width < $img_info['img_width']){
				$list[$i]['width'] = $img_width;
			}else{
				$list[$i]['width'] = $img_info['img_width'];	
			}
			$list[$i]['share_img'] = $img;
			$list[$i]['no_img'] = 0;
			$list[$i]['type'] = $img_info['type'];
			
		}else{
			$list[$i]['no_img'] = 1;//没有图片的时候
			$list[$i]['text'] = $img_info['text'];
			$list[$i]['content'] = $v['content'];
			$list[$i]['share_id'] = $v['share_id'];
			$list[$i]['type'] = $img_info['type'];
			$list[$i]['collect_count'] = $v['collect_count'];
			$list[$i]['comment_count'] = $v['comment_count'];
			$list[$i]['relay_count'] = $v['relay_count'];
		}
		
		
		$list[$i]['avt'] = avatar($v['uid'], 's', $is_src = 1);
		
		
		
		
		$parent_id = FDB::resultFirst("select parent_id from ".FDB::table("share")." where share_id = ".$v['share_id']);
		if($parent_id == 0)
		{
			$list[$i]['isOriginal'] = 1;
		}
		else 
		{
			$list[$i]['isOriginal'] = 0;
		}
		$list[$i]['likeStatus'] = FS('Share')->getIsCollectByUid($v['share_id'],$_FANWE['uid'])?1:0;
		$list[$i]['isMe'] = $v['uid'] == $_FANWE['uid']?1:0;
		//分享评论
		$share_comments = FS('Share')->getShareCommentList($v['share_id'],'0,3');
		if($share_comments)
		{
			$share_comments_data = array();
			$idxj = 0;
			foreach($share_comments as $vv)
			{
				$share_comments_data[$idxj]['comment_id'] = $vv['comment_id'];
				$share_comments_data[$idxj]['parent_id'] = $vv['parent_id'];
				$share_comments_data[$idxj]['user_url'] = FU('u/index',array('uid'=>$vv['user']['uid']));
				$share_comments_data[$idxj]['user_name'] = $vv['user']['user_name'];
				$share_comments_data[$idxj]['avt'] = avatar($vv['uid'], 's', $is_src = 1);
				$share_comments_data[$idxj]['comment'] = cutStr($vv['content'],20);
				$idxj++;
			}
			$list[$i]['comments'] = $share_comments_data;
		}
		
		$list[$i]['share_url'] = FU('note/index',array('sid'=>$v['share_id']));
		$list[$i]['u_url'] = FU('u/index',array('uid'=>$v['uid']));
		$list[$i]['relay_count'] = FDB::resultFirst("select relay_count from ".FDB::table("share")." where share_id = ".$v['share_id']);
		$album_sql = "select a.id,a.title from  ".FDB::table('album_share')." as ah left join ".FDB::table('album')." as a on ah.album_id = a.id where ah.share_id = ".$v['share_id'];
		$album = FDB::fetchFirst($album_sql);
		if($album)
		{
			$list[$i]['album_title'] = $album['title'];
			$list[$i]['is_album'] = 1;
			$list[$i]['album_url'] = FU("album/show",array('id'=>$album['id']));
		}
		else {
			$list[$i]['album_title'] = "";
			$list[$i]['is_album'] = 0;
			$list[$i]['album_url'] = "";
		}
		
		$list[$i]['user_name'] = FDB::resultFirst("select user_name from ".FDB::table('user')." where uid =".$v['uid']);
		$i++;
		}
	}
	if($list)
		$share_detail = $list[0];
		$link_url = $_FANWE['site_url']."services/service.php?m=user&a=album_share&width=190&p=2&home_uid=".$home_uid."&album_id=".$id;
		$json_url = $_FANWE['site_url']."services/service.php?m=user&a=album_share&width=190&home_uid=".$home_uid."&album_id=".$id;
		
		include template('page/album/album_show');
		display();
	}

	public function category()
	{
		global $_FANWE;
		$id = (int)$_FANWE['request']['id'];
		if(!$id)
			fHeader("location: ".FU('album'));
			
		if(!isset($_FANWE['cache']['albums']['category'][$id]))
			fHeader("location: ".FU('album'));
		
		$album_cate = $_FANWE['cache']['albums']['category'][$id];
		
		$_FANWE['nav_title'] = $album_cate['name'].$_FANWE['nav_title'];
		
		$page_args = array();
		$page_args['id'] = $id;
		
		$sort = $_FANWE['request']['sort'];
		
		switch($sort)
		{
			case 'new':
				$page_args['sort'] = 'new';
				$order = " ORDER BY s.share_id DESC";
			break;
			default:
				$sort = 'hot';
				$page_args['sort'] = 'hot';
				$order = " ORDER BY s.collect_count DESC";
			break;
		}
		
		$where = ' WHERE a.cid = '.$id;
		
		$sql = 'SELECT COUNT(DISTINCT share_id) FROM '.FDB::table('album_share').' AS a '.$where;
		$count = FDB::resultFirst($sql);
		$share_list = array();
		$share_display = array();
		
		if($count > 0)
		{
			$pager = buildPage('album/'.ACTION_NAME,$page_args,$count,$_FANWE['page'],40);
			$share_list = FDB::fetchAll('SELECT * FROM '.FDB::table('album_share').' AS a 
				INNER JOIN '.FDB::table('share').' AS s ON s.share_id = a.share_id '.$where.' 
				AND s.type = \'album_item\''.$order.' LIMIT '.$pager['limit']);
			$share_list = FS('Share')->getShareDetailList($share_list,false,false,false,true,2);
			
			$col = 4;
			$index = 0;
			$share_display = array();
			foreach($share_list as $share)
			{
				$share['empty_content'] = sprintf(lang('album','rel_album_empty_content'),$share['title']);
				$mod = $index % $col;
				$share_display['col'.$mod][] = $share;
				$index++;
			}
		}
		
		include template('page/album/album_category');
		display();
	}
	
	public function tag()
	{
		global $_FANWE;
		global $_FANWE;
		$tag = trim($_FANWE['request']['tag']);
		if(empty($tag))
			fHeader("location: ".FU('album'));
	}

	public function create()
	{
		global $_FANWE;
		if($_FANWE['uid'] == 0)
			fHeader("location: ".FU('user/login'));
		
		include template('page/album/album_create');
		display();
	}
	
	public function edit()
	{
		global $_FANWE;
		if($_FANWE['uid'] == 0)
			fHeader("location: ".FU('user/login'));
			
		$id = (int)$_FANWE['request']['id'];
		if(!$id)
			fHeader("location: ".FU('album'));
			
		$album = FS("Album")->getAlbumById($id);
		if(empty($album) || $album['uid'] != $_FANWE['uid'])
			fHeader("location: ".FU('album'));
		
		$album['tags'] = implode(' ',$album['tags']);
		include template('page/album/album_edit');
		display();
	}

	public function save()
	{
		global $_FANWE;
		
		if($_FANWE['uid'] == 0)
			fHeader("location: ".FU('user/login'));
			
		$id = (int)$_FANWE['request']['id'];
		if($id > 0)
		{
			$album = FS("Album")->getAlbumById($id);
			if(empty($album) || $album['uid'] != $_FANWE['uid'])
				fHeader("location: ".FU('album'));
		}
			
		$data = array(
			'title'        => trim($_FANWE['request']['title']),
			'content'      => trim($_FANWE['request']['content']),
			'cid'          => (int)$_FANWE['request']['cid'],
			'show_type'    => (int)$_FANWE['request']['show_type'],
			'tags'         => trim($_FANWE['request']['tags']),
		);
		
		$vservice = FS('Validate');
		$validate = array(
			array('title','required',lang('album','name_require')),
			array('title','max_length',lang('album','name_max'),60),
			array('content','max_length',lang('album','content_max'),1000),
			array('cid','min',lang('album','cid_min'),1),
			array('show_type','min',lang('album','show_type_min'),1),
		);
		
		if(!$vservice->validation($validate,$data))
			exit($vservice->getError());
		
		if(!isset($_FANWE['cache']['albums']['category'][$data['cid']]))
			exit;

		if(!checkIpOperation("add_share",SHARE_INTERVAL_TIME))
		{
			showError('提交失败',lang('share','interval_tips'),-1);
		}
		
		$check_result = FS('Share')->checkWord($_FANWE['request']['title'],'title');
		if($check_result['error_code'] == 1)
		{
			showError('提交失败',$check_result['error_msg'],-1);
		}
		
		$check_result = FS('Share')->checkWord($_FANWE['request']['content'],'content');
		if($check_result['error_code'] == 1)
		{
			showError('提交失败',$check_result['error_msg'],-1);
		}

		$check_result = FS('Share')->checkWord($_FANWE['request']['tags'],'tag');
		if($check_result['error_code'] == 1)
		{
			showError('提交失败',$check_result['error_msg'],-1);
		}
		
		$tags = str_replace('***','',$_FANWE['request']['tags']);
		$tags = str_replace('　',' ',$tags);
		$tags = explode(' ',$tags);
		$tags = array_unique($tags);
		if(count($tags) > $_FANWE['cache']['albums']['setting']['album_tag_count'])
			exit;
		
		if($id > 0)
		{
			$data['title'] = htmlspecialchars($_FANWE['request']['title']);
			$data['content'] = htmlspecialchars($_FANWE['request']['content']);
			$data['tags'] = implode(' ',$tags);
			FDB::update('album',$data,'id = '.$id);
			FS('Share')->updateShare($album['share_id'],$data['title'],$data['content']);
			FS("Album")->saveTags($id,$tags);
			
			if($data['cid'] != $album['cid'])
			{
				FDB::query('UPDATE '.FDB::table("album_share").' SET cid = '.$data['cid'].' WHERE album_id = '.$id);
			}
			$url = FU('album/show',array('id'=>$id));
			fHeader('location: '.$url);
			exit;
		}
		
		
		$_FANWE['request']['uid'] = $_FANWE['uid'];
		$_FANWE['request']['type'] = 'album';
		$share = FS('Share')->submit($_FANWE['request']);
		
		if($share['status'])
		{
			$data['title'] = htmlspecialchars($_FANWE['request']['title']);
			$data['content'] = htmlspecialchars($_FANWE['request']['content']);
			$data['tags'] = implode(' ',$tags);
			$data['uid'] = $_FANWE['uid'];
			$data['share_id'] = $share['share_id'];
			$data['create_day'] = getTodayTime();
			$data['create_time'] = TIME_UTC;
			
			$aid = FDB::insert('album',$data,true);
			
			FS("Album")->saveTags($aid,$tags);
			
			FDB::query('UPDATE '.FDB::table('share').' SET rec_id = '.$aid.' 
				WHERE share_id = '.$share['share_id']);
			FDB::query("update ".FDB::table("user_count")." set albums = albums + 1 where uid = ".$_FANWE['uid']);
			FS('Medal')->runAuto($_FANWE['uid'],'albums');
			
			$url = FU('album/show',array('id'=>$aid));
			fHeader('location: '.$url);
		}
		else
			showError('提交失败','添加数据失败',-1);
	}
}
?>