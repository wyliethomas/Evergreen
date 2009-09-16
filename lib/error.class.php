<?php
final class Error {
	static private $registeredErrors = array();
	
	## Used to track the current error ##
	static private $key;
	static private $message;
	static private $params;
	static private $errorObj;
	
	final public static function register($key, $params) {
		if (!is_array($params)) {
			$params = array("message"=>$params);
		}
		
		self::$registeredErrors[$key] = $params;
	}
	
	final public static function trigger($message, $params = array()) {
		if (array_key_exists($message, self::$registeredErrors)) {
			$key = $message;
			$params = self::$registeredErrors[$message];
			$message = self::$registeredErrors[$message]['message'];
		}
		
		self::$key = $key;
		self::$message = $message;
		self::$params = $params;
		
		throw new Exception(self::$message);
	}
	
	final public static function processError($errorObj = null) {
		self::clearAllBuffers();
		if ($errorObj != null) {
			self::$errorObj = $errorObj;
		}
		
		if (isset(self::$params['code']) && array_key_exists(self::$params['code'], Config::read("Errors"))) {
			if (isset(self::$params['url'])) {
				Error::loadURL(self::$params['url']);
			} else {
				Error::loadURL(Config::read("Errors.".self::$params['code']));
			}
		} else {
			if (isset(self::$params['url'])) {
				Error::loadURL(self::$params['url']);
			} else {
				include(Config::read("System.defaultErrorGEN"));
			}
		}
	}
	
	final public static function getMessage() {
		return self::$message;
	}
	
	final public static function getTrace() {
		return self::$errorObj->getTrace();
	}
	
	final public static function clearAllBuffers() {
		$buffer_count = ob_get_level();
		for($i = 1; $i <= $buffer_count; $i++) {
			ob_end_clean();
		}
	}
	
	final public static function loadURL($url) {
		if (isset(self::$params['code']) && self::$params['code'] == 404) {
			header("HTTP/1.0 404 Not Found");
		}
		/*
			TODO : Check for an external url
		*/
		if (!empty($url)) {
			$url = str_replace(URI_ROOT, "", $url);
			Config::register("URI.working", $url);
			Config::register("Branch.name", "");
			Config::processURI();
			
			if (Config::read("Branch.name")) {
				## Unload Main Autoloader ##
				spl_autoload_unregister(array('AutoLoaders', 'main'));
				
				## Load Branch Autoloader ##
				spl_autoload_register(array('AutoLoaders', 'branches'));
			} else {
				## Unload Branch Autoloader ##
				spl_autoload_unregister(array('AutoLoaders', 'branches'));
				
				## Load Main Autoloader ##
				spl_autoload_register(array('AutoLoaders', 'main'));
			}
			
			if (($controller = System::load(array("name"=>reset(Config::read("URI.working")), "type"=>"controller", "branch"=>Config::read("Branch.name")))) === false) {
				include(Config::read("System.defaultError404"));
			} else {
				try {
					$controller->showView();
				} catch(Exception $e) {
					if (Config::read("System.mode") == "development") {
						include(Config::read("System.defaultErrorGeneral"));
					}
				}
			}
			
		} else {
			include(Config::read("System.defaultError404"));
		}
	}
	
	
}
?>