<?php
/**
 * This service file for ULIB. This file can be changed in future versions.
 * @author Viktor Abdulov <support@reneos.com>
 * @copyright Copyright (c) 2011, game.reneos.com
 *
 * @version 1.0 
 *   
 */
require_once 'ULabelModel.php';
class UModel extends ULabelModel{
	var $target="";
	var $member="";
	var $parameters;//ArrayOf("object");
	var $value;//object
}