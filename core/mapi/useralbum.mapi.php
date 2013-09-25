<?php
class useralbumMapi
{
	public function run()
	{
		global $_FANWE;	
		
		$root = array();
		$root['return'] = 1;
		
		$uid = (int)$_FANWE['requestData']['uid'];
		$cid = (int)$_FANWE['requestData']['cid'];
		
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

		$key = 'm/useralbum'.$uid.$cid;

		$album_list = getCache($key);
		if($album_list !== NULL || (TIME_UTC - $cache_list['cache_time']) > 600)
		{
			$album_list = array();
			
			$sql = 'select id,title from '.FDB::table('album')." where cid = ".$cid." and uid = ".$uid;
			$res = FDB::query($sql);
			
			$indx = 0;
			while($data = FDB::fetch($res))
			{
				
				$album_list[$indx] = $data;
				$indx ++;
			}

			$cache_list = array();
			$cache_list['album_list'] = $album_list;
			$cache_list['cache_time'] = TIME_UTC;
			setCache($key,$cache_list);
		}
		else
		{
			$album_list = $cache_list['album_list'];
		}

		$root['item'] = $album_list;
		
		m_display($root);
	}
}
?>