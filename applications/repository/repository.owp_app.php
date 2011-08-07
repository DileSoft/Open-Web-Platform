<?php

class owp_app_repository extends owp_app_base {
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
	
	protected function list_all()
	{
		return "<h2>Пакеты:</h2>";
	}
	
	/* (non-PHPdoc)
	 * @see owp_app::call()
	 */
	public function call($method, $data) {
		$this->register_actions ( array (
				"add", 
				"add_html", 
				"edit_html", 
				"delete_html", 
				"edit", 
				"delete", 
				"show", 
				"list_all" ) );
		return $this->process_actions($method, $data);
	}

}

?>