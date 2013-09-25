<?php
class UserModule
{
	public function login()
	{
		global $_FANWE;

		if($_FANWE['uid'] > 0)
		fHeader("location: ".FU('u/index'));

		$cache_file = getTplCache('page/user/user_login');
		if(!@include($cache_file))
		{
			$login_modules = getLoginModuleList();
			include template('page/user/user_login');
		}

		display($cache_file);
	}


	public function ajaxLogin()
	{
		global $_FANWE;
		$user_name_or_email = addslashes($_FANWE['request']['email_name']);
		$password = md5(trim($_FANWE['request']['pass']));
		$life = isset($_FANWE['request']['remember']) ? intval($_FANWE['request']['remember']) : 0;
		$rhash = $_FANWE['request']['rhash'];

		if(empty($rhash) || $rhash != FORM_HASH)
		exit('Access Denied');

		$return = array();

		$user_field = $_FANWE['setting']['integrate_field_id'];

		$sql = "SELECT uid,status,{$user_field} FROM ".FDB::table('user')." WHERE (email = '$user_name_or_email' OR user_name = '$user_name_or_email') AND password = '$password'";
		$user_info = FDB::fetchFirst($sql);
		$uid = intval($user_info['uid']);
		$integrate_id = intval($user_info[$user_field]);

		//===========add by chenfq 2011-10-14==========================
		if ($uid <= 0){
			$uid = FS("Integrate")->addUserToLoacl($user_name_or_email,$password, 1);

			//重新取一下当前数据库的用户数据
			$sql = "SELECT uid,{$user_field} FROM ".FDB::table('user')." WHERE uid = '$uid'";
			$user_info = FDB::fetchFirst($sql);
			$uid = intval($user_info['uid']);
			$integrate_id = intval($user_info[$user_field]);
		}
		//===========add by chenfq 2011-10-14==========================

		if ($uid > 0)
		{
			if($user_info['status']==0){
				$return['status'] = 2;
				outputJson($return);
				exit();
			}
			$user = array(
				'uid'=>$uid,
				'password'=>$password,
			);

			$sql = 'update '.FDB::table('user_status')." set last_ip = '".$_FANWE['client_ip']."', last_time = '".TIME_UTC."' where uid = ".$uid;
			FDB::query($sql);//更新会员的登陆时间和ip地址
			fSetCookie('last_request', authcode(TIME_UTC - 10, 'ENCODE'), TIME_UTC + 816400, 1, true);
			FS('User')->setSession($user,$life);
			$syslogin_js = FS("Integrate")->synLogin($integrate_id);//js 需要在前台执行 add by chenfq 2011-10-15

			//$return['syslogin_js'] = $syslogin_js;
			if (!empty($syslogin_js))
			fSetCookie("dynamic_script",$syslogin_js);
			$return['status'] = 1;
			$return['uid'] = $uid;
			if(getCookie('redir_url'))//判断采集图片回调地址,cookie中保持的地址，只使用一次
			unset($_FANWE['cookie']['redir_url']);

		}
		else
		{
			$return['status'] = 0;
		}

		outputJson($return);
	}

	public function register()
	{
		global $_FANWE;

		if($_FANWE['uid'] > 0)
		fHeader("location: ".FU('u/index'));
		if(isset($_FANWE['request']['invite'])){
			$uid=intval($_FANWE['request']['invite']);
			$sql="select user_name from ".FDB::table('user')." where uid=".$uid;
			$user=FDB::fetchFirst($sql);
			if($user){//用户存在，跳转到注册页面
				$tpl_path='page/user/user_register';
			}else{//跳转到首页
				fHeader("location: ".FU('index/index'));
			}
		}else{
			$tpl_path = $_FANWE['setting']['is_open_register']==1? 'page/user/user_register':'page/user/user_not_register';	
		}
		$cache_file = getTplCache($tpl_path);
		if(!@include($cache_file))
		{
			$login_modules = getLoginModuleList();
			include template($tpl_path);
		}

		display($cache_file);
	}

	public function ajaxRegister()
	{
		global $_FANWE;

		$rhash = $_FANWE['request']['rhash'];
		$agreement = isset($_FANWE['request']['agreement']) ? intval($_FANWE['request']['agreement']) : 0;

		if($agreement == 0)
		exit('Access Denied');

		$verify = fAddslashes(explode("\t", authcode($_FANWE['cookie']['verify'.$rhash], 'DECODE',$_FANWE['config']['security']['authkey'])));
		if(empty($rhash) || $rhash != FORM_HASH || empty($verify) || $verify[2] != $rhash || $verify[3] != FORM_HASH)
		exit('Access Denied');

		$result = array();

		$data = array(
			'checkcode'        => strtoupper($_FANWE['request']['checkcode']),
			'email'            => $_FANWE['request']['email'],
			'user_name'        => $_FANWE['request']['user_name'],
			'password'         => $_FANWE['request']['password'],
			'confirm_password' => $_FANWE['request']['confirm_password'],
			'gender'           => intval($_FANWE['request']['gender']),
		);

		$vservice = FS('Validate');
		$validate = array(
		array('checkcode','equal',lang('user','register_checkcode_error'),$verify[0]),
		array('email','required',lang('user','register_email_require')),
		array('email','email',lang('user','register_email_error')),
		array('user_name','required',lang('user','register_user_name_require')),
		array('user_name','range_length',lang('user','register_user_name_len'),2,20),
		array('user_name','/^[\x{4e00}-\x{9fa5}a-zA-Z][\x{4e00}-\x{9fa5}a-zA-Z0-9]+$/u',lang('user','register_user_name_error')),
		array('password','range_length',lang('user','register_password_range'),6,20),
		array('confirm_password','equal',lang('user','confirm_password_error'),$data['password']),
		);

		if(!$vservice->validation($validate,$data))
		{
			$result['status'] = 0;
			$result['msg'] = $vservice->getError();
			outputJson($result);
		}

		$uservice = FS('User');
		if($uservice->getEmailExists($data['email']))
		{
			$result['status'] = 0;
			$result['msg']	= lang('user','register_email_exist');
			outputJson($result);
		}

		if($uservice->getUserNameExists($data['user_name']))
		{
			$result['status'] = 0;
			$result['msg']	= lang('user','register_user_name_exist');
			outputJson($result);
		}

		//================add by chenfq 2011-10-14 =======================
		$user_field = $_FANWE['setting']['integrate_field_id'];
		$integrate_id = FS("Integrate")->addUser($data['user_name'],$data['password'],$data['email']);
		if ($integrate_id < 0){
			$info = FS("Integrate")->getInfo();
			$result['status'] = 0;
			$result['msg']	= $info;
			outputJson($result);
		};
		//================add by chenfq 2011-10-14=======================

		$user = array(
			'email' => $data['email'],
			'user_name' => $data['user_name'],
			'user_name_match'=>segmentToUnicode($data['user_name']),
			'password'  => md5($data['password']),
			'status'    => 1,
			'email_status' => 0,
			'avatar_status' => 0,
			'gid' => 7,
			'invite_id' => FS('User')->getReferrals(),
			'reg_time' => TIME_UTC,
		$user_field => $integrate_id,
		);


		$uid = FDB::insert('user',$user,true);
		if($uid > 0)
		{
			$_FANWE['uid'] = $uid;
			FDB::insert('user_count',array('uid' => $uid));

			if($user['invite_id'] > 0)
			FS('User')->insertReferral($uid,$user['invite_id'],$user['user_name']);

			FS("User")->updateUserScore($uid,'user','register');

			unset($user);

			$user_profile = array(
				'uid' => $uid,
				'gender' => $data['gender'],
			);
			FDB::insert('user_profile',$user_profile);
			unset($user_profile);

			$user_status = array(
				'uid' => $uid,
				'reg_ip' => $_FANWE['client_ip'],
				'last_ip' => $_FANWE['client_ip'],
				'last_time' => TIME_UTC,
				'last_activity' => TIME_UTC,
			);
			FDB::insert('user_status',$user_status);

			$user = array(
				'uid'=>$uid,
				'password'=>md5($data['password']),
			);
		 //判断是否开启邮件激活 ------3.0升级内容
			if($_FANWE['setting']['is_mail_activate']){
				FS('User')->sendUserActiveMail($uid);
				fSetCookie('uid',$uid);
			}else{
				fSetCookie('last_request', authcode(TIME_UTC - 10, 'ENCODE'), TIME_UTC + 816400, 1, true);
				FS('User')->setSession($user);
			}
			$syslogin_js = FS("Integrate")->synLogin($integrate_id);//js 需要在前台执行 add by chenfq 2011-10-15
			//$result['syslogin_js'] = $integrate_id.';'.$syslogin_js;
			if (!empty($syslogin_js))
			fSetCookie("dynamic_script",$syslogin_js);
			$result['status'] = 1;
			if(getCookie('redir_url'))//判断采集图片回调地址,cookie中保持的地址，只使用一次
			unset($_FANWE['cookie']['redir_url']);
			outputJson($result);
		}
		else
		{
			$result['status'] = 0;
			$result['msg']	= lang('user','register_error');
			outputJson($result);
		}
	}

	public function bind()
	{
		global $_FANWE;
		if(empty($_FANWE['cookie']['bind_user_info']))
		fHeader("location: ".FU('user/register'));

		$bind_info = unserialize(authcode($_FANWE['cookie']['bind_user_info'], 'DECODE'));
		if(empty($bind_info))
		fHeader("location: ".FU('user/register'));

		if(FS('User')->getEmailExists($bind_info['user_email']))
		{
			$bind_info['user_email'] = '';
		}

		include template('page/user/user_bind');
		display();
	}

	public function saveBind()
	{
		global $_FANWE;
		if(empty($_FANWE['cookie']['bind_user_info']))
		fHeader("location: ".FU('user/register'));

		$bind_info = unserialize(authcode($_FANWE['cookie']['bind_user_info'], 'DECODE'));
		if(empty($bind_info))
		fHeader("location: ".FU('user/register'));

		$rhash = $_FANWE['request']['rhash'];
		$agreement = isset($_FANWE['request']['agreement']) ? intval($_FANWE['request']['agreement']) : 0;

		if($agreement == 0)
		exit('Access Denied');

		$result = array();
		$data = array(
			'email'            => $_FANWE['request']['email'],
			'user_name'        => $_FANWE['request']['user_name'],
			'password'         => $_FANWE['request']['password'],
			'confirm_password' => $_FANWE['request']['confirm_password'],
			'gender'           => intval($_FANWE['request']['gender']),
		);

		$vservice = FS('Validate');
		$validate = array(
		array('email','required',lang('user','register_email_require')),
		array('email','email',lang('user','register_email_error')),
		array('user_name','required',lang('user','register_user_name_require')),
		array('user_name','range_length',lang('user','register_user_name_len'),2,20),
		array('user_name','/^[\x{4e00}-\x{9fa5}a-zA-Z0-9_]+$/u',lang('user','register_user_name_error')),
		array('password','range_length',lang('user','register_password_range'),6,20),
		array('confirm_password','equal',lang('user','confirm_password_error'),$data['password']),
		);

		if(!$vservice->validation($validate,$data))
		showError('注册失败',$vservice->getError(),-1);

		$uservice = FS('User');
		if($uservice->getEmailExists($data['email']))
		showError('注册失败',lang('user','register_email_exist'),-1);

		if($uservice->getUserNameExists($data['user_name']))
		showError('注册失败',lang('user','register_user_name_exist'),-1);

		//================add by chenfq 2011-10-14 =======================
		$user_field = $_FANWE['setting']['integrate_field_id'];
		$integrate_id = FS("Integrate")->addUser($data['user_name'],$data['password'],$data['email']);
		if ($integrate_id < 0)
		{
			$info = FS("Integrate")->getInfo();
			showError('注册失败',$info,-1);
		};
		//================add by chenfq 2011-10-14=======================

		$user = array(
			'email' => $data['email'],
			'user_name' => $data['user_name'],
			'user_name_match'=>segmentToUnicode($data['user_name']),
			'password'  => md5($data['password']),
			'status'    => 1,
			'email_status' => 0,
			'avatar_status' => 0,
			'gid' => 7,
			'invite_id' => FS('User')->getReferrals(),
			'reg_time' => TIME_UTC,
		$user_field => $integrate_id,
		);

		$uid = FDB::insert('user',$user,true);
		if($uid > 0)
		{
			$_FANWE['uid'] = $uid;
			FDB::insert('user_count',array('uid' => $uid));

			if($user['invite_id'] > 0)
			FS('User')->insertReferral($uid,$user['invite_id'],$user['user_name']);

			FS("User")->updateUserScore($uid,'user','register');
			unset($user);

			$user_profile = array(
				'uid' => $uid,
				'gender' => $data['gender'],
			);
			FDB::insert('user_profile',$user_profile);
			unset($user_profile);

			$user_status = array(
				'uid' => $uid,
				'reg_ip' => $_FANWE['client_ip'],
				'last_ip' => $_FANWE['client_ip'],
				'last_time' => TIME_UTC,
				'last_activity' => TIME_UTC,
			);
			FDB::insert('user_status',$user_status);

			$user = array(
				'uid'=>$uid,
				'password'=>md5($data['password']),
			);

			fSetCookie('last_request', authcode(TIME_UTC - 10, 'ENCODE'), TIME_UTC + 816400, 1, true);
			FS('User')->setSession($user);

			$syslogin_js = FS("Integrate")->synLogin($integrate_id);//js 需要在前台执行 add by chenfq 2011-10-15
			if (!empty($syslogin_js))
			fSetCookie("dynamic_script",$syslogin_js);

			require_once FANWE_ROOT."core/class/user/".$bind_info['type'].".class.php";
			$class = ucfirst($bind_info['type']).'User';
			$class = new $class();
			$class->bindByData($bind_info);
			fHeader("location:".FU('u/index'));
		}
		else
		{
			showError('注册失败',lang('user','register_error'),-1);
		}
	}

	function forgetPassword()
	{
		global $_FANWE;
		$method = strtolower($_SERVER["REQUEST_METHOD"]);
		if($method == 'post')
		{
			$data = array(
				'email'            => $_FANWE['request']['email'],
				'user_name'        => $_FANWE['request']['user_name']
			);

			$data_error = false;
			$send_error = true;

			$vservice = FS('Validate');
			$validate = array(
			array('email','required',lang('user','register_email_require')),
			array('email','email',lang('user','register_email_error')),
			array('user_name','required',lang('user','register_user_name_require')),
			array('user_name','range_length',lang('user','register_user_name_len'),2,20),
			array('user_name','/^[\x{4e00}-\x{9fa5}a-zA-Z][\x{4e00}-\x{9fa5}a-zA-Z0-9_]+$/u',lang('user','register_user_name_error'))
			);

			if(!$vservice->validation($validate,$data))
			$data_error = true;
			else
			{
				$uid = FDB::resultFirst('SELECT uid
					FROM '.FDB::table('user').'
					WHERE user_name = \''.$data['user_name'].'\' AND email = \''.$data['email'].'\'');

				$uid = intval($uid);
				if($uid == 0)
				$data_error = true;
				else
				{
					$reset_hash = md5(microtime(true).random(8));
					FDB::query('UPDATE '.FDB::table('user_status').'
						SET reset_hash = \''.$reset_hash.'\'
						WHERE uid = '.$uid);

					$site_name = $_FANWE['setting']['site_name'];
					$reset_url = $_FANWE['site_url'].'user.php?action=resetpassword&resethash='.$reset_hash;
					$title = sprintf(lang('user','get_pwd_title'),$site_name);
					$content = sprintf(lang('user','get_pwd_html'),$site_name,$reset_url,$reset_url,$site_name,$_FANWE['site_url'],$site_name);
					include fimport("class/mail");
					$mail = new Mail();
					if($mail->send($data['user_name'],$data['email'],$title,$content,1))
					$send_error = false;
				}
			}
		}
		include template('page/user/user_getpwd');
		display();
	}

	public function resetPassword()
	{
		global $_FANWE;
		$hash = $_FANWE['request']['resethash'];
		if(empty($hash))
		fHeader("location: ".FU('user/login'));

		$uid = FDB::resultFirst('SELECT uid
			FROM '.FDB::table('user_status').'
			WHERE reset_hash = \''.$hash.'\'');
		$uid = intval($uid);


		if($uid == 0)
		fHeader("location: ".FU('user/login'));

		$method = strtolower($_SERVER["REQUEST_METHOD"]);
		if($method == 'post')
		{
			$data_error = false;

			$data = array(
				'password'         => $_FANWE['request']['password'],
				'confirm_password' => $_FANWE['request']['confirm_password']
			);

			$vservice = FS('Validate');
			$validate = array(
			array('password','range_length',lang('user','register_password_range'),6,20),
			array('confirm_password','equal',lang('user','confirm_password_error'),$data['password']),
			);

			if(!$vservice->validation($validate,$data))
			$data_error = true;
			else
			{
				//==================添加同步密码修改 chenfq 2011-10-15================
				$user_field = $_FANWE['setting']['integrate_field_id'];
				$sql = "SELECT {$user_field} FROM ".FDB::table('user')." WHERE uid = '$uid'";
				$integrate_id = intval(FDB::resultFirst($sql));
				if ($integrate_id > 0 ){
					FS("Integrate")->editUser($integrate_id, $_FANWE['request']['password'],'','');
				}
				//==================添加同步密码修改 chenfq 2011-10-15================

				FDB::query('UPDATE '.FDB::table('user').'
					SET password = \''.md5($data['password']).'\'
					WHERE uid = '.$uid);

				FDB::query('UPDATE '.FDB::table('user_status').'
					SET reset_hash = \'\'
					WHERE uid = '.$uid);
			}
		}
		include template('page/user/user_resetpwd');
		display();
	}

	public function logout()
	{
		global $_FANWE;
		$_FANWE['nav_title'] = lang('common','user_logout');
		FS('User')->clearSession();

		//====add by chenfq 2011-10-14=========
		$syslogout_js = FS("Integrate")->synLogout();//js 需要在前台执行
		//echo $syslogout_js; exit;
		if (!empty($syslogout_js))
		fSetCookie("dynamic_script",$syslogout_js);
		fHeader("location: ".FU('index/index'));
	}

	public function interest()
	{
		global $_FANWE;
		FanweService::instance()->cache->loadCache('usertagcate');
		include template('page/user/user_interest');
		display();
	}

	public function agreement()
	{
		global $_FANWE;
		$title = sprintf(lang('common','user_agreement'),$_FANWE['setting']['site_name']);
		$_FANWE['nav_title'] = $title;

		$cache_file = getTplCache('page/user/user_agreement');
		if(!@include($cache_file))
		{
			include template('page/user/user_agreement');
		}
		display($cache_file);
	}
	/*发送激活邮件*/
	public function sendActivateMail(){
		global $_FANWE;
		$mail_server = array(
             'sina.com'=>'http://mail.sina.com.cn/',
             '163.com'=>'http://mail.163.com/',
             'qq.com'=>'https://mail.qq.com/cgi-bin/loginpage',
             '126.com'=>'http://www.126.com/',
             'vip.sina.com'=>'http://vip.sina.com.cn/',
             'sina.cn'=>'http://mail.sina.com.cn/',
             'hotmail.com'=>'https://login.live.com/login.srf?wa=wsignin1.0&rpsnv=11&ct=1352254939&rver=6.1.6206.0&wp=MBI&wreply=http:%2F%2Fmail.live.com%2Fdefault.aspx&lc=2052&id=64855&mkt=zh-cn&cbcxt=mai&snsc=1',
             'gmail.com'=>'https://mail.google.com/',
             'sohu.com'=>'http://mail.sohu.com/',
             'yahoo.cn'=>'https://login.yahoo.com/config/login_verify2?&.src=ym',
             '139.com'=>'http://mail.10086.cn/',
             'wo.com.cn'=>'http://mail.wo.com.cn/mail/login.action',
             '189.cn'=>'http://webmail27.189.cn/webmail/',
             '21cn.com'=>'http://mail.21cn.com/', 
		);
		$title = lang('common','user_mail_activate');
		$_FANWE['nav_title'] = $title;
		$uid = $_FANWE['cookie']['uid'];
		if($uid>0){
			$sql = "SELECT email FROM ".FDB::table('user')." WHERE uid = ".$uid;
			$user_info = FDB::fetchFirst($sql);
			$mail_postfix = explode('@', $user_info['email']);
			if(array_key_exists($mail_postfix[1],$mail_server)){
				$mail_url = $mail_server[$mail_postfix[1]];
			}else{
				$mail_url = null;
			}
		}else{
			fHeader("location: ".FU('user/register'));
		}
		include template('page/user/send_activate_mail');
		display();
	}

	public function verifyUserMail(){
		global $_FANWE;
		$sn = $_REQUEST['sn'];
		$userinfo = FDB::fetchFirst('select * from '.FDB::table('user').' where active_sn =\''.$sn.'\'');
		if($userinfo){
			FDB::query('update ' . FDB::table('user') . ' set status = 1 where uid = ' . $userinfo ['uid']);
			$user = array(
                        'uid'=>$userinfo['uid'],
                        'password'=>$userinfo['password'],
			);
			fSetCookie('last_request', authcode(TIME_UTC - 10, 'ENCODE'), TIME_UTC + 816400, 1, true);
			FS('User')->setSession($user);

			$user_field = $_FANWE['setting']['integrate_field_id'];
			$integrate_id = (int)$userinfo[$user_field];
			$syslogin_js = FS("Integrate")->synLogin($integrate_id);
			if (!empty($syslogin_js))
			fSetCookie("dynamic_script",$syslogin_js);

			if($_FANWE['setting']['is_show_follow'] > 0 )
			fSetCookie("show_zone_follow",1);
		}

		fHeader("location: ".FU('u/index'));
	}
	/*分享文章*/
	public function papershare()
	{
		global $_FANWE;

		if($_FANWE['uid'] == 0)
		fHeader("location: ".FU('user/login'));
		
		
		$list = FS("Album")->getAlbumListByUid($_FANWE['uid']);
		$categoryList = FS("Album")->getAlbumCategory(0,"|--");
		include template('page/user/user_papershare');
		
		
		display();
	}
	/*编辑文章*/
	public function editpaper(){
		global $_FANWE;
		if($_FANWE['uid'] == 0)
		fHeader("location: ".FU('user/login'));
		
		$share_id = intval($_FANWE['request']['sid']);
		$share = FS("Share")->getShareById($share_id);
		$album_id = FDB::resultFirst("select album_id from ".FDB::table("album_share")." where share_id = ".$share_id);
		$album = FS("Album")->getAlbumById($album_id,false);
		$list = FS("Album")->getAlbumListByUid($_FANWE['uid']);
		$paper = FDB::fetchFirst('SELECT photo_id,text,title,server_code FROM '.FDB::table('share_photo').' WHERE share_id = '.$share_id);
		FanweService::instance()->cache->loadCache('albums');
		include template('page/user/user_editpaper');
		display();
	}
	
	
	/**
         * 2013-3-15 update3.1
         * 第三方用户不需要绑定直接登录
         */
        function bindlogin(){
            global $_FANWE;
		if(empty($_FANWE['cookie']['bind_user_info']))
			fHeader("location: ".FU('user/register'));
		
		$bind_info = unserialize(authcode($_FANWE['cookie']['bind_user_info'], 'DECODE'));
                
        //系统随机生成参数   以sys_开头的 邮箱和密码为 系统生成的，用户第一次登陆提示修改
        $random_mail = 'sys_'.$bind_info['type'].'_'.random(3).'@'.$bind_info['type'].'.com';
        $random_pwd = md5("sys_123456");
                
		if(empty($bind_info))
			fHeader("location: ".FU('user/register'));

		$data = array(
			'email' => $random_mail,
			'user_name' => $bind_info['user_name'],
			'password'  => $random_pwd,
			'invite_id' => FS('User')->getReferrals()
		);
         
                
		//================add by chenfq 2011-10-14 =======================
		$user_field = $_FANWE['setting']['integrate_field_id'];
		$integrate_id = FS("Integrate")->addUser($data['user_name'],$data['password'],$data['email']);
		if ($integrate_id < 0)
		{
			$info = FS("Integrate")->getInfo();
			showError('注册失败',$info,-1);
		};
		//================add by chenfq 2011-10-14=======================

		$user = array(
			'email' => $data['email'],
			'user_name' => $data['user_name'],
			'user_name_match'=>segmentToUnicode($data['user_name']),
			'password'  => $data['password'],
			'status'    => 1,
			'email_status' => 0,
			'avatar_status' => 0,
			'gid' => 7,
			'invite_id' => FS('User')->getReferrals(),
			'reg_time' => TIME_UTC,
		$user_field => $integrate_id,
		);

		$uid = FDB::insert('user',$user,true);
		if($uid > 0)
		{
			$_FANWE['uid'] = $uid;
			FDB::insert('user_count',array('uid' => $uid));

			if($user['invite_id'] > 0)
			FS('User')->insertReferral($uid,$user['invite_id'],$user['user_name']);

			FS("User")->updateUserScore($uid,'user','register');
			unset($user);

			$user_profile = array(
				'uid' => $uid,
				'gender' => $data['gender'],
			);
			FDB::insert('user_profile',$user_profile);
			unset($user_profile);

			$user_status = array(
				'uid' => $uid,
				'reg_ip' => $_FANWE['client_ip'],
				'last_ip' => $_FANWE['client_ip'],
				'last_time' => TIME_UTC,
				'last_activity' => TIME_UTC,
			);
			FDB::insert('user_status',$user_status);

			$user = array(
				'uid'=>$uid,
				'password'=>$data['password'],
			);

			fSetCookie('last_request', authcode(TIME_UTC - 10, 'ENCODE'), TIME_UTC + 816400, 1, true);
			FS('User')->setSession($user);

			$syslogin_js = FS("Integrate")->synLogin($integrate_id);//js 需要在前台执行 add by chenfq 2011-10-15
			if (!empty($syslogin_js))
			fSetCookie("dynamic_script",$syslogin_js);

			require_once FANWE_ROOT."core/class/user/".$bind_info['type'].".class.php";
			$class = ucfirst($bind_info['type']).'User';
			$class = new $class();
			$class->bindByData($bind_info);
			fHeader("location:".FU('u/index'));
		}
		else
		{
			showError('注册失败',lang('user','register_error'),-1);
		}
    }
}
?>