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
 * 分享
 +------------------------------------------------------------------------------
 */
class ShareAction extends CommonAction
{
	public function index()
	{
		$where = '';
		$parameter = array();
		$keyword = trim($_REQUEST['keyword']);
		$uname = trim($_REQUEST['uname']);
		$type = 'album_item';
		$share_data = !isset($_REQUEST['share_data']) ? 'all' : trim($_REQUEST['share_data']);
		$cate_id = intval($_REQUEST['cate_id']);
		$status = !isset($_REQUEST['status']) ? -1: intval($_REQUEST['status']);
		$inner_sql = '';
		
		if($share_data == 'img')
		{
			$this->assign("share_data",$share_data);
			$where .= " WHERE s.share_data IN ('goods','photo','video')";
			$where .=" AND s.is_video=0 ";
		}elseif($share_data == 'video')
		{
			$this->assign("share_data",$share_data);
			$parameter['share_data'] ='video';
			//$share_data='photo';
			$where .= " WHERE s.is_video=1 ";
		}elseif($share_data == 'goods')
		{
			$this->assign("share_data",$share_data);
			$parameter['share_data'] ='goods';
			$share_data='photo';
			$where .= " WHERE ";
			$where .="  s.is_video=2 ";
		}elseif($share_data == 'paper')
		{
			$this->assign("share_data",$share_data);
			$parameter['share_data'] ='paper';
			$share_data='paper';
			$where .= " WHERE ";
			$where .="  s.is_video=3 ";
		}
		elseif($share_data == 'default')
		{
			$this->assign("share_data",$share_data);
			$parameter['share_data'] = $share_data;
			$where .= " WHERE s.share_data  NOT IN ('goods','photo','video','paper')";
		}elseif($share_data == 'all'){
			$this->assign("share_data",$share_data);
			$parameter['share_data'] = $share_data;
			$where .= " WHERE 1 ";
		}else{
			$this->assign("share_data",'all');
			$parameter['share_data'] = 'all';
			$where .= " WHERE 1 ";
		}
		
		if(!empty($keyword))
		{
			$this->assign("keyword",$keyword);
			$parameter['keyword'] = $keyword;
			if($share_data != 'default')
			{
				$match_key = segmentToUnicodeA($keyword,'+');
				$where.=" AND match(sm.content_match) against('".$match_key."' IN BOOLEAN MODE) ";
				$inner_sql .= 'INNER JOIN '.C("DB_PREFIX").'share_match AS sm ON sm.share_id = s.share_id ';
			}
			else
			{
				$where .= " AND s.content LIKE '%".mysqlLikeQuote($keyword)."%'";
			}
		}

		if(!empty($uname))
		{
			$this->assign("uname",$uname);
			$parameter['uname'] = $uname;
			$match_key = segmentToUnicodeA($uname,'+');
			$where.=" AND match(u.user_name_match) against('".$match_key."' IN BOOLEAN MODE) ";
		}

		if(!empty($type) && $type != 'all')
		{
			$this->assign("type",$type);
			$parameter['type'] = $type;
			$where .= " AND s.type = '$type'";
		}

		if($cate_id != 0)
		{
			$this->assign("cate_id",$cate_id);
			$parameter['cate_id'] = $cate_id;

			if($cate_id > 0)
			{
				$child_ids = D('GoodsCategory')->getChildIds($cate_id,'cate_id');
				$child_ids[] = $cate_id;

				$where .= " AND sc.cate_id IN (".implode(',',$child_ids).")";
			}
			else
				$where .= " AND sc.cate_id IS NULL";
		}
		
		$this->assign("status",$status);
		if($status != -1){
			$parameter['status'] = $status;
		    $where .= " AND s.status = ".$status;
		}
		
		$model = M();

		$sql = 'SELECT COUNT(DISTINCT s.share_id) AS scount
			FROM '.C("DB_PREFIX").'share AS s
			LEFT JOIN '.C("DB_PREFIX").'share_category AS sc ON sc.share_id = s.share_id 
			LEFT JOIN '.C("DB_PREFIX").'user AS u ON u.uid = s.uid 
			'.$inner_sql.$where;
		$count = $model->query($sql);
		$count = $count[0]['scount'];

		$sql = 'SELECT s.share_id,LEFT(s.content,80) AS content,u.user_name,s.create_time,s.collect_count,s.relay_count,s.comment_count,s.type,s.share_data,GROUP_CONCAT(DISTINCT gc.cate_name SEPARATOR \'<br/>\') AS cate_name,s.status 
			FROM '.C("DB_PREFIX").'share AS s 
			LEFT JOIN '.C("DB_PREFIX").'user AS u ON u.uid = s.uid 
			LEFT JOIN '.C("DB_PREFIX").'share_category AS sc ON sc.share_id = s.share_id 
			LEFT JOIN '.C("DB_PREFIX").'goods_category AS gc ON gc.cate_id = sc.cate_id 
			'.$inner_sql.$where.' GROUP BY s.share_id';
		$this->_sqlList($model,$sql,$count,$parameter,'s.share_id');
		
		$root_id = D('GoodsCategory')->where('is_root = 1')->getField('cate_id');
		$root_id = intval($root_id);
		$root_ids = D('GoodsCategory')->getChildIds($root_id,'cate_id');
		$root_ids[] = $root_id;
		
		$cate_list = D('GoodsCategory')->where('cate_id not in ('.implode(',',$root_ids).')')->order('sort asc')->findAll();
		$cate_list = D('GoodsCategory')->toFormatTree($cate_list,'cate_name','cate_id','parent_id');
		$this->assign("cate_list",$cate_list);
		
		//审核赋值
		$this->assign('status',$status);
		$this->display ();
	}
	function getId(){
		$share_id=$_REQUEST['share_id'];
		$width=$_REQUEST['width'];
		$height=$_REQUEST['height'];
		if(empty($share_id)){
			$data['status']=0;
		    $data['msg']='没有上传分享ID';
		    echo json_encode($data);
		}
		$img=D('SharePhoto')->where('share_id='.$share_id)->getField('img');
		$url=D('Share')->where('share_id='.$share_id)->getField('refer_url');
		$type=D('SharePhoto')->where('share_id='.$share_id)->getField('type');
		$server_code=D('SharePhoto')->where('share_id='.$share_id)->getField('server_code');
		Vendor("common");
		global $_FANWE;
		
		$id=$share_id;
		if(!empty($img)&&($id>0)){
			$status=1;
			$id=$id;
			
			
			if($server_code){
				$img=FS('Image')->getImageUrl($img);
			}else{
				if(IS_UPYUN){
					if(strpos($img,'./upyun/') !== FALSE){
						$img=str_replace("./upyun/",'',$img);
						$img=$img."!"."468x468";
						$upyun=require(FANWE_ROOT."public/adv_api/UpYun.php");
						$img=$upyun['url'].$img;
					}else{
						$img=$_FANWE['site_url'].$img;
					}
				}else{
					$img=$_FANWE['site_url'].$img;	
				}
					
			}
			$msg=$img;
			$img_stat=getimagesize($img);
			if($img_stat){
				$img_width=($img_stat[0]-33)/2;
				$img_height=($img_stat[1]-33)/2;
				
				$data['width']=$img_width;
				$data['height']=$img_height;
				$data['left']=($width-$img_stat[0])/2;
				if($height<=$img_stat[1]){
					$data['top']=0;
				}else{
					$data['top']=($height-$img_stat[1])/2;
				}
			}else{
				$status=0;
			    $msg='无法在服务器找到图片,已丢失！';
			}
		}else{
			$status=0;
			$msg='没有图片';
		}
		if(!empty($url)){
			$data['url']=$url;
		}
		if(!empty($type)){
			$data['type']=$type;
		}
		$data['id']=$id;
		$data['status']=$status;
		$data['msg']=$msg;
		
		echo json_encode($data);
	}
	//修改 分享的状态，是否审核通过，并设置为管理员已经处理
	function change_status(){
		$share_id=$_REQUEST['share_id'];
		$status=$_REQUEST['status'];
		$condition['share_id']=$share_id;
		
		if($status==1){
			$share_status=D('Share')->where('share_id='.$share_id)->getField('status');
			if($share_status==1){
				$is=3;
			}else{
				$re=D('Share')->where($condition)->setField('status','1');
			if($re){
				$is=1;
			}else{
				$is=0;
			}
			}
			
		}else{
			$share_check_status=D('Share')->where('share_id='.$share_id)->getField('status');
			if($share_check_status==0){
				$is=3;
			}else{
				$re=D('Share')->where($condition)->setField('status','0');
			
			if($re){
				$is=1;
			}else{
				$is=0;
			}
			}
			
			
		}
		//表示管理员已经处理过
		D('Share')->where($condition)->setField('check_status','1');
		$data['status']=$is;
		echo json_encode($data);
	}
	function edit()
	{
		Vendor('common');
		global $_FANWE;
		$id = $_REQUEST ['share_id'];
		$share = D ("Share")->getById ($id);
		if(!$share)
		{
			$this->error(L("NO_SHARE"));
		}
		if($share['share_data'] == 'paper'){
			$paper = FDB::fetchfirst("select text,photo_id,img from ".FDB::table("share_photo")." where share_id = ".$share['share_id']);
			$server_code=D('SharePhoto')->where('share_id='.$share['share_id'])->getField('server_code');
		}else{
			$share['share_photo'] = FDB::fetchAll("select photo_id,adv_url,img,server_code from ".FDB::table("share_photo")." where share_id = ".$share['share_id']);	
			$server_code=D('SharePhoto')->where('share_id='.$share['share_id'])->getField('server_code');
			
		}
		
		foreach($share['share_photo'] as $k=>$v){
			if($server_code){
				$img=FS('Image')->getImageUrl($v['img'],1);
				$share['share_photo'][$k]['img']=$img;
			}else{
				if(IS_UPYUN){
					if(strpos($v['img'],'./upyun/') !== FALSE){
						$v['img']=str_replace("./upyun/",'',$v['img']);
						$v['img']=$v['img']."!"."100x100";
						$upyun=require(FANWE_ROOT."public/adv_api/UpYun.php");
						$share['share_photo'][$k]['img']=$upyun['url'].$v['img'];
					}else{
						$share['share_photo'][$k]['img']=$_FANWE['site_url'].getImgName($v['img'],100,100,0);
					}
				}else{
					$share['share_photo'][$k]['img']=$_FANWE['site_url'].getImgName($v['img'],100,100,0);	
				}
			}
		}
		$path = $_FANWE['site_root'];
		$this->assign ( 'path', $path );
		$this->assign ( 'paper', $paper );	
		$this->assign ( 'share', $share );
		$this->assign('server_code',$server_code);
		$this->display ();
	}
	public function change_status_js(){
		//修改状态
		error_reporting(E_ALL);
		Vendor('common');
	
		$result = array('isErr'=>0,'content'=>'');
		$id = $_REQUEST['id'];
		$kye=$_REQUEST['key'];
		$val=$_REQUEST['val']?$_REQUEST['val']:0;
		/*
		//测试
		$id='49,48';
		$kye='status';
		$val=1;
		*/
		if(!empty($id))
		{
			//$share_ids = explode ( ',', $id );
			$condition['share_id']=array('in',$id);
			$data[$kye]=$val;
			$re=D('Share')->where($condition)->save($data);
			$this->saveLog(1,$id);
		}
		else
		{
			$result['isErr'] = 1;
			$result['content'] = L('ACCESS_DENIED');
		}
		die(json_encode($result));
	}
	public function remove()
	{
		//删除指定记录
		error_reporting(E_ALL);
		Vendor('common');
		$result = array('isErr'=>0,'content'=>'');
		$id = $_REQUEST['id'];
		if(!empty($id))
		{
			$share_ids = explode ( ',', $id );
			D('Share')->removeHandler($share_ids);
			$this->saveLog(1,$id);
		}
		else
		{
			$result['isErr'] = 1;
			$result['content'] = L('ACCESS_DENIED');
		}
		die(json_encode($result));
	}

	public function removePhoto()
	{
		$photo_id = intval($_REQUEST['photo_id']);
		$photo_data = D("SharePhoto")->where("photo_id=".$photo_id)->find();
		D("SharePhoto")->where("photo_id=".$photo_id)->delete();
		//开始同步data
		$this->init_share_data($photo_data['share_id']);
		$err = D()->getDbError();
		if($err)
		{
			$result['isErr'] = 1;
			$result['content'] = $err;
		}
		else
		{
            Vendor('common');
			if($photo_data['base_id'] == 0)
			{
				$count = D("SharePhoto")->where("base_id=".$photo_id)->count();
				if($count == 0)
				{
					if($photo_data['server_code']){
						FS("Image")->deleteServerImg($photo_data);
					}else{
						if(IS_UPYUN){
							include_once FANWE_ROOT.'class/upyun';
							$uy=@require(FANWE_ROOT."public/adv_api/UpYun.php");
							$upyun = new UpYun($upyun['space_name'], $upyun['user'], $upyun['password']);
							$photo_data['img']=str_replace("./upyun/","",$photo_data['img']);
							$photo_data['img']=$uy['url'].$photo_data['img'];
							$path=$photo_data['img'];
							if($upyun->getFileInfo($path)){
								//若存在则删除文件
								$upyun->deleteFile($path);
							}
						}else{
							deleteShareImg(FANWE_ROOT.$photo_data['img']);	
						}
							
					}
					
				}
					
			}
            
            $share_id = $photo_data['share_id'];
			deleteCache('share/'.getDirsById($share_id).'/imgs');
            deleteCache('share/'.getDirsById($share_id).'/detail');
			FS('Share')->updateShareCache($share_id,'imgs');
			$result['isErr'] = 0;
		}
		die(json_encode($result));
	}

	public function removeGoods()
	{
		$goods_id = intval($_REQUEST['goods_id']);
		$goods_data = D("ShareGoods")->where("goods_id=".$goods_id)->find();
		D("ShareGoods")->where("goods_id=".$goods_id)->delete();
		//开始同步data
		$this->init_share_data($goods_data['share_id']);
		$err = D()->getDbError();
		if($err)
		{
			$result['isErr'] = 1;
			$result['content'] = $err;
		}
		else
		{
            Vendor('common');
			if($goods_data['base_id'] == 0)
			{
				$count = D("ShareGoods")->where("base_id=".$goods_id)->count();
				if($count == 0)
					deleteShareImg(FANWE_ROOT.$goods_data['img']);
			}
            
            $share_id = $goods_data['share_id'];
			deleteCache('share/'.getDirsById($share_id).'/imgs');
            deleteCache('share/'.getDirsById($share_id).'/detail');
			FS('Share')->updateShareCache($share_id,'imgs');
			$result['isErr'] = 0;
		}
		die(json_encode($result));
	}

	public function update() 
	{
		vendor("common");
		$share_type = $_REQUEST['share_type'];
		$share_id = intval($_REQUEST['share_id']);
		$photo_id = intval($_REQUEST['photo_id']);
		$server_code=$_REQUEST['server_code'];
		$img=$_REQUEST['img_url'];
		global $_FANWE;
		$name=$this->getActionName();
		$model = D ( $name );
		if (false === $data = $model->create ()) {
			$this->error ( $model->getError () );
		}
		
		// 更新数据
		$data['title'] = trim($_REQUEST['title']);
		if($_REQUEST['content']){
			$data['content'] = trim($_REQUEST['content']);	
		}else{
			$data['content'] = trim($_REQUEST['title']);
		}
		
		$list=$model->save($data);
		$id = $data[$model->getPk()];
		if (false !== $list) {
            
			if($share_type == 'paper'){//获取文章的内容，并过滤其中的图片的第一个图片
				$data['text'] =$_REQUEST['text'];
				preg_match('/src=\"(.*?)\"/',$data['text'],$matches);
				$img_url=$matches[1];
				if($matches[1]){
					if($server_code){
						$data2['server_code']=$server_code;
						$data2['src']=$_FANWE['site_url'].$img_url;
						$data2['share_id']=$share_id;
						$o_img=FS("Image")->updateImage($data2,true);
						unset($data2);
					}else{
						$img_url = FANWE_ROOT."./".$img_url;
						$o_img = copyImage($img_url,array(),'share',false,$share_id);	
					}
				} 
				$paper_data['img'] = $o_img['url'];
				$paper_data['img_width'] = intval($o_img['width']);
				$paper_data['img_height'] = intval($o_img['height']);
				$paper_data['text'] = $_REQUEST['text'];
				$paper_data['title'] = trim($_REQUEST['title']);
				$paper_data['type'] = 'paper';
				D('SharePhoto')->where("photo_id=".$photo_id)->save($paper_data);
				
			}else{
				if($img){					
					if($server_code){
						$data1['server_code']=$server_code;
						$data1['src']=$_FANWE['site_url'].$img;
						$data1['share_id']=$share_id;
						$o_img=FS("Image")->updateImage($data1,true);
						unset($data1);
					}else{
						$img = FANWE_ROOT."./".$img;
						$o_img = copyImage($img,array(),'share',true,$share_id);	
					}
					
					D('SharePhoto')->where("share_id=".$share_id)->setfield('img',$o_img['url']);
					D('SharePhoto')->where("share_id=".$share_id)->setfield('img_width',$o_img['width']);
					D('SharePhoto')->where("share_id=".$share_id)->setfield('img_height',$o_img['height']);	
				}
				
				if($_REQUEST['adv_url']){
					D('SharePhoto')->where("share_id=".$share_id)->setfield('adv_url',$_REQUEST['adv_url']);
				}
				
			}
			//成功提示
			$this->saveLog(1,$id);
			$this->success (L('EDIT_SUCCESS'));
		} else {
			//错误提示
			$this->saveLog(0,$id);
			$this->error (L('EDIT_ERROR'));
		}
	}
	public function uploadfile()
	{
		vendor("common");
		global $_FANWE;
		if(!isset($_FILES['image']) || empty($_FILES['image']))
			exit;
		
		$result = array();
		$pic = $_FILES['image'];
		include_once fimport('class/image');
		$image = new Image();
		if(intval($_FANWE['setting']['max_upload']) > 0)
			$image->max_size = intval($_FANWE['setting']['max_upload']);
		$image->init($pic);
		if($image->save())
		{
			$result['src'] =$_FANWE['site_url'].$image->file['target'];
			$image->file['target']=str_replace("./","",$image->file['target']);
			$result['img']=$image->file['target'];
			$result['status'] = 1;
		}
		else
		{
			$result['status'] = 0;
		}
		
		outputJson($result);
	}
	
	public function comments()
	{
		if(isset($_REQUEST['share_id']))
			$share_id = intval($_REQUEST['share_id']);
		else
			$share_id = intval($_SESSION['share_comment_share_id']);
		
		$_SESSION['share_comment_share_id'] = $share_id;
		
		$this->assign ( 'share_id', $share_id );
		
		$where = 'WHERE sc.share_id = ' . $share_id;
		$parameter = array();
		$uname = trim($_REQUEST['uname']);

		if(!empty($uname))
		{
			$this->assign("uname",$uname);
			$parameter['uname'] = $uname;
			$match_key = segmentToUnicodeA($uname,'+');
			$where.=" AND match(u.user_name_match) against('".$match_key."' IN BOOLEAN MODE) ";
		}

		$model = M();
		
		$sql = 'SELECT COUNT(DISTINCT sc.comment_id) AS pcount 
			FROM '.C("DB_PREFIX").'share_comment AS sc 
			LEFT JOIN '.C("DB_PREFIX").'user AS u ON u.uid = sc.uid 
			'.$where;

		$count = $model->query($sql);
		$count = $count[0]['pcount'];

		$sql = 'SELECT sc.comment_id,LEFT(sc.content,80) AS content,u.user_name,sc.create_time,sc.share_id  
			FROM '.C("DB_PREFIX").'share_comment AS sc 
			LEFT JOIN '.C("DB_PREFIX").'user AS u ON u.uid = sc.uid 
			'.$where.' GROUP BY sc.comment_id';
		$this->_sqlList($model,$sql,$count,$parameter,'sc.comment_id',false,'returnUrl1');
		
		$this->display ();
		return;
	}
	
	public function editComment()
	{
		$model = D('ShareComment');
		Cookie::set ( '_currentUrl_',NULL );
		$id = $_REQUEST [$model->getPk ()];
		$vo = $model->getById($id);

		$this->assign ( 'vo', $vo );
		$this->display ();
	}
	
	public function updateComment()
	{
		$model = D('ShareComment');
		if (false === $data = $model->create ()) {
			$this->error ( $model->getError () );
		}
		// 更新数据
		$list=$model->save ();
		$id = $data['comment_id'];
		if (false !== $list) {
			//成功提示
			Vendor("common");
			$share_id = D('ShareComment')->where("comment_id = '$id'")->getField('share_id');
			$key = getDirsById($share_id);
			clearCacheDir('share/'.$key.'/commentlist');
			$this->saveLog(1,$id);
			$this->assign ( 'jumpUrl', Cookie::get ( '_currentUrl_' ) );
			$this->success (L('EDIT_SUCCESS'));
		} else {
			//错误提示
			$this->saveLog(0,$id);
			$this->error (L('EDIT_ERROR'));
		}
	}
	
	public function removeComment()
	{
		//删除指定记录
		$result = array('isErr'=>0,'content'=>'');
		$id = $_REQUEST['id'];
		if(!empty($id))
		{
			$model = D('ShareComment');
			$pk = 'comment_id';
			$condition = array ($pk => array ('in', explode ( ',', $id ) ) );
			$count = $model->where( $condition )->count();
			$comments = $model->where($condition)->findAll();
			if(false !== $model->where($condition)->delete())
			{
				Vendor("common");
				$share_id = $_REQUEST['share_id'];
				$key = getDirsById($share_id);
				clearCacheDir('share/'.$key.'/commentlist');
				D('Share')->where("share_id = '$share_id'")->setDec('comment_count',$count);
				FS('Share')->updateShareCache($share_id,'comments');
				$this->saveLog(1,$id);
			}
			else
			{
				$this->saveLog(0,$id);
				$result['isErr'] = 1;
				$result['content'] = L('REMOVE_ERROR');
			}
		}
		else
		{
			$result['isErr'] = 1;
			$result['content'] = L('ACCESS_DENIED');
		}

		die(json_encode($result));
	}
	
	public function dapei()
	{
		$where = '';
		$parameter = array();
		$keyword = trim($_REQUEST['keyword']);
		$uname = trim($_REQUEST['uname']);
		$type = trim($_REQUEST['type']);
		$share_data = !isset($_REQUEST['share_data']) ? 'img' : trim($_REQUEST['share_data']);
		$cate_id = intval($_REQUEST['cate_id']);
		$status = !isset($_REQUEST['status']) ? 0 : intval($_REQUEST['status']);
		$inner_sql = '';
		
		$where .= " WHERE sp.type = 'dapei'";

		if(!empty($keyword))
		{
			$this->assign("keyword",$keyword);
			$parameter['keyword'] = $keyword;
			$match_key = segmentToUnicodeA($keyword,'+');
			$where.=" AND match(sm.content_match) against('".$match_key."' IN BOOLEAN MODE) ";
			$inner_sql .= 'INNER JOIN '.C("DB_PREFIX").'share_match AS sm ON sm.share_id = s.share_id ';
		}

		if(!empty($uname))
		{
			$this->assign("uname",$uname);
			$parameter['uname'] = $uname;
			$match_key = segmentToUnicodeA($uname,'+');
			$where.=" AND match(u.user_name_match) against('".$match_key."' IN BOOLEAN MODE) ";
            $like_name = mysqlLikeQuote($uname);
            $where .= ' AND u.user_name LIKE \'%'.$like_name.'%\'';
		}

		$model = M();

		$sql = 'SELECT COUNT(DISTINCT s.share_id) AS scount
			FROM '.C("DB_PREFIX").'share_photo AS sp
			INNER JOIN '.C("DB_PREFIX").'share AS s ON s.share_id = sp.share_id 
			LEFT JOIN '.C("DB_PREFIX").'user AS u ON u.uid = s.uid 
			'.$inner_sql.$where;

		$count = $model->query($sql);
		$count = $count[0]['scount'];

		$sql = 'SELECT s.share_id,LEFT(s.content,80) AS content,u.user_name,s.create_time,s.collect_count,s.relay_count,			s.comment_count,s.type,s.share_data,s.status,s.is_best 
			FROM '.C("DB_PREFIX").'share_photo AS sp  
			INNER JOIN '.C("DB_PREFIX").'share AS s ON s.share_id = sp.share_id 
			LEFT JOIN '.C("DB_PREFIX").'user AS u ON u.uid = s.uid 
			'.$inner_sql.$where.' GROUP BY s.share_id';
		$this->_sqlList($model,$sql,$count,$parameter,'s.share_id');
		
		$this->display ();
	}
	
	public function look()
	{
		$where = '';
		$parameter = array();
		$keyword = trim($_REQUEST['keyword']);
		$uname = trim($_REQUEST['uname']);
		$type = trim($_REQUEST['type']);
		$share_data = !isset($_REQUEST['share_data']) ? 'img' : trim($_REQUEST['share_data']);
		$cate_id = intval($_REQUEST['cate_id']);
		$status = !isset($_REQUEST['status']) ? 0 : intval($_REQUEST['status']);
		$inner_sql = '';
		
		$where .= " WHERE sp.type = 'look'";

		if(!empty($keyword))
		{
			$this->assign("keyword",$keyword);
			$parameter['keyword'] = $keyword;
			$match_key = segmentToUnicodeA($keyword,'+');
			$where.=" AND match(sm.content_match) against('".$match_key."' IN BOOLEAN MODE) ";
			$inner_sql .= 'INNER JOIN '.C("DB_PREFIX").'share_match AS sm ON sm.share_id = s.share_id ';
		}

		if(!empty($uname))
		{
			$this->assign("uname",$uname);
			$parameter['uname'] = $uname;
			$match_key = segmentToUnicodeA($uname,'+');
			$where.=" AND match(u.user_name_match) against('".$match_key."' IN BOOLEAN MODE) ";
            $like_name = mysqlLikeQuote($uname);
            $where .= ' AND u.user_name LIKE \'%'.$like_name.'%\'';
		}

		$model = M();

		$sql = 'SELECT COUNT(DISTINCT s.share_id) AS scount
			FROM '.C("DB_PREFIX").'share_photo AS sp
			INNER JOIN '.C("DB_PREFIX").'share AS s ON s.share_id = sp.share_id 
			LEFT JOIN '.C("DB_PREFIX").'user AS u ON u.uid = s.uid 
			'.$inner_sql.$where;

		$count = $model->query($sql);
		$count = $count[0]['scount'];

		$sql = 'SELECT s.share_id,LEFT(s.content,80) AS content,u.user_name,s.create_time,s.collect_count,s.relay_count,			s.comment_count,s.type,s.share_data,s.status,s.is_best 
			FROM '.C("DB_PREFIX").'share_photo AS sp  
			INNER JOIN '.C("DB_PREFIX").'share AS s ON s.share_id = sp.share_id 
			LEFT JOIN '.C("DB_PREFIX").'user AS u ON u.uid = s.uid 
			'.$inner_sql.$where.' GROUP BY s.share_id';
		$this->_sqlList($model,$sql,$count,$parameter,'s.share_id');
		
		$this->display ();
	}

	private function init_share_data($share_id)
	{
		$photo_count = D("SharePhoto")->where("share_id=".$share_id)->count();
		$goods_count = D("ShareGoods")->where("share_id=".$share_id)->count();
		if($photo_count==0&&$goods_count==0)
		{
			$share_data = "default";
		}
		elseif($photo_count==0&&$goods_count>0)
		{
			$share_data = "goods";
		}
		elseif($photo_count>0&&$goods_count==0)
		{
			$share_data = "photo";
		}
		else
		{
			$share_data = "goods_photo";
		}
		D("Share")->where("share_id=".$share_id)->setField("share_data",$share_data);
	}

}
function getCommentCount($count,$share_id)
	{
		if($count>0)
			return "(".$count.")&nbsp;&nbsp; <a href='".u("Share/comments",array("share_id"=>$share_id))."'>".l("CHECK_COMMENT")."</a>";
		else
			return $count;
	}
?>