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
require_once 'Mesh.php';
require_once 'PhysicMaterial.php';
class Collider extends Component{
	//var $class = 'UnityEngine.BoxCollider';
	var $isTrigger = false;
	var $sharedMaterial = null;
	
	function GetClassName(){
		return 'UnityEngine.Collider';
	}
}

class BoxCollider extends Collider{
	var $class = 'UnityEngine.BoxCollider';
	var $center;
	var $size;
	
	public function __construct(){
		$this->center = new Vector3(0, 0, 0);
		$this->size =  new Vector3(1, 1, 1);
	}
	
	function GetValues(){
		return array('class'=>$this->class,
					'sharedMaterial'=>$this->sharedMaterial,
					'isTrigger'=>$this->isTrigger,
					'center'=>$this->center,
					'size'=>$this->size);
	}
}

class SphereCollider extends Collider{
	var $class = 'UnityEngine.SphereCollider';
	var $center;
	var $radius;
	
	public function __construct(){
		$this->center = new Vector3(0, 0, 0);
		$this->radius =  0.5;
	}
	
	function GetValues(){
		return array('class'=>$this->class,
					'sharedMaterial'=>$this->sharedMaterial,
					'isTrigger'=>$this->isTrigger,
					'center'=>$this->center,
					'radius'=>$this->radius);
	}
}

class CapsuleCollider extends Collider{
	var $class = 'UnityEngine.CapsuleCollider';
	var $radius = 0.5;
	var $height = 2;
	var $direction = 1;
	var $center;
	
	public function __construct(){
		$this->center = new Vector3(0, 0, 0);
	}
	
	function GetValues(){
		return array('class'=>$this->class,
					'sharedMaterial'=>$this->sharedMaterial,
					'isTrigger'=>$this->isTrigger,
					'center'=>$this->center,
					'radius'=>$this->radius,
					'height'=>$this->height,
					'direction'=>$this->direction,);
	}
}

class MeshCollider extends Collider{
	var $class = 'UnityEngine.MeshCollider';
	var $smoothSphereCollisions = false;
	var $convex = false;
	var $sharedMesh;
	
	public function __construct($mesh){
		$this->sharedMesh = $mesh;		
	}
	
	function GetValues(){
		$result = array('class'=>$this->class,
					'sharedMaterial'=>$this->sharedMaterial,
					'isTrigger'=>$this->isTrigger,
					'smoothSphereCollisions'=>$this->smoothSphereCollisions,
					'convex'=>$this->convex);
		if($this->sharedMesh!=null)
			$result['sharedMesh'] = $this->sharedMesh;
		return $result;
	}
}

?>