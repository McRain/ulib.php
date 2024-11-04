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
require_once 'Vector3.php';
require_once 'Quaternion.php';
class Transform extends Component{
	
	var $localPosition;
	var $localRotation;
	var $localScale;
	
	public function __construct($position,$rotation,$scale){
		$this->localPosition = $position;
		$this->localRotation = $rotation;
		$this->localScale = $scale;
	}
}
?>