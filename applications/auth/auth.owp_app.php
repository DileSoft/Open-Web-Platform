<?php

class owp_app_auth extends owp_app_base {
	
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
		session_start ();
	
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
	
	/* (non-PHPdoc)
	 * @see owp_app::call()
	 */
	public function call($method, $data) {
		// TODO Auto-generated method stub

		$this->register_actions("register", "register_page", "login", "login_page", "logout", "logout_page");
		
		$this->process_actions($method, $data);
	}
	
	protected function register_page() {
	
	}
	
	protected function register() {
	
	}
	
	protected function login_page() {
	
	}
	
	protected function login() {
	
	}
	
	protected function logout_page() {
	
	}
	
	protected function logout() {
	
	}

}

?>