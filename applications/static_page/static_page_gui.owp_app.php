<?php

class owp_app_static_page_gui extends owp_app_gui_block_base {
	
	protected function prepare() {
		$url = $this->app_request->address();
		$page = $this->app_static_page;
		$page->load_page($url);
		$this->settings["title"] = $page->get_title();
		$this->settings["body"] = $page->get_body();
		$this->app_gui->set_title($page->get_title());
	}
}

?>