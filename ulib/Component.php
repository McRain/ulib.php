<?php
/**
 * This service file for ULIB. This file can be changed in future versions.
 * @author Viktor Abdulov <support@reneos.com>
 * @copyright Copyright (c) 2011, game.reneos.com
 *
 * @version 1.0 
 *   
 */
abstract class Component{
	var $parameters;
	
	function GetClassName(){
		return 'UnityEngine.'.get_class($this);
	}
}

class Components{
	const TRANSFORM = 'UnityEngine.Transform';
	const MESHFILTER = 'UnityEngine.MeshFilter';
	const MESHRENDERER = 'UnityEngine.MeshRenderer';
	const RIGIDBODY = 'UnityEngine.Rigidbody';
	const COLLIDER = 'UnityEngine.Collider';
	const CAMERA = 'UnityEngine.Camera';
	const PROJECTOR = 'UnityEngine.Projector';
	const LIGHT = 'UnityEngine.Light';
	const PARTICLEEMITTER = 'UnityEngine.ParticleEmitter';
}

?>