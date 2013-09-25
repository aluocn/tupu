<?php
class NoteModule
{
	public function index()
	{
		
		NoteModule::show();
	}

	public function goods()
	{
		NoteModule::show('bao');
	}

	public function photo()
	{
		NoteModule::show('photo');
	}

	private function show($current_type = '')
	{
		global $_FANWE;
		$share_id = intval($_FANWE['request']['sid']);
		
		$id = intval($_FANWE['request']['id']);
		$share_detail = FS('Share')->getShareDetail($share_id);
		
		include fimport('dynamic/u');
		if($share_detail === false)
			fHeader("location: ".FU('index'));
		$page_title = preg_replace("/\[[^\]]+\]/i","",$share_detail['content']);
		$_FANWE['nav_title'] = $page_title;
        $_FANWE['seo_description'] = $page_title;
        $_FANWE['setting']['site_description'] = '';
        
        
        $current_type = FDB::resultFirst("select type from ".FDB::table("share_photo")." where share_id = ".$share_id);
        
        if($current_type == 'paper'){//文章分享text字段的内容
        	$paper_content = FDB::resultFirst("select text from ".FDB::table("share_photo")." where share_id = ".$share_id);
        	
        }else{
        	$current_img = FDB::resultFirst("select img from ".FDB::table("share_photo")." where share_id = ".$share_id." and img <> '' ");	
        	
        }
        
		$current_content  = FDB::resultFirst("select content from ".FDB::table("share")." where share_id = ".$share_id);
		
		
		
		$current_img_arr = FDB::fetchFirst("select * from ".FDB::table("share_photo")." where share_id = ".$share_id." and img <> '' ");
		
		if($current_img_arr['img_width']>468){
			$current_img_arr['img_width']=468;
		}
		if($current_img_arr['server_code']){
			if($current_img_arr['is_animate']){
				$current_img=$current_img_arr['img'];
			}else{
				$current_img=FS("Image")->getImageUrl($current_img_arr['img'],1);
			}
			
		}else{
			if($current_img_arr['is_animate']){
				$current_img=$current_img_arr['img'];
			}else{
				$current_img=getImgName($current_img_arr['img'],468,468,0);	
			}
			
		}
		
		FDB::query('UPDATE '.FDB::table('share').' SET click_count = click_count + 1 WHERE share_id = '.$share_id);

		//上一个，下一个分享
		$pns = FS('Share')->getSameAlbumNextShares($share_detail['uid'],$share_id);
		
		$other_album_share = FS('share')->getSameAlbumShares($share_id);
		
		foreach($other_album_share as $k=>$v){
			$other_album_share[$k]['img'] = getImgName($v['img'],61,61,1,$v['server_code']);
		}
		$current_location = $other_album_share['location'];
		unset($other_album_share['location']);
		$same_album_count = $other_album_share['count'];
		unset($other_album_share['count']);
		
		$aid = FDB::resultFirst("select album_id from ".FDB::table('album_share')." where share_id = ".$share_id);
		$album_sql = 'select al.id as id,al.title as title from '.FDB::table('album').' as al 
					 left join '.FDB::table('album_share').' as als on al.id = als.album_id 
					 where als.share_id = '.$share_id;
		$album_data = FDB::fetchFirst($album_sql);
		$is_follow_album = FS('album')->getIsFollow($aid,$_FANWE['uid']);
		
		//发布分享的会员
		$share_user = FS('User')->getUserById($share_detail['uid']);
		
		//喜欢分享的会员
		$share_detail['collects'] = FS('Share')->getShareCollectUser($share_id);
		if(!isset($share_detail['collects'][$_FANWE['uid']]))
		{
			if(FS('Share')->getIsCollectByUid($share_id,$_FANWE['uid']))
				$share_detail['collects'][$_FANWE['uid']] = $_FANWE['uid'];
		}

		//会员显示名称
		$user_show_name = FS('User')->getUserShowName($share_detail['uid']);
		
		//会员勋章
		$user_medals = FS('User')->getUserMedal($share_detail['uid']);

		//分享标签
		$share_tags = $share_detail['cache_data']['tags']['user'];
		FS('Share')->tagsFormat($share_tags);

        foreach($share_tags as $seo_tag)
        {
            $_FANWE['seo_keywords'] .= $seo_tag['tag_name'].',';
        }
		//是否可编辑标签
		$is_eidt_tag = FS('Share')->getIsEditTag($share_detail);

		//喜欢分享的会员还喜欢
		$fav_user_fav_share = FS('Share')->getCollectShareByShare($share_id);

		//发布分享的会员喜欢的分享
		$user_collect_share = FS('Share')->getCollectShareByUser($share_user['uid']);

		//是否可删除标签
		$is_remove_comment = FS('Share')->getIsRemoveComment($share_detail);

		//分享评论
		$share_detail['comments'] = FS('Share')->getShareCommentList($share_id,'0,10');

		//分享评论分页
		$pager = buildPage('',array(),$share_detail['comment_count'],$_FANWE['page'],10);
		unset($share_detail['cache_data']);
		
		$current_obj = NULL;
		if($current_type == '' || $id == 0)
		{
			if(!empty($share_detail['imgs']))
			{
				$current_obj = current($share_detail['imgs']);
				if($current_obj['type'] == 'g')
					$current_type = 'bao';
				else
					$current_type = 'photo';
			}
		}
		else
		{
			switch($current_type)
			{
				case 'bao':
					foreach($share_detail['imgs'] as $img)
					{
						$current_obj = $img;
						if($img['type'] == 'g' && $img['id'] == $id)
							break;
					}
				break;

				case 'photo':
					foreach($share_detail['imgs'] as $img)
					{
						$current_obj = $img;
						if($img['type'] == 'm' && $img['id'] == $id)
							break;
					}
				break;
			}
		}
		
		if(!empty($current_obj['name']))
			$_FANWE['nav_title'] = $current_obj['name'].' - '.lang('common','share');

		
		$current_img_id = FDB::resultFirst("select photo_id from ".FDB::table("share_photo")." where share_id = ".$share_id);
		
		
		
		if($current_type=='video'){
			$current_video = FDB::resultFirst("select video from ".FDB::table("share_photo")." where share_id = ".$share_id);
		}
		
		
		$is_follow = FS('Share')->getIsCollectByUid($share_id,$_FANWE['uid']);
		
		
		/*采自同一个站点的图片*/
		$refer_url = $share_detail['refer_url'];
		$arr_url=parse_url($refer_url);//获取域名
		if($arr_url['host']){
			$host = $arr_url['host'];
			$sql = "select share_id from ".FDB::table("share")." where parent_id =0 and share_id !=".$share_id." and refer_url like '%".$host."%'";			
			$res = FDB::query($sql);
			$ids = array();
				while ($data = FDB::fetch($res)) {
					array_push($ids,$data['share_id']);
				}
				/*随即获取key*/
				if($ids){
					$count = count($ids);
					if($count > 3){
						$count = 3;
						$rand_keys = array_rand($ids,$count);
					}elseif(count>=2){
						$rand_keys = array_rand($ids,$count);
					}else{
						$rand_keys[] = 0;
					}
					
					$same_address_share = array();
					/*根据随机获取的share_id进行数据库的操作*/
					foreach($rand_keys as $k){
						
						$share = FDB::fetchFirst("select share_id,title from ".FDB::table('share')." where share_id = ".$ids[$k]);
						$share_img = FDB::fetchFirst("select img,server_code from ".FDB::table('share_photo')." where share_id =".$ids[$k]);
						$share['img'] = getImgName($share_img['img'],61,61,1,$share_img['server_code']);
						$same_address_share[]=$share;
						
					}	
				}
		}
		
		/*喜欢这个图片的会员*/
		$sql = "select u.uid,u.user_name from ".FDB::table('user')." as u left join ".FDB::table('share')." as s on u.uid=s.uid where s.type='fav' and s.parent_id=".$share_id;
		$res = FDB::query($sql);
		$user = array();
		while($data = FDB::fetch($res)){
			$user = $data;
			$user['avatar'] = avatar($data['uid'],'m', $is_src = 1);
			$user_fav_list[] = $user;
		}
		$fav_count  = count($user_fav_list);
		if($fav_count > 18){
			$more_fav_count = $fav_count -18;
		}else{
			$more_fav_count = 0;
		}
		
		/*转采这个图片的人*/
		$sql = "select u.uid,u.user_name,a.title,a.id from ".FDB::table('user')." as u left join ".FDB::table('share')." as s on u.uid = s.uid left join ".FDB::table('album')." as a on s.rec_id = a.id where s.type='album_item' and s.parent_id =".$share_id;
		$user_collect_list = array();
		$res = FDB::query($sql);
		while($data = FDB::fetch($res)){
			$data['avatar'] =  avatar($data['uid'],'m', $is_src = 1);
			$data['img'] = array();
			$share_sql = "select distinct(share_id) from ".FDB::table("share")." where uid = ".$data['uid']." and share_data not in('paper') order by share_id desc limit 0,3";
			$re = FDB::query($share_sql);
			while($da = FDB::fetch($re)){
				 $share=FDB::fetchFirst("select photo_id,img,share_id,server_code from ".FDB::table("share_photo")." where share_id =".$da['share_id']);
				 $share['img']=getImgName($share['img'],61,61,1,$share['server_code']);
				 $data['img'][]=$share;
			}
			$user_count = FDB::fetchFirst("select shares from ".FDB::table('user_count')." where uid=".$data['uid']);
			$data['shares'] = $user_count['shares'];
			$user_collect_list[] = $data;
		}
		include template('page/note/note_index');
		display();
	}
}
?>