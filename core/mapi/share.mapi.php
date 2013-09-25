<?php
class shareMapi
{
	public function run()
	{
		global $_FANWE;	
		
		$root = array();
		$root['return'] = 1;

		$share_id = (int)$_FANWE['requestData']['share_id'];
		$act2 = $_FANWE['requestData']['act_2'];
		$share = FS('Share')->getShareById($share_id);

		$max_width = 320;
		$max_height = 234;

		$scale = 2;

		if($share)
		{
			switch($act2)
			{
				case 'follow':
					if($_FANWE['uid'] > 0){
						FS('User')->followUser($share['uid']);
					}
				break;

				case 'collect':
					if($_FANWE['uid'] > 0 && $share['uid'] != $_FANWE['uid'])
					{
						if(FS('Share')->getIsCollectByUid($share_id,$_FANWE['uid']))
							FS('Share')->deleteShareCollectUser($share_id,$_FANWE['uid']);
						else
							FS('Share')->saveFav($share);

						$share = FDB::fetchFirst('SELECT * FROM '.FDB::table('share').' WHERE share_id = '.$share_id);
					}
				break;
			}

			$cache_data = fStripslashes(unserialize($share['cache_data']));
			unset($share['cache_data']);
			$share['prev_share'] = (int)FDB::resultFirst('SELECT share_id FROM '.FDB::table('share').' 
				WHERE share_id < '.$share_id." AND share_data IN ('goods','photo','goods_photo') ORDER BY share_id DESC LIMIT 1");
			$share['next_share'] = (int)FDB::resultFirst('SELECT share_id FROM '.FDB::table('share').' 
				WHERE share_id > '.$share_id." AND share_data IN ('goods','photo','goods_photo') ORDER BY share_id ASC LIMIT 1");
			$share['time'] = getBeforeTimelag($share['create_time']);
			$share['url'] = FU('note/index',array('sid'=>$share_id),true);
			
			$site_url = ';网址:'.$share['url'];
			$root['share_content'] = cutStr($share['content'],140 - getStrLen($site_url) - 3).$site_url;

			m_express(&$share,$share['content']);
			
			$share_user = FS('User')->getUserById($share['uid']);
			
			$share['user_name'] = $share_user['user_name'];
			
			$share['user_avatar'] = FS("Image")->getImageUrl('.'.avatar($share_user['uid'],'m',1),2);
			
			if($share['source'] == 'web')
				$share['source'] = $_FANWE['setting']['site_name'].'网站';

			
			if($_FANWE['uid'] == $share['uid'])
			{
				$share['is_follow_user'] = -1;
				$share['is_collect_share'] = -1;
			}
			else
			{
				if(FS('User')->getIsFollowUId($share['uid'],false))
					$share['is_follow_user'] = 1;
				else
					$share['is_follow_user'] = 0;

				if(FS('Share')->getIsCollectByUid($share_id,$_FANWE['uid']))
					$share['is_collect_share'] = 1;
				else
					$share['is_collect_share'] = 0;
			}

			$share['comments'] = array();
			$sql_count = "SELECT COUNT(DISTINCT comment_id) FROM ".FDB::table("share_comment")." WHERE share_id = ".$share_id;
			$total = FDB::resultFirst($sql_count);
			$page_size = PAGE_SIZE;
			
			$page_total = ceil($total/$page_size);
			$limit = "0,".$page_size;
			$sql = 'SELECT c.*,u.user_name,u.server_code FROM '.FDB::table('share_comment').' AS c 
				INNER JOIN '.FDB::table('user').' AS u ON u.uid = c.uid 
				WHERE c.share_id = '.$share_id.' ORDER BY c.comment_id DESC LIMIT '.$limit;
			$res = FDB::query($sql);
			$list = array();
			while($item = FDB::fetch($res))
			{
				$item['user_avatar'] = FS("Image")->getImageUrl('.'.avatar($item['uid'],'m',1),2);
				$item['time'] = getBeforeTimelag($item['create_time']);
				m_express(&$item,$item['content']);
				$share['comments']['list'][] = $item;
			}

			$share['comments']['page'] = array("page"=>1,"page_total"=>$page_total);

			$share['collects'] = array();
			if(!empty($cache_data['collects']))
			{
				$collect_ids = array_slice($cache_data['collects'],0,20);
				if($share['is_follow_user'] == 1)
				{
					if($ckey = array_search($_FANWE['uid'],$collect_ids) === FALSE)
					{
						array_unshift($collect_ids,$_FANWE['uid']);
						array_pop($collect_ids);
					}
					else
					{
						unset($collect_ids[$ckey]);
						array_unshift($collect_ids,$_FANWE['uid']);
					}
				}

				$collect_ids = implode(',',$collect_ids);
				$res = FDB::query("SELECT uid,user_name,server_code FROM ".FDB::table('user').' 
					WHERE uid IN ('.$collect_ids.')');
				while($item = FDB::fetch($res))
				{
					$item['user_avatar'] = FS("Image")->getImageUrl('.'.avatar($item['uid'],'m',1),2);
					unset($item['server_code']);
					$share['collects'][] = $item;
				}
			}
			
			$share['imgs'] = array();
			
			$img = array();
			$img_sql = 'select photo_id,img,img_width,img_height from '.FDB::table('share_photo').' where share_id = '.$share_id;
			$img_data = FDB::fetchFirst($img_sql);
			$img['share_id'] = $share_id;
			$img['id'] = $img_data['photo_id'];
			$img['name'] = '';
			$img['type'] = '';
			$img['price'] = ''  ;
			$img['goods_url'] = '';
			$img['taoke_url'] = '';
			$img['price_format'] = '';
			$img['server_code'] = '';

			if($img_data['img_width'] > $img_data['img_height'])
			{
				if($img_data['img_width'] > $max_width)
				{
					$img['img_width'] = $max_width;
					$img['img_height'] = $img_data['img_height'] * ($max_width / $img_data['img_width']);
				}
			}
			else
			{
				if($img_data['img_height'] > $max_height)
				{
					$img['img_height'] = $max_height;
					$img['img_width'] = $img_data['img_width'] * ($max_height / $img_data['img_height']);
				}
			}
			  
			$img['img'] = FS("Image")->getImageUrl($img_data['img'],2);
			$img['small_img'] =  FS("Image")->getImageUrl(getImgName($img_data['img'],$img['img_width'],$img['img_height'],0,$img['server_code']),2);
			
			$share['imgs'][] = $img;
		}

		$root['item'] = $share;
		m_display($root);
	}
}
?>