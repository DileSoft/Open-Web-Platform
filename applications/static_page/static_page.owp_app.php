<?php

class owp_app_static_page extends owp_app_base {
	
	protected $page;
	
	public function action_load_page($url)
	{
		$res = $this->app_mysql->query("SELECT * FROM page WHERE url = '{$url}'");
		$this->page =  $this->app_mysql->fetch($res);
	}
	
	public function action_get_body()
	{
		return $this->page["html"];
	}
	
	public function action_get_title()
	{
		return $this->page["title"];
	}
}

?>