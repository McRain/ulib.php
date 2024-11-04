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
final class MeshFilter extends Component{	
	var $sharedMesh;
	
	public function __construct($newMesh){
		$this->sharedMesh = $newMesh;
	}
	
} 
?>