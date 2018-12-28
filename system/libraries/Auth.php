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
 * Auth Class
 */

class Auth {
	
	// PDO Instance
	protected $_pdo;
	
	// User table settings
	protected $_userTable;
	
	// Group table settings
	protected $_groupTable;
	
	// Access table settings
	protected $_accessTable;
	
	// Access level of pages
	protected $_accessLevel = array();
	
	// Data of users
	protected $_usersLevel  = array();
	
	// Id of current user
	protected $_currentUser;

	// Framework instance
	private $_TL;
	
	/**
	 * Constructor
	 */
	public function __construct() 
	{
		$this->_TL =& getInstance();
		$this->_TL->config->load('auth', 'auth');
		
		// Set table settings
		$this->_userTable   = $this->_TL->config->get('user', 'auth');
		$this->_groupTable  = $this->_TL->config->get('group', 'auth');
		$this->_accessTable = $this->_TL->config->get('access', 'auth');
		
		if ( ! $this->_TL->load->isLoaded('session')) {
			$this->_TL->load->library('session');
		}
		
		if ( ! $this->_TL->load->isLoaded('db')) {
			$this->_TL->load->library('db');
		}
		
		$this->_TL->load->helper('auth');
		
		// Instantiate PDO
		$database = $this->_TL->config->get('database', 'auth');
		
		$this->_TL->db->load($database);
		$this->_pdo = $this->_TL->db->$database;
		
		// Initialize
		$this->isAuth();
	}
	
	/**
	 * isAuth
	 * Check if current client is connected.
	 * 
	 * @return bool
	 */
	public function isAuth()
	{
		if ($this->_currentUser === NULL) {
			if ($this->_TL->session->getItem('auth') === '') {
				return FALSE;
			}

			if ( ! $this->hasUser($this->_TL->session->getItem('auth'))) {
				$this->_TL->session->delItem('auth');
				return FALSE;
			}

			$this->_currentUser = intval($this->_TL->session->getItem('auth'));
		}
		
		return TRUE;
	}
	
	/**
	 * hasUser
	 * Check if user id exists in database.
	 * 
	 * @param int $uid Id of user that you want to check
	 * @return bool
	 */
	public function hasUser($uid)
	{
		if (isset($this->_userData[$uid])) {
			return TRUE;
		}
		
		return ($this->_queryHasUser($uid));
	}
	
	/**
	 * auth
	 * Authentify user with username and password.
	 * 
	 * @param string $username
	 * @param string $password
	 * @return bool
	 */
	public function auth($username, $password)
	{
		if ($this->isAuth()) {
			return FALSE;
		}
		
		$request = $this->_queryGetUser('name', $username);
		if ( ! empty($request) && $request->password === $this->hashPassword($password, $username)) {
			$this->_TL->session->setItem('auth', $request->id);
			$this->_TL->session->setItem('username', $request->name);
				
			$this->_currentUser = $request->id;
			return TRUE;
		}
		
		return FALSE;
	}
	
	/**
	 * Disconnect user
	 * 
	 * @return bool
	 */
	public function unAuth()
	{
		if ( ! $this->isAuth()) {
			return FALSE;
		}
		
		$this->_TL->session->delItem('auth');
		$this->_TL->session->delItem('user');
		
		return TRUE;
	}
	
	/**
	 * getCurrentId
	 * Return id of current connected user. If user is not connected
	 * we return 0.
	 * 
	 * @return int
	 */
	public function getCurrentId()
	{
		if ( ! $this->_currentUser) {
			return 0;
		} else {
			return $this->_currentUser;
		}
	}
	
	/**
	 * getUserLevel
	 * Get level of user
	 * 
	 * @param int $uid Id of user that you wan't to get level
	 * @return int
	 */
	public function getUserLevel($uid)
	{
		if (isset($this->_usersLevel[$uid])) {
			return $this->_usersLevel[$uid];
		}
		
		$userData = $this->_queryGetUser('id', $uid, TRUE);
		
		if ( ! empty($userData)) {
			if ( ! $userData->herited || $userData->gid === NULL) {
				$this->_usersLevel[$uid] = $userData->level;
			} else {
				$this->_usersLevel[$uid] = $userData->groupLevel;
			}
			
			return $this->_usersLevel[$uid];
		}
		else {
			return 0;
		}
	}
	
	/**
	 * hasLevel
	 * Verify if a user has required level
	 * 
	 * @param int $uid User id
	 * @param int $level Minimun level that user must have
	 * @return bool
	 */
	public function hasLevel($uid, $level)
	{
		return ($this->getUserLevel($uid) >= $level);
	}
	
	/**
	 * getAccessLevel
	 * Get minimun access level for a page
	 * 
	 * @param string $controller
	 * @param string $method
	 * @return int
	 */
	public function getAccessLevel($controller, $method = '')
	{
		$concat = ($method === '') ? $controller : $controller . '@' . $method;
		
		if (isset($this->_accessLevel[$concat])) {
			return $this->_accessLevel[$concat];
		}
		
		$request = $this->_queryGetAccess($concat, $controller);
		if ( ! empty($request)) {
			// The most precise win
			foreach ($request as $data) {
				$this->_accessLevel[$concat] = $data->level;
				
				if ($data->path == $concat) {
					break;
				}
			}
		}
		else {
			$this->_accessLevel[$concat] = $this->_TL->config->get('default_access', 'auth');
		}
		
		return $this->_accessLevel[$concat];
	}
	
	/**
	 * hashPassword
	 * Hash string for password database entry
	 * @see http://php.net/manual/fr/function.hash.php
     * @see http://php.net/manual/fr/function.hash-algos.php
	 * 
	 * @param string $in
	 * @param string $salt
	 * @return string
	 */
	public function hashPassword($in, $salt = '')
	{
		$secretKey = $this->_TL->config->get('secretKey');
		
		$prep = '';
        if ($salt != '') {
            for ($i = 0, $j = 0, $z = 0; $i < strlen($in); $i++, $j++, $z++) {
                if ($j >= strlen($salt)) {
                    $j = 0;
                }
                if ($z >= strlen($secretKey)) {
                    $z = 0;
                }
                
                $prep .= chr( ord($in[$i]) + ord($salt[$j]) - ord($secretKey[$z]) );
            }
        } else {
            for ($i = 0, $z = 0; $i < strlen($in); $i++, $z++) {
                if ($z >= strlen($secretKey)) {
                    $z = 0;
                }
                
                $prep .= chr( ord($in[$i]) + ord($secretKey[$z]) );
            }
        }
        
        $prep = base64_encode($prep);
		return hash($this->_TL->config->get('password_hash', 'auth'), $prep);
	}
	
	/**
	 * groupPrepDelete
	 * Prepare group from deleting
	 * 
	 * @param int $gid
	 * @param int $replacement Id of replacement group, if null is on_group_delete
	 * @return bool
	 */
	public function groupPrepDelete($gid, $replacement = 'undefined')
	{
		if ($replacement === 'undefined') {
			$replacement = $this->_TL->config->get('on_group_delete', 'auth');
		}
		
		if ($replacement === 'default') {
			$newGroup = $this->_TL->config->get('default_group', 'auth');
		} else {
			$newGroup = $replacement;
		}
		
		$request = $this->_pdo->prepare(
				'UPDATE ' . $this->_userTable['table'] . ' SET ' . $this->_userTable['gid']
				. ' = ? WHERE ' . $this->_userTable['gid'] . ' = ?'
		);
		
		return $request->execute(array($newGroup, $gid));
	}
	
	/**
	 * getUser
	 * Public alias of _queryGetUser
	 * 
	 * @param string $column
	 * @param mixed $value
	 * @param bool $group if is true we join group
	 * @return object
	 */
	public function getUser($column, $value, $group = FALSE)
	{
		return $this->_queryGetUser($column, $value, $group);
	}
	
	
	/**
	 * userTouch
	 * Update user after he login
	 * 
	 * @access protected
	 * @param object $userData
	 * @return bool
	 */
	public function userTouch($userData)
	{
		if ( ! isset($this->_userTable['currentConnect']) && 
				! isset($this->_userTable['currentIp'])) {
			return FALSE;
		}
		
		$params = array('id' => $userData->id);
		$query  = 'UPDATE ' . $this->_userTable['table'] . ' SET ';
		
		if (isset($this->_userTable['currentConnect'])) {
			$query .= $this->_userTable['currentConnect'] . ' = NOW(), ';
			
			if (isset($this->_userTable['lastConnect'])) {
				$query .= $this->_userTable['lastConnect'] . ' = :lastConnect, ';
				$params['lastConnect'] = $userData->currentConnect;
			}
		}
		
		if (isset($this->_userTable['currentIp'])) {
			$query .= $this->_userTable['currentIp'] . ' = :currentIp, ';
			$params['currentIp'] = $_SERVER['REMOTE_ADDR'];
			
			if (isset($this->_userTable['lastIp'])) {
				$query .= $this->_userTable['lastIp'] . ' = :lastIp, ';
				$params['lastIp'] = $userData->currentIp;
			}
		}
		
		$query = substr($query, 0, -2);
		
		$query .= ' WHERE ' . $this->_userTable['id'] . ' = :id';
		$request = $this->_pdo->prepare($query);
		return $request->execute($params);
	}
	
	/**
	 * _queryHasUser
	 * Query that return if has user
	 * 
	 * @access protected
	 * @param int $uid
	 * @return bool
	 */
	protected function _queryHasUser($uid)
	{
		$request = $this->_pdo->prepare(
			'SELECT COUNT(*) FROM ' . $this->_userTable['table'] . ' WHERE '
			. $this->_userTable['id'] . ' = ?'
		);
		
		$request->execute(array($uid));
		return ($request->fetchColumn() > 0);
	}
	
	/**
	 * _queryGetUser
	 * Query that return user data from database with where filter
	 * 
	 * @access protected
	 * @param string $column
	 * @param mixed $value
	 * @param bool $group if is true we join group
	 * @return object
	 */
	protected function _queryGetUser($column, $value, $group = FALSE)
	{
		if ( ! isset($this->_userTable[$column])) {
			return FALSE;
		}
		
		$query = 'SELECT ' . $this->_userTable['id'] . ' as `id`, '
				. $this->_userTable['name'] . ' as `name`, '
				. $this->_userTable['password'] . ' as `password`, '
				. $this->_userTable['table'] . '.' . $this->_userTable['level'] . ' as `level`, '
				. $this->_userTable['herited'] . ' as `herited`, ';
		
		if (isset($this->_userTable['currentConnect'])) {
			$query .= $this->_userTable['currentConnect'] . ' as `currentConnect`, ';
			
			if (isset($this->_userTable['lastConnect'])) {
				$query .= $this->_userTable['lastConnect'] . ' as `lastConnect`, ';
			}
		}
		
		if (isset($this->_userTable['currentIp'])) {
			$query .= $this->_userTable['currentIp'] . ' as `currentIp`, ';
			
			if (isset($this->_userTable['lastIp'])) {
				$query .= $this->_userTable['lastIp'] . ' as `lastIp`, ';
			}
		}
		
		if ($group) {
			$query .= $this->_groupTable['table'] . '.' . $this->_groupTable['id'] . ' as `gid`, '
					. $this->_groupTable['table'] . '.' . $this->_groupTable['name'] . ' as `groupName`, '
					. $this->_groupTable['table'] . '.' . $this->_groupTable['level'] . ' as `groupLevel`, ';
		}
		else {
			$query .= $this->_userTable['gid'] . ' as `gid`, ';
		}
		
		$query = substr($query, 0, -2);
		$query .= ' FROM ' . $this->_userTable['table'];
		
		if ($group) {
			$query .= ' LEFT JOIN ' . $this->_groupTable['table'] . ' ON '
					. $this->_userTable['table'] . '.' . $this->_userTable['gid'] . ' = '
					. $this->_groupTable['table'] . '.' . $this->_groupTable['id'];
		}
		
		$query .= ' WHERE ' . $this->_userTable[$column] . ' = ?';
		
		$request = $this->_pdo->prepare($query);
		$request->execute(array($value));
		
		return $request->fetch(PDO::FETCH_OBJ);
	}
	
	/**
	 * _queryGetAccess
	 * Query that return access for a page
	 * 
	 * @access protected
	 * @param string $fullname
	 * @param string $controller
	 * @return object
	 */
	protected function _queryGetAccess($fullname, $controller = '')
	{
		$params[] = $fullname;
		
		$query = 'SELECT ' . $this->_accessTable['level'] . ' as `level`, '
				. $this->_accessTable['path'] . ' as `path` FROM ' . $this->_accessTable['table']
				. ' WHERE `path` = ?';
	   
		if ($controller !== '' && $fullname !== $controller) {
			$query .= ' OR `path` = ?';
			$params[] = $controller;
			
		}
		
		$request = $this->_pdo->prepare($query);
		$request->execute($params);
		
		return $request->fetchAll(PDO::FETCH_OBJ);
	}

}

/* End of file Auth.php */
/* Location : ./system/librairies/Auth.php */