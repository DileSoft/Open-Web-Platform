<?php

class owp_app_auth_simple extends owp_app_base {
	
	protected $page;
	
	public function action_login($user, $password) {
		if ($user == "admin" && $password == "admin") {
			$this->app_session->set ( "auth_login", $user);
		}
	}
	
	public function action_get_login() {
		return $this->app_session->get ( "auth_login" );
	}
	
	public function action_logout() {
		if ($this->app_session->get ( "auth_login" )) {
			
			$this->app_session->delete ( "auth_login" );
		}
	}

}

?>