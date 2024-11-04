<?php
/**
 * This service file for ULIB gateway. This file can be changed in future versions.
 * Please do not change it yourself.
 * @author Viktor Abdulov <support@reneos.com>
 * @copyright Copyright (c) 2011, game.reneos.com
 * 
 */
class Rect{
	
	var $x = 0;
	var $y = 0;
	var $width = 0;
	var $height = 0;
	
	public function __construct($ix,$iy,$iw,$ih){
		$this->x = $ix;
		$this->y = $iy;
		$this->width = $iw;
		$this->height = $ih;
	}
	
	function fromString($val){
		$arr = explode(",",$val);
		$this->x = (floatval($arr[0]));
		$this->y = (floatval($arr[1]));
		$this->width = (floatval($arr[2]));
		$this->height = (floatval($arr[3]));
	}
	
	function toString(){
		return $this->x.",".$this->y.",".$this->width.",".$this->height;
	}
}
?>