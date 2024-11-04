<?php
/**
 * This service file for ULIB gateway. This file can be changed in future versions.
 * Please do not change it yourself.
 * @author Viktor Abdulov <support@reneos.com>
 * @copyright Copyright (c) 2011, game.reneos.com
 * 
 */
class Vector4{
	var $x = 0.0000;
	var $y = 0.0000;
	var $z = 0.0000;
	var $w = 0.0000;
	
	public function __construct($ix,$iy,$iz,$iw){
		$this->x = $ix;
		$this->y = $iy;
		$this->z = $iz;
		$this->w = $iw;
	}
	
	function toString(){
		return $this->x.",".$this->y.",".$this->z.",".$this->w;
	}
	
	function fromString($val){
		$arr = explode(",",$val);
		$this->x = (floatval($arr[0]));
		$this->y = (floatval($arr[1]));
		$this->z = (floatval($arr[2]));
		$this->w = (floatval($arr[3]));
	}
}
?>