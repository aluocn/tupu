<?php
class BookModule
{
	public function share()
	{
		BookModule::getShare();
	}
	
	private function getShare()
	{
		global $_FANWE;
		
		
		$kwy_word = urldecode($_REQUEST['kw']);
		
		
		$page_title = preg_replace("/\[[^\]]+\]/i","",$kwy_word);
		$_FANWE['nav_title'] = $page_title.' - '.lang('common','share');
        $_FANWE['seo_description'] = $page_title;
        $_FANWE['setting']['site_description'] = '';
        
		$count_result = BookModule::searchInfo();
        $count = $count_result['share_count'];
		if($count_result['user_count'] > 0)
		{
			$match_key = segmentToUnicode($kwy_word,'+');
			$user_sql = "SELECT u.*, uc.*, us.*, up.* FROM ".FDB::table('user')." u 
			LEFT JOIN ".FDB::table('user_count')." uc USING(uid)
			LEFT JOIN ".FDB::table('user_status')." us USING(uid)
			LEFT JOIN ".FDB::table('user_profile')." up USING(uid)
			WHERE match(u.user_name_match) against('".$match_key."' IN BOOLEAN MODE)  limit 10";
			$user_list = FDB::fetchAll($user_sql);
			if($user_list)
			{
                            foreach($user_list as $k => $v)
				{
					$is_follow = FS("User")->getIsFollowUId($v['uid']);
					$user_list[$k]['is_follow'] = $is_follow;
				}
			}
		}
		
		
		$page_num = intval($_FANWE['setting']['share_search_page'])?intval($_FANWE['setting']['share_search_page']):30;
		
		$is_root = false;
		
		$img_width = 190;
		$kwy_word = urldecode($_FANWE['request']['kw']);
		
		if(!empty($kwy_word))
		{
			$match_key = segmentToUnicode($kwy_word,'+');
			$share_condition = " AND match(share_content_match) against('".$match_key."' IN BOOLEAN MODE) ";
		}
		if($_FANWE['setting']['audit_index'] == 1){
			$share_condition .= " and status = 1";	
		}
		$limit = $page_num;
		
		$sql = 'SELECT *
				FROM '.FDB::table("share").'
				WHERE share_data <> '." 'default'  ".$share_condition."
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
			$img = FDB::fetchFirst("select adv_url,text,type,img,img_height,img_width,is_animate,video,server_code from ".FDB::table("share_photo")." where share_id = ".$v['share_id']." and img <> '' ");
			
			if($img['img']){
					if($img['is_animate']==1){
						$img_url=$img['img'];
					}
					else{
						$img_url=getImgName($img['img'],$img_width,999,0,$img['server_code']);
						
					}
					
					$list[$i] = $v;
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
					$list[$i]['is_animate']=$img['is_animate'];
					if($img_width < $img['img_width']){
						$list[$i]['width'] = $img_width;
					}else{
						$list[$i]['width'] = $img['img_width'];	
					}
					$list[$i]['no_img'] = 0;
					$list[$i]['type'] = $img['type'];
					$list[$i]['adv_url'] = $img['adv_url'];
				}else{
					$list[$i]['no_img'] = 1;//没有图片的时候
					$list[$i]['text'] = $img['text'];
					$list[$i]['content'] = $v['content'];
					$list[$i]['share_id'] = $v['share_id'];
					$list[$i]['type'] = $img['type'];
					$list[$i]['collect_count'] = $v['collect_count'];
					$list[$i]['comment_count'] = $v['comment_count'];
					$list[$i]['relay_count'] = $v['relay_count'];
					$list[$i]['parent_id'] = $v['parent_id'];
					
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
			$list[$i]['zf_count'] = 10;
			$list[$i]['xh_count'] = 20;
			
			$list[$i]['user_name'] = FDB::resultFirst("select user_name from ".FDB::table('user')." where uid =".$v['uid']);
			$i++;
			}
		}
		
		
		
		$link_url = $_FANWE['site_url']."services/service.php?m=search&a=share&width=190&p=2&kwy_word=".$kwy_word;
		$json_url = $_FANWE['site_url']."services/service.php?m=search&a=share&width=190&kwy_word=".$kwy_word;
                
                
		include template('page/book/book_index');
		display();
	}
        
     public function user()
	{
		global $_FANWE;
        $count_result = BookModule::searchInfo();
        $count = $count_result['user_count'];
                
		$kwy_word = urldecode($_FANWE['request']['kw']);
		
		$page_num = intval($_FANWE['setting']['share_search_page'])?intval($_FANWE['setting']['share_search_page']):30;
		$is_root = false;
		
		$img_width = 190;
		
		if(!empty($kwy_word))
		{
			$match_key = segmentToUnicode($kwy_word,'+');
			$share_condition = " WHERE match(u.user_name_match) against('".$match_key."' IN BOOLEAN MODE) ";
		}     
		
		$page = intval($_REQUEST['p']);
		$limit = $page_num;
		
		
		$sql="SELECT u.*, uc.*, us.*, up.* FROM ".FDB::table('user')." u 
				LEFT JOIN ".FDB::table('user_count')." uc USING(uid)
				LEFT JOIN ".FDB::table('user_status')." us USING(uid)
				LEFT JOIN ".FDB::table('user_profile')." up USING(uid) ".$share_condition."
	                        ORDER BY u.uid DESC LIMIT ".$limit;
		
		$user_list = FDB::fetchAll($sql);
	
		if($user_list)
		{
			$list = array();
			$current_user = array();
			$current_user['u_url'] = FU('u/index',array('uid'=>$_FANWE['uid']));
			$current_user['avt'] = avatar($_FANWE['uid'], 'b', $is_src = 1);
			
	        foreach($user_list as $k => $v)
	        {
	              $is_follow = FS("User")->getIsFollowUId($v['uid']);
	              if($_FANWE['uid'] == $v['uid'])
	              	$user_list[$k]['is_me'] = 1;
	              else
	              	$user_list[$k]['is_me'] = 0;
	              	
	              $user_list[$k]['is_follow'] = $is_follow;
	              $album_sql = "select count(*) from ".FDB::table("album")." where uid=".$v['uid'];
	              $album_count = FDB::resultFirst($album_sql);
	              $user_list[$k]['album']=$album_count;
	              $user_list[$k]['avt'] = avatar($v['uid'], 'b', $is_src = 1);
	              $user_list[$k]['u_url'] = FU('u/index',array('uid'=>$v['uid']));
	              if($v['gender'] == 1)
	              	$user_list[$k]['sex'] = '男';
	              else
	              	$user_list[$k]['sex'] = '女';
	        }
		}
		
		$link_url = $_FANWE['site_url']."services/service.php?m=search&a=user&width=190&p=2&kwy_word=".$kwy_word;
		$json_url = $_FANWE['site_url']."services/service.php?m=search&a=user&width=190&kwy_word=".$kwy_word;
                
		include template('page/book/book_index');
		display();
	}
        
    public function album()
	{
		global $_FANWE;
        $count_result = BookModule::searchInfo();
        $count = $count_result['album_count'];
        $kwy_word = urldecode($_FANWE['request']['kw']);
		
		$page_num = intval($_FANWE['setting']['share_search_page'])?intval($_FANWE['setting']['share_search_page']):30;
		$is_root = false;
		
		$img_width = 190;
		
		if(!empty($kwy_word))
		{
			$match_key = segmentToUnicode($kwy_word,'+');
			$share_condition = " WHERE match(album_title_match) against('".$match_key."' IN BOOLEAN MODE) ";
		}
		
		$limit = $page_num;
		
		$sql = 'SELECT *
				FROM '.FDB::table("album").$share_condition."
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
					$img_sql = "select sp.img as img from ".FDB::table("share_photo")." as sp left join ".FDB::table("album_share")." as als on als.share_id = sp.share_id left join ".FDB::table("album")." as a on a.id = als.album_id where a.id = ".$v['id'];
							
					$res_img = FDB::query($img_sql);
					$photo_data = array();
					while($img_data = FDB::fetch($res_img))
					{
						$photo_data[] = $img_data['img'];
					}
					$album_list[$k]['url'] = FU('album/show',array('id'=>$v['id'])); 
					$album_list[$k]['is_follow_album'] = FS('album')->getIsFollow($v['id'],$_FANWE['uid']);
					$album_list[$k]['is_Me'] = $v['uid'] == $_FANWE['uid'] ? 1:0;
					
					$album_list[$k]['album_imgs'] =  $photo_data;   
			}
		}
        
		$link_url = $_FANWE['site_url']."services/service.php?m=search&a=album&width=190&p=2&kwy_word=".$kwy_word;
		$json_url = $_FANWE['site_url']."services/service.php?m=search&a=album&width=190&kwy_word=".$kwy_word;
                
		include template('page/book/book_index');
		display();
	}
	
	private function searchInfo()
	{
		global $_FANWE;
		$kwy_word = urldecode($_FANWE['request']['kw']);
		if(!empty($kwy_word))
		{
			$match_key = segmentToUnicode($kwy_word,'+');
			$user_condition = " AND match(user_name_match) against('".$match_key."' IN BOOLEAN MODE) ";
			$share_condition = " AND match(share_content_match) against('".$match_key."' IN BOOLEAN MODE) ";
			$album_condition = " AND match(album_title_match) against('".$match_key."' IN BOOLEAN MODE) ";
		}
		
		//查找的会员数量
		$user_sql = "select count(*) from ".FDB::table("user")." where status = 1 ".$user_condition;
		$user_count = FDB::resultFirst($user_sql);
		
		//查找的专辑数量
		$album_sql = "select count(*) from ".FDB::table("album")." where 1=1 ".$album_condition;
		
		$album_count = FDB::resultFirst($album_sql);
		
		if($_FANWE['setting']['audit_index'] == 1){
			$share_condition .= " and status = 1";	
		}
		//查找的分享数量
		$share_sql = "select count(*) from ".FDB::table("share")." where share_data <> 'default' ".$share_condition;
		$share_count = FDB::resultFirst($share_sql);
		
		$result = array();
		$result['user_count'] = $user_count;
		$result['album_count'] = $album_count;
		$result['share_count'] = $share_count;
		return $result;
	}
}
?>