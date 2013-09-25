<?php
class addshareMapi
{
	public function run()
	{
		global $_FANWE;	
		
		$root = array();
		$root['return'] = 0;

		if($_FANWE['uid'] == 0)
			exit;
		
		$_FANWE['requestData']['uid'] = $_FANWE['uid'];
		if(isset($_FILES['image_1']))
		{
			if($img = FS("Image")->save('image_1'))
			{
				if(FS("Image")->getIsServer())
				{
					$server = FS("Image")->getServer();
					$args = array();
					$args['pic_url'] = FS("Image")->getImageUrl($img['url'],2);
					$server = FS("Image")->getImageUrlToken($args,$server,1);
					$body = FS("Image")->sendRequest($server,'uploadtemp',true);
					
					if(!empty($body))
					{
						$img = unserialize($body);
						$info = array('path'=>$img['path'],'type'=>$_FANWE['requestData']['cate_type'],'server_code'=>$img['server_code']);
					}
				}
				else
				{
					$info = array('path'=>$img['path'],'type'=>$_FANWE['requestData']['cate_type'],'server_code'=>'');
				}
					
				$info = authcode(serialize($info), 'ENCODE');
				$_FANWE['requestData']['pics'][] = $info;
			}
			else
			{
				$root['info'] = "上传图片失败";
				m_display($root);
			}
		}
		$_FANWE['requestData']['rec_id']=$_FANWE['requestData']['albumid'];
		$share = FS('Share')->submit($_FANWE['requestData'],true,true);
		if($share['status'])
		{
			$root['return'] = 1;
			$root['info'] = "发表分享成功";
		}
		else
		{
			$root['info'] = "发表分享失败";
			if(!empty($share['error_msg']))
				$root['info'] = $share['error_msg'];
		}
		m_display($root);
	}
}
?>