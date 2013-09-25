<?php
@set_include_path(FANWE_ROOT.'sdks/paipai/');
require_once 'config.inc.php';
require_once 'PaiPaiOpenApiOauth.php';
class paipai_sharegoods implements interface_sharegoods
{
	public function fetch($url)
	{
        global $_FANWE;
		
		//QQ号
		define('PAIPAI_API_UIN',trim($_FANWE['cache']['business']['paipai']['uin']));
		//令牌
		define('PAIPAI_API_APPOAUTHID',trim($_FANWE['cache']['business']['paipai']['appoauthid']));
		//APP_KEY
		define('PAIPAI_API_APPOAUTHKEY',trim($_FANWE['cache']['business']['paipai']['appoauthkey']));
		define('PAIPAI_API_ACCESSTOKEN',trim($_FANWE['cache']['business']['paipai']['accesstoken']));
		define('PAIPAI_API_USERID',trim($_FANWE['cache']['business']['paipai']['userid']));

		$id = $this->getID($url);
		
		if(empty($id))
			return false;

		$key = 'paipai_'.$id;
		/*
		$share_goods = FDB::resultFirst('SELECT share_id,goods_id FROM '.FDB::table('share_goods').' 
			WHERE uid = '.$_FANWE['uid']." AND goods_key = '$key'");
		if($share_goods)
		{
			$result['status'] = -1;
			$result['share_id'] = $share_goods['share_id'];
			$result['goods_id'] = $share_goods['goods_id'];
			return $result;
		}
		*/

		$sdk = new PaiPaiOpenApiOauth(PAIPAI_API_APPOAUTHID,PAIPAI_API_APPOAUTHKEY,PAIPAI_API_ACCESSTOKEN,PAIPAI_API_UIN);
		
		$sdk->setApiPath("/item/getItem.xhtml");
		$sdk->setMethod("get");
		$sdk->setCharset("utf-8");
		
		$params = &$sdk->getParams();
		$params["itemCode"] = $id;
		$params["needDetailInfo"] = 1;
	
		
		$goods = $sdk->invoke();
		$goods = Util::getXmlData($goods);
		
		if($goods['errorCode'] > 0)
			return false;
	
		if(empty($goods['picLink']))
			return false;
			
		$image = copyFile($goods['picLink'],"temp",false);
		
		if(!$image){
			return false;
		}
		
		$share_goods['info'] = authcode(serialize($image), 'ENCODE');
		$share_goods['id'] = 0;
		$share_goods['name'] = $goods['itemName'];
		$share_goods['desc'] = htmlspecialchars_decode($goods['detailInfo']);
		$share_goods['price'] = $goods['itemPrice'] / 100;
		$share_goods['delist_time'] = (int)str2Time($goods['lastToSaleTime']) + (int)$goods['validDuration'];
		$share_goods['server_code'] = $image['server_code'];
		$share_goods['pic_url'] = $goods['picLink'];
		$share_goods['url'] = 'http://auction1.paipai.com/'.$goods['itemCode'];

		$sdk = new PaiPaiOpenApiOauth(PAIPAI_API_APPOAUTHID,PAIPAI_API_APPOAUTHKEY,PAIPAI_API_ACCESSTOKEN,PAIPAI_API_UIN);
		$sdk->setApiPath("/cps/constructCpsUrl.xhtml");
		$sdk->setMethod("get");
		$sdk->setCharset("utf-8");
		
		$params = &$sdk->getParams();
		$params["userId"] = PAIPAI_API_USERID;
		$params["urlType"] = 2;
		$params["itemCodes"] = $id;
		
		$cps = $sdk->invoke();
		$cps = Util::getXmlData($cps);
		if($cps && $goods['errorCode'] == 0 && count($cps['urlList']) > 0)
		{
			$share_goods['taoke_url'] = current($cps['urlList']);
		}
		
		$result['item'] = $share_goods;
		return $result;
	}

	public function getID($url)
	{
		$id = '';
		$parse = parse_url($url);
		if(isset($parse['path']))
		{
			$parse = explode('/',$parse['path']);
			$parse = end($parse);
			$parse = explode('-',$parse);
			$id = current($parse);
        }
		return $id;
	}

	public function getKey($url)
	{
		$id = $this->getID($url);
		return 'paipai_'.$id;
	}
}
?>