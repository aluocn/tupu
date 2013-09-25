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
class DianGaoAction extends CommonAction
{
	public function index()
	{
		
		$is_has=0;
		$diangao=array();
		if(!file_exists(FANWE_ROOT."./public/adv_api/DianGao.php")){
			
		}else{
			$is_has=1;
			$diangao=@require(FANWE_ROOT."./public/adv_api/DianGao.php");
		}
		$this->assign('diangao',$diangao);
		$this->assign('is_has',$is_has);
		$this->display();
	}
   public function add()
   {

       $this->display ();
   }
   public function insert()
   {
       $appkey=DIANGAOKEY;
	   $siteurl=$_REQUEST['siteurl'];
	   $sitename=urlencode($_REQUEST['sitename']);
	   $useremail=$_REQUEST['useremail'];
	   $url="http://partner.diangao.com/reg.ashx?appkey=".$appkey."&siteurl=".$siteurl."&sitename=".$sitename."&useremail=".$useremail;
	   
	   $result=trim(file_get_contents($url));
	  
	   
	   if(strpos($result, "script")==FALSE){
	   		$this->error ($result);
	   }else{
	   		$diangao_str='<?php 
	   			return  array("diangao_js" =>\''.$result.'\' ,"siteurl" =>"'.$siteurl.'" 
	   						,"sitename" =>"'.urldecode($sitename).'" ,"useremail" =>"'.$useremail.'" , );
	   		 ?> ';
			$re= file_put_contents(FANWE_ROOT."./public/adv_api/DianGao.php", $diangao_str);
			if($re>0){
			$this->assign("jumpUrl",u("DianGao/index"));
	   		$this->success (L('OPEN_SUCCESS'));
			}else{
				$this->error (L('WRITE_FAILD'));
			}
	
	   }
	   
   }

	public function edit()
	{
		
		$this->display ();
	}

	

	public function update()
	{
		 $appkey=DIANGAOKEY;
	   $siteurl=$_REQUEST['siteurl'];
	   $sitename=urlencode($_REQUEST['sitename']);
	   $useremail=$_REQUEST['useremail'];
	   $diangao_js=$_REQUEST['diangao_js'];
	  $diangao_str='<?php 
	   			return  array("diangao_js" =>\''.$diangao_js.'\' ,"siteurl" =>"'.$siteurl.'" 
	   						,"sitename" =>"'.urldecode($sitename).'" ,"useremail" =>"'.$useremail.'" , );
	   		 ?> ';
			$re= file_put_contents(FANWE_ROOT."./public/adv_api/DianGao.php", $diangao_str);

	   if($re>0){
	   		$this->assign("jumpUrl",u("DianGao/index"));

			
	   		$this->success (L('UPDATE_SUCCESS'));
	   }else{
	   		$this->error (L('WRITE_FAILD'));
	   		
	   }
	}
}

?>