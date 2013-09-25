<?php
class createalbumMapi
{
	public function run()
	{
		global $_FANWE;	
		
		$root = array();
		$root['return'] = 0;
		
		$uid = (int)$_FANWE['requestData']['uid'];
		$cid = (int)$_FANWE['requestData']['cid'];
		
		$album_title = trim($_FANWE['requestData']['title']);
		
		$data = array(
			'title' => $album_title,
			'cid' => $cid,
		);

		$vservice = FS('Validate');
		$validate = array(
			array('title','required',lang('album','name_require')),
			array('title','max_length',lang('album','name_max'),60),
			array('cid','min',lang('album','cid_min'),1),
		);

		if(!$vservice->validation($validate,$data))
		{
			$root['info'] = $vservice->getError();
			m_display($root);
		}
		
		$check_result = FS('Share')->checkWord($album_title,'title');
		if($check_result['error_code'] == 1)
		{
			$root['info'] = $check_result['error_msg'];
			m_display($root);
		}

		if($uid > 0)
		{
			if(!FS('User')->getUserExists($uid))
				$uid = 0;
		}

		if($cid == 0)
		{
			$uid == 0;
		}

		if($uid == 0)
		{
			$root['info'] = "请先登录";
			m_display($root);
		}

		$share_data = array();
		$share_data['uid'] = $uid;
		$share_data['type'] = 'album';
		$share_data['content'] = $album_title;

		$share = FS('Share')->submit($share_data,false,true,true);

		
		if($share['status'])
		{
			$data = array();

			$data['title'] = htmlspecialchars($album_title);
			$data['album_title_match'] = segmentToUnicode(clearSymbol($album_title));
			$data['uid'] = $uid;
			$data['cid'] = $cid;
			$data['share_id'] = $share['share_id'];
			$data['create_day'] = getTodayTime();
			$data['create_time'] = TIME_UTC;
			$data['show_type'] = 2;
			
			$aid = FDB::insert('album',$data,true);
			
			FDB::query('UPDATE '.FDB::table('share').' SET rec_id = '.$aid.' 
				WHERE share_id = '.$share['share_id']);
			FDB::query("update ".FDB::table("user_count")." set albums = albums + 1 where uid = ".$uid);
			
			$root['aid'] = $aid;
			$root['album_name'] = $album_title;
			$root['return'] = 1;
		}
		m_display($root);
	}
}
?>