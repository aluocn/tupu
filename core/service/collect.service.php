<?php
// +----------------------------------------------------------------------
// | 方维购物分享网站系统 (Build on ThinkPHP)
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://fanwe.com All rights reserved.
// +----------------------------------------------------------------------

/**
 * collect.service.php
 *
 * 分享服务类
 *
 * @package service
 * @author fishsword <fishsword@qq.com>
 */
class CollectService
{
	/**
	 * 通过的分享提交表单的数据处理
	 * @param mix $_POST 为标准分享表单 $_POST['type'] default:默认,bar:主题,ershou:二手,ask:问答
	 * $_POST['share_data'] = photo 有图 goods 有产品 goods_photo:有图有商品 default:都没有
	 * 	* 返回
	 *  array(
	 *   'status' => xxx  状态  bool
	 *   'share_id' => share_id
	 *   'error_code' => '错误代码'
	 *   'error_msg' => 错误描述
	 * )
	 */
	public function submitList($post_data,$is_album = FALSE,$is_score = FALSE,$is_check = TRUE,$type = 'album_item')
	{
		//创建分享数据
		global $_FANWE;
		$result = true;
		foreach($post_data['album_id'] as $k => $v)
		{
			$share_content = htmlspecialchars(trim($post_data['content'][$k]));
			$share_data = array();
			$share_data['content'] = $share_content;
			$share_data['uid'] = intval($_FANWE['uid']);
			$share_data['parent_id'] = 0; //分享的转发
			$share_data['rec_id'] = 0; //关联的编号
			$share_data['base_id'] = 0;
			$share_data['refer_url'] = $post_data['pageUrl'];
			if($post_data['pageUrl']){
				$share_data['is_refer'] = 1;	
			}else{
				$share_data['is_refer'] = 0;
			}
			
			$share_data['albumid'] = intval($post_data['album_id'][$k]);
			//默认设置is_video 为0
			$share_data['is_video'] = $post_data['is_video'];
			//检测关键字
			if($is_check)
			{
				$check_result = FS("share")->checkWord($share_data['content'],'content');
				if($check_result['error_code'] == 1)
				{
					$check_result['status'] = false;
					return $check_result;
				}
			}
			$share_data['type'] = $type;
			$share_data['title'] = isset($post_data['title'][$k]) ? htmlspecialchars(trim($post_data['title'][$k])) : '';
			if(!empty($share_data['title']) && $is_check)
			{
				$check_result = FS("share")->checkWord($share_data['title'],'title');
				if($check_result['error_code'] == 1)
				{
					$check_result['status'] = false;
					return $check_result;
				}
			}
			$data = array();
			
			
			//创建图库数据
			$share_photos_data = array();
			
			
			$c_data = array();
			
//			if(isset($post_data['is_video'])){//本地上传的图片保存的时候
//				if()
//			}else{
//				if(strpos($post_data['imgs'][$k],$_FANWE['site_url']) === false){
//					$c_data['img'] = $_FANWE['site_url'].".".$post_data['imgs'][$k];
//				}else{
//					$c_data['img'] = $post_data['imgs'][$k];
//				}
//			}
			if(!empty($post_data['server_code'])){
				if(!empty($post_data['share_info'][$k])){
					$c_data['img']=$_FANWE['site_url'].".".$post_data['imgs'][$k];
				}else{
					$c_data['img'] = $post_data['imgs'][$k];
				}
				
			}else{
				if(!empty($post_data['share_info'][$k])){
					$arr = authcode($post_data['share_info'][$k],'DECODE');
					$arr = unserialize($arr);
					$c_data['img']=$arr['path'];
				}else{
					$c_data['img'] = $post_data['imgs'][$k];
				}
			}
			//添加视频数据，同时添加 is_video 标识
			if( (!empty($post_data['videos'][$k])&&($post_data['videos'][$k]!='undefined'))){
				$c_data['video']=$post_data['videos'][$k];
				$share_data['is_video']=1;
			}
			
			if(empty($type))
					$type = 'photo';
			switch($share_data['is_video']){
				case 1:
				$type = 'video';
				break;
				case 2:
				$type = 'goods';
				break;
				default:
				$type = 'photo';
				break;
			}
			$share_data['share_data'] = $type;
			
			$data['share'] = $share_data;
			
			$c_data['sort'] = 10;
			
			$c_data['server_code']=$post_data['server_code'];
			array_push($share_photos_data,$c_data);
					
			$data['share_photo'] = $share_photos_data;
			
			if($share_data['albumid'] > 0 && count($share_photos_data) == 0 )
				exit;
	
			$data['pub_out_check'] = intval($post_data['pub_out_check']);  //发送到外部微博
			
			$submit_result = FS("share")->save($data,$is_score,$is_album);
			
			if(!$submit_result)
			{
				$result = false;
				break;
			}
		}
		return $result;
	}
	
	
	
	public function submitImg($post_data,$is_album = FALSE,$is_score = FALSE,$is_check = TRUE,$type = 'album_item')
	{
		//创建分享数据
		global $_FANWE;
		$result = true;
		
		$share_content = htmlspecialchars(trim($post_data['content']));
		$share_data = array();
		$share_data['content'] = $share_content;
		$share_data['uid'] = intval($_FANWE['uid']);
		$share_data['parent_id'] = 0; //分享的转发
		$share_data['rec_id'] = 0; //关联的编号
		$share_data['base_id'] = 0;
		$share_data['refer_url'] = $post_data['pageUrl'];
		$share_data['is_refer'] = 1;
		$share_data['albumid'] = intval($post_data['album_id']);
		//默认设置is_video 为0
		$share_data['is_video'] = $post_data['is_video'];
		//检测关键字
		if($is_check)
		{
			$check_result = FS("share")->checkWord($share_data['content'],'content');
			if($check_result['error_code'] == 1)
			{
				$check_result['status'] = false;
				return $check_result;
			}
		}
		$share_data['type'] = $type;
		$share_data['title'] = isset($post_data['title']) ? htmlspecialchars(trim($post_data['title'])) : '';
		if(!empty($share_data['title']) && $is_check)
		{
			$check_result = FS("share")->checkWord($share_data['title'],'title');
			if($check_result['error_code'] == 1)
			{
				$check_result['status'] = false;
				return $check_result;
			}
		}
		$data = array();
		
		
		//创建图库数据
		$share_photos_data = array();
		
		
		$c_data = array();
		
		
		if($post_data['info']){//拍拍分享的宝贝
			
			$arr = authcode($post_data['info'],'DECODE');
			$arr = unserialize($arr);
			if($post_data['server_code']){
				 $c_data['img']=$_FANWE['site_url'].".".$arr['url'];
			}else{
				$c_data['img']=$arr['path'];
			}
			
		}else{
			$c_data['img'] = $post_data['imgs'];	
		}
			
		
		
		//添加视频数据，同时添加 is_video 标识
		if( (!empty($post_data['videos'])&&($post_data['videos']!='undefined'))){
			$c_data['video']=$post_data['videos'];
			$share_data['is_video']=1;
		}
		
		if(empty($type))
				$type = 'photo';
		switch($share_data['is_video']){
			case 1:
			$type = 'video';
			break;
			case 2:
			$type = 'goods';
			break;
			default:
			$type = 'photo';
			break;
		}
		$share_data['share_data'] = $type;
		
		$data['share'] = $share_data;
		
		$c_data['sort'] = 10;
		$c_data['server_code']=$post_data['server_code'];

		array_push($share_photos_data,$c_data);
				
		$data['share_photo'] = $share_photos_data;
		
		if($share_data['albumid'] > 0 && count($share_photos_data) == 0 )
			exit;

		$data['pub_out_check'] = intval($post_data['pub_out_check']);  //发送到外部微博
		
		
		$submit_result = FS("share")->save($data,$is_score,$is_album);

		if(!$submit_result)
		{
			$result = false;
		}
		return $result;
	}
}
?>