<?php

abstract class owp_app_gui_block_base extends owp_app_base {
	
	protected $settings = array();
	
	protected $template;
	
	protected $html;

	public function action_setup($settings)
	{
		$this->settings = $settings;
	}
	
	abstract protected function prepare();
	
	protected function set($name, $value)
	{
		$settings[$name] = $value;
	}
	
	protected function get($name)
	{
		return isset($this->settings[$name]) ? $this->settings[$name] : "";
	}
	
	public function action_html()
	{
		if (isset($this->settings["template"]))
		{
			$this->template = file_get_contents(dirname(__FILE__) . "/{$this->settings["template"]}.tpl"); 
		}
		else 
		{
			$file = owp_core::get_resource($this->settings["type"] . "_gui", "{$this->settings["type"]}_gui.tpl");
			$this->template = file_get_contents($file);
		}
		
		$this->prepare();
		
		
		$this->html = preg_replace_callback('#\<owp\:var name\=\"([a-z0-9\_\-]+)\"\/\>#i', array($this, "parse_variables"), $this->template);
		$this->html = preg_replace_callback('#\{\{owp\:var name\=\"([a-z0-9\_\-]+)\"\/\}\}#i', array($this, "parse_variables"), $this->html);
		$this->html = $this->app_gui->parse_blocks($this->html);
		
		return $this->html;
	}
	
	protected function parse_variables($matches)
	{
		return $this->get($matches[1]);
	}
	
	public function action_event ($event, $data)
	{
		$this->action_setup($data);
		$this->call($event, $data);
	}
}

?>