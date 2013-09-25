<?php
/*
 *  bindCacheSetting
 *  获取采集JS所需要的配置信息 保存在js/setting.js中
 *  生成 采集的JS文件 保存在js/im.ping.js
 *  生成配置文件 config.global.php
 *  将采集用到的语言写入到文件中 保存在 js/lang.js
 *  清空CSS目录下文件，清空js目录下文件 分别是：data/tpl/css/，data/tpl/js/
 *  获取模板的CSS文件 并将其移动到 缓存中CSS绝对路径中 
 *  FanweService::instance()->cache->saveCache('setting', $settings); 获取数据库中的setting信息 保存在setting中
 */
function bindCacheSetting()
{
	global $_FANWE;
	$settings = array();
	$js_settings = array();
	$res = FDB::query("SELECT name,val,is_js FROM ".FDB::table('sys_conf')." WHERE status = 1");
	while($data = FDB::fetch($res))
	{
		$name = strtolower($data['name']);
		$settings[$name] = $data['val'];
		if($data['is_js'] == 1)
			$js_settings[$name] = $data['val'];
	}
	// 获取采集JS所需要的配置信息
	writeFile(PUBLIC_ROOT.'./js/setting.js','var SETTING = '.getJson($js_settings).';');
	
	$pingJs = '';//采集JS初始化

	$pingJs = @file_get_contents(PUBLIC_ROOT.'./js/demo.js');
	$site_name = 'var siteName = "'.$_FANWE['setting']['site_name'].'"; ';
	$domain = 'var domain = "'.$_FANWE['site_url'].'"; ';
	
	$host_name = 'var hostName = "'.str_replace("http://", "", $_FANWE['site_url']).'"; ';
	
	$tpl_name = 'var tplName = "'.$_FANWE['setting']['site_tmpl'].'"; ';
	$yz_name='var global="TUPU";document[global]=document[global] || {};';
	
	$im_ping_js = '';
	$im_ping_js = $site_name.$host_name.$domain.$tpl_name.$yz_name.$pingJs;
	//生成 采集的JS文件
	writeFile(PUBLIC_ROOT.'./js/im.ping.js', $im_ping_js);
	
	
	$config_file = @file_get_contents(PUBLIC_ROOT.'config.global.php');
	$config_file = trim($config_file);
	$config_file = preg_replace("/[$]config\['time_zone'\].*?=.*?'.*?'.*?;/is", "\$config['time_zone'] = '".$settings['time_zone']."';", $config_file);
	$config_file = preg_replace("/[$]config\['default_lang'\].*?=.*?'.*?'.*?;/is", "\$config['default_lang'] = '".$settings['default_lang']."';", $config_file);
	//生成配置文件
	@file_put_contents(PUBLIC_ROOT.'config.global.php', $config_file);
	unset($config_file);
	
	$lang_arr = array();
	//获取语言文件
	$lang_files = array(
		FANWE_ROOT.'./core/language/'.$settings['default_lang'].'/template.lang.php',
		FANWE_ROOT.'./tpl/'.$settings['site_tmpl'].'/template.lang.php',
	);
	//遍历语言文件，若存在文件的话
	foreach($lang_files as $lang_file)
	{
		if(@include $lang_file)
		{
			
			foreach($lang as $lkey=>$lval)
			{
				$lang_pre = strtolower(substr($lkey,0,3));
				if($lang_pre == 'js_')
				{
					//获取采集用到的语言包
					$lang_key = substr($lkey,3);
					if($lang_key != '')
						$lang_arr[$lang_key] = $lval;
				}
			}
		}
	}
	//将采集用到的语言写入到文件中
	writeFile(PUBLIC_ROOT.'./js/lang.js','var LANG = '.getJson($lang_arr).';');
	//清空CSS目录下文件
	clearDir(FANWE_ROOT.'./public/data/tpl/css/');
	//清空js目录下文件
	clearDir(FANWE_ROOT.'./public/data/tpl/js/');
	//获取模板的CSS绝对路径
	$css_dir = FANWE_ROOT.'./tpl/'.$settings['site_tmpl'].'/css/';
	//获取 缓存中CSS绝对路径
	$css_cache_dir = FANWE_ROOT.'./public/data/tpl/css/';
	//获取模板相对路径
	$css_site_path = $_FANWE['site_root'].'tpl/'.$settings['site_tmpl'].'/';
	//读取模板下的文件，
	$directory = dir($css_dir);
	while($entry = $directory->read())
	{
		//路径不为上层路径，或本层路径，并且要包含CSS
		if($entry != '.' && $entry != '..' && stripos($entry,'.css') !== false)
		{
			//获取CSS绝对路径
			$css_path = $css_dir.$entry;
			$css_content = @file_get_contents($css_path);
			//替换CSS中../为模板的相对路径
			$css_content = preg_replace("/\.\.\//",$css_site_path,$css_content);
			//获取CSS文件在缓存中的绝对路径
			$css_cache_path = $css_cache_dir.'/'.$entry;
			writeFile($css_cache_path,$css_content);
		}
	}
	$directory->close();
	// 在数组中保存基本配置文件
	FanweService::instance()->cache->saveCache('setting', $settings);
}
?>