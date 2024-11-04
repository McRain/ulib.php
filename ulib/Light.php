<?php
/**
 * This service file for ULIB. This file can be changed in future versions.
 * @author Viktor Abdulov <support@reneos.com>
 * @copyright Copyright (c) 2011, game.reneos.com
 *
 * @version 1.0 
 *   
 */
require_once 'Component.php';
require_once 'UnityColor.php';
class Light extends Component{
	var $type=2;//0-Spot;1=Directional;2-Point
	var $color;
	var $intensity=1;
	var $shadows=0;
	var $shadowStrength=1;
	var $shadowBias=0.05;
	var $shadowSoftness=4;
	var $shadowSoftnessFade=1;
	var $range=10;
	var $spotAngle=30;
	var $renderMode=0;
	var $cullingMask=1;
	
	public function __construct($lightColor){
		if($lightColor==null)
			$lightColor = new UnityColor(1, 1, 1, 1);
		$this->color = $lightColor;
	}
}
?>