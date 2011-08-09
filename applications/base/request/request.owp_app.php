<?php

class owp_app_request extends owp_app_base {
	
	protected $url_parsed;
	
	protected $url;
	
	public function start() {
		$this->url_parsed = parse_url ( $this->url = "http://" . $_SERVER ['SERVER_NAME'] . $_SERVER ['REQUEST_URI'] );
	}
	
	public function action_address() {
		return $this->url_parsed ["path"];
	}
	
	public function action_url() {
		return $this->url;
	}
	
	public function action_get($name) {
		return isset($_GET [$name]) ? $_GET [$name] : null;
	}
	
	public function action_post($name) {
		return isset($_POST [$name]) ? $_POST [$name] : null;
	}

	public function action_post_all() {
		return $_POST;
	}

	public function action_get_all() {
		return $_GET;
	}

}

?>