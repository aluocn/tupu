<?php
$result = array();
$result['status'] = 0;

$data = array(
	'title' => trim($_FANWE['request']['title']),
	'cid' => (int)$_FANWE['request']['cid'],
	'id'	=> (int)$_FANWE['request']['id'],
);

if($data['title'] == '输入新专辑名')
	exit;

$vservice = FS('Validate');
$validate = array(
	array('title','required',lang('album','name_require')),
	array('title','max_length',lang('album','name_max'),60),
	array('cid','min',lang('album','cid_min'),1),
);

if(!$vservice->validation($validate,$data))
{
	$result['msg'] = $vservice->getError();
	outputJson($result);
}

FanweService::instance()->cache->loadCache('albums');
if(!isset($_FANWE['cache']['albums']['category'][$data['cid']]))
	exit;


$check_result = FS('Share')->checkWord($_FANWE['request']['title'],'title');
if($check_result['error_code'] == 1)
{
	$result['msg'] = $check_result['error_msg'];
	outputJson($result);
}

$data['title'] = htmlspecialchars($_FANWE['request']['title']);
if(FDB::query("update ".FDB::table("album")." set title = '".$data['title']."',cid = ".$data['cid']." where id = ".$data['id']))
{
	$cid=FDB::resultFirst("select cid from ".FDB::table("album_share")." where album_id=".$data['id']);
	if($cid!=$data['cid']){
		//若cid修改，album_share的也要修改
		if(FDB::query("update ".FDB::table("album_share")." set cid=".$data['cid']." where album_id=".$data['id'])){
			$result['status'] = 1;
			$result['url'] = FU("album/show",array('id'=>$data['id']));
		}else{
			$result['status'] = 0;
		}
	}else{
	$result['status'] = 1;
	$result['url'] = FU("album/show",array('id'=>$data['id']));
	}
	
}
else
{
	$result['status'] = 0;
}

outputJson($result);
?>