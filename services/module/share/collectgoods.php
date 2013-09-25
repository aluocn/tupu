<?php
/*
 * http://localhost/tupu_svn/services/service.php?m=share&a=collectgoods&url=http://detail.tmall.com/item.htm?spm=640.1000539.1000193.3&id=12323799752&cm_cat=50034530&source=dou
 */
$url = $_FANWE['request']['url'];

if($_FANWE['request']['test']==1){
	$url='http://item.taobao.com/item.htm?id=17379472206&ali_refid=a3_420869_1007:1102311960:7:15978cb1baa344ca909956929743f117:35261aa53499f3e4a8e07bec94f1785c&ali_trackid=1_35261aa53499f3e4a8e07bec94f1785c';
}
if(empty($url))
	exit;
$result = array();
require fimport('service/sharegoods');
require fimport('service/image');
$url = urldecode($url);

$share_module = new SharegoodsService($url);
$goods = $share_module->fetch();

if($goods)
{
	if($goods['status'] == -1)
	{
		$result['status'] = -3;
		$result['url'] = FU('note/g',array('sid'=>$goods['share_id'],'id'=>$goods['goods_id']));
	}
	else
	{		
		$res['pic_url'] = $goods['item']['pic_url'];//图片
		$res['url'] = $goods['item']['url'];//商品链接
		$res['taoke_url'] = $goods['item']['taoke_url'];
		$res['name'] = trim($goods['item']['name']);
		$res['info']=$goods['item']['info'];
		if(!$res['taoke_url']){
			$res['taoke_url']=$goods['item']['url'];
		}
		
		if(FS('Image')->getIsServer()){//如果有可用的图片服务器
			$imgServer = FS('Image')->getMinImgServerCode();
			$res['server_code']=$imgServer['code'];
		}
		$args = array('res'=>$res);
		$result['html'] = tplFetch("services/share/pic_itemGoods",$args);
		$result['status'] = 1;
	}
}
else
{
	$result['status'] = 0;
}
outputJson($result);
?>