<?php
/**
 * This service file for ULIB. This file can be changed in future versions.
 * @author Viktor Abdulov <support@reneos.com>
 * @copyright Copyright (c) 2011, game.reneos.com
 *
 * @version 1.0 
 *   
 */
require_once 'Vector3.php';
class PhysicMaterial{
	var $name = 'NewPhysicMaterial';
	var $dynamicFriction = 0;
	var $staticFriction = 0;
	var $bounciness = 0;
	var $dynamicFriction2 = 0;
	var $staticFriction2 = 0;
	var $frictionDirection;
	var $frictionDirection2;
	var $frictionCombine = 0 ;
	var $bounceCombine = 0;
	
	public function __construct(){
		$this->frictionDirection = new Vector3(0, 0, 0);
		$this->frictionDirection2 = new Vector3(0, 0, 0);
	}
}
?>