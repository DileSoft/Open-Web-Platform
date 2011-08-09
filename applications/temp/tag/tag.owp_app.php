<?php

class owp_app_tag extends owp_app_base {
	/* (non-PHPdoc)
	 * @see owp_app::start()
	 */
	public function start() {
		// TODO Auto-generated method stub
		
	}

	/* (non-PHPdoc)
	 * @see owp_app::autoload()
	 */
	public function autoload() {
		session_start();
		
	}

	/* (non-PHPdoc)
	 * @see owp_app::stop()
	 */
	public function stop() {
		// TODO Auto-generated method stub
		
	}

	/* (non-PHPdoc)
	 * @see owp_app::install()
	 */
	public function install() {
		// TODO Auto-generated method stub
		
	}

	/* (non-PHPdoc)
	 * @see owp_app::uninstall()
	 */
	public function uninstall() {
		// TODO Auto-generated method stub
		
	}
	
	protected function register()
	{
		
	}
	
	protected function login()
	{
		
	}
	
	protected function logout()
	{
		
	}

	/* (non-PHPdoc)
	 * @see owp_app::call()
	 */
	public function call($method, $data) {
		// TODO Auto-generated method stub
		
		if ($method == "set")
		{
			$_SESSION[$data["name"]] = $data["content"];
		}
		
		if ($method == "get")
		{
			return print_r($_SESSION[$data["name"]], true);
		}
		
		if ($method == "id")
		{
			return session_id();
		}
		
		if ($method == "dump")
		{
			return print_r($_SESSION, true);
		}

		if ($method == "cookies")
		{
			return print_r($_COOKIE, true);
		}
	}


}

?>