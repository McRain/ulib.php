<?php
/**
 * This service file for ULIB gateway. This file can be changed in future versions.
 * Please do not change it yourself.
 * @author Viktor Abdulov <support@reneos.com>
 * @copyright Copyright (c) 2011, game.reneos.com
 * 
 */
class LanguageManager{
	
	static function LoadTranslation($parameters){
		require_once 'Dictionary.php';
		$result = array();
		$section= '';
		if(isset($parameters['section']))
			$section = $parameters['section'];
		$lang = $parameters['lang'];
		if(is_integer($lang)){
			require_once 'GatewayManager.php';
			$languages = GatewayManager::get('languages');
			$lang = $languages[(integer)$lang];
		}	
		$result['lang'] = $lang;
		$result['section'] = $section;
		$list = new Dictionary('string','string');
		$filename = U_APPS_PATH.U_APP_NAME.'/'.U_CONTENT_DIR.'/'.U_LANG_DIR.'/'.$lang.U_LANG_EXT;
		if(file_exists($filename) && is_file($filename)){
			$translate_array = self::ParseTranslation($filename);
			if($section!=''){
				foreach ($translate_array as $tKey=>$tVal) {
					if($section==$tKey){
						foreach ($tVal as $key=>$val) {
							$list->Add((string)trim($key),(string)trim($val));
						}
					}
				}
			}else{
				foreach ($translate_array as $tKey=>$tVal) {
					if(is_array($tVal)){
						foreach ($tVal as $key=>$val) {
							$list->Add((string)trim($key),(string)trim($val));
						}
					}else
						$list->Add((string)trim($tKey),(string)trim($tVal));
				}
			}
		}
		$result['result'] = $list;
		return $result;
	}
	
	private static function ParseTranslation($filename) { 
    	$head = ""; 
    	$handle = @fopen($filename, "r") or 
    		die('Cannot open INI file'); 
    	$contents = @fread($handle, filesize($filename)) or 
    		die('Cannot read INI file'); 
    	$contents = explode("\n", trim($contents)); 
    	foreach ($contents as $k => $v) 
        	if ((substr(trim($v), 0, 1) != ";") && (substr(trim($v), 0, 1) != "#")) { 
            	if (substr(trim($v), 0, 1) == "[") { 
                	$head = substr(trim($v), 1, -1); 
            	} elseif ((trim($v) <> "") and (substr($v, 0, 1) <> "#")) { 
                	$tmp = explode("=", $v); 
                	$ini_file[$head][trim($tmp[0])] = trim($tmp[1]); 
            	} 
        	} 
    		@fclose($handle); 
    	return $ini_file; 
	} 
	
	static function LoadList(){
		require_once 'Dictionary.php';
		require_once 'GatewayManager.php';
		$result = new Dictionary('string', 'string');
		foreach (GatewayManager::get('languages') as $langp=>$lang){
			$result->Add($langp,$lang);
		}
		return $result;
	}	
	
	static function LoadTexture($parameter){
		require_once 'Texture2D.php';
		$parameter['value'] = new Texture2D();
		$parameter['value']->LoadFromPng(U_APPS_PATH.U_APP_NAME.'/'.U_CONTENT_DIR.'/'.U_LANG_DIR.'/'.$parameter['key']);
		return $parameter;
	}
	
	static function LoadAudoClip($parameter){
		require_once 'Texture2D.php';
		$parameter['value'] = new Texture2D();
		$parameter['value']->LoadFromPng(U_APPS_PATH.U_APP_NAME.'/'.U_CONTENT_DIR.'/'.U_LANG_DIR.'/'.$parameter['key']);
		return $parameter;
	}
}

?>