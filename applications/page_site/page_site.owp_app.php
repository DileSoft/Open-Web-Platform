<?php

class owp_app_page_site extends owp_app_base {
	public function autoload() {

		$this->app_mysql->connect("localhost", "owp", "fsDfFD6Q9Bs44Jf5", "page_site");
		
		if (preg_match("#/edit$#", $this->app_request->address()))
		{
			$this->app_gui->set_page("page_site", "edit");			
		}
		
		elseif (preg_match("#/add$#", $this->app_request->address()))
		{
			$this->app_gui->set_page("page_site", "edit");			
		}
		
		elseif (preg_match("#/delete$#", $this->app_request->address()))
		{
			$this->app_gui->set_page("page_site", "dalete");			
		}
		else
		{
			$this->app_gui->set_page("page_site", "page");
		}
		
		$this->app_gui->render();
	}
	
}