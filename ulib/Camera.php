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
require_once 'Rect.php';
require_once 'UnityColor.php';
class Camera extends Component{
	var $clearFlags = 1;
	var $backgroundColor;
	var $cullingMask = 1;
	var $orthographic = false;
	var $orthographicSize = 100;
	var $fov = 60;
	var $near = 0.1;
	var $far = 1000;
	var $rect;
	var $depth = 1;
	var $renderingPath = -1;
	
	public function __construct(){
		$this->backgroundColor = new UnityColor(0, 0, 0, 1);
		$this->rect = new Rect(0, 0, 1, 1);
	}
}
?>