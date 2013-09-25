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
 专辑分类
 +------------------------------------------------------------------------------
 */
class PageSeoAction extends CommonAction
{
	public function  index(){
		$name=$this->getActionName();
		$model = D ($name);
		if(false === $data = $model->create())
		{
			$this->error($model->getError());
		}
		$list = $model->findAll();
		$this->assign('list',$list);
		$this->display();
	}
	public function  add(){
		$this->display();
	}
	
	public function insert()
	{
		$name=$this->getActionName();
		$model = D ($name);
		if(false === $data = $model->create())
		{
			$this->error($model->getError());
		}
		
		//保存当前数据对象
		$list=$model->add($data);
		if ($list !== false)
		{	
			$this->saveLog(1,$list);
			$this->success (L('ADD_SUCCESS'));
		}
		else
		{
			$this->saveLog(0,$list);
			$this->error (L('ADD_ERROR'));
		}
	}
	
	public function update()
	{
		$id = intval($_REQUEST['id']);
		$name=$this->getActionName();
		$model = D ($name);
		if (false === $data = $model->create ()) {
			$this->error ( $model->getError () );
		}
		
		// 更新数据
		$list=$model->save($data);
		if (false !== $list)
		{	
			$this->saveLog(1,$id);
			$this->assign('jumpUrl', U("PageSeo/index"));
			$this->success (L('EDIT_SUCCESS'));
		}
		else
		{
			//错误提示
			$this->saveLog(0,$id);
			$this->error (L('EDIT_ERROR'));
		}
	}
}
?>