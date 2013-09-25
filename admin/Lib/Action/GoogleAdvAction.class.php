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
 * 管理员
 +------------------------------------------------------------------------------
 */
class GoogleAdvAction extends CommonAction
{
	public function index()
	{
		
		$is_has=1;
		$google=array();
		if(!file_exists(FANWE_ROOT."./public/adv_api/Google.php")){
			
		}else{
			$is_has=1;
			$google=@require(FANWE_ROOT."./public/adv_api/Google.php");
			$google['google_js']=urldecode($google['google_js']);
		}
		
		$this->assign('google',$google);
		$this->assign('is_has',$is_has);
		$this->display();
	}

	public function update()
	{
		
	   $is_open=urlencode($_REQUEST['is_open']);
	  
	   $google_js=urlencode($_REQUEST['google_js']);
	  $diangao_str='<?php 
	   			return  array("is_open" =>\''.$is_open.'\' ,"google_js" =>"'.$google_js.'" , );
	   		 ?> ';
			$re= file_put_contents(FANWE_ROOT."./public/adv_api/Google.php", $diangao_str);

	   if($re>0){
	   		$this->assign("jumpUrl",u("GoogleAdv/index"));

			
	   		$this->success (L('UPDATE_SUCCESS'));
	   }else{
	   		$this->error (L('WRITE_FAILD'));
	   		
	   }
	}
}

?>