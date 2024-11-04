<?php
/**
 * This service file for ULIB gateway. This file can be changed in future versions.
 * Please do not change it yourself.
 * @author Viktor Abdulov <support@reneos.com>
 * @copyright Copyright (c) 2011, game.reneos.com
 * 
 */
class ListOf{
	var $typeOf = "integer";
	var $data = array();

	public function __construct($type){
		$this->typeOf = $type;
	}
	
	public function Add($val){
		$this->data[] = $val;
	}
	
}
?>