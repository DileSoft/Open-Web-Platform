<?php

require dirname ( __FILE__ ) . '/owp_app.php';
require dirname ( __FILE__ ) . '/owp_lib.php';

class owp_core {
	
	protected static $instance;
	
	protected static $applications_running = array ();
	
	protected static $applications_processes = array ();
	
	protected static $applications = array ();
	
	protected static $libraries = array ();
	
	protected static $libraries_loaded = array ();
	
	protected static $log = array ();
	
	protected static $errors = array ();
	
	public static function get_resource($app, $file) {
		
		if (! preg_match ( '#^[a-z0-9\_\-\.]+$#', $file )) {
			return false;
		}
		
		return realpath ( dirname ( __FILE__ ) . "/../applications/{$app}/{$file}" );
	}
	
	public static function app($name) {
		
		if (!isset(self::$applications_processes [$name]))
		{
			self::start($name);
		}
		
		return self::$applications_processes [$name];
	}
	
	public static function error($app, $name, $code, $comment) {
		self::$errors [] = array (
				"app" => $app, 
				"process" => $name, 
				"code" => $code, 
				"comment" => $comment );
	}
	
	public static function log($log) {
		self::$log [] = $log;
	}
	
	public static function get_log() {
		return self::$log;
	}
	
	public static function applications_list() {
	
	}
	
	public static function install_all() {
	}
	
	public static function core() {
		self::$instance = new owp_core ();
		self::autoload ();
	}
	
	protected static function autoload() {
		$path = realpath ( dirname ( __FILE__ ) . "/../libraries" );
		$libs = scandir ( $path );
		
		foreach ( $libs as $lib ) {
			if ($lib == "." || $lib == "..") {
				continue;
			}
			require_once $path . "/" . $lib . "/{$lib}.owp_lib.php";
			
			if (file_exists ( $path . "/" . $lib . "/manifest.xml" )) {
				$manifest = simplexml_load_file ( $path . "/" . $lib . "/manifest.xml" );
				if ($manifest->autoload) {
					self::library($lib);
				}
			}
		}
		
		$path = realpath ( dirname ( __FILE__ ) . "/../applications" );
		$apps = scandir ( $path );
		
		foreach ( $apps as $app ) {
			if ($app == "." || $app == "..") {
				continue;
			}
			
			require_once $path . "/" . $app . "/{$app}.owp_app.php";
		}
		
		foreach ( $apps as $app ) {
			if ($app == "." || $app == "..") {
				continue;
			}
			
			if (file_exists ( $path . "/" . $app . "/manifest.xml" )) {
				$manifest = simplexml_load_file ( $path . "/" . $app . "/manifest.xml" );
				if ($manifest->autoload) {
					self::start ( $app, true );
				}
			}
		}
	}
	
	protected static function get_application($application) {
		if (! isset ( self::$applications_running [$application] )) {
			self::start ( $application );
		}
		
		return self::$applications_running [$application];
	}
	
	public static function get_process($name) {
		if (isset ( self::$applications_running [$name] )) {
			return new owp_app_process ( $name );
		}
	}
	
	public static function call($application, $method, $data = array(), $new_instance = false) {
		/**
		 * 
		 * 
		 * @var owp_app
		 */
		$app = self::get_application ( $application );
		
		return $app->call ( $method, $data );
	}
	
	public static function path() {
		return dirname ( dirname ( __FILE__ ) );
	}
	
	public static function start($application, $autoload = false) {
		$class = "owp_app_{$application}";
		$app = self::$applications_running [$application] = new $class ( $application );
		self::$applications_processes [$application] = new owp_app_process ( $application );
		
		if ($autoload) {
			self::log ( "autoload $application" );
			$app->autoload ();
		} else {
			self::log ( "load $application" );
			$app->start ();
		}
		
		return new owp_app_process ( $application );
	}
	
	public static function end($application) {
		$app = self::get_application ( $application );
		$app->stop ();
		unset ( self::$applications_running [$application] );
	}
	
	public static function library($library) {
		self::log ( "load lib $library" );
		$class = "owp_lib_{$library}";
		self::$libraries_loaded [$library] = new $class ();
		
		self::$libraries_loaded [$library]->load ();
	}
	
	public static function install_repository($name) {
	}
	
	public static function uninstall_repository($name) {
	}

}