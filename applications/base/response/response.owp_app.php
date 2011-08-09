<?php

class owp_app_response extends owp_app_base {

	public function action_redirect($url)
	{
		header("Location: {$url}");
	}
	
	public function action_refresh()
	{
		$this->action_redirect($this->app_request->url());
	}
}

?>