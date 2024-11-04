<?php
/**
 * This service file for ULIB gateway. This file can be changed in future versions.
 * Please do not change it yourself.
 * @author Viktor Abdulov <support@reneos.com>
 * @copyright Copyright (c) 2011, game.reneos.com
 * 
 */
require_once 'UChildModel.php';
require_once 'UCommand.php';
require_once 'ListOf.php';
class UMenu extends UChildModel {
	var $commandIds;//ListOf("integer");
	var $commands;//ListOf("UCommand");
	
	
	public function __construct(){
		$this->commandIds = new ListOf("integer");
		$this->commands = new ListOf("UCommand");
	}
}
?>