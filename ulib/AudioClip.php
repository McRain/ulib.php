<?php
/**
 * This service file for ULIB gateway. This file can be changed in future versions.
 * Please do not change it yourself.
 * @author Viktor Abdulov <support@reneos.com>
 * @copyright Copyright (c) 2011, game.reneos.com
 * 
 */
require_once 'UnityResource.php';
class AudioClip extends UnityResource{
	var $data;//bytearray from file
	
	//var $filterMode = 0;
	//var $mipMapBias = 0; 
	
	function GetResource($val){
		$this->LoadFromFile($val);
		//header('Content-Type:audio/mpeg;');
		header('Content-Length:'.strlen($this->data).';');
		header('Accept-Ranges:bytes');
		header('Connection:Keep-Alive');
		header('Content-Type:audio/ogg');
		
	}
		
	function LoadFromFile($fname){
		$filename = str_replace('..', '', $fname);		
		$handle = fopen($filename, "rb");
		$this->data = fread($handle, filesize($filename));
		fclose($handle);
	}
}