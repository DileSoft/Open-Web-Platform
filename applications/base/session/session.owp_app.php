<?php

class owp_app_session extends owp_app_base {
	
	public function start() {
		session_start ();
	}
	
	public function action_set($name, $value) {
		$_SESSION [$name] = $value;
	}
	
	public function action_get($name) {
		if (isset($_SESSION[$name]))
		{
			return $_SESSION [$name];
		}
	}

	public function action_delete($name) {
		unset($_SESSION [$name]);
	}

	public function action_id() {
		return session_id();
	}

}

?>