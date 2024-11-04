<?php
/**
 * This service file for ULIB/UWeb. This file can be changed in future versions.
 * @author Viktor Abdulov <support@reneos.com>
 * @copyright Copyright (c) 2011, game.reneos.com
 *
 * @version 1.0
 * 
 * Please do not change structure it yourself. 
 * You can change the values ​​of the parameters used in this file.
 *
 * This is parameters for current application only (Folder where is this file)
 */



/********************************************
 * 				VARIABLES					*
********************************************/
$u_enabled = true;
$g_session_allow = true;
$g_use_cash = false;

$g_targetKey = 't';
$g_methodKey = 'm';

$g_open_var = '_ufunctions';
$g_open_vars = true;
$g_open_list = true;

if(!defined('U_APP_PARAM'))
	define('U_APP_PARAM', 'u_app_param');

$g_appNameKeys = array('UjjsdkUlkjsdfnsdk33jhnsdfj'=>'0');

$g_appconfigfile = false;

$u_modules = array('UWebDatabase'=>array('ulib','dblogin','dbpass'));

//$u_apps = array(''=>'');


/********************************************
 * 				DIRS						*
********************************************/
if(!defined('U_CLASS_DIR')){
	define('U_CLASS_DIR', 'classes');
	define('U_CONTENT_DIR', 'contents');
	define('U_MODULES_DIR', 'modules');//uweb
	define('U_WORKED_DIR', 'uweb');//uweb
	define('U_APPS_DIR', 'apps');//uweb
}


/********************************************
 * 				PATHS						*
********************************************/
if(!defined('U_ROOT_PATH')){
	define('U_ROOT_PATH', dirname(__FILE__).'/');//uweb
	define('U_ULIB_PATH', U_ROOT_PATH.'ulib/');
	define('U_MANAGERS_PATH', U_ULIB_PATH.'managers/');
	define('U_MODULES_PATH', U_ROOT_PATH.U_MODULES_DIR.'/');//uweb
	define('U_WORKED_PATH', U_ROOT_PATH.U_WORKED_DIR.'/');//uweb
	define('U_APPS_PATH',U_ROOT_PATH.U_APPS_DIR.'/');//uweb
}

?>