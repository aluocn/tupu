<?php
/**
 *  当前模板用到的相关函数
*/


function getHotCate()
{
			global $_FANWE;
			$args = array();
			FanweService::instance()->cache->loadCache('albums');
			//获取分类ID
			$category_id = urldecode($_FANWE['request']['hot']);
			$tag = $_FANWE['request']['tag'];
			$sort = $_FANWE['request']['sort'];
			
			
			
			$link_url = $_FANWE['site_url']."services/service.php?m=index&a=share&width=190&p=2";
			$json_url = $_FANWE['site_url']."services/service.php?m=index&a=share&width=190";
			//判断排序
			if($sort)
			{
				if($sort == 'hot')
				{
					$link_url .= "&sort=collect_count";	
					$json_url .= "&sort=collect_count";
				}	
			}
			//判断分类
			if($category_id)
			{
				$args['category_id'] = $category_id;
				$link_url .= "&cate=".$category_id;
				$json_url .= "&cate=".$category_id;
			}
			$is_video=$_FANWE['request']['is_video'];
			if($is_video){
				
				$link_url .= "&is_video=".$is_video;
				$json_url .= "&is_video=".$is_video;
				$is_video=($is_video=='pic')?0:$is_video;
				$condition .= " AND s.is_video = ".$is_video;
			}
			
			if($tag)
			{
				$link_url .= "&tag=".$tag;
				$json_url .= "&tag=".$tag;
			}
			
			
			$is_cate = false;
			
			$today_time = getTodayTime();
			//7天前的时间
			$day7_time = $today_time - 604800;
			$img_width = 190;
			$cate_id = $category_id;
			$sort_field = $_FANWE['request']['sort'];
			//每次显示的数目，默认为30
			$page_num = intval($_FANWE['setting']['share_index_page'])?intval($_FANWE['setting']['share_index_page']):30;
			
			
			//$is_cate = false;
			
			if($cate_id)
			{
				$is_cate = true;
			}
			//若有分类 ,则获取album_share 表中，相应album中相应的 share_id
			if($is_cate)
			{
				
				$condition .= " AND als.cid = ".$cate_id;
				$join_sql = ' LEFT JOIN '.FDB::table('album_share').' AS als ON als.share_id = s.share_id ';
			}
			//audit_index 表示首页时候有审核
			$audit_index=intval($_FANWE['setting']['audit_index']);
			if($audit_index){
				$condition .= " AND s.status =1 ";
				
			}
			//当前的分页数
			$page = intval($_REQUEST['p']);
			//显示最近7天的数目
			$field = ",(s.create_time > $day7_time) AS time_sort ";
			$sort = " ";
			if($sort_field == 'hot')
			{
				$sort = "  ORDER BY s.collect_count DESC ";
			}
			else
			{
				$sort = "  ORDER BY s.share_id DESC ";
			}
			
			//$field  $join_sql与album_share表关联  $condition  $sort排序  $page_num每页数
			$sql = 'SELECT DISTINCT(s.share_id),s.uid,s.content,s.collect_count,s.comment_count,s.create_time,s.cache_data ,s.parent_id '.$field.'
					FROM '.FDB::table('share').' AS s  '.$join_sql.' where s.share_data <> '." 'default' ".$condition .$sort.' LIMIT '.$page_num;
			
			$is_next = 1;
			
			$share_list = FDB::fetchAll($sql);
			$scale = 1;
			
			if($share_list)
			{
				//获取分享详情
				$share_list = FS('Share')->getShareDetailList($share_list,false,false,false,true,2);
				
				$list = array();
				$current_user = array();
				$current_user['u_url'] = FU('u/index',array('uid'=>$_FANWE['uid']));
				$current_user['avt'] = avatar($_FANWE['uid'], 's', $is_src = 1);
				$i = 0;
				
				foreach($share_list as $k => $v)
				{
				
				$img = FDB::fetchFirst("select img,img_height,img_width,is_animate,video from ".FDB::table("share_photo")." where share_id = ".$v['share_id']." and img <> '' ");
				if($img['is_animate']==1){
					$img_url=$img['img'];
				}
				else{
					$img_url = getImgName($img['img'],$img_width,999,2,true);
				}
				$img_url_1=str_replace('./upyun/','',$img['img']);
				
				if(!is_file(FANWE_ROOT.$img_url_1)){
					continue;
				}
			
				$list[$i] = $v;
				$is_video=0;
				if(!empty($img['video'])){
					$is_video=1;
					$vedio_url=$img['video'];
				}
				$list[$i]['is_video'] =$is_video;
				$list[$i]['video'] =$vedio_url;
				//$img_url = getImgName($img['img'],$img_width,999,2,true);
				
				
			
				$list[$i]['share_img'] = content_parse_2($img_url);
				
				//echo $list[$i]['share_img'];
				//var_dump(IS_UPYUN);
				
				$list[$i]['height'] = $img['img_height'] * ($img_width / $img['img_width']);
				/*获取play图标的位置*/
				$list[$i]['video_style_top']=($list[$i]['height']-33)/2;
				$list[$i]['video_style_right']=(254-33)/2;
				
				$list[$i]['height'] = round($list[$i]['height'] / $scale);
				$list[$i]['is_animate']=$img['is_animate'];
				$list[$i]['width'] = $img_width;
				
				$list[$i]['avt'] = avatar($v['uid'], 's', $is_src = 1);
				//是否来自转载
				$parent_id = FDB::resultFirst("select parent_id from ".FDB::table("share")." where share_id = ".$v['share_id']);
				if($parent_id == 0)
				{
					$list[$i]['isOriginal'] = 1;
				}
				else 
				{
					$list[$i]['isOriginal'] = 0;
				}
				//是否喜欢
				$list[$i]['likeStatus'] = FS('Share')->getIsCollectByUid($v['share_id'],$_FANWE['uid'])?1:0;
				//是否自己
				$list[$i]['isMe'] = $v['uid'] == $_FANWE['uid']?1:0;
				$list[$i]['is_relay'] = $v['is_relay'];
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
						$share_comments_data[$idxj]['user_url'] = FU('u/album',array('uid'=>$vv['user']['uid']));
						$share_comments_data[$idxj]['user_name'] = $vv['user']['user_name'];
						$share_comments_data[$idxj]['avt'] = avatar($vv['uid'], 's', $is_src = 1);
						$share_comments_data[$idxj]['comment'] = cutStr($vv['content'],20);
						$idxj++;
					}
					$list[$i]['comments'] = $share_comments_data;
				}
				
				$list[$i]['share_url'] = FU('note/index',array('sid'=>$v['share_id']));
				$list[$i]['u_url'] = FU('u/album',array('uid'=>$v['uid']));
				$list[$i]['relay_count'] = FDB::resultFirst("select relay_count from ".FDB::table("share")." where share_id = ".$v['share_id']);
				// 分享对应的 杂志社
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
				$args['list'] = $list;
			}
			
			$args['current_user'] = $current_user;
			//下一页
			$args['link_url'] = $link_url;
			//
			$args['json_url'] = $json_url;
			return tplFetch('inc/index/index_cate',$args);
}

/**
 * 首页 热门活动,最新活动
 */
function getUUHotEvent()
{
	$cache_file = getTplCache('inc/index/hot_event',array(),1);
	if(getCacheIsUpdate($cache_file,600))
	{
		$args['img_event'] = FS('Event')->getHotImgEvent(1);
		$img_tid = array();
		if(!empty($args['img_event']))
		{
			$args['img_event'] = array_slice($args['img_event'],0,1);
            $args['img_event'] = $args['img_event'][0];
			$img_tid[] = $args['img_event']['id'];
		}
		$args['new_topics'] = FS('Event')->getHotNewEvent(3,$img_tid);
	}
	return tplFetch('inc/index/hot_event',$args,'',$cache_file);
}

/**
 * 新入会员
 */
function getUUNewUsers()
{
    $args['users'] = FS('User')->getNewUsers(10);
	$args['user_count'] = FS('User')->getUserstCount();
    return tplFetch('inc/index/new_user',$args);
}

/**
 * 首页 热门主题
 */
function getUUHotTopic()
{
	$cache_file = getTplCache('inc/index/hot_topic',array(),1);
	if(getCacheIsUpdate($cache_file,600))
	{
		$args['hot_topics'] = FS('Topic')->getImgTopic('hot',6,1);
		if(!empty($args['hot_topics']))
		{
			$args['img_topic'] = current(array_slice($args['hot_topics'],0,1));
			$args['hot_topics'] = array_slice($args['hot_topics'],1);
		}
	}
	return tplFetch('inc/index/hot_topic',$args,'',$cache_file);
}

/**
 * 首页 推荐达人
 */
function getUUBestDarens()
{
	$args['daren_list'] = FS('Daren')->getBestDarens(4);
	return tplFetch('inc/index/best_daren',$args);
}

/**
 * 首页 最新的主题
 */
function getUUIndexTopic()
{
	global $_FANWE;
	$args = array();
	$cache_file = getTplCache('inc/index/new_topic',array(),1);
	if(getCacheIsUpdate($cache_file,600))
	{
		$res = FDB::query('SELECT fid,thread_count FROM '.FDB::table('forum').' WHERE parent_id = 0');
		while($data = FDB::fetch($res))
		{
			$_FANWE['cache']['forums']['all'][$data['fid']]['thread_count'] = $data['thread_count'];
		}

		$args['new_list'] = FS('Topic')->getImgTopic('new',5,1);
		$args['ask_list'] = FS('Ask')->getImgAsk('new',5,1);
	}

	return tplFetch('inc/index/topics',$args,'',$cache_file);
}
?>