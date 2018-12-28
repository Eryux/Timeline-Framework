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
 * Lang Class
 */
 
class Lang {

	// Localization directory
	private $langDir;
	
	// List of loaded localization files
	private $isLoaded = array();
	
	// List of translation
	private $translation = array();
	
	// Current localization
	private $currentLang;
	
	// Constructor
	public function __construct() {
		$config =& getConfig();
		$this->langDir = APPPATH . 'langs/';
		
		$this->setLang($config['localization']);
		$this->load('system');
	}
	
	// ------------------------------------------
	
	/**
	 * load
	 * Load localization file. Localization file must be in lang directory.
	 * 
	 * @param string $file
	 *		Name of localization file that will be loaded (without .php)
	 *
	 * @param string $language
	 *		Language name (english, french ...). If any language name is
	 *		specified we take the $this->currentLang. (optional)
	 *
	 * @return bool
	 */
	public function load($file, $language = '') {
		$filename = clearPath($file) . '_lang.php';
		$language = ($language === '') ? $this->currentLang : clearPath($language);
		
		if (file_exists($this->langDir . $language . '/' . $filename)) {
			include($this->langDir . $language . '/' . $filename);
			
			if ( ! isset($lang) || ! is_array($lang))
				return FALSE;
			
			$this->isLoaded[$language][] = $file;
			$this->translation = array_merge($this->translation, $lang);
			return TRUE;
		}
		else {
			// showError('Unable to load the requested language file : ' . $this->langDir . $language . '/' . $filename);
			return FALSE;
		}
	}
	
	/**
	 * setLang
	 * Change the current language.
	 *
	 * @param string $localization
	 *		New current localization
	 *
	 * @return bool
	 */
	public function setLang($localization) {
		$localization = clearPath($localization);
		
		if ( ! is_dir($this->langDir . $localization))
			return FALSE;
			
		// If localization exists, we try to load new languages files
		if (isset($this->isLoaded[$this->currentLang])) {
			foreach ($file as $this->isLoaded[$this->currentLang]) {
				$this->load($file, $localization);
			}
		}
		
		$this->currentLang = $localization;
	}
	
	/**
	 * line
	 * Fetch a single line of text from translation list.
	 *
	 * @param string $line
	 *		Line that you want to fetch.
	 *
	 * @return string
	 */
	public function line($line) {
		$out = (isset($this->translation[$line])) ? $this->translation[$line] : '';
		return $out;
	}
	
}

/* End of file Lang.php */
/* Location : ./system/libraries/Lang.php */