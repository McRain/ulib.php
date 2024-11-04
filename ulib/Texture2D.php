<?php
/**
 * This service file for ULIB gateway. This file can be changed in future versions.
 * Please do not change it yourself.
 * @author Viktor Abdulov <support@reneos.com>
 * @copyright Copyright (c) 2011, game.reneos.com
 * 
 */
require_once 'UnityResource.php';
class Texture2D extends UnityResource{
	var $width = 1;
	var $height = 1;
	var $wrapMode = 0;
	var $name = "texture"; 
	var $data;//bytearray from file
	var $format = 2;
	
	//var $filterMode = 0;
	//var $mipMapBias = 0; 
	
	function GetResource($val){
		$this->LoadFromPNG($val);
	}
		
	function LoadFromPNG($fname){
		$filename = str_replace('..', '', $fname);
		$filename = str_replace('.png', '', $filename).'.png';
		if(!is_file($filename) || !file_exists($filename)){
			$this->data = null;
			echo $filename;
		}else {
			//$this->name = $fname;
			list($w, $h) = getimagesize($filename);
			$this->width = $w;
			$this->height = $h;
			$this->data = file_get_contents($filename);
		}
	}
	
	function SetData($pngData,$w,$h){
		$this->data = $pngData;
		$this->width = $w;
		$this->height = $h;
	}
}
?>