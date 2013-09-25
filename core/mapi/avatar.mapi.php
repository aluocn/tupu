<?php
class avatarMapi
{
	public function run()
	{
		global $_FANWE;	
		
		$root = array();
		$root['return'] = 0;
		
		if($_FANWE['uid'] == 0)
		{
			$root['info'] = "请先登陆";
			m_display($root);
		}

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
						$server = FS("Image")->getServer($img['server_code']);
						if(!empty($server))
						{
							$args = array();
							$args['pic_url'] = $img['path'];
							$args['types'] = array(
								'small' =>'32',
								'middle'=>'64',
								'big'   =>'160',
							);

							$server = FS("Image")->getImageUrlToken($args,$server,1);
							$body = FS("Image")->sendRequest($server,'saveavatar',true);
							if(!empty($body))
							{
								$avatar = unserialize($body);
								FS("Image")->setServerUploadCount($avatar['server_code']);
								FS('User')->updateAvatar($_FANWE['uid'],$avatar['server_code']);
							}
						}
						$root['user_avatar'] = avatar($_FANWE['uid'],'m',$avatar['server_code'],1,true);
					}
					else
					{
						$root['info'] = "上传图片失败";
						m_display($root);
					}
				}
				else
				{
					FS('User')->saveAvatar($_FANWE['uid'],$img['path']);
					$root['user_avatar'] = avatar($_FANWE['uid'],'m','',1,true);
				}
			}
			else
			{
				$root['info'] = "上传图片失败";
				m_display($root);
			}
		}
		else
		{
			$root['info'] = "请上传图片";
			m_display($root);
		}

		$root['return'] = 1;
		m_display($root);
	}
}
?>