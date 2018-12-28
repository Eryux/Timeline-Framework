<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Twig library path
 * Relative link of twig library from your index.php
 * @default $config['twigPath'] = SYSPATH . 'vendors/Twig/lib/Twig'
 */
$config['twigPath'] = SYSPATH . 'vendors/Twig/lib/Twig';

/**
 * Cache directory
 * Folder where cache files are saved
 * @default $config['cachePath'] = APPPATH . 'caches/'
 */
$config['cachePath'] = APPPATH . 'caches/';


/**
 * Extend twig function
 * Associative array of function name that you want to
 * use in twig. Key is real name of our function and value
 * is name of twig function.
 */
$config['functions'] = array(
	'getURL'		   => 'getURL',
	'redirectTo'	   => 'redirectTo',
	'widgetLoad'	   => 'widgetLoad',
	'hasErrorForm'	   => 'hasErrorForm',
	'getErrorForm'	   => 'getErrorForm',
	'getPostData'	   => 'getPostData',
	'getUploadError'   => 'getUploadError',
	'getSessionItem'   => 'getSessionItem',
	'getSessionFlash'  => 'getSessionFlash',
	'lang'			   => 'lang',
	'isLogin'		   => 'isLogin',
	'getCurrentAuthId' => 'getCurrentAuthId',
	'hasAuthLevel'	   => 'hasAuthLevel',
	'getAccessLevel'   => 'getAccessLevel',
);

/* End of file twig.php */
/* Location : ./apps/configs/twig.php */