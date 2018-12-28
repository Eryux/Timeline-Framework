<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/* ----------------------------------------------
 * Routing
 * ----------------------------------------------
 */

/**
 * controllerParam
 * GET parameter name that has controller to load.
 * @default $config['controllerParam'] = 'm'
 */
	$config['controllerParam'] = 'm';
	
/**
 * methodParam
 * GET parameter name that has method to load in controller.
 * @default $config['methodParam'] = 'a'
 */
	$config['methodParam'] = 'a';
	
/**
 * defaultController
 * If any controller is specified we take the defaultController.
 * @default $config['defaultController'] = 'home'
 */
	$config['defaultController'] = 'home';
	
/**
 * defaultMethod
 * If any method is specified we take the defaultMethod.
 * @default $config['defaultMethod'] = 'index'
 */
	$config['defaultMethod'] = 'index';
	
/* ----------------------------------------------
 * Localization
 * ----------------------------------------------
 */
 
/**
 * localization
 * Default localization file for the system.
 * @default $config['localization'] = 'english'
 */
	$config['localization'] = 'french';
	
/* ----------------------------------------------
 * Security
 * ----------------------------------------------
 */

 /**
  * secretKey
  * It's unique key used for encryption or hashes salt.
  */
	$config['secretKey'] = '!#ùsPa78&sp:?)è/89a1';
	
/* ----------------------------------------------
 * Session
 * ----------------------------------------------
 */
 
 /**
  * sessionCookie
  * It's the name of cookie that contains session id.
  * @default $config['sessionCookie'] = 'PHPSESSID'
  */
	$config['sessionCookie'] = 'PHPSESSID';
	
 /**
  * sessionLifetime
  * It's time in second before session expiring.
  * @default $config['sessionExpire'] = 600
  */
	$config['sessionExpire'] = 600;
	
 /**
  * sessionFlash
  * Key name for array that contains flash session.
  * @default $config['sessionFlash'] = '_flash'
  */
	$config['sessionFlash'] = '_flash';
  
/* ----------------------------------------------
 * Environment
 * ----------------------------------------------
 */
 
/**
 * debug
 * If debug is on some additionals errors are printed.
 * @default $config['debug'] = FALSE
 */
	$config['debug'] = TRUE;
	
/* End of file settings.php */
/* Location : ./apps/configs/settings.php */