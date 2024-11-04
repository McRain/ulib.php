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
class Projector extends Component{
	var $material  = null;
	var $nearClipPlane = 0.1;
	var $farClipPlane=50;
	var $fieldOfView=30;
	var $aspectRatio=1;
	var $orthographic=false;
	var $orthographicSize=2;
	var $ignoreLayers=0;
}
?>