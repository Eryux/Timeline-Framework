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
 * Wload Class
 */

class Wload {
	
	// List of path to load widgets from
	protected $_widget_path;
	
	// List of loaded widgets
	protected $_widgets = array();
	
	// Widget method to call
	protected $_call_method;
	
	// TimeLine Instance
	private $_TL;
	
	// --------------------------------------
	
	/**
	 * Constructor
	 * @return void
	 */
	public function __construct()
	{
		$this->_TL =& getInstance();
		$this->_TL->config->load('widget', 'widget');
		
		// Load widget core
		require_once SYSPATH . 'core/Widget.php';
		
		// Load and set settings
		$this->_widget_path = $this->_TL->config->get('path', 'widget');
		$this->_call_method = $this->_TL->config->get('callMethod', 'widget');
		
		// Load helper
		$this->_TL->load->helper('widget');
	}
	
	/**
	 * Load widget
	 * 
	 * Load widget and return result.
	 * Designed to be called from application controllers or helpers used in views.
	 * 
	 * @param string $widget Widget name
	 * @param array $params Optional parameters to pass to the widget class constructor
	 * @return string
	 */
	public function load($widget, $params = array())
	{
		// Separate widget name and subdir
		list($widget, $subdir) = $this->_widgetInfo($widget);
		
		// Check if we don't loaded it before
		if (key_exists($widget, $this->_widgets)) {
			return $this->_callWidget($widget, $params);
		}
		
		// Let's load requested widget file
		$filepath = $this->_widget_path . $subdir . $widget . '.php';
		
		if ( ! file_exists($filepath)) {
			showError("Widget '" . $widget . "' not found in directory : " . $this->_widget_path . $subdir);
		}
		
		require_once $filepath;
		
		// Check if widget class exists
		if ( !class_exists($widget)) {
			showError("Class '" . $widget . "' not exists in file : " . $filepath);
		}
		
		// Instanciate widget class
		$objWidget = new $widget;
		
		if ( ! ($objWidget instanceof Widget)) {
			showError("Class '" . $widget . "' is not an instance of Widget.");
		}
		
		// Finally add widget to list and call it
		$this->_widgets[$widget] = $objWidget;
		return $this->_callWidget($widget, $params);
	}
	
	// --------------------------------------
	
	/**
	 * Call widget default method for a loaded widget
	 * 
	 * @param string $widget Widget name
	 * @param array $params Optional parameters for widget
	 * @return string
	 */
	protected function _callWidget($widget, $params = array())
	{
		return call_user_func_array(array($this->_widgets[$widget], $this->_call_method), $params);
	}
	
	/**
	 * Separe widget name and widget sub-directory
	 * 
	 * @param string $widget Widget call string
	 * @return array
	 */
	protected function _widgetInfo($widget)
	{
		// Get the widget name, and while we're at it trim any slashes.
		// The directory path can be included as part of the widget name,
		// but we don't want a leading slash
		$widget = str_replace('.php', '', trim($widget, '/'));
		
		// We look for a slash to determine path
		if (($last_slash = strrpos($widget, '/')) !== FALSE) {
			$subdir = substr($widget, 0, ++$last_slash);
			$widget = substr($widget, $last_slash);
		}
		else {
			$subdir = '';
		}
		
		$widget = ucfirst($widget);
		
		return array($widget, $subdir);
	}
	
}

/* End of file Wload.php */
/* Location : ./system/libraries/Wload.php */