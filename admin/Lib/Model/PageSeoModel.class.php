<?php
class PageSeoModel extends CommonModel
{
	public $_validate = array(
		array('name','require','{%NAME_EMPTY_TIP}'),
		array('action_name','require','{%ACTION_NAME_EMPTY_TIP}'),
	);
}
?>