<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Auth library require database with specifics tables.
 * 
 * Tables required :
 * 
 * Users [
 *		#user_id:int,
 *		~group_id:int,
 *		username:varchar,
 *		password:varchar,
 *		level:int,
 *		level_herited:boolean
 *		current_connect:datetime (optional)
 *		current_ip:varchar (optional)
 *		last_connect (optional, require current_connect)
 *		last_ip (optional, require current_ip)
 * ]
 * 
 * Groups [
 *		#group_id:int,
 *		name:varchar,
 *		level:int
 * ]
 * 
 * access [
 *		#path:varchar,
 *		required_level:int
 * ]
 * 
 * It is the minimal requirements, you can add another fields to tables 
 * for your application. You can change name of field but don't forget
 * to update the configuration below with new fields names.
 */

/**
 * Database to use in configs/database.php
 * @default $config['database'] = 'default'
 */

$config['database'] = 'default';

/**
 * Database user configuration
 */

$config['user']['table']	= 'users';
$config['user']['id']	    = 'uid';
$config['user']['gid']	    = 'gid';
$config['user']['name']	    = 'username';
$config['user']['password'] = 'password';
$config['user']['level']	= 'level';
$config['user']['herited']  = 'level_herited';

// $config['user']['currentConnect'] = 'current_connect';
// $config['user']['currentIp']	     = 'current_ip';
// $config['user']['lastConnect']	 = 'last_connect';
// $config['user']['lastIp']		 = 'last_ip';

/**
 * Database group configuration
 */

$config['group']['table'] = 'groups';
$config['group']['id']	  = 'gid';
$config['group']['name']  = 'name';
$config['group']['level'] = 'level';

/**
 * Database access configuration
 */

$config['access']['table'] = 'access';
$config['access']['path']  = 'path';
$config['access']['level'] = 'required_level';

/**
 * Password encryption in database
 * To hash password we use hash function of PHP
 * @see http://php.net/manual/en/function.hash.php
 * 
 * You can choose what algorithm we use to hash between
 * algorithm of function hash_algos return.
 * 
 * @default $config['password_hash'] = 'sha256'
 */

$config['password_hash'] = 'sha256';

/**
 * Default group
 * @default $config['default_group'] = NULL
 */

$config['default_group'] = NULL;

/**
 * Default page level
 * What level we set if page isn't found in access table.
 * 
 * @default $config['default_access'] = 0
 */

$config['default_access'] = 0;

/**
 * On group delete
 * When you delete group what do of user in this group ?
 * By default we set user group to null but you also can set user
 * group to default_group (if is specified) or to another group. 
 * (you need to specifed group id)
 * 
 * @example $config['on_group_delete'] = 'default'
 * @example $config['on_group_delete'] = 1
 * 
 * @default $config['on_group_delete'] = NULL
 */

$config['on_group_delete'] = NULL;

/**
 * Development environment
 * Set to TRUE to bypass any auth. Is usefull when you develop your
 * application but don't forget to disable in production.
 * 
 * @default $config['development'] = FALSE
 */

$config['development'] = FALSE;

/**
 * On restricted area
 * Action we do when user not has level to access at page.
 * 
 * error = show 403 error
 * redirect = redirect to index with 403 in reponse header
 * 
 * @default $config['on_restricted'] = 'error';
 */
$config['on_restricted'] = 'error';

/* End of file auth.php */
/* Location : ./apps/configs/auth.php */