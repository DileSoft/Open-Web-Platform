<?php

class owp_lib_db_orm implements owp_lib {
	
	public function unload() {
	
	}
	
	public function load() {
		
		require_once (dirname(__FILE__) . "/include/db_field_base.owp_app.php");
	}
}

?>