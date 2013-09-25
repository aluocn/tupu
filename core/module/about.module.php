<?php
class AboutModule
{
	
	public function index()
	{
		global $_FANWE;
		
		include template('page/about/about_index');
		display();
	}
	
	public function goodies()
	{
		global $_FANWE;
		
		include template('page/about/about_goodies');
		display();
	}
	
	public function help()
	{
		global $_FANWE;
		
		include template('page/about/about_help');
		display();
	}
	
	public function etiquette()
	{
		global $_FANWE;
		
		include template('page/about/about_etiquette');
		display();
	}
}
?>