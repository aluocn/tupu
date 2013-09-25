<?php
	global $_FANWE;
	
	$page_num = intval($_FANWE['setting']['share_search_page'])?intval($_FANWE['setting']['share_search_page']):30;
	$is_root = false;
	
	$img_width = $_FANWE['request']['width'];
	$kwy_word = $_FANWE['request']['kwy_word'];
	
	if(!empty($kwy_word))
	{
		$match_key = segmentToUnicode($kwy_word,'+');
		$share_condition = " WHERE match(u.user_name_match) against('".$match_key."' IN BOOLEAN MODE) ";
	}     
	
	$page = intval($_REQUEST['p']);
	$limit = (($page - 1)*$page_num).","."$page_num";
	$next_limit = ($page *$page_num).","."1";
	
	$sql="SELECT u.*, uc.*, us.*, up.* FROM ".FDB::table('user')." u 
			LEFT JOIN ".FDB::table('user_count')." uc USING(uid)
			LEFT JOIN ".FDB::table('user_status')." us USING(uid)
			LEFT JOIN ".FDB::table('user_profile')." up USING(uid) ".$share_condition."
                        ORDER BY u.uid DESC LIMIT ".$limit;
	
	
	$next_sql ="SELECT u.*, uc.*, us.*, up.* FROM ".FDB::table('user')." u 
			LEFT JOIN ".FDB::table('user_count')." uc USING(uid)
			LEFT JOIN ".FDB::table('user_status')." us USING(uid)
			LEFT JOIN ".FDB::table('user_profile')." up USING(uid) ".$share_condition."
                        ORDER BY u.uid DESC LIMIT ".$next_limit;
	
	$hasNextPage = FDB::fetchAll($next_sql);
	if($hasNextPage)
	{
		$is_next = 1;
	}
	else 
	{
		$is_next = 0;
	}
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
                
		outputJson(array('result'=>$user_list,'current_user'=>$current_user,'status'=>1,'hasNextPage'=>$is_next));
	}
	else {
		outputJson(array('status'=>0,'hasNextPage'=>$is_next));
	}
	
?>