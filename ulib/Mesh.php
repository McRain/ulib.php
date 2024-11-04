<?php
/**
 * This service file for ULIB gateway. This file can be changed in future versions.
 * Please do not change it yourself.
 * @author Viktor Abdulov <support@reneos.com>
 * @copyright Copyright (c) 2011, game.reneos.com
 * 
 */
require_once 'ArrayOf.php';
require_once 'Vector3.php';
require_once 'Vector2.php';
class Mesh{
	var $length = 0;
	var $data;//bytearray from file
	var $isFile = false;
	var $name = "mesh";
	var $vertices;//of Vector3
	var $uv;//of Vector2
	var $triangles;//of int
	
	public function __construct(){
		$this->vertices = new ArrayOf('Vector3');
		$this->uv = new ArrayOf('Vector2');
		$this->triangles = new ArrayOf('integer');
	}
	
	function SetFromData($fileData){
		$this->isFile = true;
		$this->data = $fileData;
	}
	
	function LoadFromFile($fname){
		$filename = str_replace('..', '', $fname);		
		/*if(!is_file($filename) || !file_exists($filename)){
			$this->data = null;
			$this->isFile = false;
		}else {*/
			$handle = fopen($filename, "rb");
			$content = fread($handle, filesize($filename));
			fclose($handle);
			$serializer = new Serializer();
			$tMesh = $serializer->Decode($content);
			$this->name = $tMesh->name;
			$this->vertices = $tMesh->vertices;
			$this->uv = $tMesh->uv;
			$this->triangles = $tMesh->triangles;
			
		//}
	}
	
	function CreateNew($name){
		$this->name = $name;
	}
	
	function AddVertex($vert){
		if($this->vertices==NULL)
			$this->vertices = new ArrayOf("Vector3"); 
		$this->vertices[] = $vert;
	}
	
	function AddUv($uvNew){
		if($this->uv==NULL)
			$this->uv = new ArrayOf("Vector2"); 
		$this->uv[] = $uvNew;
	}
	
	function AddTriangle($trNew){
		if($this->triangles==NULL)
			$this->triangles = new ArrayOf("integer"); 
		$this->triangles[] = $uvNew;
	}
	
	function CreatePrimitive($primitive){
		if($primitive==MeshPrimitive::CUBE){
			$this->vertices->data = array(
					new Vector3(0.5, -0.5, 0.5),new Vector3(-0.5, -0.5, 0.5),new Vector3(0.5, 0.5, 0.5),new Vector3(-0.5, 0.5, 0.5),
					new Vector3(0.5, 0.5, -0.5),new Vector3(-0.5, 0.5, -0.5),new Vector3(0.5, -0.5, -0.5),new Vector3(-0.5, -0.5, -0.5),
					new Vector3(0.5, 0.5, 0.5),new Vector3(-0.5, 0.5, 0.5),new Vector3(0.5, 0.5, -0.5),new Vector3(-0.5, 0.5, -0.5),
					new Vector3(0.5, -0.5, -0.5),new Vector3(-0.5, -0.5, 0.5),new Vector3(-0.5, -0.5, -0.5),new Vector3(0.5, -0.5, 0.5),
					new Vector3(-0.5, -0.5, 0.5),new Vector3(-0.5, 0.5, -0.5),new Vector3(-0.5, -0.5, -0.5),new Vector3(-0.5, 0.5, 0.5),
					new Vector3(0.5, -0.5, -0.5),new Vector3(0.5, 0.5, 0.5),new Vector3(0.5, -0.5, 0.5),new Vector3(0.5, 0.5, -0.5)
			);
			$this->uv->data = array(
					new Vector2(0.0, 0.0),new Vector2(1.0, 0.0),new Vector2(0.0, 1.0),new Vector2(1.0, 1.0),
					new Vector2(0.0, 1.0),new Vector2(1.0, 1.0),new Vector2(0.0, 1.0),new Vector2(1.0, 1.0),
					new Vector2(0.0, 0.0),new Vector2(1.0, 0.0),new Vector2(0.0, 0.0),new Vector2(1.0, 0.0),
					new Vector2(0.0, 0.0),new Vector2(1.0, 1.0),new Vector2(1.0, 0.0),new Vector2(0.0, 1.0),
					new Vector2(0.0, 0.0),new Vector2(1.0, 1.0),new Vector2(1.0, 0.0),new Vector2(0.0, 1.0),
					new Vector2(0.0, 0.0),new Vector2(1.0, 1.0),new Vector2(1.0, 0.0),new Vector2(0.0, 1.0));
			$this->triangles->data = array(1,0,3,3,0,2,9,8,5,5,8,4,11,10,7,7,10,6,14,12,13,13,12,15,18,16,17,17,16,19,22,20,21,21,20,23);  
		}
		else if($primitive==MeshPrimitive::PLANE){
			$this->vertices->data = array(
					new Vector3(0.5, -0.5, 0.5),new Vector3(-0.5, -0.5, 0.5),new Vector3(0.5, 0.5, 0.5),new Vector3(-0.5, 0.5, 0.5),
					new Vector3(0.5, 0.5, -0.5),new Vector3(-0.5, 0.5, -0.5),new Vector3(0.5, -0.5, -0.5),new Vector3(-0.5, -0.5, -0.5),
					new Vector3(0.5, 0.5, 0.5),new Vector3(-0.5, 0.5, 0.5),new Vector3(0.5, 0.5, -0.5),new Vector3(-0.5, 0.5, -0.5),
					new Vector3(0.5, -0.5, -0.5),new Vector3(-0.5, -0.5, 0.5),new Vector3(-0.5, -0.5, -0.5),new Vector3(0.5, -0.5, 0.5),
					new Vector3(-0.5, -0.5, 0.5),new Vector3(-0.5, 0.5, -0.5),new Vector3(-0.5, -0.5, -0.5),new Vector3(-0.5, 0.5, 0.5),
					new Vector3(0.5, -0.5, -0.5),new Vector3(0.5, 0.5, 0.5),new Vector3(0.5, -0.5, 0.5),new Vector3(0.5, 0.5, -0.5)
			);
			$this->uv->data = array(
					new Vector2(0.0, 0.0),new Vector2(1.0, 0.0),new Vector2(0.0, 1.0),new Vector2(1.0, 1.0),
					new Vector2(0.0, 1.0),new Vector2(1.0, 1.0),new Vector2(0.0, 1.0),new Vector2(1.0, 1.0),
					new Vector2(0.0, 0.0),new Vector2(1.0, 0.0),new Vector2(0.0, 0.0),new Vector2(1.0, 0.0),
					new Vector2(0.0, 0.0),new Vector2(1.0, 1.0),new Vector2(1.0, 0.0),new Vector2(0.0, 1.0),
					new Vector2(0.0, 0.0),new Vector2(1.0, 1.0),new Vector2(1.0, 0.0),new Vector2(0.0, 1.0),
					new Vector2(0.0, 0.0),new Vector2(1.0, 1.0),new Vector2(1.0, 0.0),new Vector2(0.0, 1.0));
			$this->triangles->data = array(1,0,3,3,0,2,9,8,5,5,8,4,11,10,7,7,10,6,14,12,13,13,12,15,18,16,17,17,16,19,22,20,21,21,20,23); 
		}else if($primitive==MeshPrimitive::SPRITE){
			$this->vertices->data = array(new Vector3(5,5,0),new Vector3(-5,5,0),new Vector3(-5,-5,0),new Vector3(5,-5,0));
			$this->uv->data = array(new Vector2(1.0, 1.0),new Vector2(0.0, 1.0),new Vector2(0.0, 0.0),new Vector2(1.0, 0.0));
			$this->triangles->data = array(3,2,0,0,2,1);
		}
	}
}

class MeshPrimitive{
	const CUBE ='CUBE'; 
	const PLANE ='PLANE';
	const SPRITE = 'SPRITE';
}

?>