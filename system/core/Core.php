<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * TimeLine
 * An open source PHP micro-framework based on CodeIgniter Framework.
 *
 * The MIT License (MIT)
 * Copyright (c) 2015 Candia Nicolas
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @licence		MIT
 * @author 		Candia Nicolas
 * @copyright	Copyright (c) 2014, Candia Nicolas
 */

/**
 * Boostrap
 * Define main function, load bases classes and right controller.
 */
 
// ----------------------------------------------

/**
 * loadClass
 * This function acts as a singleton? If the requested class doesn't loaded
 * it's instantiated and set in a static array. If it has previously loaded
 * the instance in static array that correspond to the class is returned.
 *
 * @param string $class
 *		Class name being requested
 *
 * @param string $dir = 'libraries'
 *		Directory where class should be found (optional)
 *
 * @return object
 */
function &loadClass($class, $dir = 'libraries') {
	static $classes = array();
	
	// If class already loaded we return it
	if (isset($classes[$class]) === TRUE) {
		return $classes[$class];
	}
	
	// If itn't, we try to load it
	if (file_exists(SYSPATH . $dir . '/' . $class . '.php')) {
		if (class_exists($class) === FALSE) {
			require SYSPATH . $dir . '/' . $class . '.php';
		}
	}
	else {
		exit('Unable to find ' . htmlentities($class) . '.php in ' . SYSPATH . htmlentities($dir));
	}
	
	// Keep track of what we just loaded
	isLoaded($class);
	
	$classes[$class] = new $class();
	return $classes[$class];
}

/**
 * isLoaded
 * Keeps track of which libraries have been loaded. This function is called by
 * the loadClass() function above. And return all loaded class name.
 *
 * @param string $class = ''
 *		Class name that you want to add at loaded classes
 *
 * @return array
 */
function &isLoaded($class = '') {
	static $isLoaded = array();
	
	if ($class != '') {
		$isLoaded[strtolower($class)] = $class;
	}
		
	return $isLoaded;
}

/**
 * getConfig
 * This function lets grab the config file even if Config class hasn't
 * been instantiated yet.
 *
 * @return array
 */
function &getConfig($replace = array()) {
	static $setting;
	
	if (isset($setting[0]) === TRUE)
		return $setting[0];
		
	if ( ! file_exists(APPPATH . 'configs/settings.php')) {
		exit('The configuration file does not exist.');
	}
		
	require APPPATH . '/configs/settings.php';
	
	if ( ! isset($config) || ! is_array($config)) {
		exit('Your config file does not appear to be formatted correctly.');
	}
		
	if (count($replace) > 0) {
		foreach ($replace as $key => $val) {
			if (isset($config[$key]))
				$config[$key] = $val;
		}
	}
	
	$setting[0] = $config;
    return $setting[0];
}

/**
 * setHTTPStatus
 * Set HTTP header status.
 *
 * @param int $code
 *		Status code of page in the headers
 *
 * @return void
 */
function setHTTPStatus($code) {
	$codeText = array(
		200 => 'OK', 201 => 'Created', 202 => 'Accepted', 203 => 'Non-Authoritative Information', 204 => 'No Content', 205 => 'Reset Content', 206 => 'Partial Content', 207 => 'Multi-Status', 208 => 'Already Reported', 226 => 'IM Used',
		300 => 'Multiple Choices', 301 => 'Moved Permanently', 302 => 'Found', 304 => 'Not Modified', 305 => 'Use Proxy', 307 => 'Temporary Redirect', 308 => 'Permanent Redirect',
		400 => 'Bad Request', 401 => 'Unauthorized', 403 => 'Forbidden', 404 => 'Not Found', 405 => 'Method Not Allowed', 406 => 'Not Acceptable', 407 => 'Proxy Authentification Required', 408 => 'Request Timeout', 409 => 'Conflict',
		410 => 'Gone', 411 => 'Length Required', 412 => 'Precondition Failed', 413 => 'Request Entity Too Large', 414 => 'Request-URI Too Long', 415 => 'Unsupported Media Type', 416 => 'Requested Range Not Satisfiable', 417 => 'Exeptation Failed',
		418 => 'I\'m a teapot', 423 => 'Locked', 426 => 'Upgrade Required', 429 => 'Too Many Requests',
		500 => 'Internal Server Error', 501 => 'Not Implemented', 502 => 'Bad Gateway', 503 => 'Service Unvailable', 504 => 'Gateway Timeout', 505 => 'HTTP Version Not Supported'
	);
	
	if (is_int($code) === FALSE) {
		showError('Status codes must be numeric.', 500);
	}
		
	$error = (isset($codeText[$code]) === TRUE) ? $codeText[$code] : $codeText[500];
	$serverProtocol = (isset($_SERVER['SERVER_PROTOCOL'])) ? $_SERVER['SERVER_PROTOCOL'] : FALSE;
	
	if (substr(php_sapi_name(), 0, 3) == 'cgi') {
		header("Status: {$code} {$error}", TRUE);
	} else if ($serverProtocol == 'HTTP/1.1' || $serverProtocol == 'HTTP/1.0') {
		header($serverProtocol . " {$code} {$error}", TRUE, $code);
	} else {
		header("HTTP/1.1 {$code} {$error}", TRUE, $code);
	}
}
	
/**
 * showError
 * Takes an error message as input and display usings a template file.
 * This function will send the error page directly to the client and exit.
 *
 * @param string $notice = ''
 *		Error message
 *
 * @param int $code = 500
 *		Status code of page in the headers
 * 
 * @param string $view = SYSPATH . 'errors/error.php'
 *		View
 *
 * @return void
 */
function showError($notice = '', $code = 500, $view = '') {
	if ($view === '') {
		if (file_exists(APPPATH . 'views/errors/' . $code . '.php')) {
			$view = APPPATH . 'views/errors/' . $code . '.php';
		} else if (file_exists(APPPATH . 'views/errors/common.php')) {
			$view = APPPATH . 'views/errors/common.php';
		}
	}
	
	setHTTPStatus($code);
	
	if ($view !== '') {
		ob_start();

		include_once($view);
		$buffer = ob_get_contents();
		
		ob_end_clean();
	
		echo $buffer;
	}
	else {
		echo $notice;
	}
	
	exit();
}

/**
 * clearPath
 * Delete "./", "../" and null characters in the string that passed in
 * parameter.
 *
 * @param string $in
 *		String to clean
 *
 * @return string
 */
function clearPath($in) {
	$disallowed = array("../", "./", "\0");
	$out = str_replace($disallowed, '', $in);
	return $out;
}

/* ----------------------------------------------
 * Load configuration library
 * ----------------------------------------------
 */
	$CONFIG =& loadClass('Config');
	
/* ----------------------------------------------
 * Start session
 * ----------------------------------------------
 */
	if (strlen($CONFIG->get('sessionCookie')) > 3) {
		session_name($CONFIG->get('sessionCookie'));
	}
	
	if (intval($CONFIG->get('sessionExpire')) > 0) {
		session_set_cookie_params(intval($CONFIG->get('sessionExpire')));
	}
	
	session_start();
	
/* ----------------------------------------------
 * Load output library
 * ----------------------------------------------
 */
	$OUTPUT =& loadClass('Output');
	
/* ----------------------------------------------
 * Load language library
 * ----------------------------------------------
 */
	$LANG =& loadClass('Lang');
	
/* ----------------------------------------------
 * Load Router library
 * ----------------------------------------------
 */
	$ROUTER =& loadClass('Router');
	
/* ----------------------------------------------
 * Load controller
 * ----------------------------------------------
 */
	require SYSPATH . 'core/Controller.php';
	
	function &getInstance() {
		return Controller::getInstance();
	}
	
	$ROUTER->launch();
	
/* ------------------------------------------------------
 *	Send final rendered output to client
 * ------------------------------------------------------
 */
	echo $OUTPUT->getOutput();
	
/* End of file Core.php */
/* Location : ./system/core/Core.php */