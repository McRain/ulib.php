<?php
/**
 * This service file for ULIB. This file can be changed in future versions.
 * @author Viktor Abdulov <support@reneos.com>
 * @copyright Copyright (c) 2011, game.reneos.com
 *
 * @version 1.0 
 *   
 */
include 'ulib_config.php';//Read global configuration for gateway
set_include_path(get_include_path().
		PATH_SEPARATOR.U_ULIB_PATH.
		PATH_SEPARATOR.U_MANAGERS_PATH.
		PATH_SEPARATOR.U_MODULES_PATH.
		PATH_SEPARATOR.U_WORKED_PATH);//Add path to ULIB managers

if($u_enabled){//check if gateway enabled
	
	include '/UWebGateway.php';
	
	//First, check the application name in the session.
	if($g_session_allow){//read from global config : check permission to use the sessions for all applications.
		session_start();
		if(isset($_SESSION[U_APP_PARAM])){//if parameters with session name is set
			define('U_APP_NAME', $_SESSION[U_APP_PARAM]);//Declare the name of the application.
			define('U_APP_PATH', U_APPS_PATH.U_APP_NAME.'/');//Declare full path to application
		}
	}
	
	//Or Second (if application not find in the session) - look for the application name in the request.
	if(!defined('U_APP_NAME')){
		//Check if set code of application and the application is in the list of applications.
		if(isset($_REQUEST[U_APP_PARAM]) && array_key_exists($_REQUEST[U_APP_PARAM], $g_appNameKeys)){
			$appName = $g_appNameKeys[$_REQUEST[U_APP_PARAM]] ;
				define('U_APP_NAME',$appName);//Declare the name of the application.
				define('U_APP_PATH', U_APPS_PATH.U_APP_NAME.'/');//Declare full path to application
		}else{
			echo 'Application not set';
			exit;
		}
	}

	//Start worker if application exists and config find
	if(defined('U_APP_NAME')){
		define('U_USECACHE', $g_use_cash);
		UWebGateway::Work($g_appconfigfile);
	}else{
		echo 'Application not find ';
		exit;
	}
}else{
	echo 'Disabled';
	exit;
}

?>