<?php
/**
 * This service file for ULIB gateway. This file can be changed in future versions.
 * Please do not change it yourself.
 * @author Viktor Abdulov <support@reneos.com>
 * @copyright Copyright (c) 2011, game.reneos.com
 * 
 */
class Vector3{
	var $x = 0;
	var $y = 0;
	var $z = 0;
	
	public function __construct($ix,$iy,$iz){
		$this->x = $ix;
		$this->y = $iy;
		$this->z = $iz;
	}
	
	function toString(){
		return $this->x.",".$this->y.",".$this->z;
	}
	
	function fromString($val){
		$arr = explode(',',$val);
		$this->x = (floatval($arr[0]));
		$this->y = (floatval($arr[1]));
		$this->z = (floatval($arr[2]));
	}
	
	static function ParseString($val){
		$arr = explode(',',$val);
		return new Vector3(floatval($arr[0]), floatval($arr[1]), floatval($arr[2]));
	}
}
?>