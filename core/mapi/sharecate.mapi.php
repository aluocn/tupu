<?php
class sharecateMapi
{
	public function run()
	{
		global $_FANWE;	
		
		$root = array();
		$root['return'] = 1;
		
		$key = 'm/sharecate';
		$cache_list = getCache($key);
		if($cache_list !== NULL || (TIME_UTC - $cache_list['cache_time']) > 600)
		{
			$cate_list = array();
			$min_time = $this->getQuarterMinTime();
			$max_time = getTodayTime();
			FanweService::instance()->cache->loadCache('albums');
			$album_cate = $_FANWE['cache']['albums']['category'];
			
			foreach($album_cate as $k => $v)
			{
				$cate = array();
				$cate['cate_id'] = $v['id'];
				$cate['cate_name'] = $v['name'];
				$cate['short_name'] = $v['name'];
				$cate['cate_code'] = $v['cate_code'];
				$cate['cate_icon'] = FS("Image")->getImageUrl($v['img'],2);
				$cate['desc'] = $v['desc']?$v['desc']:$v['name'];
				$cate['create_time'] = $v['create_time'];

				//获取本季分享数量
				$share_count_sql = 'select count(DISTINCT s.share_id) from '.FDB::table("share").' as s 
					INNER JOIN '.FDB::table("album_share").' as al on s.share_id = al.share_id where al.cid = '.$v['id']." and s.day_time >= $min_time AND s.day_time <= $max_time ";
				$cate['share_count'] = FDB::resultFirst($share_count_sql);
				$cate['img_tags'] = array();
				$img_size = 320;
				$sql = 'select s.share_id,al.title,sp.img from '.FDB::table("share").' as s
					INNER JOIN '.FDB::table("album_share").' as als ON s.share_id = als.share_id 
					INNER JOIN '.FDB::table("album").' as al ON als.album_id = al.id
					INNER JOIN '.FDB::table("share_photo")." as sp ON s.share_id = sp.share_id 
					WHERE s.day_time >= $min_time AND s.day_time <= $max_time AND als.cid = ".$v['id']." GROUP BY s.share_id ORDER BY s.share_id desc limit 5";
				
				$res = FDB::query($sql);
				while($data = FDB::fetch($res))
				{
					$img_data = array();

					$img_data['share_id'] = $data['share_id'];
					$img_data['tag_name'] = $data['title'];
					$img_data['is_tag'] = 0;
					$img_data['img'] = FS("Image")->getImageUrl(getImgName($data['img'],$img_size,$img_size,1,$data['server_code']),2);
					$img_data['url_tag'] = urlencode($data['title']);
					
					$cate['img_tags'][] = $img_data;
					$img_size = 160;
					
				}
				$cate['txt_tags'] = array();
				$cate_list[] = $cate;
			}

			$cache_list = array();
			$cache_list['cate_list'] = $cate_list;
			$cache_list['cache_time'] = TIME_UTC;
			setCache($key,$cache_list);
		}
		else
		{
			$cate_list = $cache_list['cate_list'];
		}

		$root['item'] = $cate_list;
		
		m_display($root);
	}

	public function getQuarterMinTime()
	{
		$now_year = fToDate(TIME_UTC,'Y');
		$now_month = fToDate(TIME_UTC,'n');
		$quarter = ceil($now_month / 3);
		$min_month = ($quarter - 1) * 3 + 1;
		return str2Time($now_year.'-'.$min_month.'-1 00:00:00');
	}
}
?>