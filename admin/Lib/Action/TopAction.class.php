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
 * 后台首页
 +------------------------------------------------------------------------------
 */
class TopAction extends FanweAction
{
	public function index()
	{
		$modle=M('SysConf');
		$condition['name']="SYS_VERSION";
		//查询版本信息
		$res=$modle->where($condition)->select();
		$version=$res[0]['val'];
		//给版本信息赋值
		$this->assign('sys_version',$version);
		//当前时间Y-m-d H:i:s
		$now=date('Y-m-d H:i:s',time());
		$this->assign('sys_date',$now);
		//会员总数
		$modle_user=M('User');
		$condition_user['status']=1;
		$res_user=$modle_user->where($condition_user)->count();
		//会员总数
		$this->assign('sys_users',$res_user);
		//统计 分享总数
		$modle_share=M('Share');
		$condition_share['status']=1;
		//图片分享总数
		$condition_share['is_video']=0;
		$condition_share['share_data']=array('in',"goods,photo,video");
		$res_share_all=$modle_share->where($condition_share)->count();
		$this->assign('sys_share_all',$res_share_all);
		//有图片的
		//视频分享数
		$condition_share_v['is_video']=1;
		$res_video_all=$modle_share->where($condition_share_v)->count();
		$this->assign('sys_share_video',$res_video_all);
		//商品分享数
		$condition_share_g['is_video']=2;
		$res_goods_all=$modle_share->where($condition_share_g)->count();
		$this->assign('sys_share_goods',$res_goods_all);
		//文章分享数
		$condition_share_g['is_video']=3;
		$res_paper_all=$modle_share->where($condition_share_g)->count();
		$this->assign('sys_share_paper',$res_paper_all);
		
		//未处理过的
		$c['status']=0;
		$c['share_data']=array('in',"goods,photo,video");
		//图片未处理
		$c['is_video']=0;
		$res_share_photo_none=$modle_share->where($c)->count();
		//视频未处理
		$v['is_video']=1;
		$v['status']=0;
		$res_share_video_none=$modle_share->where($v)->count();
		//商品未处理
		$g['is_video']=2;
		$g['status']=0;
		$res_goods_video_none=$modle_share->where($g)->count();
		//文章未处理
		$p['is_video']=3;
		$p['status']=0;
		$res_share_paper_none=$modle_share->where($p)->count();
		
		$this->assign('res_share_photo_none',$res_share_photo_none);
		$this->assign('res_share_video_none',$res_share_video_none);
		$this->assign('res_share_goods_none',$res_goods_video_none);
		$this->assign('res_share_paper_none',$res_share_paper_none);
		
		$sys_host=$_SERVER['HTTP_HOST'];
		$this->assign('sys_host',$sys_host);
		//是否有权限
		$grant=check_grant();
		if(!$grant){
			$is_sys_tel_grant=0;
		}else{
			$is_sys_tel_grant=1;
		}
		$this->assign('is_sys_tel_grant',$is_sys_tel_grant);
		$this->assign('sys_tel_grant',$grant);
		//判断手机端是否安装成功
		$re=M()->query("SHOW TABLES LIKE '%_apns\_%' ");
		$m_num_1=count($re);
		$re=M()->query("SHOW TABLES LIKE '".C("DB_PREFIX")."m\_%' ");
		$m_num_2=count($re);
		$all=$m_num_1+$m_num_2;
		$this->assign('sys_tel_exists',$all);
		
		
		$this->display();
	}


}
?>