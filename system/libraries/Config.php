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
 * Config Class
 */
 
class Config {

	// Array which contains all loaded settings
	private $settings = array();
	
	// Configurations directories
	private $modConfigDir;
	
	// Constructor
	public function __construct() {
		$this->modConfigDir = APPPATH . 'configs/';
		$this->settings['setting'] =& getConfig();
	}
	
	// ------------------------------------------
	
	/**
	 * load
	 * Load a new config file (for libraries or modules)
	 *
	 * @param string $file
	 *		File name
	 *
	 * @param string $alias
	 *		Alias (by default is setting)
	 *
	 * @return bool
	 */
	public function load($file, $alias = 'setting') {
		// If file is a module configuration file
		if (file_exists($this->modConfigDir . clearPath($file) . '.php')) {
			include_once($this->modConfigDir . clearPath($file) . '.php');
		}
		else {	// File doesn't exists
			return FALSE;
		}
		
		if ( ! isset($config)) {
			return FALSE;
		}
		
		$this->settings[$alias] = (isset($this->settings[$alias])) ? array_merge($this->settings[$alias], $config) : $config;
		return TRUE;
	}
	
	/**
	 * get
	 * Get config item
	 *
	 * @param string $name
	 *		Name of case in settings array that you want to get
	 *
	 * @param string $alias = 'setting'
	 *		Alias (by default is setting)
	 *
	 * @return mixed
	 */
	public function get($name, $alias = 'setting') {
		// If case name doesn't exists in array, we can't return it [cpt. obvious]
		if ( ! isset($this->settings[$alias]) || ! array_key_exists($name, $this->settings[$alias]))
			return '';
		else
			return $this->settings[$alias][$name];
	}
	
	/**
	 * set
	 * Set config item. If the item doesn't exists, we create it.
	 *
	 * @param string $name
	 *		Name of item
	 *
	 * @param mixed $value
	 *		The new value of item that you want to set
	 *
	 * @param string $alias
	 *		Alias
	 *
	 * @return void
	 */
	public function set($name, $value, $alias = 'setting') {
		$this->settings[$alias][$name] = $value;
	}
	
}

/* End of file Config.php */
/* Location : ./system/libraries/Config.php */