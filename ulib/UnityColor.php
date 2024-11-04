<?php
/**
 * This service file for ULIB gateway. This file can be changed in future versions.
 * Please do not change it yourself.
 * @author Viktor Abdulov <support@reneos.com>
 * @copyright Copyright (c) 2011, game.reneos.com
 * 
 */
class UnityColor{
	var $r = 0.0000;
	var $g = 0.0000;
	var $b = 0.0000;
	var $a = 1.0000;
	
	public function __construct($ir,$ig,$ib,$ia){
		$this->r = $ir;
		$this->g = $ig;
		$this->b = $ib;
		$this->a = $ia;
	}
	
	function toString(){
		return $this->r.",".$this->g.",".$this->b.",".$this->a;
	}
	
	function toStringRGB(){
		return $this->r.",".$this->g.",".$this->b;
	}
	
	function fromString($val){
		$arr = explode(",",$val);
		$this->r = (floatval($arr[0]));
		$this->g = (floatval($arr[1]));
		$this->b = (floatval($arr[2]));
		$this->a = (floatval($arr[3]));
	}
	
	function fromStringRGB($val){
		$arr = explode(",",$val);
		$this->r = (floatval($arr[0]));
		$this->g = (floatval($arr[1]));
		$this->b = (floatval($arr[2]));
		$this->a = 1.0;
		return $this;
		
	}
	
	function fromHex($color) {
    	$this->r=hexdec($color[0].$color[1]);
    	$this->g=hexdec($color[2].$color[3]);
    	$this->b=hexdec($color[4].$color[5]);
	}
	
	function toHex(){
		
	}
	
	function toHexString(){
    	//return bin2hex(pack("CCC",$this->r*255,$this->g*255,$this->b*255));
    	return sprintf ('%02x%02x%02x', (integer)$this->r, (integer)$this->g, (integer)$this->b);
	}
}?>