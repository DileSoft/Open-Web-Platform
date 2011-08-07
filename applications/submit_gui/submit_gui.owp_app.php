<?php

class owp_app_submit_gui extends owp_app_gui_block_base {
	
	protected function prepare() {
		if ($this->get("ajax"))
		{
			$this->settings["ajax_script"] = <<<EOF
    <script type="text/javascript"> 
        $(document).ready(function() {
        	 
            $('#owp_core_block_{$this->settings["name"]}_button')
            .parents('form')
            .ajaxForm({
            	'target':
            		'#owp_core_block\\\\|{$this->settings["block_type"]}\\\\|{$this->settings["block_name"]}'
            }); 
        }); 
    </script> 
EOF;
		}
		else
		{
			$this->settings["ajax_script"] = "";
		}
	}
}

?>