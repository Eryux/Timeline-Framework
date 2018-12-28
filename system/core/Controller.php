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
 * Controller Class
 */
 
class Controller {

	private static $instance;
	
	// Constructor
	public function __construct() {
		self::$instance =& $this;
		
		foreach (isLoaded() as $var => $class) {
			$this->$var =& loadClass($class);
		}
			
		$this->load =& loadClass('Load');
		$this->load->initialize();
	}
	
	public static function &getInstance() {
		return self::$instance;
	}
	
}

/* End of file Controller.php */
/* Location : ./system/core/Controller.php */