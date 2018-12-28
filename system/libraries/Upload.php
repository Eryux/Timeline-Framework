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
 * Upload Class
 */

class Upload {
	
	// Upload directory
	protected $_uploadPath   = '';
	
	// Allowed mime type
	protected $_allowedTypes = array();
	
	// File name
	protected $_dstName;
	
	// Max size
	protected $_maxSize;
	
	// Max filename length
	protected $_maxFilename;
	
	// Encrypt file name after upload
	protected $_encryptName = FALSE;
	
	// Contains errors
	protected $_errors		= array();
	
	// Contains success uploading
	protected $_uploads		= array();
	
	// Framework instance
	private $_TL;
	
	// Constants for error (extending common upload error)
	const UPLOAD_ERR_BAD_NAME = 9;
	const UPLOAD_ERR_BAD_TYPE = 10;
	const UPLOAD_ERR_MOVE	  = 11;
	
	/**
	 * Constructor
	 */
	public function __construct($params = array())
	{
		$this->_TL =& getInstance();
		
		// Load localization
		if ($this->_TL->load->isLoaded('lang')) {
			$this->_TL->load->lang('lang');
		}
		
		$this->_TL->lang->load('upload');
		
		// Set parameters
		$this->setParams($params);
	}
	
	/**
	 * doUpload
	 * Check if are no errors and make upload.
	 * 
	 * @param type $field
	 * @return boolean
	 */
	public function doUpload($field)
	{
		if ( ! isset($_FILES[$field])) {
			return FALSE;
		}
		
		// Check has error (if yes set error class param)
		if ($_FILES[$field]['error'] != UPLOAD_ERR_OK) {
			$this->_setError($field, $_FILES[$field]['error']);
			return FALSE;
		}
		
		// Check file name
		if ( ! $this->_checkName($field)) {
			$this->_setError($field, self::UPLOAD_ERR_BAD_NAME);
			return FALSE;
		}
		
		// Check size
		if ($_FILES[$field]['size'] > $this->_maxSize * 1024) {
			$this->_setError($field, UPLOAD_ERR_FORM_SIZE);
			return FALSE;
		}
		
		// Check types
		if ( ! $this->_checkType($field)) {
			$this->_setError($field, self::UPLOAD_ERR_BAD_TYPE);
			return FALSE;
		}
		
		// Move file
		if ( ! $this->_moveFile($field)) {
			$this->_setError($field, self::UPLOAD_ERR_MOVE);
			return FALSE;
		}
		
		return TRUE;
	}
	
	/**
	 * Return file path after upload
	 * 
	 * @param type $field
	 * @return string
	 */
	public function getFilename($field) {
		if ( ! isset($this->_uploads[$field])) {
			return '';
		}
		else {
			return $this->_uploads[$field];
		}
	}
	
	/**
	 * Return error
	 * 
	 * @param type $field
	 * @return boolean
	 */
	public function getError($field) {
		if ( ! isset($this->_errors[$field])) {
			return FALSE;
		}
		else {
			return $this->_errors[$field];
		}
	}
	
	/**
	 * Set params of upload library
	 * 
	 * @param type $params
	 */
	public function setParams($params) {
		// Set upload path
		 if (isset($params['uploadPath'])) {
			$this->_uploadPath = $params['uploadPath'];
		 }
		
		// Set allowed types
		if (isset($params['allowed'])) {
			$this->_allowedTypes = explode('|', $params['allowed']);
		}
		
		// Set encryption name
		if (isset($params['encryptName'])) {
			$this->_encryptName = $params['encryptName'];
		}
		
		// Set destination name
		if (isset($params['filename'])) {
			$this->_dstName = $params['filename'];
		}
		
		// Set max size (ko)
		if (isset($params['maxSize'])) {
			$this->_maxSize = $params['maxSize'];
		}
		
		// Set max length of file name
		if (isset($params['maxFilename'])) {
			$this->_maxFilename = $params['maxFilename'];
		}
	}
	
	// ------------------------------------------
	
	protected function _setError($field, $error)
	{
		$filepath = clearPath($_FILES[$field]['tmp_name']);
		if (file_exists($filepath)) {
			unlink($filepath);
		}
		
		switch ($error) {
			case UPLOAD_ERR_INI_SIZE:
			case UPLOAD_ERR_FORM_SIZE:
				$this->_errors[$field] = $this->_TL->lang->line('upload_err_size');
				break;
			case UPLOAD_ERR_PARTIAL:
				$this->_errors[$field] = $this->_TL->lang->line('upload_err_part');
				break;
			case UPLOAD_ERR_NO_FILE:
				$this->_errors[$field] = $this->_TL->lang->line('upload_err_nofi');
				break;
			case UPLOAD_ERR_NO_TMP_DIR:
				$this->_errors[$field] = $this->_TL->lang->line('upload_err_tmp');
				break;
			case UPLOAD_ERR_CANT_WRITE:
				$this->_errors[$field] = $this->_TL->lang->line('upload_err_perm');
				break;
			case UPLOAD_ERR_EXTENSION:
				$this->_errors[$field] = $this->_TL->lang->line('upload_err_ext');
				break;
			case self::UPLOAD_ERR_BAD_NAME:
				$this->_errors[$field] = $this->_TL->lang->line('upload_err_name');
				break;
			case self::UPLOAD_ERR_BAD_TYPE:
				$this->_errors[$field] = $this->_TL->lang->line('upload_err_type');
				break;
			case self::UPLOAD_ERR_MOVE:
				$this->_errors[$field] = $this->_TL->lang->line('upload_err_move');
				break;
			default:
				$this->_errors[$field] = $this->_TL->lang->line('upload_err');
		}
	}


	protected function _checkName($field)
	{
		if ($this->_maxFilename) {
			return (strlen($_FILES[$field]['name']) <= $this->_maxFilename &&
				! preg_match('/(\.\.\/)+|(\x00)+/', $_FILES[$field]['name']));
		}
		else {
			return ( ! preg_match('/(\.\.\/)+|(\x00)+/', $_FILES[$field]['name']));
		}
	}


	protected function _checkType($field) 
	{
		if (empty($this->_allowedTypes)) {
			return TRUE;
		}
		
		if (class_exists('finfo')) {
			$finfo = new finfo(FILEINFO_MIME_TYPE);
			$mimeType = $finfo->file($_FILES[$field]['tmp_name']);

			return in_array($mimeType, $this->_allowedTypes);
		}
		
		return TRUE;
	}
	
	protected function _moveFile($field)
	{
		if ($this->_dstName) {
			$filepath = $this->_uploadPath . '/' . $this->_dstName;
		}
		else if ($this->_encryptName) {
			$ext = $extension_upload = strtolower(substr(strrchr($_FILES[$field]['name'], '.'), 1));
			$filepath = $this->_uploadPath . '/' . sha1(uniqid(rand()) . $_FILES[$field]['name']) . '.' . $ext;
		}
		else {
			$filepath = $this->_uploadPath . '/' . clearPath($_FILES[$field]['name']);
		}
		
		if (move_uploaded_file($_FILES[$field]['tmp_name'], $filepath)) {
			$this->_uploads[$field] = $filepath;
			return TRUE;
		}
		else {
			return FALSE;
		}
	}
	
}

/* End of file Upload.php */
/* Location : ./system/libraries/Upload.php */
