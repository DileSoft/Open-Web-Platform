<?php 

class owp_app_mysql extends owp_app_base
{
	protected $sql;
	
	public function start() {
		owp_core::library("mysql");
	}
	
	public function action_connect($host, $user, $password, $db)
	{
		$this->sql = new sqlDB($host, $user, $password, $db);
		$this->action_query("SET NAMES 'UTF8'");
	}

	public function action_query($sql)
	{
		return $this->sql->query($sql);
	}

	public function action_fetch($query)
	{
		return $this->sql->fetch_request($query);
	}

	public function action_insert_id()
	{
		return $this->sql->get_insert_id();
	}
}