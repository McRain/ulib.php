<?php
/**
 * This service file for ULIB gateway. This file can be changed in future versions.
 * Please do not change it yourself.
 * @author Viktor Abdulov <support@reneos.com>
 * @copyright Copyright (c) 2011, game.reneos.com
 * 
 */
class ArrayOf{
	var $typeOf = "integer";
	var $data = array();

	var $_explicitType = "";
	
	public function __construct($type){
		$this->typeOf = $type;
	}
	
	function Add($val){
		$this->data[] = $val;
	}
	
}
?>