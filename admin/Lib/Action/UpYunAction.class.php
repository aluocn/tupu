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
class UpYunAction extends CommonAction
{
	public function index()
	{
		
		$is_has=0;
		$UpYun=array();
		if(!file_exists(FANWE_ROOT."./public/adv_api/UpYun.php")){
			
		}else{
			$is_has=1;
			$UpYun=@require(FANWE_ROOT."./public/adv_api/UpYun.php");
		}
		$this->assign('UpYun',$UpYun);
		$this->assign('is_has',$is_has);
		
		$this->display();
	}
	public function test(){
		@require(FANWE_ROOT."./core/class/upyun.class.php");
		 $space_name=$_REQUEST['space_name'];
	     $user=urlencode($_REQUEST['user']);
	     $password=$_REQUEST['password'];
	     $upyun = new UpYun($space_name, $user,$password);
	     //$upyun->debug = true;
	     $space_num=$upyun->getBucketUsage();
	    // var_dump($space_num);
	     $result=array();
	     if($space_num!==null){
	     	
	     	$result['status']=1;
	     }else{
	     	
	     	$result['status']=0;
	     }
	     echo $result['status'];
	     
	}
   public function add()
   {

       $this->display ();
   }
   /*
   public function insert()
   {
       $appkey=UpYunKEY;
	   $siteurl=$_REQUEST['siteurl'];
	   $sitename=urlencode($_REQUEST['sitename']);
	   $useremail=$_REQUEST['useremail'];
	   $url="http://partner.UpYun.com/reg.ashx?appkey=".$appkey."&siteurl=".$siteurl."&sitename=".$sitename."&useremail=".$useremail;
	   
	   $result=trim(file_get_contents($url));
	  
	   
	   if(strpos($result, "script")==FALSE){
	   		$this->error ($result);
	   }else{
	   		$UpYun_str='<?php 
	   			return  array("UpYun_js" =>\''.$result.'\' ,"siteurl" =>"'.$siteurl.'" 
	   						,"sitename" =>"'.urldecode($sitename).'" ,"useremail" =>"'.$useremail.'" , );
	   		 ?> ';
			$re= file_put_contents(FANWE_ROOT."./public/adv_api/UpYun.php", $UpYun_str);
			if($re>0){
			$this->assign("jumpUrl",u("UpYun/index"));
	   		$this->success (L('OPEN_SUCCESS'));
			}else{
				$this->error (L('WRITE_FAILD'));
			}
	
	   }
	   
   }
	*/
	public function edit()
	{
		
		$this->display ();
	}

	

	public function update()
	{
		 $appkey=UpYunKEY;
	   $space_name=$_REQUEST['space_name'];
	   $user=urlencode($_REQUEST['user']);
	   $password=$_REQUEST['password'];
	   $status=$_REQUEST['status'];
	   $old_status=$_REQUEST['old_status'];
	   $url=$_REQUEST['url'];
	  $UpYun_str='<?php 
	   			return  array("space_name" =>\''.$space_name.'\' ,"user" =>"'.$user.'" 
	   						,"password" =>"'.$password.'" ,"status" =>"'.$status.'","old_status" =>"'.$old_status.'" ,"url" =>"'.$url.'" , );
	   		 ?> ';
			$re= file_put_contents(FANWE_ROOT."./public/adv_api/UpYun.php", $UpYun_str);

	   if($re>0){
	   		$this->assign("jumpUrl",u("UpYun/index"));

			
	   		$this->success (L('UPDATE_SUCCESS'));
	   }else{
	   		$this->error (L('WRITE_FAILD'));
	   		
	   }
	}
}

?>