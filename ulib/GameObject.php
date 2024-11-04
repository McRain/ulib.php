<?php
/**
 * This service file for ULIB gateway. This file can be changed in future versions.
 * Please do not change it yourself.
 * @author Viktor Abdulov <support@reneos.com>
 * @copyright Copyright (c) 2011, game.reneos.com
 * 
 */
require_once 'Transform.php';
class GameObject{
	var $name = 'NewGameObject';
	var $layer = 0;
	var $active = true;
	var $tag = "Untagged";
	
	var $length = 0;
	var $data;//bytearray from file
	var $isFile = false;
	
	
	var $parameters;
	var $components;
	var $childrens;
	
	public function __construct(){
		$this->parameters = array('name'=>$this->name,'layer'=>0,'active'=>false,'tag'=>'Untagged','materialPack'=>false,'texturePack'=>false);
		$this->components = array();
		$tr = new Transform(new Vector3(0, 0, 0), new Quaternion(0, 0, 0, 0), new Vector3(1, 1, 1));
		$this->components[Components::TRANSFORM] = $tr;
		$this->childrens = array();
	}
	
	function SetFromFile($fileData){
		$this->isFile = true;
		$this->data = $fileData;
	}
	
	function LoadFromFile($fname){
		$filename = str_replace('..', '', $fname);		
		$handle = fopen($filename, "rb");
		$content = fread($handle, filesize($filename));
		fclose($handle);
		$ser = new Serializer();
		$tGo = $ser->Decode($content);
		$this->name = $tGo;//$tGo->name;
	}
}
?>