<?php
global $_FANWE;

$list = FS("Album")->getAlbumListByUid($_FANWE['uid']);
$categoryList = FS("Album")->getAlbumCategory(0,"|--");
$id=intval($_FANWE['request']['id']);
$args = array(
	'list'=>&$list,
	'categoryList'=>&$categoryList,
	'id'=>&$id,
);
if($_FANWE['request']['type'] == 'more')//采集页面出现多图分别选择杂志社
{
	$result['html'] = tplFetch('services/share/select_album_list',$args);
}
else
{
	$result['html'] = tplFetch('services/share/select_album',$args);
}
outputJson($result);
?>