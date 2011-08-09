<?php

require dirname ( __FILE__ ) . '/owp_app.php';
require dirname ( __FILE__ ) . '/owp_lib.php';

class owp_core {
	
	protected static $instance;
	
	protected static $applications_running = array ();
	
	protected static $applications_processes = array ();
	
	protected static $applications_path = array ();
	
	protected static $applications = array ();
	
	protected static $libraries = array ();
	
	protected static $libraries_loaded = array ();
	
	protected static $log = array ();
	
	protected static $errors = array ();
	
	public static function get_resource($app, $file) {
		
		if (! preg_match ( '#^[a-z0-9\_\-\.]+$#', $file )) {
			return false;
		}
		
		return realpath ( self::$applications_path [$app] . "/{$file}" );
	}
	
	public static function app($name) {

		if (! isset ( self::$applications_processes [$name] )) {
			self::start ( $name );
		}
		
		return self::$applications_processes[$name][0];
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
	
	protected static function dir_tree($dir) {
		$dir = realpath ( $dir );
		
		$files = '';
		$stack [] = $dir;
		while ( $stack ) {
			$thisdir = array_pop ( $stack );
			if ($dircont = scandir ( $thisdir )) {
				foreach ( $dircont as $key => $file ) {
					if ($file !== '.' && $file !== '..') {
						$current_file = "{$thisdir}/{$file}";
						if (is_file ( $current_file )) {
							$files [] = array (
									"path" => $thisdir, 
									"file" => $file );
						} elseif (is_dir ( $current_file )) {
							$stack [] = $current_file;
						}
					}
				}
			}
		}
		return $files;
	}
	
	protected static function autoload() {
		$path = realpath ( dirname ( __FILE__ ) . "/../libraries" );
		$libs = self::dir_tree ( $path );
		
		foreach ( $libs as $lib ) {
			if (! preg_match ( '/^(.+)\.owp_lib.php$/', $lib ["file"], $matches )) {
				continue;
			}
			
			$name = $matches [1];
			
			require_once ($lib ["path"] . "/" . $lib ["file"]);
			
			if (file_exists ( $lib ["path"] . "/{$name}.manifest.xml" )) {
				$manifest = simplexml_load_file ( $lib ["path"] . "/{$name}.manifest.xml" );
				if ($manifest->autoload) {
					self::library ( $name );
				}
			}
		}
		
		$path = realpath ( dirname ( __FILE__ ) . "/../applications" );
		$apps = self::dir_tree ( $path );
		
		foreach ( $apps as $app ) {
			if (preg_match ( '/^(.+)\.owp_app\.php$/', $app ["file"], $matches )) {
				
				$name = $matches [1];
				
				require_once ($app ["path"] . "/" . $app ["file"]);
				self::$applications_path [$name] = $app ["path"];
			}
		}
		
		foreach ( $apps as $app ) {
			if (preg_match ( '/^(.+)\.owp_app\.php$/', $app ["file"], $matches )) {
				
				$name = $matches [1];
				
				if (file_exists ( $app ["path"] . "/{$name}.manifest.xml" )) {
					$manifest = simplexml_load_file ( $app ["path"] . "/{$name}.manifest.xml" );
					if ($manifest->autoload) {
						self::start ( $name, true );
					}
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
	
	public static function call($application, $method, $data = array()) {
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
	
	public static function start_app_instance($application) {
		$class = "owp_app_{$application}";
		
		if (!isset(self::$applications_running [$application]))
		{
			$app = self::$applications_running [$application] = new $class ( $application );
		}
		
		$process = self::$applications_processes [$application][] = new owp_app_process ( $application );
		
		$app->start ();
		
		return new owp_app_process ( $application );
	}
	
	public static function start($application, $autoload = false) {
		
		if (! isset ( self::$applications_running [$application] )) {
			
			$class = "owp_app_{$application}";
			$app = self::$applications_running [$application] = new $class ( $application );
			$process = self::$applications_processes [$application][] = new owp_app_process ( $application );
			
			if ($autoload) {
				self::log ( "autoload $application" );
				$app->autoload ();
			} else {
				self::log ( "load $application" );
				$app->start ();
			}
			
			return $process;
		} else {
			return self::$applications_processes [$application];
		}
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