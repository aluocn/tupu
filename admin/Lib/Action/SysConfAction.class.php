<?php
// +----------------------------------------------------------------------
// | 方维购物分享网站系统 (Build on ThinkPHP)
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: awfigq <awfigq@qq.com>
// +----------------------------------------------------------------------
/**
 +------------------------------------------------------------------------------
 * 系统设置
 +------------------------------------------------------------------------------
 */
class SysConfAction extends CommonAction
{
	public function index()
	{
		$list = D('SysConf')->where("status=1 and group_id > 0 and is_show = 1")->order("sort asc")->findAll();
		$conf_list = array(); //用于输出分组格式化后的数组

		foreach($list as $k=>$v)
		{
			if($v['name']=='DEFAULT_LANG')
			{
			   $lang_list = Dir::getList(LANG_PATH);
			   $v['val_arr'] = array();
			   foreach($lang_list as $lang_item)
			   {
				   if($lang_item != '.' && $lang_item != '..')
				   {
					   $v['val_arr'][] = $lang_item;
				   }
			   }
			}
			elseif($v['name']=='SITE_TMPL')
			{
			   $tmpl_list = Dir::getList(FANWE_ROOT.'tpl');
			   $v['val_arr'] = array();
			   foreach($tmpl_list as $tmpl_item)
			   {
				   if($tmpl_item != '.' && $tmpl_item != '..')
				   {
					   $v['val_arr'][] = $tmpl_item;
				   }
			   }
			}
			else
			{
				$v['val_arr'] = explode(",",$v['val_arr']);
			}

			$conf_list[L("SYSCONF_GROUP_".$v['group_id'])][$k] = $v;
		}
		$this->assign("conf_list",$conf_list);
		$this->display();
	}

	public function update()
	{
		$upload_list = $this->uploadImages();
		
		if($upload_list)
		{
			foreach($upload_list as $upload_item)
			{
				if($upload_item['key']=="SITE_LOGO")
				{
					$site_logo = $upload_item['recpath'].$upload_item['savename'];
					$img_exe=get_image_type($site_logo);
					$site_logo = moveFile($site_logo,'./logo.'.$img_exe);
				}
				if($upload_item['key']=="WATER_IMAGE")
				{
					$water_image = $upload_item['recpath'].$upload_item['savename'];
				}
				if($upload_item['key']=="FOOT_LOGO")
				{
					$foot_logo = $upload_item['recpath'].$upload_item['savename'];
					$img_exe=get_image_type($foot_logo);
					$foot_logo = moveFile($foot_logo,'./foot_logo.'.$img_exe);
				}
				if($upload_item['key']=="LINK_LOGO")
				{
					$link_logo = $upload_item['recpath'].$upload_item['savename'];
					$img_exe=get_image_type($link_logo);
					$link_logo = moveFile($link_logo,'./link_logo.'.$img_exe);
				}
				if($upload_item['key'] == "SITE_VIDEO")
				{
					$site_video_logo = $upload_item['recpath'].$upload_item['savename'];
					$swf_name=basename(strtolower($site_video_logo));
					$swf_name=explode(".",$swf_name);
					$swf_name=$swf_name[1];
					if( ($swf_name=='swf')||($swf_name=='flv')){
						$site_video_logo = moveFile($site_video_logo,'./site_video_logo.'.$swf_name);
					}else{
						$site_video_logo='';
					}
				}
			}
		}

		$list = D('SysConf')->where("status=1")->findAll();
		foreach($list as $k=>$v)
		{
			$v['val'] = isset($_REQUEST[$v['name']])?$_REQUEST[$v['name']]:$v['val'];
			if($v['name']=="SITE_LOGO" && !empty($site_logo))
			{
				if($site_logo != $v['val'])
				{
					@unlink(FANWE_ROOT.$v['val']);
					$v['val'] = $site_logo;
				}
			}

			if($v['name']=="WATER_IMAGE" && !empty($water_image))
			{
				if($water_image != $v['val'])
				{
					@unlink(FANWE_ROOT.$v['val']);
					$v['val'] = $water_image;
				}
			}

			if($v['name']=="FOOT_LOGO" && !empty($foot_logo))
			{
				if($foot_logo != $v['val'])
				{
					@unlink(FANWE_ROOT.$v['val']);
					$v['val'] = $foot_logo;
				}
			}

			if($v['name']=="LINK_LOGO" && !empty($link_logo))
			{
				if($link_logo != $v['val'])
				{
					@unlink(FANWE_ROOT.$v['val']);
					$v['val'] = $link_logo;
				}
			}
			if($v['name']=="SITE_VIDEO" && !empty($site_video_logo))
			{
				if($site_video_logo != $v['val'])
				{
					@unlink(FANWE_ROOT.$v['val']);
					$v['val'] = $site_video_logo;
				}
			}

			D('SysConf')->save($v);
		}

		$this->saveLog(1);
		$this->success(L('EDIT_SUCCESS'));
	}
}

function moveFile($file_name,$target_name)
{
	$name = $file_name;

	if (function_exists("move_uploaded_file"))
	{
		if (move_uploaded_file(FANWE_ROOT.$file_name,FANWE_ROOT.$target_name))
		{
			chmod(FANWE_ROOT.$target_name,0755);
			$name = $target_name;
			unlink(FANWE_ROOT.$file_name);
		}
		else if (copy(FANWE_ROOT.$file_name,FANWE_ROOT.$target_name))
		{
			chmod(FANWE_ROOT.$target_name,0755);
			$name = $target_name;
			unlink(FANWE_ROOT.$file_name);
		}
	}
	elseif (copy(FANWE_ROOT.$file_name,FANWE_ROOT.$target_name))
	{
		chmod(FANWE_ROOT.$target_name,0755);
		$name = $target_name;
		unlink(FANWE_ROOT.$file_name);
	}

	return $name;
}

//获取图片类型
function get_image_type($origin_path){
		$image_name=basename($origin_path);
		$type=explode(".",$image_name);
		$type=strtolower($type[1]);
	 	//$type=	$image_info['type']; //获取图片类型
	 	if(empty($type)||!get_image_exe($type)){
	 		$imags=get_image_info_g($origin_path);
	 		if(!empty($imags['type'])){
	 			$type=$imags['type'];
	 		}else{
	 			return false;
	 		}
	 		
	 	}
	 	if($type=='jpg'){
	 		$type='jpeg';
	 	}
	 	return $type;
}

//图片后缀
function get_image_exe($exe){
	$info=array('gif','jpeg','jpg','png');
	return in_array($exe,$info);
}
//获取远程信息
function file_get_url_g($url=''){
	if(!empty($url)){
		$ctx = stream_context_create(array(
			'method'=>'GET',
			'http' => array(  
				'timeout' => 1 //设置一个超时时间，单位为秒  
			)  
		)  
		); 
		$files=file_get_contents($url,false,$ctx);
		return $files;
	}else{
		return false;
	}
}
//获取图片格式的方法
 function get_image_info_g($src){
		$image_info=getimagesize($src);
		$result=array();
		$result['width']=$image_info[0];
		$result['height']=$image_info[1];
		switch($image_info[2]){
			case 1:
			$result['type']='gif';
			break;
			case 2:
			$result['type']='jpeg';
			break;
			case 3:
			$result['type']='png';
			break;
			case 15:
			$result['type']='wbmp';
			break;
			case 16:
			$result['type']='xmb';
			break;
		}
		return $result;
		
	}
?>