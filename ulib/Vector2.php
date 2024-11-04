<?php
/**
 * This service file for ULIB gateway. This file can be changed in future versions.
 * Please do not change it yourself.
 * @author Viktor Abdulov <support@reneos.com>
 * @copyright Copyright (c) 2011, game.reneos.com
 * 
 */
class Vector2{
	var $x = 0;
	var $y = 0;
	
	public function __construct($ix,$iy){
		$this->x = $ix;
		$this->y = $iy;
	}
	
	function toString(){
		return $this->x.",".$this->y;
	}
	
	function fromString($val){
		$arr = explode(",",$val);
		$this->x = (floatval($arr[0]));
		$this->y = (floatval($arr[1]));
	}
}
?>