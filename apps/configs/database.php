<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * To connect to a database with DB library you need
 * precise connection information in this file.
 *
 * You can record more than one database information by using
 * a key like : $config['db'][key] ...
 *
 * Parameters you need to precise for connection :
 * 
 * -> dbms : type of database (mysql, pgsql ...)
 * -> host : ip address or hostname of database
 * -> db   : name of database that you want to connect
 * -> user : user for connection
 * -> pwd  : password for connection
 * 
 * Library also accept additionals parameters :
 * 
 * -> autoload : set it true if you wan't load database when db library is loaded
 * -> params   : array that contains PDO parameters @see http://php.net/manual/fr/pdo.setattribute.php
 */
 
$config['db']['default'] = array(
	'dbms'	   => 'mysql',
	'host'	   => 'localhost',
	'db'	   => 'timeline',
	'user'	   => 'root',
	'pwd'	   => 'root',
	'autoload' => TRUE,
	'params'   => array(),
);

/* End of file database.php */
/* Location : ./apps/configs/database.php */