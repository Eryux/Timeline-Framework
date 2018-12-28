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
 * @copyright	Copyright (c) 2015, Candia Nicolas
 */

/**
 * Db Class
 */

class Db {
	
	// Database configuration
	protected $_dbconf = array();
	
	// Debug mode
	protected $_debug;
	
	// Array of instance of PDO
	protected $_pdo	= array();
	
	// Database when we do the requests
	protected $_currentDB;
	
	/**
	 * Constructor
	 */
	public function __construct() 
	{
		$TL =& getInstance();
		
		$this->_debug = $TL->config->get('debug');
		
		$TL->config->load('database');
		$this->_dbconf = $TL->config->get('db');
		
		if ( ! is_array($this->_dbconf)) {
			showError('Database configuration file isn\'t correct. Please check the manual.');
		}
		
		foreach ($this->_dbconf as $name => $info) {
			if (isset($info['autoload']) && $info['autoload']) {
				$this->load($name);
			}
		}
	}
	
	/**
	 * Load
	 * Set current database and instantiate PDO of this database if
	 * there isn't yet instance.
	 * 
	 * @param string $database
	 * @param bool $switch
	 * @param bool $persistent
	 * @return bool
	 */
	public function load($database, $switch = TRUE, $persistent = FALSE)
	{
		// If we already loaded we set has current and return TRUE
		if (isset($this->_pdo[$database])) {
			if ($switch) {
				$this->_currentDB = $database;
			}
			return TRUE;
		}
		
		if ( ! $this->_checkDBinfo($database)) {
			showError('Database ' . htmlentities($database) . ' doesn\'t exists or isn\'t correct in configuration file.');
		}
		
		// Set PDO parameters
		$pdoParams[PDO::ATTR_DEFAULT_FETCH_MODE] = PDO::FETCH_OBJ;
		
		if ($this->_debug) {
			$pdoParams[PDO::ATTR_ERRMODE] = PDO::ERRMODE_WARNING;
		} else {
			$pdoParams[PDO::ATTR_ERRMODE] = PDO::ERRMODE_SILENT;
		}
		
		// Merge params with database configuration
		if (isset($this->_dbconf[$database]['params'])) {
			$pdoParams = $this->_dbconf[$database]['params'] + $pdoParams;
		}
		
		// Set/overwrite persistent mode
		if ($persistent) {
			$pdoParams[PDO::ATTR_PERSISTENT] = TRUE;
		}
		
		try {
			$this->_pdo[$database] = new PDO($this->_dbconf[$database]['dbms'] . ':'
				. 'host=' . $this->_dbconf[$database]['host'] . ';'
				. 'dbname=' . $this->_dbconf[$database]['db'],
				$this->_dbconf[$database]['user'], $this->_dbconf[$database]['pwd'], $pdoParams
			);
			
			if ($switch) {
				$this->_currentDB = $database;
			}
		} catch (PDOException $e) {
			if ($this->_debug) {
				showError('PDO : ' . utf8_encode($e->getMessage()));
			}
		}
		
		return (isset($this->_pdo[$database]));
	}
	
	/**
	 * Close
	 * Close database connection
	 * 
	 * @param string $database
	 * @return bool
	 */
	public function close($database)
	{
		if ( ! isset($this->_pdo[$database])) {
			return FALSE;
		}
		
		unset($this->_pdo[$database]);
		return TRUE;
	}
	
	/**
	 * __get
	 * Allow to use pdo instance of database by calling directly
	 * pdo method from Db class.
	 * 
	 * @param string $name
	 * @return object
	 */
	public function __get($name) 
	{
		if (isset($this->_pdo[$name])) {
			return $this->_pdo[$name];
		}
		else if ($this->_currentDB) {
			return $this->_pdo[$this->_currentDB];
		}
		else {
			showError('Any database loaded. Please check your configuration or load database first.');
		}
	}
	
	/**
	 * _checkDBinfo
	 * Check database connection parameters before trying to connect in.
	 * 
	 * @param string $database
	 * @return bool
	 */
	protected function _checkDBinfo($database)
	{
		if ( ! isset($this->_dbconf[$database])) {
			return FALSE;
		}
		
		$requiredParams = array('dbms', 'host', 'db', 'user', 'pwd');
		
		foreach ($requiredParams as $key) {
			if ( ! array_key_exists($key, $this->_dbconf[$database])) {
				return FALSE;
			}
		}
		
		return TRUE;
	}
	
}

/* End of file Db.php */
/* Location : ./system/libraries/Db.php */