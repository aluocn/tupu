<?php
$array = array(
	'SHAREGOODSMODULE'=>'商品接口管理',
	'SHAREGOODSMODULE_INDEX'=>'接口列表',
	'SHAREGOODSMODULE_EDIT'=>'编辑接口',
	
	'CLASS'=>'接口代码',
	'NAME'=>'接口名称',
	'DOMAIN'=>'域名限制',
	'ICON'=>'图标',
	'LOGO'=>'LOGO',
	'CONTENT'=>'说明',
	'URL'=>'网站链接',
	
	'TAOBAO_TIPS'=>'申请时请将回调地址，设置到 callback 目录下tb.php<br/>例：http://www.uu43.com/callback/tb.php',
	'TAOBAO_APP_KEY'=>'App Key',
	'TAOBAO_APP_SECRET'=>'App Secret',
	'TAOBAO_TK_PID'=>'淘宝客PID',
	'TAOBAO_TK_PID_TIPS'=>'淘宝客PID：例：PID为mm_xxxxxxxx_0_0，填写xxxxxxxx即可',
	'TAOBAO_SESSIONKEY'=>'SessionKey',
	'TAOBAO_SESSIONKEY_TIPS'=>'用于获取淘宝客报表，请先设置App Key、App Secret、淘宝客PID，提交保存后，更新缓存；<br/>请点击 <a href="../login.php?tbsk=true" target="_blank">获取SessionKey</a>，并用淘宝客关联的淘宝帐户登陆；<br/>将页面输出的<span style="color:#f00;">红色字符串</span>复制粘贴到 SessionKey 文本框；<br/>将页面输出的<span style="color:#00f;">蓝色字符串</span>复制粘贴到 SessionKey过期时间 文本框；',
	
	'PAIPAI_UIN'=>'QQ号',
	'PAIPAI_APPOAUTHID'=>'appOAuthID',
	'PAIPAI_APPOAUTHKEY'=>'secretOAuthKey',
	'PAIPAI_ACCESSTOKEN'=>'accessToken',
	'PAIPAI_USERID'=>'推广者ID',
	
	'NAME_REQUIRE'=>'接口名称不能为空',
);
return $array;
?>