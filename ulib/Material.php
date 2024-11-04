<?php
/**
 * This service file for ULIB. This file can be changed in future versions.
 * @author Viktor Abdulov <support@reneos.com>
 * @copyright Copyright (c) 2011, game.reneos.com
 *
 * @version 1.0 
 *   
 */
require_once 'UnityColor.php';
class Material{
	var $name = 'NewMaterial';
	var $shader = 'Diffuse';
	var $parameters=array('shader'=>'Diffuse','name'=>'NewMaterial');
	
	function SetColor($colorName,$color){
		$this->parameters[$colorName] = $color;
	}
	
	function GetColor($colorName){
		if(array_key_exists($colorName, $this->parameters))
			return $this->parameters[$colorName];
		return null;
	}
}
?>