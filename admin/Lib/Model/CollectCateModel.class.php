<?php
class CollectCateModel extends CommonModel
{
	public $_validate = array(
		array('name','require','{%NAME_EMPTY_TIP}'),
		array('uname','require','{%UNAME_EMPTY_TIP}'),
	);
}
?>