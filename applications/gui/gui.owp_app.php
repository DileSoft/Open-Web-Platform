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
			if (! isset($block ['attributes'] ["name"])) {
				$block ['attributes'] ["name"] = $block ['attributes'] ["type"];
			}
			
			if (!isset($block ["attributes"] ["type"]))
			{
				$block ["attributes"] ["type"] = "";
			}
			
			$inner_html = $block ['contents'];
			
			$div = $this->get_block_html ( $block ["attributes"] ["type"], $block ["attributes"] ["name"], $block ["attributes"], $inner_html );
			
			$html = str_replace ( $block ["full_tag"], $div, $html );
		}
		
		return $html;
		
	/*				$blocks = $xml->xpath ( "//owp_block" );
		foreach ( $blocks as $block ) {
			if (! $block ["name"]) {
				$block ["name"] = $block ["type"];
			}
			
			$inner_html = "";
			foreach ( $block->children () as $child ) {
				$inner_html .= $child->asXML ();
			}
			
			$div = simplexml_load_string ( $this->get_block_html ( $block ["type"] [0], $block ["name"] [0], $block->attributes (), $inner_html ) );
			
			if ($div) {
				$this->simplexml_insert_after ( $div, $block );
			}
		}
		
		while ( $blocks = $xml->xpath ( "//owp_block" ) ) {
			unset ( $blocks [0] [0] );
		}
		
		return $xml;
*/
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
		
		owp_core::library ( "gui" );
		
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
<script type="text/javascript" src="/applications/gui/jquery.js"></script>
<script type="text/javascript" src="/applications/gui/jquery.form.js"></script>
EOF;
		$html = str_ireplace ( "<head>", "<head>{$head}", $html );
		
		echo $html;
		/*		$xml = simplexml_load_string ( $html );
		
		$this->parse_blocks ( $xml );
		
		$title = $xml->head->addChild ( "title" );
		$title [0] = $this->title;
		$script = $xml->head->addChild ( "script" );
		$script [0] = "
		";
		
		$script = $xml->head->addChild ( "script" );
		$script ["type"] = "text/javascript";
		$script ["src"] = "/applications/gui/jquery.js";
		
		$script = $xml->head->addChild ( "script" );
		$script ["type"] = "text/javascript";
		$script ["src"] = "/applications/gui/jquery.form.js";
		
		$doc = new DOMDocument ();
		$doc->encoding = "UTF-8";
		$doc->loadXML ( $xml->asXML () );
		echo $html = $doc->saveHTML ();
*/	}
	
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
/*		foreach ( $xml_settings as $key => $value ) {
			$settings [$key] = strval ( $value [0] );
		}
		
*/		if (! $type) {
			$type = "raw";
		}
		
		$settings ["type"] = $type;
		$settings ["name"] = $name;
		$settings ["html"] = $inner_html;
		owp_core::app ( "{$type}_gui" )->setup ( $settings );
		
		return "<div id=\"owp_core_block|{$type}|{$name}\">" . owp_core::app ( "{$type}_gui" )->html () . "</div>";
	}
	
	protected function extract_tags($html, $tag) {
		
		if (is_array ( $tag )) {
			$tag = implode ( '|', $tag );
		}
		
		$tag_pattern_sc = '@<(?P<tag>' . $tag . ')			# <tag
			(?P<attributes>\s[^>]+)?		# attributes, if any
			\s*/>					# /> or just >, being lenient here 
			@xsi';
		$tag_pattern = '@<(?P<tag>' . $tag . ')			# <tag
			(?P<attributes>\s[^>]+)?		# attributes, if any
			\s*[^/]*>					# >
			(?P<contents>.*?)			# tag contents
			</(?P=tag)>				# the closing </tag>
			@xsi';
		
		$attribute_pattern = '@
		(?P<name>\w+)							# attribute name
		\s*=\s*
		(
			(?P<quote>[\"\'])(?P<value_quoted>.*?)(?P=quote)	# a quoted value
			|							# or
			(?P<value_unquoted>[^\s"\']+?)(?:\s+|$)			# an unquoted value (terminated by whitespace or EOF) 
		)
		@xsi';
		
		preg_match_all ( $tag_pattern, $html, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE );
		preg_match_all ( $tag_pattern_sc, $html, $matches_sc, PREG_SET_ORDER | PREG_OFFSET_CAPTURE );
		
		$matches = array_merge ( $matches, $matches_sc );
		
		$tags = array ();
		foreach ( $matches as $match ) {
			
			//Parse tag attributes, if any
			$attributes = array ();
			if (! empty ( $match ['attributes'] [0] )) {
				
				if (preg_match_all ( $attribute_pattern, $match ['attributes'] [0], $attribute_data, PREG_SET_ORDER )) {
					//Turn the attribute data into a name->value array
					foreach ( $attribute_data as $attr ) {
						if (! empty ( $attr ['value_quoted'] )) {
							$value = $attr ['value_quoted'];
						} else if (! empty ( $attr ['value_unquoted'] )) {
							$value = $attr ['value_unquoted'];
						} else {
							$value = '';
						}
						
						//Passing the value through html_entity_decode is handy when you want
						//to extract link URLs or something like that. You might want to remove
						//or modify this call if it doesn't fit your situation.
						//						$value = html_entity_decode ( $value, ENT_QUOTES, $charset );
						

						$attributes [$attr ['name']] = $value;
					}
				}
			
			}
			
			$tag = array (
					'tag_name' => $match ['tag'] [0], 
					'offset' => $match [0] [1], 
					'contents' => ! empty ( $match ['contents'] ) ? $match ['contents'] [0] : '',  //empty for self-closing tags
					'attributes' => $attributes );
			$tag ['full_tag'] = $match [0] [0];
			
			$tags [] = $tag;
		}
		
		return $tags;
	}
}

?>