<?php

class owp_app_hate_site extends owp_app_base {
	public function autoload() {
		
		owp_core::call ( "mysql", "connect", array (
			"localhost", 
			"dilesoft", 
			"", 
			"repository" ) );
		
		$html = owp_core::call ( "repository", "list_all" );
		$this->draw ( $html );
		
	}
	
	protected function draw($body, $title = "") {
		
		$log = implode("<br/>", owp_core::get_log());
		
		echo <<<EOF
<html>
<head>
<title>Репозиторий - {$title}</title>
</head>
<body>
<h1>Репозиторий</h1>
{$body}
<div>
<small>
<hr/>
Лог:<br/>
{$log}
</small>
</div>
</body>
</html>
EOF;
	}
	
	public function start() {
	
	}
	
	public function stop() {
	
	}
	
	public function install() {
	
	}
	
	public function uninstall() {
	
	}
	
	public function call($method, $data) {
	}

}