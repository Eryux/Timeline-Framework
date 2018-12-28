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
 * View Class
 */
 
class View {
	
	// Framework instance
	private $FW;
	
	// Constructor
	public function __construct() {
		$this->FW =& getInstance();
	}
	
	// ------------------------------------------
	
	/**
	 * load
	 * Load view file and set to output.
	 *
	 * @param string $_file
	 *		The file that will be loaded
	 *
	 * @param array/object $_data
	 *		Variables that will be passed to the view (optional)
	 *
	 * @param bool $_dir
	 *		Location of views folder (optional, default is APPPATH . 'views/' (optional)
	 *
	 * @return bool
	 */
	public function load($_file, $_data = array(), $_dir = '') {
		if ($_dir === '')
			$_dir = APPPATH . 'views/';
			
		if ( ! file_exists($_dir . $_file))
			return FALSE;
			
		$data = $this->_objectToArray($_data);
		extract($_data);
		
		ob_start();
		include($_dir . $_file);
		$this->FW->output->appendOutput(ob_get_contents());
		ob_end_clean();
		
		return TRUE;
	}
	
	/**
	 * _objectToArray
	 * Takes an object as input and converts class variables to array key/values.
	 * @access private
	 * 
	 * @param object $object
	 *		Object that you want to convert
	 *
	 * @return array
	 */
	private function _objectToArray($object) {
		return (is_object($object)) ? get_object_vars($object) : $object;
	}
	
}

/* End of file View.php */
/* Location : ./system/libraries/View.php */