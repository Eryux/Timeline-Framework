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
 * @copyright	Copyright (c) 2015, Candia Nicolas.
 */

class AuthHook {
	
	// Instance of framework
	private $_TL;
	
	// Constructor
	public function __construct() {
		$this->_TL =& getInstance();
		
		if ( ! $this->_TL->load->isLoaded('auth'))
			$this->_TL->load->library('auth');
		
		$this->verifyHasLevel();
	}
	
	// -------------------------------------------
	
	public function verifyHasLevel() {
		$minLevel = $this->_TL->auth->getAccessLevel(
				$this->_TL->router->getController(),
				$this->_TL->router->getMethod()
		);
		
		if ( ! $this->_TL->config->get('development', 'auth') && ! $this->_TL->auth->hasLevel($this->_TL->auth->getCurrentId(), $minLevel)) {
			if ($this->_TL->config->get('on_restricted', 'auth') == 'redirect') {
				$this->_TL->load->helper('url');
				redirectTo( getURL() );
			}
			else {
				showError('Vous n\'avez pas les droits requis pour accèder à cette page.', 403);
			}
		}
	}
	
}

/* End of file AuthHook.php */
/* Location : ./apps/hooks/AuthHook.php */