<?php

abstract class owp_app_db_field_base extends owp_app_base {
	
	protected $name;
	
	protected $config;
	
	protected $data;
	
	protected $write_data;
	
	protected $errors;
	
	public function action_setup($name, $config)
	{
		$this->name = $name;
		$this->config = $config;
	}
	
	public function action_set_data($data)
	{
		$this->data = $data;
	}
	
	abstract protected function check();

	public function action_check()
	{
		return $this->check();
	}
	
	public function action_get_errors()
	{
		return $this->errors;
	}
	
	abstract protected function set_write_data(); 

	public function action_get_write_data()
	{
		$this->set_write_data();
		
		return $this->write_data;
	}
	
	abstract protected function db_fields();

	public function action_get_read_db_fields()
	{
		return $this->db_fields();
	}
	
	public function action_get_data()
	{
		return $this->data;
	}

}

?>