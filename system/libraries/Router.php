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
 * Router Class
 */
 
class Router {
	
	// Configuration library instance
	private $config;
	
	// Language library instance
	private $lang;
	
	// Constructor
	public function __construct() {
		$this->config =& loadClass('Config');
		$this->lang =& loadClass('Lang');
	}
	
	// ------------------------------------------
	
	/**
	 * getController
	 * Return controller name, by default controller is $_GET['m'] and
	 * if any controller is specified, we return the default controller in
	 * general configuration file.
	 *
	 * @return string
	 */
	public function getController() {
		if (isset($_GET[$this->config->get('controllerParam')]))
			$controller = clearPath(urldecode($_GET[$this->config->get('controllerParam')]));
		else
			$controller = clearPath($this->config->get('defaultController'));
		
		return strtolower($controller);
	}
	
	/**
	 * getMethod
	 * Return method name, by default method is $_GET['a'] and if any method
	 * is specified, we return the default method in general configuration file.
	 *
	 * @return string
	 */
	public function getMethod() {
		if (isset($_GET[$this->config->get('methodParam')]))
			$method = urldecode($_GET[$this->config->get('methodParam')]);
		else
			$method = $this->config->get('defaultMethod');
		
		return strtolower($method);
	}
	
	/**
	 * launch
	 * Load right controller and call the correct method. If controller or method
	 * doesn't exists we make a 404 error.
	 *
	 * @return void
	 */
	public function launch() {
		$controller = ucfirst($this->getController());
		$method = $this->getMethod();
		
		if (file_exists(APPPATH . 'modules/' . $controller . '.php') === FALSE)
			showError($this->lang->line('syserr_notfound'), 404);
		
		include_once(APPPATH . 'modules/' . $controller . '.php');
		
		if (class_exists($controller) === FALSE)
			showError($this->lang->line('syserr_notfound'), 404);

		$app = new $controller();
		
		if (method_exists($app, $method) === FALSE)
			showError($this->lang->line('syserr_notfound'), 404);
		
		
		$app->$method();
	}
	
}

/* End of file Router.php */
/* Location : ./system/libraries/Router.php */