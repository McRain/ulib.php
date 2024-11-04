<?php
/**
 * This service file for ULIB gateway. This file can be changed in future versions.
 * Please do not change it yourself.
 * @author Viktor Abdulov <support@reneos.com>
 * @copyright Copyright (c) 2011, game.reneos.com
 * 
 */
class Dictionary{
	var $keyOf = "string";
	var $valueOf = "integer";
	var $data = array();
	
	var $_explicitType = "";
	
	public function __construct($keyType,$valueType){
		$this->keyOf = $keyType;
		$this->valueOf = $valueType;
	}
	
	function Add($key,$item){
		$this->data[$key] = $item;
	}
}
?>