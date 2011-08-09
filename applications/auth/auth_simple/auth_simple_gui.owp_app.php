<?php

class owp_app_auth_simple_gui extends owp_app_gui_block_base {
	
	public function action_login($name, $password)
	{
		$this->app_auth_simple->login($name, $password);
	}
	
	public function action_logout()
	{
		$this->app_auth_simple->logout();
	}
	
	protected function prepare() {
		
		
		if ($login = $this->app_auth_simple->get_login())
		{
			$this->settings["html"] = <<<EOF
<div>{$login}, вы вошли.</div>
<form method="POST">
	<owp_block type="submit" title="Выйти" 
		block_name="{$this->settings["name"]}"
		block_type="{$this->settings["type"]}"
		ajax="1"
		event="logout"
	/>
</form>
EOF;
		}
		else 
		{
			$this->settings["html"] = <<<EOF
			<form method="POST">
				<div>Логин: <input name="login"/></div>
				<div>Пароль: <input type="password" name="password"/></div>		
				<div>
					<owp_block type="submit" title="Войти" 
						block_name="{$this->settings["name"]}"
						block_type="{$this->settings["type"]}"
						ajax="1"
						event="login"
					/>
				</div>
			</form>
EOF;
		}
	}
	
}

?>