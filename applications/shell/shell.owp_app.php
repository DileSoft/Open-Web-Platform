<?php 

if ( !function_exists('json_decode') ){
function json_decode($json)
{
    $comment = false;
    $out = '$x=';
  
    for ($i=0; $i<strlen($json); $i++)
    {
        if (!$comment)
        {
            if (($json[$i] == '{') || ($json[$i] == '['))       $out .= ' array(';
            else if (($json[$i] == '}') || ($json[$i] == ']'))   $out .= ')';
            else if ($json[$i] == ':')    $out .= '=>';
            else                         $out .= $json[$i];          
        }
        else $out .= $json[$i];
        if ($json[$i] == '"' && $json[($i-1)]!="\\")    $comment = !$comment;
    }
    $x = eval($out . ';');
    return $x;
}
}

class owp_app_shell extends owp_app_base
{
	public function autoload() {
		//owp_core::call("shell", "me", range(1,10));
		owp_core::start("repository_site", true);
//		owp_core::start("tlink_site", true);
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
//		owp_core::library("mysql");

		if (isset($_POST["app"]))
		{
			echo owp_core::call($_POST["app"], $_POST["method"], json_decode($_POST["data"], true));
			owp_core::call("auth", "login", 
				array("user" => "me", "password" => 123)
			);
			owp_core::call("auth", "user_id");
		echo <<<EOF
<form method="POST">
Компонент: <input name="app" value="{$_POST["app"]}"><br/>
Метод: <input name="method" value="{$_POST["method"]}"><br/>
Данные: <textarea name="data">{$_POST["data"]}</textarea><br/>
<input type="submit"/>
</form>
EOF;
		}
		else 
		{
		echo <<<EOF
<form method="POST">
Компонент: <input name="app" value=""><br/>
Метод: <input name="method" value=""><br/>
Данные: <textarea name="data"></textarea><br/>
<input type="submit"/>
</form>
EOF;
		}
//		new sqlDB("localhost", "a", "b");
	}

	
}