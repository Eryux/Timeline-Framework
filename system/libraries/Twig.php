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
 * Twig Class
 */
class Twig {
	
	protected $_template_dir;
	protected $_cache_dir;
	
	private $_TL;
	private $_twig_env;
	
	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->_TL =& getInstance();
		$this->_TL->config->load('twig', 'twig');
		
		// Set include path for twig (@see http://twig.sensiolabs.org/doc/intro.html)
		ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . $this->_TL->config->get('twigPath', 'twig'));
		require_once 'Autoloader.php';
		
		// Register Autoloader
		Twig_Autoloader::register();
		
		// Init paths (to configure it, see file : application/config/twig.php)
		$this->_template_dir = APPPATH . 'views/';
		$this->_cache_dir	 = $this->_TL->config->get('cachePath', 'twig'); 
		
		// Load Twig environment
		$loader = new Twig_Loader_Filesystem($this->_template_dir, $this->_cache_dir);
		$this->_twig_env = new Twig_Environment($loader, array(
			'debug'		=>	$this->_TL->config->get('debug')
		));
	
		$this->_functionInit();
	}
	
	/**
	 * Render a twig template file (return html text (for example) but not
	 * screen it).
	 *
	 * @param string $template (contains template name)
	 * @param array $data (contains all varnames)
	 * @param boolean $render
	 */
	public function render($template, $data = array(), $render = TRUE)
	{
		$template = $this->_twig_env->loadTemplate($template);
		return ($render) ? $template->render($data):$template;
	}
	
	/**
	 * Register twig template in CodeIgniter output
	 *
	 * @param string $template
	 * @param array $data
	 */
	public function display($template, $data = array())
	{
		$handle = $this->_twig_env->loadTemplate($template);
		$this->_TL->output->appendOutput($handle->render($data));
	}
	
	/**
	 * Register new twig function in your controllers
	 *
	 * @param string $name (contains your twig function name)
	 * @param string $function (contains your function name)
	 */
	public function registerFunction($name, $function)
	{
		$this->_twig_env->addFunction(
			new Twig_SimpleFunction($name, $function)
		);
	}
	
	/**
	 * Register useful function
	 */
	public function _functionInit()
	{
		$functions = $this->_TL->config->get('functions', 'twig');
		foreach ($functions as $real => $inTwig) {
			$this->registerFunction($real, $inTwig);
		}
	}
}

/* End of file Twig.php */
/* Location : ./system/libraries/Twig.php */