<?php

interface owp_app {
	
	public function __construct($app, $process_name);
	
	public function start();
	
	public function autoload();
	
	public function stop();
	
	public function install();
	
	public function uninstall();
	
	public function call($method, $data);
}

abstract class owp_app_base implements owp_app {
	
	
	public function __get($name)
	{
		if (preg_match("#^app_(.*)$#", $name, $match))
		{
			return owp_core::app($match[1]);
		}
	}
	
	public function start()
	{
		
	}
	
	public function autoload()
	{
		
	}
	
	public function stop()
	{
		
	}
	
	public function install()
	{
		
	}
	
	public function uninstall()
	{
		
	}
	
	protected $actions = array ();
	
	protected $process_name;
	
	protected $app;
	
	protected $auto_register_actions_on = true;
	
	public function __construct($app, $process_name = "")
	{
		$this->app = $app;
		$this->name = $process_name ? $process_name : $app;

		if ($this->auto_register_actions_on)
		{
			$this->auto_register_actions();
		}		
	}
	
	protected function auto_register_actions()
	{
		$methods = get_class_methods(get_class($this));
		
		$actions = array();
		
		foreach ($methods as $method)
		{
			if (preg_match('#^action\_?([A-Z].*)$#i', $method, $matches))
			{
				$actions[] = $matches[1];
			}
		}
		
		$this->register_actions($actions);
	}
	
	public function error($code, $comment)
	{
		owp_core::error($this->app, $this->process_name, $code, $comment);
	}
	
	protected function process_actions($current_action, $data) {
		foreach ( $this->actions as $action ) {
			if ($action == $current_action) {
				return call_user_func_array ( array (
						$this, 
						"action_$action" ), $data );
			}
		}
	}
	
	protected function register_actions($actions) {
		$this->actions = array_unique ( array_merge ( $this->actions, $actions ) );
	}
	
	public function call($method, $data)
	{
		return $this->process_actions($method, $data);
	}

}

class owp_app_process
{
	protected $process_name;
	
	protected $app;
	
	public function __construct($app, $process_name = "")
	{
		$this->app = $app;
		$this->process_name = $process_name ? $process_name : $app;
	}
	
	public function __call($method, $data)
	{
		return owp_core::call($this->process_name, $method, $data);
	}
	
	public function get_name()
	{
		return $this->name;
	}
}
?>