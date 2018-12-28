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
 * Output Class
 */
 
class Output {
	
	// Variable which contain output
	private $output;
	
	// Constructor
	public function __construct() {
		$this->ouput = '';
	}
	
	// ------------------------------------------
	
	/**
	 * setOutput
	 *
	 * @param string $value
	 *		Value of output
	 *
	 * @return void
	 */
	public function setOutput($value) {
		$this->output = $value;
	}
	
	/**
	 * getOutput
	 * @return string
	 */
	public function getOutput() {
		return $this->output;
	}
	
	/**
	 * appendOutput
	 * Add data in output
	 *
	 * @param string $data
	 *		Data that you want to add in output
	 *
	 * @return void
	 */
	public function appendOutput($data) {
		$this->output .= $data;
	}
	
	/**
	 * addHeader
	 * 
	 * @param mixed $header
	 *		Header(s) line(s) that you want to add
	 *
	 * @return void
	 */
	public function addHeader($header) {
		if (is_array($header) === TRUE) {
			foreach($header as $data)
				header($data);
		}
		else
			header($header);
	}
	
	/**
	 * setOutputType
	 *
	 * @param string $type
	 *		mime-type of output
	 *
	 * @return void
	 */
	public function setOutputType($type = 'text/html') {
		$this->addHeader('Content-Type: ' . $type);
	}
	
}

/* End of file Output.php */
/* Location : ./system/libraries/Output.php */