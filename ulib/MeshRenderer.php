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
require_once 'Vector4.php';
require_once 'ArrayOf.php';
require_once 'Material.php';
final class MeshRenderer extends Component{	
	var $castShadows = true;
	var $receiveShadows = true;
	//var $lightmapIndex = 1;
	//var $lightmapTilingOffset;//Vector4(0, 0, 0, 0);
	var $sharedMaterials;//ArrayOf(Material) 
	
	public function __construct(){
		//$this->lightmapTilingOffset = new Vector4(1, 1,0, 0);
		$this->sharedMaterials = new ArrayOf('Material');
	}
}
?>