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
 * Vform class
 */

class Vform {
	// Element(s) in form
	protected $_fields = array();
	
	// All form. errors after validation
	protected $_errors = array();
	
	// Fields value (for re-populating)
	protected $_postData = array();
	
	// Error prefix
	protected $_errorPrefix = '<p>';
	
	// Error suffix
	protected $_errorSuffix = '</p>';
	
	// CSRF token protection
	private $_csrfToken = 'undefined';
	
	// Debug mode
	protected $_debug = FALSE;
	
	// Framework instance
	private $_TL;

	// Constructor
	public function __construct() {
		$this->_TL =& getInstance();
		$this->_TL->lang->load('form');
		
		// Set the debug mode
		$this->_debug = $this->_TL->config->get('debug');

		// load helper
		$this->_TL->load->helper('vform');
	}
	
	// ------------------------------------------
	
	/**
	 * addRules
	 * Add new rules for field. The function also accept an array on
	 * primary parameter.
	 *
	 * @param mixed $field
	 *		Field name or array of rules
	 *
	 * @param string $label
	 *		Field's label
	 *
	 * @param mixed $rules
	 *		String or array that contains rules
	 *
	 * @param mixed $prep
	 *		String or array that contains prepping functions
	 *
	 * @return void
	 */
	public function addRules($field, $label = '', $rules = '', $prep = '') {
		// If $field is an array, we execute addRules for each case of it
		if (is_array($field)) {
			foreach ($field as $name => $data) {
				if ( ! isset($data['label']))
					$data['label'] = $name;
					
				if ( ! isset($data['rules']))
					$data['rules'] = array();
					
				if ( ! isset($data['prep']))
					$data['prep'] = array();
					
				$this->addRules($name, $data['label'], $data['rules'], $data['prep']);
			}
		}
		else {
			// If label is an empty string we set label is field name
			if ($label === '') $label = $field;
			
			// If rules is a string, we transform is at an array
			if (is_string($rules)) $rules = $this->_parse_to_array($rules);
			
			// If prepping function is a string, we transform it at an array
			if (is_string($prep)) $prep = $this->_parse_to_array($prep);
			
			// Save all in a class variable
			$this->_fields[$field] = array(
				'name' 	=> $field,
				'label' => $label,
				'rules'	=> $rules,
				'prep'	=> $prep
			);
		}
	}
	
	/**
	 * run
	 * Launch fields verifications and call prepping function. If there're
	 * errors this function return a boolean FALSE, if test is ok, it return
	 * TRUE.
	 *
	 * @param string $method
	 *		HTTP method
	 * 
	 * @return bool
	 */
	public function run($method = 'POST') {
		// CSRF Pre-verification
		if ($this->_csrfToken !== 'undefined' && ! isset($_SESSION['csrf_token']))
			return FALSE;
			
		// If request method isn't $method, we are not concern
		if ($_SERVER['REQUEST_METHOD'] != $method)
			return FALSE;
			
		foreach ($this->_fields as $field) {
			// If field isn't required and isn't exists we don't check if datas are correct
			if ( ! isset($_POST[$field['name']]) && array_search('required', $field['rules']) === FALSE)
				continue;
				
			foreach ($field['rules'] as $rule) {
				$rule = $this->_parse_to_func_array($rule);	
				
				// If the rule it's a predefined rule, we call it
				if (method_exists($this, $rule[0])) {
					$params = array_merge(array($field), $rule[1]); 				
					$result = call_user_func_array(array($this, $rule[0]), $params);
					
					if ( ! $result)
						$this->_errors[$field['name']]['rule'] = $rule[0];
				}
				else if ($this->_debug) {
					showError(sprintf('vForm : The rule %s is not a valid rule for input %s.', htmlentities($rule[0]), htmlentities($field['name'])));
					$result = FALSE;
				}
				
				// If result is FALSE, we break foreach beacause if one rule isn't respected the field is not correct
				if ($result === FALSE)
					break;
			}
		
			// Save post data is class variable (only if there is no error on field)
			if ( ! isset($this->_errors[$field['name']])) {
				$this->_postData[$field['name']] = $_POST[$field['name']];
				// Do prepping
				foreach ($field['prep'] as $prep) {
					$prep = $this->_parse_to_func_array($prep);
					$params = array_merge(array($field), $prep[1]);
					
					if (method_exists($this, $prep[0])) {
						$_POST[$field['name']] = call_user_func_array(array($this, $prep[0]), $params);
					}
					else if (function_exists($prep[0])) {
						$_POST[$field['name']] = call_user_func_array($prep[0], $prep[1]);
					}
					else if ($this->_debug) {
						showError(sprintf('vForm : The prepping function %s is not valid for input %s.', htmlentities($prep[0]), htmlentities($field['name'])));
					}
				}
			}
			
			// End of first foreach
		}
		
		// If we have any error, we return TRUE
		return (count($this->_errors) === 0);
	}
	
	/**
	 * hasError
	 * Return TRUE if have errors in form. else, return FALSE
	 *
	 * @return bool
	 */
	public function hasError() {
		return (count($this->_errors) !== 0);
	}
	
	/**
	 * getError
	 * Generate errors messages, if first param is empty, this function generate message
	 * for all errors. If is not empty, function generate message only for specified field.
	 *
	 * @param string $field
	 *		field name (optional)
	 *
	 * @return string
	 */
	public function getError($field = '') {
		// If we have no error we return an empty string
		if ( ! $this->hasError())
			return '';
			
		// Var. which will contain generated error message
		$result = '';
		
		if ($field === '') {
			foreach ($this->_errors as $field => $error) {
				$result .= $this->_errorPrefix;
				
				if ( ! isset($error['param']))
					$error['param'] = '';
				
				if ($this->_TL->lang->line('error_' . $error['rule']) === '')
					$error['rule'] = 'default';
					
				$result .= sprintf($this->_TL->lang->line('error_' . $error['rule']), htmlentities($this->_fields[$field]['label']), $error['param']);
				$result .= $this->_errorSuffix . "\n";
			}
		}
		else {
			if ( ! isset($this->_errors[$field]))
				return '';
				
			$result .= $this->_errorPrefix;
			
			if ( ! isset($this->_errors[$field]['param']))
				$this->_errors[$field]['param'] = '';
			
			if ( ! array_key_exists($this->_errors[$field]['rule'], $this->_errorText))
				$this->_errors[$field]['rule'] = 'default';
				
			$result .= sprintf($this->_errorText[$this->_errors[$field]['rule']], htmlentities($this->_fields[$field]['label']), $error['param']);
			$result .= $this->_errorSuffix . "\n";
		}
		
		return $result;
	}
	
	/**
	 * postData
	 * Re-populate field. If field doesn't exists or has an error, this
	 * function return an empty string.
	 *
	 * @param string $field
	 *		Field name
	 *
	 * @param bool $escaped
	 *		Enabled htmlentities (optional)
	 *
	 * @return string
	 */
	public function postData($field, $escaped = TRUE) {
		if ( ! isset($_POST[$field]) || isset($this->_errors[$field]))
			return '';
			
		$result = ($escaped === TRUE) ? htmlentities($_POST[$field]) : $_POST[$field];
		return $result;
	}
	
	// ------------------------------------------
	
	/**
	 * required
	 * Verify if field exists in request
	 *
	 * @param array $field
	 *		Field array
	 *
	 * @return bool
	 */
	public function required($field) {
		return (isset($_POST[$field['name']]));
	}
	
	/**
	 * notEmpty
	 * Verify if field is empty or not.
	 *
	 * @param array $field
	 *		Field array
	 *
	 * @return bool
	 */
	public function notEmpty($field) {
		return ( ! empty($_POST[$field['name']]));
	}
	
	/**
	 * matches
	 * Verify if field data match to another.
	 *
	 * @param array $field
	 *		Field array
	 *
	 * @param string $matches
	 *		Field name
	 *
	 * @return bool
	 */
	public function matches($field, $matches) {
		if ( ! isset($_POST[$matches]) || $_POST[$field['name']] !== $_POST[$matches]) {
			$this->_errors[$field['name']]['param'] = $matches;
			return FALSE;
		}
		
		return TRUE;
	}
	
	/**
	 * minLength
	 * Verify if length of data in field is upper than minimal length.
	 *
	 * @param array $field
	 *		Field array
	 *
	 * @param int $length
	 *		Minimal length
	 *
	 * @return bool
	 */
	public function minLength($field, $length) {
		$length = intval($length);
		$postData = $_POST[$field['name']];
		
		if ( ! is_string($postData))
			strval($postData);
			
		if (strlen($postData) < intval($length)) {
			$this->_errors[$field['name']]['param'] = $length;
			return FALSE;
		}
		
		return TRUE;
	}
	
	/**
	 * maxLength
	 * Verify if length of data in field is lower than maximal length.
	 *
	 * @param array $field
	 *		Field array
	 *
	 * @param int $length
	 *		Maximal length
	 *
	 * @return bool
	 */
	public function maxLength($field, $length) {
		$length = intval($length);
		$postData = $_POST[$field['name']];
		
		if ( ! is_string($postData))
			strval($postData);
			
		if (strlen($postData) > intval($length)) {
			$this->_errors[$field['name']]['param'] = $length;
			return FALSE;
		}
		
		return TRUE;
	}
	
	/**
	 * btwLength
	 * Verify if length of data in field is between minimal and maximal length.
	 *
	 * @param array $field
	 *		Field array
	 *
	 * @param int $minLength
	 *		Minimal length
	 *
	 * @param int $maxLength
	 *		Maximal length
	 *
	 * @return bool
	 */
	public function btwLength($field, $_minLength, $_maxLength) {
		$_minLength = intval($_minLength);
		$_maxLength = intval($_maxLength);
		$postData = $_POST[$field['name']];
		
		if ( ! is_string($postData))
			strval($postData);
			
		if ( ! $this->minLength($field, $_minLength) || ! $this->maxLength($field, $_maxLength)) {
			return FALSE;
		}
		
		return TRUE;
	}
	
	/**
	 * exactLength
	 * Verify if length of data in field is equal to the specified length.
	 *
	 * @param array $field
	 *		Field array
	 *
	 * @param int $length
	 *		Length
	 *
	 * @return bool
	 */
	public function exactLength($field, $length) {
		$length = intval($length);
		$postData = $_POST[$field['name']];
		
		if ( ! is_string($postData))
			strval($postData);
			
		if (strlen($postData) != intval($length)) {
			$this->_errors[$field['name']]['param'] = $length;
			return FALSE;
		}
		
		return TRUE;
	}
	
	/**
	 * isNumeric
	 * Verify if data in field is numeric.
	 *
	 * @param array $field
	 *		Field array
	 *
	 * @return bool
	 */
	public function isNumeric($field) {
		return (is_numeric($_POST[$field['name']]));
	}
	
	/**
	 * isInt
	 * Verify if data in field is an integer.
	 *
	 * @param array $field
	 *		Field array
	 *
	 * @return bool
	 */
	public function isInt($field) {
		if ( ! $this->isNumeric($field))
			return FALSE;
			
		$in_numeric = floatval($_POST[$field['name']]);
		return (intval($in_numeric) == $in_numeric);
	}
	
	/**
	 * gt
	 * Verify if data in field is greater than specified value.
	 *
	 * @param array $field
	 *		Field array
	 *
	 * @param numeric $value
	 *		Value
	 *
	 * @return bool
	 */
	public function gt($field, $value) {
		if ( ! $this->isNumeric($field))
			return FALSE;
			
		$postData = floatval($_POST[$field['name']]);
		if ($postData <= floatval($value)) {
			$this->_errors[$field['name']]['param'] = $value;
			return FALSE;
		}
		
		return TRUE;
	}
	
	/**
	 * lt
	 * Verify if data in field is lower than value.
	 *
	 * @param array $field
	 *		Field array
	 *
	 * @param numeric $value
	 *		Value
	 *
	 * @return bool
	 */
	public function lt($field, $value) {
		if ( ! $this->isNumeric($field))
			return FALSE;
			
		$postData = floatval($_POST[$field['name']]);
		if ($postData >= floatval($value)) {
			$this->_errors[$field['name']]['param'] = $value;
			return FALSE;
		}
		
		return TRUE;
	}
	
	/**
	 * alpha
	 * Verify if data in field contain only alphabetic characters.
	 *
	 * @param array $field
	 *		Field array
	 *
	 * @return bool
	 */
	public function alpha($field) {
		return (preg_match('/^[a-zA-Z]+$/', $_POST[$field['name']]));
	}
	
	/**
	 * alphaNum
	 * Verify if data in field contains only alpha and numeric characters.
	 *
	 * @param array $field
	 *		Field array
	 *
	 * @return bool
	 */
	public function alphaNum($field) {
		return (preg_match('/^[a-zA-Z0-9]+$/', $_POST[$field['name']]));
	}
	
	/**
	 * alphaExt
	 * Verify if data in field contains only alpha, numeric and some others char.
	 *
	 * @param array $field
	 *		Field array
	 *
	 * @return bool
	 */
	public function alphaExt($field) {
		return (preg_match('/^[a-zA-Z0-9-, _]+$/', $_POST[$field['name']]));
	}
	
	/**
	 * check
	 * Verify if data in field correspond at a specified regexp.
	 *
	 * @param array $field
	 *		Field array
	 *
	 * @param string $exp
	 *		Regular expression
	 *
	 * @return bool
	 */
	public function check($field, $exp) {
		return (preg_match($exp, $_POST[$field['name']]));
	}
	
	/**
	 * email
	 * Verify if data in field is an email.
	 *
	 * @param array $field 
	 *		Field array
	 *
	 * @return	bool
	 */
	public function email($field) {
		return (preg_match('/^[a-zA-Z0-9-, _.]+@[a-zA-Z0-9-, _.]+$/', $_POST[$field['name']]));
	}
	
	/**
	 * csrfCheck
	 * Check if data in field correspond at csrf token.
	 *
	 * @param array $field
	 *		Field array
	 *
	 * @param string $token
	 *
	 * @return bool
	 */
	public function csrfCheck($field, $token) {
		return ($token === $_POST[$field['name']]);
	}
	
	/**
	 * setErrorPrefix
	 *
	 * @param string $prefix
	 * @return	void
	 */
	public function setErrorPrefix($prefix) {
		$this->_errorPrefix = $prefix;
	}
	
	/**
	 * setErrorSuffix
	 *
	 * @param string $suffix
	 * @return	void
	 */
	public function setErrorSuffix($suffix) {
		$this->_errorSuffix = $suffix;
	}
	
	/**
	 * csrf
	 * Enable CSR security and generate a token.
	 *
	 * @param string $input
	 *		Name of input string that contains csrf token
	 *
	 * @param string $page
	 *		Name of page of csrf
	 *
	 * @param int $expire
	 *		Time (in second) before token expire (optional)
	 *
	 * @return void
	 */
	public function csrf($input, $page = '', $expire = 600) {
		// If CSRF Token exists, we add rules
		if (isset($_SESSION['csrf_token'])) {
			if (isset($_SESSION['csrf_page']) && $_SESSION['csrf_page'] === $page) {
				if (isset($_SESSION['csrf_time']) && $_SESSION['csrf_time'] >= (time() - $expire))
					$this->addRules($input, $input, 'required|notEmpty|csrfCheck[' . $_SESSION['csrf_token'] . ']');
				else 
					$this->_errors[$input]['rule'] = 'csrfCheck';
			}
		}
		
		// Generate new csrf token
		$this->_csrfToken = md5(base64_encode(rand()));
		$_SESSION['csrf_token'] = $this->_csrfToken;
		$_SESSION['csrf_page'] = $page;
		$_SESSION['csrf_time'] = time();
	}
	
	/**
	 * getToken
	 * @return string
	 */
	public function getToken() {
		return $this->_csrfToken;
	}
	
	/**
	 * getField
	 *
	 * @param string $name
	 *		Field name
	 *
	 * @return array
	 */
	public function getField($name) {
		if (isset($this->_fields[$name]))
			return $this->_fields[$name];
		else
			return array();
	}
	
	/**
	 * _parse_string
	 * Parse rules (or prep) string to an array of rules (or prep)
	 *
	 * @param string $in
	 *
	 * @return array
	 */
	public function _parse_to_array($in) {
		$string = str_replace(' ', '', trim($in));
		
		if ( ! empty($string)) {
			return (array) explode('|', $string);
		}
		else {
			return array();
		}
	}
	
	/**
	 * _parse_to_func_array
	 * Parse rule (or prep) string to an array.
	 *
	 * @param string $in
	 *
	 * @return array
	 */
	public function _parse_to_func_array($in) {
		if (preg_match('/^(.+)\[(.+)\]$/', $in, $matches)) {
			$rule = $matches[1];
			$params = ($matches[2] != '') ? explode(',', str_replace(', ', '', trim($matches[2]))) : array();
		}
		else {
			$rule = $in;
			$params = array();
		}
		
		return array($rule, $params);
	}
	
}

/* End of file Vform.php */
/* Location : ./system/libraries/Vform.php */
