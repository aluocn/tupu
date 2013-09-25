<?php
class UModule
{
	public function me()
	{
		global $_FANWE;
		Cache::getInstance()->loadCache('citys');
		$home_uid = $_FANWE['home_uid'];
		$home_user = FS('User')->getUserById($home_uid);

		$reside_province = $_FANWE['cache']['citys']['all'][$home_user['reside_province']]['name'];
		$reside_city = $_FANWE['cache']['citys']['all'][$home_user['reside_city']]['name'];

		$page_size = 30;

		$uids = array();
		$uids[] = $_FANWE['uid'];

		//获取我关注的会员编号
		$sql = 'SELECT uid
			FROM '.FDB::table('user_follow').'
			WHERE f_uid = '.$_FANWE['uid'];
		$res = FDB::query($sql);
		while($data = FDB::fetch($res))
		{
			$uids[] = $data['uid'];
		}
		
		$is_all = false;

		$sql = 'SELECT COUNT(share_id)
			FROM '.FDB::table("share").'
			WHERE uid IN ('.implode(',',$uids).')';
		
		$count = FDB::resultFirst($sql);

		if($count == 0)
		{
			$max_count = $page_size * 5;
			$is_all = true;
			$sql = 'SELECT COUNT(share_id) FROM '.FDB::table("share");
			$count = FDB::resultFirst($sql);
			if($count > $max_count)
				$count = $max_count;
		}

		$pager = buildPage('u/'.ACTION_NAME,array(),$count,$_FANWE['page'],$page_size);

		if($is_all)
			$sql = 'SELECT *
				FROM '.FDB::table("share").'
				ORDER BY share_id DESC LIMIT '.$pager['limit'];
		else
			$sql = 'SELECT *
				FROM '.FDB::table("share").'
				WHERE uid IN ('.implode(',',$uids).')
				ORDER BY share_id DESC LIMIT '.$pager['limit'];
		
		$share_list = FDB::fetchAll($sql);
		$share_list = FS('Share')->getShareDetailList($share_list,true,true,true);



		$hot_events = FS('Event')->getHotImgEvent(3);
		$today_daren = FS('Daren')->getIndexTodayDaren();
		//获取我关注的会员编号
		$uids = FS("User")->getUserFollowsCache($home_uid);
		
		//获取我关注的会员编号
		$fav_ids = FS('User')->getUserFans($home_uid,12);
		
		
		$page_num = intval($_FANWE['setting']['share_self_page'])?intval($_FANWE['setting']['share_self_page']):30;
		$is_root = false;
		
		$img_width = 190;
		
		$limit = $page_num;
		
		$sql = 'SELECT *
				FROM '.FDB::table("share").'
				WHERE uid = '.$home_uid.' and share_data <> '." 'default'
				ORDER BY share_id DESC LIMIT ".$limit;
		
		
		$share_list = FDB::fetchAll($sql);
		$scale = 1;
		
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
				
			$img = FDB::fetchFirst("select text,type,img,img_height,img_width,is_animate,video,server_code from ".FDB::table("share_photo")." where share_id = ".$v['share_id']);		
				
			$list[$i] = $v;
			
			if($img['img']){
				if($img['is_animate']){
					$img_url=$img['img'];
				}else{
					$img_url=getImgName($img['img'],$img_width,999,0,$img['server_code']);
				}
		 		
		
				$is_video=0;
				if(!empty($img['video'])){
					$is_video=1;
					$vedio_url=$img['video'];
				}
				$list[$i]['is_video'] =$is_video;
				$list[$i]['video'] =$vedio_url;
					
					$list[$i]['share_img'] = $img_url;
					
				if($img_width < $img['img_width']){
					$list[$i]['height'] = $img['img_height'] * ($img_width / $img['img_width']);	
				}else{
					$list[$i]['height'] = $img['img_height'];
				}
					
				$list[$i]['height'] = round($list[$i]['height'] / $scale);
					/*获取play图标的位置*/
				$list[$i]['video_style_top']=($list[$i]['height']-33)/2;
				$list[$i]['video_style_right']=(254-33)/2;
				
		 		$list[$i]['height'] = round($list[$i]['height'] / $scale);
				if($img_width < $img['img_width']){
					$list[$i]['width'] = $img_width;
				}else{
					$list[$i]['width'] = $img['img_width'];	
				}
				$list[$i]['no_img'] = 0;	
			}else{
				$list[$i]['no_img'] = 1;//没有图片的时候
				$list[$i]['text'] = $img['text'];
				$list[$i]['content'] = $v['content'];
				$list[$i]['share_id'] = $v['share_id'];
			}
			$list[$i]['type'] = $img['type'];
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
			$share_comments = FS('Share')->getShareCommentList($v['share_id'],'0,2');
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
	
		$link_url = $_FANWE['site_url']."services/service.php?m=user&a=shareSelf&width=190&p=2&home_uid=".$home_uid;
		$json_url = $_FANWE['site_url']."services/service.php?m=user&a=shareSelf&width=190&home_uid=".$home_uid;
		
		include template('page/u/u_me');
		display();
	}

	public function book()
	{
		global $_FANWE;
		$home_uid = $_FANWE['home_uid'];
		$home_user = FS('User')->getUserById($home_uid);
		Cache::getInstance()->loadCache('citys');
		$reside_province = $_FANWE['cache']['citys']['all'][$home_user['reside_province']]['name'];
		$reside_city = $_FANWE['cache']['citys']['all'][$home_user['reside_city']]['name'];

		$tags_sql = 'SELECT DISTINCT(st.tag_name)
			FROM '.FDB::table('share').' AS s
			INNER JOIN '.FDB::table('share_tags').' AS st ON st.share_id = s.share_id
			WHERE s.uid = '.$home_uid.' LIMIT 0,20';
		$focus_tags = FDB::fetchAll($tags_sql);

		$condition = " WHERE s.share_data in ('goods','photo','goods_photo') AND s.uid = ".$home_uid;
		$sql = 'SELECT COUNT(DISTINCT s.share_id) FROM '.FDB::table('share').' AS s '.$condition;
		$count=FDB::resultFirst($sql);

		$page_size = 43;
		$pageargs = array();
		$pageargs['uid'] = $home_uid;
		$pager = buildPage('u/'.ACTION_NAME,$pageargs,$count,$_FANWE['page'],$page_size,'',2);

		$sql = 'SELECT s.*
			FROM '.FDB::table('share').' as s '.$condition.' GROUP BY s.share_id LIMIT '.$pager['limit'];

		$share_list = FDB::fetchAll($sql);
		$share_list = FS('Share')->getShareDetailList($share_list,false,false,false,true,2);

		$is_show_follow = false;
		if($home_uid != $_FANWE['uid'])
		{
			if(!FS('User')->getIsFollowUId($home_uid))
				$is_show_follow = true;
		}

		$col = 4;
		$index = 0;
		$findex = 0;
		$share_display = array();
		foreach($share_list as $share)
		{
			if($findex < 3)
			{
				$share_display['col'.($findex + 1)][] = $share;
			}
			else
			{
				$mod = $index % $col;
				$share_display['col'.$mod][] = $share;
				$index++;
			}
			$findex++;
		}
		//获取我关注的会员编号
		$uids = FS("User")->getUserFollowsCache($home_uid);
		
		//获取我关注的会员编号
		$fav_ids = FS('User')->getUserFans($home_uid,12);
		include template('page/u/u_book');
		display();
	}

	public function talk()
	{
		global $_FANWE;
		Cache::getInstance()->loadCache('citys');
		
		$home_uid = $_FANWE['home_uid'];
		$home_user = FS('User')->getUserById($home_uid);
		
		$reside_province = $_FANWE['cache']['citys']['all'][$home_user['reside_province']]['name'];
		$reside_city = $_FANWE['cache']['citys']['all'][$home_user['reside_city']]['name'];

		$page_size = 30;

		$pageargs = array();
		$pageargs['uid'] = $home_uid;

		$sql = 'SELECT COUNT(share_id)
			FROM '.FDB::table("share").'
			WHERE uid = '.$home_uid;
		$count = FDB::resultFirst($sql);

		$pager = buildPage('u/'.ACTION_NAME,$pageargs,$count,$_FANWE['page'],$page_size);

		$sql = 'SELECT *
			FROM '.FDB::table("share").'
			WHERE uid = '.$home_uid.'
			ORDER BY share_id DESC LIMIT '.$pager['limit'];

		$share_list = FDB::fetchAll($sql);
		$share_list = FS('Share')->getShareDetailList($share_list,true,true,true);

		$hot_events = FS('Event')->getHotImgEvent(3);
		$today_daren = FS('Daren')->getIndexTodayDaren();
		//获取我关注的会员编号
		$uids = FS("User")->getUserFollowsCache($home_uid);
		
		//获取我关注的会员编号
		$fav_ids = FS('User')->getUserFans($home_uid,12);
		
		$is_show_follow = false;
		if($home_uid != $_FANWE['uid'])
		{
			if(!FS('User')->getIsFollowUId($home_uid))
				$is_show_follow = true;
		}
		
	
	$page_num = intval($_FANWE['setting']['share_self_page'])?intval($_FANWE['setting']['share_self_page']):30;
	$is_root = false;
	
	$img_width = 190;
	
	$page = intval($_REQUEST['p']);
	$limit = $page_num;
	
	$sql = 'SELECT *
			FROM '.FDB::table("share").'
			WHERE uid = '.$home_uid.' and share_data <> '." 'default'
			ORDER BY share_id DESC LIMIT ".$limit;
	
	
	$share_list = FDB::fetchAll($sql);
	$scale = 1;
	
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
		$list[$i] = $v;
		$img = FDB::fetchFirst("select text,img,img_height,img_width,is_animate,video,server_code from ".FDB::table("share_photo")." where share_id = ".$v['share_id']);
		
		if($img['img']){
			if($img['is_animate']){
				$img_url = $img['img'];
			}else{
				$img_url = getImgName($img['img'],$img_width,999,0,$img['server_code']);
			}
			
			$is_video=0;
			if(!empty($img['video'])){
				$is_video=1;
				$vedio_url=$img['video'];
			}
			$list[$i]['is_video'] =$is_video;
			$list[$i]['video'] =$vedio_url;
			
			$list[$i]['share_img'] = $img_url;
			
			
			if($img_width < $img['img_width']){
				$list[$i]['height'] = $img['img_height'] * ($img_width / $img['img_width']);	
			}else{
				$list[$i]['height'] = $img['img_height'];
			}
		
			
			/*获取play图标的位置*/
			$list[$i]['video_style_top']=($list[$i]['height']-33)/2;
			$list[$i]['video_style_right']=(254-33)/2;
			
			$list[$i]['height'] = round($list[$i]['height'] / $scale);
			
			if($img_width < $img['img_width']){
				$list[$i]['width'] = $img_width;
			}else{
				$list[$i]['width'] = $img['img_width'];	
			}
			$list[$i]['no_img'] = 0;
		}else{
			$list[$i]['no_img'] = 1;
			$list[$i]['text'] = $img['text'];
			$list[$i]['collect_count'] = $v['collect_count'];
			
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
		
		
		$link_url = $_FANWE['site_url']."services/service.php?m=user&a=shareself&width=190&p=2&home_uid=".$home_uid;
		$json_url = $_FANWE['site_url']."services/service.php?m=user&a=shareself&width=190&home_uid=".$home_uid;
			
		include template('page/u/u_me');
		display();
	}
	
	public function atme()
	{
		global $_FANWE;
		Cache::getInstance()->loadCache('citys');
		$home_uid = $_FANWE['home_uid'];
		$home_user = FS('User')->getUserById($home_uid);
		$type = $_FANWE['request']['type'];
		
		$reside_province = $_FANWE['cache']['citys']['all'][$home_user['reside_province']]['name'];
		$reside_city = $_FANWE['cache']['citys']['all'][$home_user['reside_city']]['name'];

		$page_size = 30;

		$pageargs = array();
		$pageargs['uid'] = $home_uid;
		$pageargs['type'] = $type;
		if($type =='faved')
		{
			if($_FANWE['uid']==$home_uid)
			{
				//将评论我的统计设置为0
				FDB::query("update ".FDB::table("user_notice")." set num=0 where `type`=2 and uid=".intval($_FANWE['uid']));
			}
			
			$sql = 'SELECT COUNT(s.share_id)
				FROM '.FDB::table("share").' AS s
				LEFT JOIN '.FDB::table("user_collect").' as uc on uc.c_uid=s.uid and uc.share_id=s.parent_id
				WHERE uc.uid = '.$home_uid.' and s.type=\'fav\' ';
			$count = FDB::resultFirst($sql);
		
			$pager = buildPage('u/'.ACTION_NAME,$pageargs,$count,$_FANWE['page'],$page_size);
		
			$sql = 'SELECT s.*
				FROM '.FDB::table("share").' AS s
				LEFT JOIN '.FDB::table("user_collect").' as uc on uc.c_uid=s.uid and uc.share_id=s.parent_id
				WHERE uc.uid = '.$home_uid.' and s.type=\'fav\'
				ORDER BY s.share_id DESC LIMIT '.$pager['limit'];
		
			$share_list = FDB::fetchAll($sql);
		}
		else
		{
			if($_FANWE['uid']==$home_uid)
			{
				//将评论我的统计设置为0
				FDB::query("update ".FDB::table("user_notice")." set num=0 where `type`=4 and uid=".intval($_FANWE['uid']));
			}
			
			$sql = 'SELECT COUNT(*)
				FROM '.FDB::table("share").' AS s
				LEFT JOIN '.FDB::table("atme").' as a on a.share_id=s.share_id
				WHERE a.uid = '.$home_uid.' and s.uid <> '.$home_uid.' and s.type<>\'fav\' ';
			$count = FDB::resultFirst($sql);
		
			$pager = buildPage('u/'.ACTION_NAME,$pageargs,$count,$_FANWE['page'],$page_size);
		
			$sql = 'SELECT s.*
				FROM '.FDB::table("share").' AS s
				LEFT JOIN '.FDB::table("atme").' as a on a.share_id=s.share_id
				WHERE a.uid = '.$home_uid.' and s.uid <> '.$home_uid.' and s.type<>\'fav\'
				ORDER BY s.share_id DESC LIMIT '.$pager['limit'];
		
			$share_list = FDB::fetchAll($sql);
		}
		$share_list = FS('Share')->getShareDetailList($share_list,true,true,true);
		
		$args = array('share_list'=>&$share_list,'pager'=>&$pager);
		$share_list_html = tplFetch("inc/u/u_share_list",$args);
		$hot_events = FS('Event')->getHotImgEvent(3);
		$today_daren = FS('Daren')->getIndexTodayDaren();
		
		//获取我关注的会员编号
		$uids = FS("User")->getUserFollowsCache($home_uid);
		
		//获取我关注的会员编号
		$fav_ids = FS('User')->getUserFans($home_uid,12);
		
		include template('page/u/u_me');
		display();
	}

	public function comments()
	{
		global $_FANWE;
		Cache::getInstance()->loadCache('citys');
		$home_uid = $_FANWE['home_uid'];
		$home_user = FS('User')->getUserById($home_uid);

		if($_FANWE['uid']==$home_uid)
		{
			//将评论我的统计设置为0
			FDB::query("update ".FDB::table("user_notice")." set num=0 where `type`=3 and uid=".intval($_FANWE['uid']));
		}
		$reside_province = $_FANWE['cache']['citys']['all'][$home_user['reside_province']]['name'];
		$reside_city = $_FANWE['cache']['citys']['all'][$home_user['reside_city']]['name'];

		$page_size = 30;

		$pageargs = array();
		$pageargs['uid'] = $home_uid;

		$sql = 'SELECT COUNT(sc.comment_id)
			FROM '.FDB::table("share").' AS s
			INNER JOIN '.FDB::table("share_comment").' AS sc ON sc.share_id = s.share_id
			WHERE s.uid = '.$home_uid;

		$count = FDB::resultFirst($sql);

		$pager = buildPage('u/'.ACTION_NAME,$pageargs,$count,$_FANWE['page'],$page_size);
		$comment_list = array();
		$sql = 'SELECT sc.*,s.content as scontent
			FROM '.FDB::table("share").' AS s
			INNER JOIN '.FDB::table("share_comment").' AS sc ON sc.share_id = s.share_id 
			WHERE s.uid = '.$home_uid.'
			ORDER BY comment_id DESC LIMIT '.$pager['limit'];
		$res = FDB::query($sql);
		while($data = FDB::fetch($res))
		{
			$data['time'] = getBeforeTimelag($data['create_time']);
			$data['url'] = FU('note/index',array('sid'=>$data['share_id']));
			$comment_list[] = $data;
		}

		$args = array('comment_list'=>&$comment_list,'pager'=>&$pager);
		$share_list_html = tplFetch("inc/u/u_comments_list",$args);

		$hot_events = FS('Event')->getHotImgEvent(3);
		
		$today_daren = FS('Daren')->getIndexTodayDaren();
		//获取我关注的会员编号
		$uids = FS("User")->getUserFollowsCache($home_uid);
		
		//获取我关注的会员编号
		$fav_ids = FS('User')->getUserFans($home_uid,12);
		include template('page/u/u_me');
		display();
	}

	public function all()
	{
		global $_FANWE;
		Cache::getInstance()->loadCache('citys');
		$home_uid = $_FANWE['home_uid'];
		$home_user = FS('User')->getUserById($home_uid);

		$reside_province = $_FANWE['cache']['citys']['all'][$home_user['reside_province']]['name'];
		$reside_city = $_FANWE['cache']['citys']['all'][$home_user['reside_city']]['name'];

		$page_size = 30;

		$max_count = $page_size * 5;
		$is_all = true;
		$sql = 'SELECT COUNT(share_id) FROM '.FDB::table("share");
		$count = FDB::resultFirst($sql);
		if($count > $max_count)
			$count = $max_count;

		$pager = buildPage('u/'.ACTION_NAME,array(),$count,$_FANWE['page'],$page_size);

		$sql = 'SELECT *
			FROM '.FDB::table("share").'
			ORDER BY share_id DESC LIMIT '.$pager['limit'];

		$share_list = FDB::fetchAll($sql);
		$share_list = FS('Share')->getShareDetailList($share_list,true,true,true);

		$args = array('share_list'=>&$share_list,'pager'=>&$pager);
		$share_list_html = tplFetch("inc/u/me_share_list",$args);

		$hot_events = FS('Event')->getHotImgEvent(3);
		$today_daren = FS('Daren')->getIndexTodayDaren();
		//获取我关注的会员编号
		$uids = FS("User")->getUserFollowsCache($home_uid);
		
		//获取我关注的会员编号
		$fav_ids = FS('User')->getUserFans($home_uid,12);
		include template('page/u/u_me');
		display();
	}

	public function fav()
	{
		global $_FANWE;
		Cache::getInstance()->loadCache('citys');
		$home_uid = $_FANWE['home_uid'];
		$home_user = FS('User')->getUserById($home_uid);
		$current_menu = 'fav';
		$reside_province = $_FANWE['cache']['citys']['all'][$home_user['reside_province']]['name'];
		$reside_city = $_FANWE['cache']['citys']['all'][$home_user['reside_city']]['name'];

		$page_size = 30;

		$pageargs = array();
		$pageargs['uid'] = $home_uid;

		$fav_share_ids = array();
		$sql = 'SELECT parent_id
			FROM '.FDB::table("share").'
			WHERE type = \'fav\' AND uid = '.$home_uid.'
			GROUP BY parent_id';

		$res = FDB::query($sql);
		while($data = FDB::fetch($res))
		{
			$fav_share_ids[] = $data['parent_id'];
		}

		$count = count($fav_share_ids);
		if($count > 0)
		{
			$sql = 'SELECT uid
				FROM '.FDB::table("share").'
				WHERE share_id '.FDB::createIN($fav_share_ids).'
				GROUP BY uid';
			$fav_uids = array();
			$res = FDB::query($sql);
			while($data = FDB::fetch($res))
			{
				$fav_uids[] = $data['uid'];
			}

			$fav_share_ids = array();
			$sql = 'SELECT parent_id
				FROM '.FDB::table("share").'
				WHERE type = \'fav\' AND uid '.FDB::createIN($fav_uids).'
				GROUP BY parent_id
				ORDER BY parent_id DESC LIMIT 0,100';
			$res = FDB::query($sql);
			while($data = FDB::fetch($res))
			{
				$fav_share_ids[] = $data['parent_id'];
			}

			$sql = 'SELECT *
				FROM '.FDB::table("share").'
				WHERE share_id '.FDB::createIN($fav_share_ids).'
					AND share_data IN (\'goods\',\'goods_photo\',\'photo\')
					AND uid <> '.$home_uid.'
				GROUP BY share_id ORDER BY share_id DESC LIMIT 0,10';
			$fav_list = FDB::fetchAll($sql);
			$fav_list = FS('Share')->getShareDetailList($fav_list);
		}

		$pager = buildPage('u/'.ACTION_NAME,$pageargs,$count,$_FANWE['page'],$page_size);

		$fav_share_ids = array();
		$sql = 'SELECT parent_id
			FROM '.FDB::table("share").'
			WHERE type = \'fav\' AND uid = '.$home_uid.'
			GROUP BY parent_id
			ORDER BY parent_id DESC LIMIT '.$pager['limit'];
        $res = FDB::query($sql);
		while($data = FDB::fetch($res))
		{
			$fav_share_ids[] = $data['parent_id'];
		}

		$share_list = array();
		if(count($fav_share_ids) > 0)
		{
			$sql = 'SELECT *
				FROM '.FDB::table("share").'
				WHERE share_id '.FDB::createIN($fav_share_ids).'
				GROUP BY share_id ORDER BY share_id DESC LIMIT 0,10';
			$share_list = FDB::fetchAll($sql);
			$share_list = FS('Share')->getShareDetailList($share_list,true,true,true);
		}

		$args = array('share_list'=>&$share_list,'pager'=>&$pager);
		$share_list_html = tplFetch("inc/u/u_share_list",$args);

		$hot_events = FS('Event')->getHotImgEvent(3);
		
		$today_daren = FS('Daren')->getIndexTodayDaren();
		//获取我关注的会员编号
		$uids = FS("User")->getUserFollowsCache($home_uid);
		
		//获取我关注的会员编号
		$fav_ids = FS('User')->getUserFans($home_uid,12);
		
		$is_show_follow = false;
		if($home_uid != $_FANWE['uid'])
		{
			if(!FS('User')->getIsFollowUId($home_uid))
				$is_show_follow = true;
		}
		
	$page_num = intval($_FANWE['setting']['share_self_page'])?intval($_FANWE['setting']['share_self_page']):30;
	$is_root = false;
	
	$img_width = 190;
	
	$page = intval($_REQUEST['p']);
	$limit = $page_num;
	
	$fav_share_ids = array();
	$sql = 'SELECT parent_id
			FROM '.FDB::table("share").'
			WHERE type = \'fav\' AND uid = '.$home_uid.'
			GROUP BY parent_id
			ORDER BY parent_id DESC LIMIT '.$limit;
	
	
    $res = FDB::query($sql);
	while($data = FDB::fetch($res))
	{
		$fav_share_ids[] = $data['parent_id'];
	}

	$share_list = array();
	if(count($fav_share_ids) > 0)
	{
			$sql = 'SELECT *
				FROM '.FDB::table("share").'
				WHERE share_id '.FDB::createIN($fav_share_ids).' and share_data <> '." 'default' 
				GROUP BY share_id ORDER BY share_id DESC LIMIT 0,10";
			$share_list = FDB::fetchAll($sql);
			$share_list = FS('Share')->getShareDetailList($share_list,true,true,true);
	}
	
	$share_list = FDB::fetchAll($sql);
	$scale = 1;
	
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
		$list[$i] = $v;
		$img = FDB::fetchFirst("select text,type,img,img_height,img_width,is_animate,video,server_code from ".FDB::table("share_photo")." where share_id = ".$v['share_id']);
		if($img['img']){
			if($img['is_animate']==1){
				$img_url=$img['img'];
			}
			else{	
				$img_url=getImgName($img['img'],$img_width,999,0,$img['server_code']);
			}
			$is_video=0;
			if(!empty($img['video'])){
				$is_video=1;
				$vedio_url=$img['video'];
			}
			$list[$i]['is_video'] =$is_video;
			$list[$i]['video'] =$vedio_url;
			
			$list[$i]['share_img'] = $img_url;
			
			if($img_width < $img['img_width']){
				$list[$i]['height'] = $img['img_height'] * ($img_width / $img['img_width']);	
			}else{
				$list[$i]['height'] = $img['img_height'];
			}
			/*获取play图标的位置*/
			$list[$i]['video_style_top']=($list[$i]['height']-33)/2;
			$list[$i]['video_style_right']=(254-33)/2;
			$list[$i]['height'] = round($list[$i]['height'] / $scale);
			if($img_width < $img['img_width']){
				$list[$i]['width'] = $img_width;
			}else{
				$list[$i]['width'] = $img['img_width'];	
			}
			$list[$i]['no_img'] = 0;
			$list[$i]['type'] = $img['type'];
		}else{
			$list[$i]['no_img'] = 1;//没有图片的时候
			$list[$i]['text'] = $img['text'];
			$list[$i]['content'] = $v['content'];
			$list[$i]['share_id'] = $v['share_id'];
			$list[$i]['type'] = $img['type'];
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
	
		$link_url = $_FANWE['site_url']."services/service.php?m=user&a=sharefav&width=190&p=2&home_uid=".$home_uid;
		$json_url = $_FANWE['site_url']."services/service.php?m=user&a=sharefav&width=190&home_uid=".$home_uid;
		
		include template('page/u/u_fav');
		display();
	}

	public function bao()
	{
		global $_FANWE;
		$home_uid = $_FANWE['home_uid'];
		$home_user = FS('User')->getUserById($home_uid);
		$user_names = FS('User')->getUserShowName($home_uid);

		$page_args = array(
			'uid'=>$home_uid
		);
		$current_menu = 'bao';
		$pager = buildPage('u/'.ACTION_NAME,$page_args,$home_user['goods'],$_FANWE['page'],36);

		$goods_list = array();
		$res = FDB::query('SELECT sg.goods_id,sg.share_id,sg.img,sg.name,sg.price,s.collect_count
			FROM '.FDB::table('share_goods').' AS sg
			INNER JOIN '.FDB::table("share").' AS s ON s.share_id = sg.share_id
			WHERE sg.uid = '.$home_uid.' ORDER BY sg.goods_id DESC LIMIT '.$pager['limit']);
		while($goods = FDB::fetch($res))
		{
			$goods['url'] = FU('note/g',array('sid'=>$goods['share_id'],'id'=>$goods['goods_id']));
			$goods_list[] = $goods;
		}

		include template('page/u/u_bao');
		display();
	}

	public function photo()
	{
		global $_FANWE;
		$home_uid = $_FANWE['home_uid'];
		$home_user = FS('User')->getUserById($home_uid);
		$user_names = FS('User')->getUserShowName($home_uid);

		$page_args = array(
			'uid'=>$home_uid
		);
		$current_menu = 'photo';
		$pager = buildPage('u/'.ACTION_NAME,$page_args,$home_user['photos'],$_FANWE['page'],36);
		$photo_list = array();
		$res = FDB::query('SELECT sp.photo_id,sp.share_id,sp.img,s.collect_count
			FROM '.FDB::table('share_photo').' AS sp
			INNER JOIN '.FDB::table("share").' AS s ON s.share_id = sp.share_id
			WHERE sp.uid = '.$home_uid.' ORDER BY sp.photo_id DESC LIMIT '.$pager['limit']);
		while($photo = FDB::fetch($res))
		{
			$photo['url'] = FU('note/m',array('sid'=>$photo['share_id'],'id'=>$photo['photo_id']));
			$photo_list[] = $photo;
		}

		include template('page/u/u_photo');
		display();
	}

	public function topic()
	{
		global $_FANWE;
		$home_uid = $_FANWE['home_uid'];
		$home_user = FS('User')->getUserById($home_uid);
		FanweService::instance()->cache->loadCache('forums');
		$current_menu = 'topic';
		$page_args = array(
			'uid'=>$home_uid
		);

		$pager = buildPage('u/'.ACTION_NAME,$page_args,$home_user['forums'],$_FANWE['page'],10);

		$thread_list = array();
		$res = FDB::query('SELECT ft.*,s.cache_data FROM '.FDB::table('forum_thread').' AS ft
			INNER JOIN '.FDB::table('share').' AS s ON s.share_id = ft.share_id
			WHERE ft.uid = '.$home_uid.' ORDER BY ft.tid DESC LIMIT '.$pager['limit']);
		while($thread = FDB::fetch($res))
		{
			$thread['cache_data'] = unserialize($thread['cache_data']);
			$thread['url'] = FU('club/detail',array('tid'=>$thread['tid']));
			$thread['time'] = getBeforeTimelag($thread['create_time']);
			$thread['cate'] = $_FANWE['cache']['forums']['all'][$thread['fid']];
			FS('Share')->shareImageFormat($thread);
            unset($thread['cache_data']);
			$thread_list[$thread['share_id']] = $thread;
		}

		include template('page/u/u_topic');
		display();
	}

	public function maybe()
	{
		global $_FANWE;
		$home_uid = $_FANWE['home_uid'];
		$home_user = FS('User')->getUserById($home_uid);
		$user_names = FS('User')->getUserShowName($home_uid);
		FanweService::instance()->cache->loadCache('forums');

		$page_args = array(
			'uid'=>$home_uid
		);
		$current_menu = 'topic';
		$follow_uids = array();
		$sql = 'SELECT uid
			FROM '.FDB::table('user_follow').'
			WHERE f_uid = '.$home_uid.'
			GROUP BY uid';
		$res = FDB::query($sql);
		while($data = FDB::fetch($res))
		{
			$follow_uids[] = $data['uid'];
		}

		if(count($follow_uids) > 0)
		{
			$sql = 'SELECT COUNT(tid)
				FROM '.FDB::table('forum_thread').'
				WHERE uid '.FDB::createIN($follow_uids);
			$count = FDB::resultFirst($sql);
			$pager = buildPage('u/'.ACTION_NAME,$page_args,$count,$_FANWE['page'],10);

			$thread_list = array();
			$res = FDB::query('SELECT ft.*,s.cache_data FROM '.FDB::table('forum_thread').' AS ft
				INNER JOIN '.FDB::table('share').' AS s ON s.share_id = ft.share_id
				WHERE ft.uid '.FDB::createIN($follow_uids).' ORDER BY ft.tid DESC LIMIT '.$pager['limit']);
			while($thread = FDB::fetch($res))
			{
				$thread['cache_data'] = unserialize($thread['cache_data']);
				$thread['url'] = FU('club/detail',array('tid'=>$thread['tid']));
				$thread['time'] = getBeforeTimelag($thread['create_time']);
				$thread['cate'] = $_FANWE['cache']['forums']['all'][$thread['fid']];
				FS('Share')->shareImageFormat($thread);
            	unset($thread['cache_data']);
				$thread_list[$thread['share_id']] = $thread;
			}
		}

		include template('page/u/u_topic');
		display();
	}

	public function ask()
	{
		global $_FANWE;
		$home_uid = $_FANWE['home_uid'];
		$home_user = FS('User')->getUserById($home_uid);
		FanweService::instance()->cache->loadCache('asks');

		$page_args = array(
			'uid'=>$home_uid
		);
		$current_menu = 'topic';
		$pager = buildPage('u/'.ACTION_NAME,$page_args,$home_user['ask'],$_FANWE['page'],10);

		$thread_list = array();
		$res = FDB::query('SELECT at.*,s.cache_data FROM '.FDB::table('ask_thread').' AS at
			INNER JOIN '.FDB::table('share').' AS s ON s.share_id = at.share_id
			WHERE at.uid = '.$home_uid.' ORDER BY at.tid DESC LIMIT '.$pager['limit']);
		while($thread = FDB::fetch($res))
		{
			$thread['cache_data'] = unserialize($thread['cache_data']);
			$thread['url'] = FU('ask/detail',array('tid'=>$thread['tid']));
			$thread['time'] = getBeforeTimelag($thread['create_time']);
			$thread['cate'] = $_FANWE['cache']['asks'][$thread['aid']];
			FS('Share')->shareImageFormat($thread);
            unset($thread['cache_data']);
			$thread_list[$thread['share_id']] = $thread;
		}

		include template('page/u/u_topic');
		display();
	}

	public function attention()
	{
		global $_FANWE;
		$home_uid = $_FANWE['home_uid'];
		$home_user = FS('User')->getUserById($home_uid);
		FanweService::instance()->cache->loadCache('asks');
		FanweService::instance()->cache->loadCache('forums');

		$page_args = array(
			'uid'=>$home_uid
		);
		$current_menu = 'topic';
		$share_datas = array();
		$count = FDB::resultFirst('SELECT COUNT(uid) FROM '.FDB::table('user_attention').' WHERE uid = '.$home_uid);
		$pager = buildPage('u/'.ACTION_NAME,$page_args,$count,$_FANWE['page'],10);

		$thread_list = array();
		$res = FDB::query('SELECT ua.rec_id,s.cache_data,ua.type,ua.share_id
			FROM '.FDB::table('user_attention').' AS ua
			INNER JOIN '.FDB::table('share').' AS s ON s.share_id = ua.share_id
			WHERE ua.uid = '.$home_uid.' ORDER BY ua.create_time DESC LIMIT '.$pager['limit']);
		while($attention = FDB::fetch($res))
		{
			if($attention['type'] == 'bar')
			{
				$sql = 'SELECT tid,fid,title,content,ft.uid,post_count,ft.create_time
					FROM '.FDB::table('forum_thread').' AS ft
					WHERE ft.tid = '.$attention['rec_id'];
			}
			else
			{
				$sql = 'SELECT tid,aid,title,content,at.uid,post_count,at.create_time
					FROM '.FDB::table('ask_thread').' AS at
					WHERE at.tid = '.$attention['rec_id'];
			}

			$thread = FDB::fetchFirst($sql);
			if($attention['type'] == 'bar')
			{
				$thread['url'] = FU('club/detail',array('tid'=>$thread['tid']));
				$thread['cate'] = $_FANWE['cache']['forums']['all'][$thread['fid']];
			}
			else
			{
				$thread['url'] = FU('ask/detail',array('tid'=>$thread['tid']));
				$thread['cate'] = $_FANWE['cache']['asks'][$thread['aid']];
			}
			
			$thread['time'] = getBeforeTimelag($thread['create_time']);
			$thread['cache_data'] = unserialize($thread['cache_data']);
			FS('Share')->shareImageFormat($thread);
            unset($thread['cache_data']);
			$thread_list[$attention['share_id']] = $thread;
		}

		include template('page/u/u_topic');
		display();
	}

	public function feed()
	{
		global $_FANWE;
		$home_uid = $_FANWE['home_uid'];
		$home_user = FS('User')->getUserById($home_uid);
		FanweService::instance()->cache->loadCache('asks');
		FanweService::instance()->cache->loadCache('forums');

		$page_args = array(
			'uid'=>$home_uid
		);
		$current_menu = 'topic';
		$count = FDB::resultFirst('SELECT COUNT(DISTINCT parent_id)
			FROM '.FDB::table('share').'
			WHERE type IN (\'bar_post\',\'ask_post\') AND uid = '.$home_uid);

		$pager = buildPage('u/'.ACTION_NAME,$page_args,$count,$_FANWE['page'],10);

		$thread_list = array();
		$res = FDB::query('SELECT s1.share_id,s.rec_id,s1.cache_data,s.type
			FROM '.FDB::table('share').' AS s
			INNER JOIN '.FDB::table('share').' AS s1 ON s1.share_id = s.parent_id
			WHERE s.type IN (\'bar_post\',\'ask_post\') AND s.uid = '.$home_uid.'
			GROUP BY s.parent_id
			ORDER BY s.parent_id DESC LIMIT '.$pager['limit']);

		while($data = FDB::fetch($res))
		{
			if($data['type'] == 'bar_post')
			{
				$sql = 'SELECT tid,fid,title,content,ft.uid,post_count,ft.create_time
					FROM '.FDB::table('forum_thread').' AS ft
					WHERE ft.tid = '.$data['rec_id'];
			}
			else
			{
				$sql = 'SELECT tid,aid,title,content,at.uid,post_count,at.create_time
					FROM '.FDB::table('ask_thread').' AS at
					WHERE at.tid = '.$data['rec_id'];
			}

			$thread = FDB::fetchFirst($sql);
			if($data['type'] == 'bar_post')
			{
				$thread['url'] = FU('club/detail',array('tid'=>$thread['tid']));
				$thread['cate'] = $_FANWE['cache']['forums']['all'][$thread['fid']];
			}
			else
			{
				$thread['url'] = FU('ask/detail',array('tid'=>$thread['tid']));
				$thread['cate'] = $_FANWE['cache']['asks'][$thread['aid']];
			}
			
			$thread['cache_data'] = unserialize($data['cache_data']);
			$thread['time'] = getBeforeTimelag($thread['create_time']);
			FS('Share')->shareImageFormat($thread);
            unset($thread['cache_data']);
			$thread_list[$data['share_id']] = $thread;
		}

		include template('page/u/u_topic');
		display();
	}

	public function follow()
	{
		global $_FANWE;
		$home_uid = $_FANWE['home_uid'];
		$home_user = FS('User')->getUserById($home_uid);

		$page_args = array(
			'uid'=>$home_uid
		);

		Cache::getInstance()->loadCache('citys');

		$reside_province = $_FANWE['cache']['citys']['all'][$home_user['reside_province']]['name'];
		$reside_city = $_FANWE['cache']['citys']['all'][$home_user['reside_city']]['name'];

		$count = FDB::resultFirst('SELECT COUNT(uid) FROM '.FDB::table('user_follow').' WHERE f_uid = '.$home_uid);
		$pager = buildPage('u/'.ACTION_NAME,$page_args,$count,$_FANWE['page'],10);

		$user_list = array();

		$res = FDB::query('SELECT u.uid,u.user_name,uc.fans,up.reside_province,up.reside_city
				FROM '.FDB::table('user_follow').' AS uf
				INNER JOIN '.FDB::table('user').' AS u ON u.uid = uf.uid
				INNER JOIN '.FDB::table('user_count').' AS uc ON uc.uid = uf.uid
				INNER JOIN '.FDB::table('user_profile').' AS up ON up.uid = uf.uid
				WHERE uf.f_uid = '.$home_uid.' ORDER BY uf.create_time DESC LIMIT '.$pager['limit']);
		while($data = FDB::fetch($res))
		{
			if($home_uid == $_FANWE['uid'])
				$data['is_follow'] = FS('User')->getIsFollowUId2($data['uid'],$home_uid);

			if($home_uid != $_FANWE['uid'])
				$data['is_follow'] = FS('User')->getIsFollowUId($data['uid']);

			$data['reside_province'] = $_FANWE['cache']['citys']['all'][$data['reside_province']]['name'];
			$data['reside_city'] = $_FANWE['cache']['citys']['all'][$data['reside_city']]['name'];

			$share_sql = 'SELECT share_id ,share_data
				FROM '.FDB::table('share').'
				WHERE uid = '.$data['uid'].' and share_data <> "default" 
				ORDER BY share_id DESC
				LIMIT 0,5';
			
			$share_res = FDB::query($share_sql);
			
			$share_detail = array();
			$i = 0;
			while($share_data = FDB::fetch($share_res))
			{
				$share_detail_photo = FS("Share")->getShareImage($share_data['share_id'],$share_data['share_data']);
				$share_detail[$i]['share_id'] = $share_data['share_id'];
				$share_detail[$i]['url'] = FU('note/index',array('sid'=>$share_data['share_id']));
				$share_detail[$i]['img'] = $share_detail_photo;
				
				$i++;
			}
			$data['share'] = $share_detail;

			$user_list[] = $data;
		}
		
		//获取我关注的会员编号
		$uids = FS("User")->getUserFollowsCache($home_uid);
		
		//获取我关注的会员编号
		$fav_ids = FS('User')->getUserFans($home_uid,12);
		
		$is_show_follow = false;
		if($home_uid != $_FANWE['uid'])
		{
			if(!FS('User')->getIsFollowUId($home_uid))
				$is_show_follow = true;
		}
		
		include template('page/u/u_follow');
		display();
	}
	
	public function friends()
	{
		global $_FANWE;
		$home_uid = $_FANWE['home_uid'];
		$home_user = FS('User')->getUserById($home_uid);
		
		if($_FANWE['uid']==$home_uid)
		{
			//将我的粉丝信息统计设置为0
			FDB::query("update ".FDB::table("user_notice")." set num=0 where `type`=1 and uid=".intval($_FANWE['uid']));
		}
		$page_args = array(
			'uid'=>$home_uid
		);

		Cache::getInstance()->loadCache('citys');

		$reside_province = $_FANWE['cache']['citys']['all'][$home_user['reside_province']]['name'];
		$reside_city = $_FANWE['cache']['citys']['all'][$home_user['reside_city']]['name'];

		$sql_friends = 'select count(u.uid) from '.FDB::table('user_follow').' as u left join '.FDB::table('user_follow').' as uf on u.uid = uf.f_uid where u.uid = '.$home_uid .' and uf.uid = u.f_uid';
		
		$count = FDB::resultFirst($sql_friends);
		$pager = buildPage('u/'.ACTION_NAME,$page_args,$count,$_FANWE['page'],20);

		$user_list = array();

		$res = FDB::query('SELECT u.uid,u.user_name,uc.fans,up.reside_province,up.reside_city
				FROM '.FDB::table('user_follow').' AS uf
				INNER JOIN '.FDB::table('user').' AS u ON u.uid = uf.f_uid
				INNER JOIN '.FDB::table('user_count').' AS uc ON uc.uid = uf.f_uid
				INNER JOIN '.FDB::table('user_profile').' AS up ON up.uid = uf.f_uid
				WHERE uf.uid = '.$home_uid.' ORDER BY uf.create_time DESC LIMIT '.$pager['limit']);
		while($data = FDB::fetch($res))
		{
			$data['is_follow'] = FS('User')->getIsFollowUId($data['uid']);
			if(!$data['is_follow'])
				continue;
			$data['reside_province'] = $_FANWE['cache']['citys']['all'][$data['reside_province']]['name'];
			$data['reside_city'] = $_FANWE['cache']['citys']['all'][$data['reside_city']]['name'];

			$share_sql = 'SELECT share_id ,share_data
				FROM '.FDB::table('share').'
				WHERE uid = '.$data['uid'].' and share_data <> "default" 
				ORDER BY share_id DESC
				LIMIT 0,5';
			$share_res = FDB::query($share_sql);
			
			$share_detail = array();
			$i = 0;
			while($share_data = FDB::fetch($share_res))
			{
				$share_detail_photo = FS("Share")->getShareImage($share_data['share_id'],$share_data['share_data']);
				$share_detail[$i]['share_id'] = $share_data['share_id'];
				$share_detail[$i]['url'] = FU('note/index',array('sid'=>$share_data['share_id']));
				$share_detail[$i]['img'] = $share_detail_photo;
				$i++;
			}
			$data['share'] = $share_detail;
			$user_list[] = $data;
		}
		
		//获取我关注的会员编号
		$uids = FS("User")->getUserFollowsCache($home_uid);
		
		//获取我关注的会员编号
		$fav_ids = FS('User')->getUserFans($home_uid,12);
		
		$is_show_follow = false;
		if($home_uid != $_FANWE['uid'])
		{
			if(!FS('User')->getIsFollowUId($home_uid))
				$is_show_follow = true;
		}
		
		include template('page/u/u_friends');
		display();
	}
	
	public function fans()
	{
		global $_FANWE;
		$home_uid = $_FANWE['home_uid'];
		$home_user = FS('User')->getUserById($home_uid);
		
		if($_FANWE['uid']==$home_uid)
		{
			//将我的粉丝信息统计设置为0
			FDB::query("update ".FDB::table("user_notice")." set num=0 where `type`=1 and uid=".intval($_FANWE['uid']));
		}
		$page_args = array(
			'uid'=>$home_uid
		);

		Cache::getInstance()->loadCache('citys');

		$reside_province = $_FANWE['cache']['citys']['all'][$home_user['reside_province']]['name'];
		$reside_city = $_FANWE['cache']['citys']['all'][$home_user['reside_city']]['name'];

		$count = FDB::resultFirst('SELECT COUNT(uid) FROM '.FDB::table('user_follow').' WHERE uid = '.$home_uid);
		$pager = buildPage('u/'.ACTION_NAME,$page_args,$count,$_FANWE['page'],20);

		$user_list = array();

		$res = FDB::query('SELECT u.uid,u.user_name,uc.fans,up.reside_province,up.reside_city
				FROM '.FDB::table('user_follow').' AS uf
				INNER JOIN '.FDB::table('user').' AS u ON u.uid = uf.f_uid
				INNER JOIN '.FDB::table('user_count').' AS uc ON uc.uid = uf.f_uid
				INNER JOIN '.FDB::table('user_profile').' AS up ON up.uid = uf.f_uid
				WHERE uf.uid = '.$home_uid.' ORDER BY uf.create_time DESC LIMIT '.$pager['limit']);
		while($data = FDB::fetch($res))
		{
			$data['is_follow'] = FS('User')->getIsFollowUId($data['uid']);

			$data['reside_province'] = $_FANWE['cache']['citys']['all'][$data['reside_province']]['name'];
			$data['reside_city'] = $_FANWE['cache']['citys']['all'][$data['reside_city']]['name'];

			$share_sql = 'SELECT share_id ,share_data
				FROM '.FDB::table('share').'
				WHERE uid = '.$data['uid'].' and share_data <> "default" 
				ORDER BY share_id DESC
				LIMIT 0,5';
			
			$share_res = FDB::query($share_sql);
			
			$share_detail = array();
			$i = 0;
			while($share_data = FDB::fetch($share_res))
			{
				$share_detail_photo = FS("Share")->getShareImage($share_data['share_id'],$share_data['share_data']);
				$share_detail[$i]['share_id'] = $share_data['share_id'];
				$share_detail[$i]['url'] = FU('note/index',array('sid'=>$share_data['share_id']));
				$share_detail[$i]['img'] = $share_detail_photo;
				$i++;
			}
			$data['share'] = $share_detail;

			$user_list[] = $data;
		}
		
		//获取我关注的会员编号
		$uids = FS("User")->getUserFollowsCache($home_uid);
		
		//获取我关注的会员编号
		$fav_ids = FS('User')->getUserFans($home_uid,12);
		
		$is_show_follow = false;
		if($home_uid != $_FANWE['uid'])
		{
			if(!FS('User')->getIsFollowUId($home_uid))
				$is_show_follow = true;
		}
		include template('page/u/u_fans');
		display();
	}

	public function message()
	{
		global $_FANWE;
		$home_uid = $_FANWE['uid'];
		$home_user = FS('User')->getUserById($home_uid);
		$current_menu = 'message';

		$count = FS('Message')->getMsgCount($_FANWE['uid']);
		$pager = buildPage('u/'.ACTION_NAME,array(),$count,$_FANWE['page'],10);

		$sys_msgs = FS('Message')->getSysMsgs($_FANWE['uid']);
		$sys_notices = FS('Notice')->getList($_FANWE['uid']);
		$msg_list = FS('Message')->getMsgList($_FANWE['uid'],$pager['limit']);
		
		FDB::query("DELETE FROM ".FDB::table('user_notice')." WHERE uid='$home_uid' AND type=5");
		
		//获取我关注的会员编号
		$uids = FS("User")->getUserFollowsCache($home_uid);
		
		//获取我关注的会员编号
		$fav_ids = FS('User')->getUserFans($home_uid,12);
		include template('page/u/u_message');
		display();
	}
	
	public function sendmsg()
	{
		global $_FANWE;
		$home_uid = $_FANWE['uid'];
		$home_user = FS('User')->getUserById($home_uid);
		$current_menu = 'message';

		$count = FDB::resultFirst('SELECT COUNT(uid) FROM '.FDB::table('user_follow').' WHERE uid = '.$home_uid);
		$pager = buildPage('',array(),$count,$_FANWE['page'],30);

		$user_list = FDB::fetchAll('SELECT u.uid,u.user_name
				FROM '.FDB::table('user_follow').' AS uf
				INNER JOIN '.FDB::table('user').' AS u ON u.uid = uf.f_uid
				WHERE uf.uid = '.$home_uid.' ORDER BY uf.create_time DESC LIMIT '.$pager['limit']);
		include template('page/u/u_sendmsg');
		display();
	}

	public function msgview()
	{
		global $_FANWE;
		$home_uid = $_FANWE['uid'];
		$home_user = FS('User')->getUserById($home_uid);
		$current_menu = 'message';

		$mlid = intval($_FANWE['request']['lid']);
		$mid = intval($_FANWE['request']['mid']);
		if($mlid == 0 && $mid == 0)
			fHeader("location: ".FU('u/message',array('uid'=>$_FANWE['uid'])));

		$pageargs = array();

		//获取我关注的会员编号
		$uids = FS("User")->getUserFollowsCache($home_uid);
		
		//获取我关注的会员编号
		$fav_ids = FS('User')->getUserFans($home_uid,12);
		
		if($mlid > 0)
		{
			$pageargs['lid'] = $mlid;
			$mlist = FS('Message')->getListByMlid($mlid,$_FANWE['uid']);

			if(empty($mlist))
				fHeader("location: ".FU('u/message',array('uid'=>$_FANWE['uid'])));

			$pager = buildPage('u/'.ACTION_NAME,$pageargs,$mlist['num'],$_FANWE['page'],10);
			$msg_list = FS('Message')->getMsgsByMlid($mlid,$_FANWE['uid'],$pager['limit']);
			include template('page/u/u_msgview');
		}
		elseif($mid)
		{
			$msg = FS('Message')->getSysMsgByMid($_FANWE['uid'],$mid);
			include template('page/u/u_smsgview');
		}
		
		
		display();
	}
	
	public function exchange()
	{
		global $_FANWE;
		$home_uid = $_FANWE['uid'];
		$home_user = FS('User')->getUserById($home_uid);
		$current_menu = 'exchange';

		$args = array();
		$where = " AND uid = ".$_FANWE['uid'];
		$goods_status = (int)$_FANWE['request']['status'];
		if($goods_status > 0)
		{
			$where.=" AND goods_status = ".($goods_status - 1);
			$args['status'] = $goods_status;
		}
		
		$count = FDB::resultFirst('SELECT COUNT(id) FROM '.FDB::table('order').' WHERE status < 2 '.$where);
		$pager = buildPage('u/exchange',$args,$count,$_FANWE['page'],15);

		$sql = 'SELECT * FROM '.FDB::table('order').' WHERE status < 2 '.$where.' GROUP BY id ORDER BY id DESC LIMIT '.$pager['limit'];

		$res = FDB::query($sql);
		$order_list = array();
		while($order = FDB::fetch($res))
		{
			$order['goods_status'] = lang('exchange','order_goods_status_'.$order['goods_status']);
			$order['create_time'] = fToDate($order['create_time']);
			$order_list[] = $order;
		}
		
		include template('page/u/u_exchange');
		display();
	}
	
	public function album()
	{
		global $_FANWE;
		$home_uid = $_FANWE['home_uid'];
		$home_user = FS('User')->getUserById($home_uid);
		$current_menu = 'album';
		
		
		//获取我关注的会员编号
		$uids = FS("User")->getUserFollowsCache($home_uid);
		
		//获取我关注的会员编号
		$fav_ids = FS('User')->getUserFans($home_uid,12);
		
		$is_show_follow = false;
		if($home_uid != $_FANWE['uid'])
		{
			if(!FS('User')->getIsFollowUId($home_uid))
				$is_show_follow = true;
		}
		
		$is_root = false;
		
		$img_width = 190;
	
	
		$page_num = intval($_FANWE['setting']['share_self_page'])?intval($_FANWE['setting']['share_self_page']):30;
		
		$limit = $page_num;
		
		
		
		$sql = 'SELECT *
				FROM '.FDB::table("album")." where uid =".$home_uid." 
				ORDER BY id DESC LIMIT ".$limit;
		
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
					$img_sql = "select sp.img,sp.is_animate,server_code from ".FDB::table("share_photo")." as sp left join ".FDB::table("album_share")." as als on als.share_id = sp.share_id left join ".FDB::table("album")." as a on a.id = als.album_id where a.id = ".$v['id'];
					$content_sql = "select s.share_id as ids from ".FDB::table("share_photo")." as sp left join ".FDB::table("album_share")." as als on als.share_id = sp.share_id left join ".FDB::table("share")." as s on s.share_id = als.share_id left join ".FDB::table("album")." as a on a.id = als.album_id where a.id = ".$v['id'];
					$res_ids = FDB::query($content_sql);
					$photo_content = array();
					while($data = FDB::fetch($res_ids))
					{
						$sql = "select content from ".FDB::table("share")." where share_id = ".$data['ids'];
						$photo_content1 = FDB::fetchFirst($sql);
						$photo_content[] = $photo_content1['content'];
					}
					$res_img = FDB::query($img_sql);
					$photo_data = array();
					$photo_animate=array();
					
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
						$photo_animate[]=$img_data['is_animate'];
					}
					$album_list[$k]['url'] = FU('album/show',array('id'=>$v['id'])); 
					
					$album_list[$k]['album_imgs_animate'] =  $photo_animate; 
					$album_list[$k]['album_imgs'] =  $photo_data;
					$album_list[$k]['content'] =  $photo_content; 
					
					$album_list[$k]['isMe'] = $_FANWE['uid'] == $v['uid'] ? 1:0;
					$album_list[$k]['is_follow_album'] = FS('album')->getIsFollow($v['id'],$_FANWE['uid']);
			}
			
		}
		$link_url = $_FANWE['site_url']."services/service.php?m=u&a=album&width=190&p=2&home_id=".$home_uid;
		$json_url = $_FANWE['site_url']."services/service.php?m=u&a=album&width=190&home_id=".$home_uid;
		
		include template('page/u/u_album');
		display();
	}
}
?>