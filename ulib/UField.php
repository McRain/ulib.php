<?php
/**
 * This service file for ULIB gateway. This file can be changed in future versions.
 * Please do not change it yourself.
 * @author Viktor Abdulov <support@reneos.com>
 * @copyright Copyright (c) 2011, game.reneos.com
 * 
 */
class UField{
	var $name = "";
	var $value;	
	
	public function __construct($names,$val){
		$this->name = $names;
		$this->value = $val;
	}
}
?>