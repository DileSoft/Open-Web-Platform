<?php

class owp_lib_mysql implements owp_lib {
	
	public function unload() {
	
	}
	
	public function load() {
		require 'include/DB_class.php';
	}
}

?>