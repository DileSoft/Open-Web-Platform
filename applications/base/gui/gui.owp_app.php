<?php

class owp_app_gui extends owp_app_base {
	
	protected $title;
	
	protected $template;
	
	protected $ajax = false;
	
	protected function simplexml_insert_after(SimpleXMLElement $insert, SimpleXMLElement $target) {
		$target_dom = dom_import_simplexml ( $target );
		$insert_dom = $target_dom->ownerDocument->importNode ( dom_import_simplexml ( $insert ), true );
		if ($target_dom->nextSibling) {
			return $target_dom->parentNode->insertBefore ( $insert_dom, $target_dom->nextSibling );
		} else {
			return $target_dom->parentNode->appendChild ( $insert_dom );
		}
	}
	
	public function action_set_page($app, $page) {
		$this->template = file_get_contents ( owp_core::get_resource ( $app, "{$app}_{$page}.tpl" ) );
	}
	
	public function action_parse_blocks($html) {
		$html = $this->parse_blocks ( $html );
		
		return $html;
	}
	
	protected function parse_blocks($html) {
		
		$blocks = $this->extract_tags ( $html, "owp_block" );
		
		foreach ( $blocks as $block ) {
			if (! isset ( $block ['attributes'] ["name"] )) {
				$block ['attributes'] ["name"] = $block ['attributes'] ["type"];
			}
			
			if (! isset ( $block ["attributes"] ["type"] )) {
				$block ["attributes"] ["type"] = "";
			}
			
			$inner_html = $block ['contents'];
			
			$div = $this->get_block_html ( $block ["attributes"] ["type"], $block ["attributes"] ["name"], $block ["attributes"], $inner_html );
			
			//$html = str_replace ( $block ["full_tag"], $div, $html );
			

			$block ["node"]->outertext = $div;
		}
		
		if (isset ( $block )) {
			$html = strval ( $block ["root"] );
		}
		
		return $html;
	
	}
	
	protected function action_process_events() {
		$post = $this->app_request->post_all ();
		
		if ($this->app_request->post ( "owp_gui_ajax" )) {
			$this->ajax = true;
		}
		
		if (isset ( $post ["owp_gui_event"] )) {
			$this->action_event ( $post ["owp_gui_block_type"], $post ["owp_gui_block_name"], $post ["owp_gui_event"], $post );
			
			return true;
		}
		
		return false;
	}
	
	public function action_render() {
		
		if ($this->action_process_events ()) {
			return;
		}
		header ( "content-type: text/html; charset=\"UTF-8\"" );
		
		$html = $this->template;
		$html = $this->parse_blocks ( $html );
		$head = <<<EOF
<title>{$this->title}</title>
<script type="text/javascript" language="text/javascript">
function owp_gui_class()
{
	this.event = function owp_gui_event(block, event, data)
	{
		
	}
}

owp_gui = new owp_gui_class();

</script>
<script type="text/javascript" src="/libraries/gui/include/jquery.js"></script>
<script type="text/javascript" src="/libraries/gui/include/jquery.form.js"></script>
EOF;
		$html = str_ireplace ( "<head>", "<head>{$head}", $html );
		
		echo $html;
	}
	public function action_set_title($title) {
		$this->title = $title;
	}
	
	public function action_event($type, $name, $event, $data) {
		$data ["type"] = $type;
		$data ["name"] = $name;
		owp_core::app ( "{$type}_gui" )->event ( $event, $data );
		if ($this->ajax) {
			$result = owp_core::call ( "{$type}_gui", "html", $data );
			echo $result;
		} else {
			$this->refresh ();
		}
	}
	
	protected function refresh() {
		$this->app_response->refresh ();
	}
	
	protected function get_block_html($type, $name, $settings, $inner_html) {
		if (! $type) {
			$type = "raw";
		}
		
		$settings ["type"] = $type;
		$settings ["name"] = $name;
		$settings ["html"] = $inner_html;
		owp_core::app ( "{$type}_gui" )->setup ( $settings );
		
		return "<div id=\"owp_core_block|{$type}|{$name}\">" . owp_core::app ( "{$type}_gui" )->html () . "</div>";
	}
	
	protected function extract_tags($html, $tag) {
		$html_dom = str_get_html ( $html );
		
		if (! $html_dom) {
			return array ();
		}
		
		$found = $html_dom->find ( $tag );
		
		$tags = array ();
		foreach ( $found as $match ) {
			$tag = array (
					'tag_name' => $match->tag, 
					'contents' => $match->innertext, 
					'attributes' => $match->getAllAttributes () );
			$tag ['full_tag'] = $match->outertext;
			$tag ['node'] = $match;
			$tag ['root'] = $html_dom;
			
			$tags [] = $tag;
		}
		
		return $tags;
	}
}

?>