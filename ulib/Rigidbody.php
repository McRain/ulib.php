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
class Rigidbody extends Component{
	var $useGravity = false;
	var $drag = 0;
	var $angularDrag = 0;
	var $mass = 1;
	var $isKinematic = false;
	var $freezeRotation = false;
	var $constraints = 0;
	var $collisionDetectionMode = 0;
	var $interpolation = 0;
	
}
?>