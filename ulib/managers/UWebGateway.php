<?php
class UWebGateway{
	
		/**
	 * 
	 * @param boolean $g_appconfigfile - use application config ?
	 */
	static function Work($g_appconfigfile){
		if($g_appconfigfile)
			include U_APP_PATH.'app_config.php';
		else {
			include 'ulib_config.php';
			$u_modules = array();
		}
		if($u_enabled){//application is enabled?
			if($g_session_allow){//if global aparameters not rewrited from config for current application
				if(session_id()=='')//if session not started
					session_start();
				$_SESSION[U_APP_PARAM] = U_APP_NAME;//store name of application???????????
			}
			self::SendHeaders();
			set_include_path(get_include_path().PATH_SEPARATOR.U_APP_PATH.PATH_SEPARATOR.U_APP_PATH.U_CLASS_DIR);
		
			//---------------------------Start work
			self::LoadModules($u_modules);
			$targetName = str_replace('..', '', $_REQUEST[$g_targetKey]);
			$methodName = $_REQUEST[$g_methodKey];
			require_once $targetName.'.php';//Load target class
		
			$param = NULL;
			$targetClass = new $targetName;
			$targetObject = new $targetClass;
		
			//Check if method open for call through the gateway
			$allow = false;
			$openFunctionName = $targetName.$g_open_var;
		
			if($g_open_list){
				if(isset($$openFunctionName) && in_array($methodName, $$openFunctionName)){
					$allow = true;
				}
			}
			if($g_open_vars){
				$vars = get_class_vars($targetName);
				if(array_key_exists($openFunctionName, $vars) && in_array($methodName, $vars[$openFunctionName])){
					$allow = true;
				}
			}
		
			if(!$allow){
				echo 'ACCESS DENIED: '.$targetName.'.'.$methodName.' NOT IN ACCES LIST.';
				exit;
			}
			
			//Deserialize data and call method
			if(method_exists($targetObject,$methodName) && is_callable(array($targetObject, $methodName))){
				require_once 'Serializer.php';
				$serializer = new Serializer();
						
				if(isset($_FILES['data'])){
					$fls = $_FILES['data'];
					$fileContent = file_get_contents($_FILES['data']['tmp_name']);
					$param = $serializer->Decode($fileContent);
				}
				echo $serializer->Encode($targetObject->$methodName($param));				
				exit;
			}else{//ON METHOD NOT EXIST OR IS NOT CALLABLE
				echo 'Method Not Exists:'.$targetName.'.'.$methodName;
				exit;
			}
			//------------------------End work
		}else{
			echo 'Disabled';
			exit;
		}
	}
	
	static function SendHeaders(){
		header('P3P: CP="NOI ADM DEV PSAi COM NAV OUR OTRo STP IND DEM"');
		header('Accept-Ranges: bytes');
	}
	
	static function LoadModules($mods){
		foreach ($mods as $modFileName=>$modParameters) {
			$file = $modFileName.'.php';
			//if(!is_dir($file) && pathinfo($file, PATHINFO_EXTENSION)=='php'){
				include $file;
				call_user_func(array($modFileName,'ModuleLoad'),$modParameters);
			//}
		}
	}	
}
?>