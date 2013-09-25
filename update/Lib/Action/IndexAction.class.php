<?php
//系统转换
class IndexAction extends Action
{
	private function getRealPath()
		{
		return FANWE_ROOT;
		}
	
	public function __construct()
		{
		import("ORG.Io.Dir");
		parent::__construct();
		}
	public function has_update(){
		$dir=FANWE_ROOT.'update/sql';
		$db = $this->getDB();
		$db_version = $db->query("select val from ".C('DB_PREFIX')."sys_conf where name='SYS_VERSION'");
		$db_version = trim($db_version[0]['val']);
		$is=false;
		if(is_dir($dir)){
			if($dh=opendir($dir)){
				while(($file=readdir($dh))!==false){
					
					if(strpos($file,$db_version.'-')!==false){
						$flie_type=filetype($dir.'/'.$file);
						if($flie_type=='file'){
							$path=$dir.'/'.$file;
							
							if($this->is_sql($file)){
								//存在升级文件
								$is=true;
								return $file;
								
							}
						}
					}
				}
			}
		}
		return false;
	}
	public function tel_index(){
		
		$dir=FANWE_ROOT.'update/sql';
		$note=file_get_contents($dir.'/tel.txt');
			if(!$this->is_utf8($note)){
				$note=$this->to_utf8($note);
			}
		$db = $this->getDB();
		$re=$db->query("SHOW TABLES LIKE '%_apns\_%' ");
		$m_num_1=count($re);
		$re=$db->query("SHOW TABLES LIKE '".C("DB_PREFIX")."m\_%' ");
		$m_num_2=count($re);
		$all=$m_num_1+$m_num_2;
		if($all>=8){
			$is=0;
			$this->assign('note',"手机端数据库已经升级成功,无需再次升级");
		}else{
			if(is_file($dir.'/tel.sql')){
			$is=1;
			$this->assign('note',$note);
		}else{
			$is=0;
			$this->assign('note','无可升级的手机端数据库文件,请联系客服');
		}
		}
		
		
		
		$this->assign('is',$is);
		$this->assign('sql','tel.sql');
		$this->display();
			
	}
	public function index()
	   {
	  
		clear_cache();
		$dir=FANWE_ROOT.'update/sql';
		$db = $this->getDB();
		$db_version = $db->query("select val from ".C('DB_PREFIX')."sys_conf where name='SYS_VERSION'");
		$db_version = trim($db_version[0]['val']);
		$files=array();
		$is=false;
		if(is_dir($dir)){
			if($dh=opendir($dir)){
				while(($file=readdir($dh))!==false){					
					if(strpos($file,$db_version.'-')!==false){
						$flie_type=filetype($dir.'/'.$file);
						if($flie_type=='file'){
							$path=$dir.'/'.$file;
							
							if($this->is_sql($file)){
								$is=true;
								$file_stat=stat($path);
								$note=file_get_contents($path);
								$is_utf8=$this->is_utf8($note);
								if(!$is_utf8){
									$note=$this->to_utf8($note);	
								}
								$files['sql']=array('name'=>$file,'note'=>nl2br($note),'path'=>$path,'mtime'=>date('Y-m-d H:i:s',$file_stat['mtime']),'is_utf8'=>$is_utf8);
								
							}elseif($this->is_txt($file)){
								
								$note=file_get_contents($path);
								$is_utf8=$this->is_utf8($note);
								if(!$is_utf8){
									$note=$this->to_utf8($note);	
								}
								
								
								$files['txt']=array('name'=>$file,'note'=>$note,'path'=>$path,'is_utf8'=>$is_utf8);
								
							}
							
							
						}
						
					}
				}
			}
			closedir();
		}
		
		$this->assign('is',$is);
		$this->assign('files',$files);
		$this->assign('sql',$files['sql']['name']);
		if($is){
			$note=nl2br($files['txt']['note']);
		}else{
			$note=file_get_contents($dir.'/no.txt');
			if(!$this->is_utf8($note)){
				$note=$this->to_utf8($note);
			}
		}
		
		$this->assign("note",$note);
		
		
		
		
		$this->display();
		}
	
	public function is_sql($file){
		return strpos($file,'sql')!==false?true:false;
	}
	public function is_txt($file){
		return strpos($file,'txt')!==false?true:false;
	}
	public function to_utf8($content){
		return iconv('gbk','utf-8',$content);
	}
	// Returns true if $string is valid UTF-8 and false otherwise.
	public function is_utf8($word)
		{
		if (preg_match("/^([".chr(228)."-".chr(233)."]{1}[".chr(128)."-".chr(191)."]{1}[".chr(128)."-".chr(191)."]{1}){1}/",$word) == true || preg_match("/([".chr(228)."-".chr(233)."]{1}[".chr(128)."-".chr(191)."]{1}[".chr(128)."-".chr(191)."]{1}){1}$/",$word) == true || preg_match("/([".chr(228)."-".chr(233)."]{1}[".chr(128)."-".chr(191)."]{1}[".chr(128)."-".chr(191)."]{1}){2,}/",$word) == true)
		{
			return true;
		}
		else
		{
			return false;
		}
		} // function is_utf8
	
	public function checkBOM ($filename) {
		$auto=1;
		$contents = file_get_contents($filename);
		$charset[1] = substr($contents, 0, 1);
		$charset[2] = substr($contents, 1, 1);
		$charset[3] = substr($contents, 2, 1);
		if (ord($charset[1]) == 239 && ord($charset[2]) == 187 &&
			ord($charset[3]) == 191) {
			if ($auto == 1) {
				$rest = substr($contents, 3);
				$this->rewrite ($filename, $rest);
				return true;
			} else {
				return false;
			}
		}
		else {
			return true;
		}
	}
	public function rewrite ($filename, $data) {
		$filenum = fopen($filename, "w");
		flock($filenum, LOCK_EX);
		fwrite($filenum, $data);
		fclose($filenum);
	}
	
	public function msg($msg)
		{
		$this->assign("msg",$msg);
		$this->display('msg');
		exit;
		}
	
	public function update()
		{
		$sql_name=trim($_REQUEST['sql']);
		$is_file=!empty($sql_name)?true:false;
		if($is_file){
			$path=FANWE_ROOT.'update/sql/'.$sql_name;
			$no_bom=$this->checkBom($path);
			if($no_bom){
			$sqls = file_get_contents($path);
			if(!$this->is_utf8($sqls)){
				$sqls=$this->to_utf8($sqls);
			}
			
			$sqls = str_replace("\r", '', $sqls);
			$version = explode(";\n", $sqls);
			
			if(empty($version[0]))
			{
				$this->msg("脚本没有版本号，无法更新");
			}
			else
			{
				$version = $version[0];
				
				$db = $this->getDB();
				$db_version = $db->query("select val from ".C('DB_PREFIX')."sys_conf where name='SYS_VERSION'");
				$db_version = $db_version[0]['val'];
				
				/*
				 if(floatval($db_version) == floatval($version))
				 {
				 $this->msg("已经是最新版本");
				 }
				 */
				/*
				 if(floatval($db_version) > floatval($version))
				 {
				 $this->msg("不能更新旧版本");
				 }
				 */
			}
			
			$this->doupdate($path);
			}else{
				$this->msg("SQL文件含有BOM,无法更新,请联系售后处理");
			}
			
		}else{
			$this->msg("没有找到相应的SQL文件，无法更新");
		}
		
		}
	
	public function doupdate($path='')
		{
		@set_time_limit(0);
		if(function_exists('ini_set'))
			ini_set('max_execution_time',3600);
		
		$this->display("doupdate");
		flush();
		ob_flush();
		
		showjsmessage('',-1);
		showjsmessage("开始更新分享程序",2);
		
		usleep(100);
		
		if($this->restore($path))
			showjsmessage("更新 数据库 成功");
		else
		{
			showjsmessage("更新 数据库 失败");
			exit;
		}
		if(strpos($path,'2.13-3.0.sql')!==false){
			//判断是否有is_video 字段，若有的话，则进行更新
			$is_exists_column=$this->check_table_column(C("DB_PREFIX").'share','is_video');
			if($is_exists_column){
				$share_table=C("DB_PREFIX").'share';
				$share_photo=C("DB_PREFIX").'share_photo';
	   			$sql="UPDATE IGNORE  ".$share_table." SET ".$share_table.".is_video=1 where (select type from ".$share_photo." where share_id=".$share_table.".share_id )='video' ";
				$db = $this->getDB();
				$re=$db->query($sql);
				if(!$re){
					echo mysql_error();
				}
				$sql="UPDATE IGNORE  ".$share_table." SET share_data='video' where is_video=1  ";
				$re=$db->query($sql);
				if(!$re){
					echo mysql_error();
				}
			}
		}
		$file=$this->has_update();
		if(!empty($file)){
			//还有更新文件，继续更新
		$db = $this->getDB();
		$db_version = $db->query("select val from ".C('DB_PREFIX')."sys_conf where name='SYS_VERSION'");
		$db_version = trim($db_version[0]['val']);
			showjsmessage("您更新成功，现在的版本号为$db_version,您还可以运行$file,是否继续升级",6);
		}else{
			// 没有更新文件了，跳转到后台
			showjsmessage("更新成功",4);
		}
		
		exit;
		}
		// 检测表单的属性中是否有相应字段
	public function check_table_column($table,$column){
		$db = $this->getDB();
		$re=$db->query('DESCRIBE '.$table);
		$is_exists=0;
		if($re){
			foreach($re as $k=>$v){
				if(!empty($v['Field'])&&($v['Field']==$column)){
					$is_exists=1;
					return $is_exists;
				}
			}
		}
		return $is_exists;
		
		
	}
	public function updatetable()
		{
		set_time_limit(0);
		
		$tables = array(
			'share'=>'table/share.table.php',
		);
		
		$table = $_REQUEST['table'];
		$begin = intval($_REQUEST['begin']);
		$begin = max($begin,0);
		$this->display();
		
		flush();
		ob_flush();
		
		if(array_key_exists($table,$tables))
		{
			global $db;
			$db = $this->getDB();
			@include FANWE_ROOT.'update/Common/'.$tables[$table];
		}
		else
		{
			showjsmessage("没有此转换数据表",1);
			exit;
		}
		}
	
	private function getDB()
		{
		static $db = NULL;
		if($db == NULL)
		{
			$db_config['DB_HOST'] = C('DB_HOST');
			$db_config['DB_NAME'] = C('DB_NAME');
			$db_config['DB_USER'] = C('DB_USER');
			$db_config['DB_PWD'] = C('DB_PWD');
			$db_config['DB_PORT'] = C('DB_PORT');
			$db_config['DB_PREFIX'] = C('DB_PREFIX');
			
			$db = Db::getInstance(array('dbms'=>'mysql','hostname'=>$db_config['DB_HOST'],'username'=>$db_config['DB_USER'],'password'=>$db_config['DB_PWD'],'hostport'=>$db_config['DB_PORT'],'database'=>$db_config['DB_NAME']));
		}
		
		return $db;
		}
	
	/**
	 * 执行SQL脚本文件
	 *
	 * @param array $filelist
	 * @return string
	 */
	private function restore($file)
		{
		$db = $this->getDB();
		$sql = file_get_contents($file);
		$sql = $this->remove_comment($sql);
		$sql = trim($sql);
			
			
		$bln = true;
		
		$tables = array();
		//若非UTF-8，则转化为UTF-8
		if(!$this->is_utf8($sql)){
				$sql=$this->to_utf8($sql);
			}
		
		$segmentSql = explode(";\r", $sql);
		$segmentSql = str_replace("\r", '', $segmentSql);
		unset($segmentSql[0]);
		$table = "";
		
		foreach($segmentSql as $k=>$itemSql)
		{
			$itemSql = trim(str_replace("%DB_PREFIX%",C('DB_PREFIX'),$itemSql));
			
			if(!$itemSql)
				continue;
			if(strtoupper(substr($itemSql, 0, 12)) == 'CREATE TABLE')
			{
				$table = preg_replace("/CREATE TABLE (?:IF NOT EXISTS |)(?:`|)([a-z0-9_]+)(?:`|).*/is", "\\1", $itemSql);
				
				if(!in_array($table,$tables)){
					$tables[] = $table;
				}
					
				
				if($db->query($itemSql) === false)
				{
					$bln = false;
					
					showjsmessage("建立数据表 ".$table." ... 失败",1);
					break;
				}
				else
				{
					showjsmessage("建立数据表 ".$table." ... 成功");
				}
			}elseif( (strtoupper(substr(trim($itemSql),0,11))=='ALTER TABLE')&&(strpos(strtoupper($itemSql),'ADD')!==FALSE)&&(strpos(strtoupper($itemSql),'index')==FALSE)){
			//如果字段已经存在就不执行,添加索引除外
				$matches=array();
				preg_match_all('/ALTER TABLE(.*)ADD(.*) /i',$itemSql,$matches);
				
				$table_name=trim($matches[1][0]);
				$column_sql=explode(' ',trim($matches[2][0]));
				$column=trim($column_sql[0]);
				if(strpos(strtoupper($column),'COLUMN')!==FALSE){
					$column=trim($column_sql[1]);
				}
				
				$result=$db->query("DESCRIBE $table_name $column");//获取字段信息
				if(empty($result[0])){
					//若字段不存在就执行
					if($db->query($itemSql) === false)
				{
					$bln = false;
					showjsmessage("执行查询  ".$itemSql." ... 失败",1);
					break;
				}
				}else{
					showjsmessage("表  ".$table_name."中的".$column."已经存在 ... 跳过",2);
				}
			}
			else
			{
				
				if($db->query($itemSql) === false)
				{
					echo $itemSql;
					$bln = false;
					showjsmessage("执行查询  ".$itemSql." ... 失败",1);
					break;
				}
			}
		}
		
		return $bln;
		
		}
	
	
	
	/**
	 * 过滤SQL查询串中的注释。该方法只过滤SQL文件中独占一行或一块的那些注释。
	 *
	 * @access  public
	 * @param   string      $sql        SQL查询串
	 * @return  string      返回已过滤掉注释的SQL查询串。
	 */
	private function remove_comment($sql)
		{
		/* 删除SQL行注释，行注释不匹配换行符 */
		$sql = preg_replace('/^\s*(?:--|#).*/m', '', $sql);
		
		/* 删除SQL块注释，匹配换行符，且为非贪婪匹配 */
		//$sql = preg_replace('/^\s*\/\*(?:.|\n)*\*\//m', '', $sql);
		$sql = preg_replace('/^\s*\/\*.*?\*\//ms', '', $sql);
		
		return $sql;
		}
}
?>