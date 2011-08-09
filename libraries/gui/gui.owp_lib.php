<?php

class owp_lib_gui implements owp_lib {
	
	public function unload() {
	
	}
	
	public function load() {
		
		require_once (dirname(__FILE__) . "/include/owp_app_gui_block_base.php");
		require_once (dirname(__FILE__) . "/include/simple_html_dom.php");
	}
}

?>