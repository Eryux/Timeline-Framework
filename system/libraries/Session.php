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
 * Session Class
 */
 
class Session {
	
	// Flash session name
	private $flashName;
	
	// Session cookie name
	private $cookieName;
	
	// Flash session array
	private $flashData = array();
	
	// Constructor
	public function __construct() {
		$TL =& getInstance();
		
		$this->flashName = $TL->config->get('sessionFlash');
		$this->cookieName = session_name();
		
		if (isset($_SESSION[$this->flashName]) && is_array($_SESSION[$this->flashName])) {
			
			foreach ($_SESSION[$this->flashName] as $key => $value) {
				$this->flashData[$key] = $value;
				unset($_SESSION[$this->flashName][$key]);
			}
			
			unset($_SESSION[$this->flashName]);
		}
		
		$TL->load->helper('session');
	}
	
	// ------------------------------------------
	
	/**
	 * setItem
	 * Set session item.
	 *
	 * @param string $name
	 *		Item name
	 *
	 * @param mixed $value
	 *		Item's value
	 *
	 * @return void
	 */
	public function setItem($name, $value) {
		$_SESSION[$name] = $value;
	}
	
	/**
	 * getItem
	 * Return session item. If item doesn't exists return an empty string.
	 *
	 * @param string $name
	 *		Item name
	 *
	 * @return mixed
	 */
	public function getItem($name) {
		if (isset($_SESSION[$name]))
			return $_SESSION[$name];
		else
			return '';
	}
	
	/**
	 * setFlash
	 * Set flash session item.
	 *
	 * @param string $name
	 *		Item name
	 *
	 * @param mixed $value
	 *		Item's value
	 *
	 * @return void
	 */
	public function setFlash($name, $value) {
		$_SESSION[$this->flashName][$name] = $value;
		$this->flashData[$name] = $value;
	}
	
	/**
	 * getFlash
	 * Return flash session item. If item doesn't exists return an empty string.
	 *
	 * @param string $name
	 *		Item name
	 *
	 * @return mixed
	 */
	public function getFlash($name) {
		if (isset($this->flashData[$name]))
			return $this->flashData[$name];
		else
			return '';
	}
	
	/**
	 * delItem
	 * Remove session item.
	 *
	 * @param string $name
	 *		Item name
	 *
	 * @return void
	 */
	public function delItem($name) {
		if (isset($_SESSION[$name]))
			unset($_SESSION[$name]);
	}
	
}

/* End of file Session.php */
/* Location : ./system/libraries/Session.php */