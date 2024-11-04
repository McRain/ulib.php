<?php
/**
 * This service file for ULIB gateway. This file can be changed in future versions.
 * Please do not change it yourself.
 * @author Viktor Abdulov <support@reneos.com>
 * @copyright Copyright (c) 2011, game.reneos.com
 * 
 */
require_once 'UField.php';
class Serializer{
	
	const Version = 4;
	
	static $utypes = array("string"=>"System.String",
							"integer"=>"System.Int32",
							"UnityColor"=>"UnityEngine.Color",
							"object"=>"System.Object",
							"Vector2"=>"UnityEngine.Vector2",
							"Vector3"=>"UnityEngine.Vector3",
							"Vector4"=>"UnityEngine.Vector4",
							"Texture2D"=>"UnityEngine.Texture2D",
							"ArrayOf"=>"System.Object",
							"ListOf"=>"System.Object");
	
	private static $_objectPack;
	private static $_bytesPack;
	private static $_objectBytesPack;
	private static	$_bytePackCount=0; 
	
	public static $_shaderPropertys = array('_Color'=>'Color','_SpecColor'=>'Color',
											'_Emission'=>'Color','_SpecularColor'=>'Color',
											'_HighlightColor'=>'Color','_Shininess'=>'Float',
											'_ReflectColor'=>'Color','_MainTex'=>'Texture',
											'_ShadowTex'=>'Texture','_FalloffTex'=>'Texture'); 
	private static $_externalObjectEncoders = array();
	private static $_externalObjectDecoders = array();
	private static $_externalComponentEncoders = array();
	private static $_externalComponentDecoders = array();
		
	function Encode($obj){
		
		if(U_USECACHE){
			$md = md5(json_encode($obj));
			header("HASH: ".$md);
			if(isset($_SESSION[$md])){
				return $_SESSION[$md];
			}
			$_SESSION[$md] = microtime(true);
		}
		self::$_objectPack = array();
		self::$_objectBytesPack = null;
		self::$_bytePackCount = 0;
		$result = $this->Encoding($obj);
		if(count(self::$_objectBytesPack)>0){
			return pack('C3I*',self::Version,0,250,self::$_bytePackCount).self::$_objectBytesPack.$result;
		}else
			return pack('C2',self::Version,0).$result;
	}
	
	private function Encoding(&$obj){
		switch(gettype($obj)){
			case 'NULL':
				return pack('C',0);
			break;
			case 'boolean':
				if($obj==false){
					return pack('C',01);
				}else{
					return pack('C',02);
				}
			break;
			case 'integer':
				return pack('C',3).$this->EncodeInteger($obj);
			break;
			case 'float':
				return pack('C',4).$this->EncodeFloat($obj);
			break;
			case 'double':
				return pack('C',4).$this->EncodeFloat($obj);
			break;
			case 'string':
				return pack('C',5).$this->EncodeString($obj);
			break;
			case 'array':
				$keyArray = array_keys($obj);
				if(count($keyArray)>0 && gettype($keyArray[0])=='string'){
					return pack('C',12).$this->EncodeHashtable($obj);
				}else{
					return pack('C',11).$this->EncodeArrayList($obj);
				}
			break;
			default:
				switch (get_class($obj)) {
					case 'Vector2':
						return pack('C1',16).$this->EncodeVector2($obj);
						break;
					case 'Vector3':
						return pack('C1',17).$this->EncodeVector3($obj);
						break;
					case 'Vector4':
						return pack('C1',18).$this->EncodeVector4($obj);
						break;
					case 'Quaternion':
						return pack('C1',19).$this->EncodeQuaternion($obj);
						break;
					case 'Rect':
						return pack('C1',21).$this->EncodeRect($obj);
						break;
					case "UnityColor":
						return pack('C1',20).$this->EncodeColor($obj);
					default:
						if(!in_array($obj, self::$_objectPack)){
							$bytesPack = $this->ResourceEncoding($obj);
							self::$_bytePackCount++;
							self::$_objectPack[]=$obj;
							self::$_objectBytesPack.= $this->EncodeInteger(strlen($bytesPack)).$bytesPack;
						}
						return pack('C1I*',251,array_search($obj, self::$_objectPack));
				}
				break;
		}
		return "USerializer:Encode";
	}
	
	private function ResourceEncoding(&$obj){
		$objClassName = get_class($obj);
				switch ($objClassName) {
					case "Texture2D":
						return pack('C1',23).$this->EncodeTexture2D($obj);
					case 'PhysicMaterial':
						return pack('C1',24).$this->EncodePhysicMaterial($obj);
					case "Mesh":
						if($obj->data==null || !$obj->isFile){
							return pack('C1',22).$this->EncodeMesh($obj);
						}else
							return $obj->data;
					case "GameObject":
						if($obj->data==null || !$obj->isFile){
							return pack('C1',25).$this->EncodeGameObject($obj);
						}else
							return $obj->data;
					case 'Material':
						return pack('C1',26).$this->EncodeMaterial($obj);
					case "ULabelModel":
						return pack('C1',32).$this->EncodeULabelModel($obj);
					case "UModel":
						return pack('C1',33).$this->EncodeUModel($obj);
					case "UCompare":
						return pack('C1',34).$this->EncodeUCompare($obj);
					case "UCommand":
						return pack('C1',35).$this->EncodeUCommand($obj);
					case "UValue":
						return pack('C1',36).$this->EncodeUValue($obj);
					case "UMenu":
						return pack('C1',37).$this->EncodeUMenu($obj);
					case 'ArrayOf':
						return pack('C1',10).$this->EncodeArray($obj);
					case 'ListOf':
						return pack('C1',13).$this->EncodeList($obj);
					case 'Dictionary':
						return pack('C1',14).$this->EncodeDictionary($obj);
					break;
					
					default:
						if(array_key_exists($objClassName, self::$_externalObjectEncoders))
							return call_user_func(self::$_externalObjectEncoders,$obj);
						return $this->EncodeObject($obj);
					break;
				}
	}
	
	private function EncodeBoolean($val){
		if($val==false){
			return pack('C',01);
		}else{
			return pack('C',02);
		}
	}
	
	private function EncodeInteger($int){
		return pack("I*",$int);
	}
	
	private function EncodeFloat($float){
		return pack("f*",$float);
	}
	
	private function EncodeString($str){
		return pack("i1A*",strlen($str),$str);
	}
	
	private function EncodeArray($arr){//ArrayOf
		//require_once 'GatewayManager.php';
		$count = count($arr->data);
		$result = pack("i1",$count);
		if($arr->typeOf=="")
			$arr->typeOf = "System.Object";
		if(array_key_exists($arr->typeOf, self::$utypes)){
			$arr->typeOf = self::$utypes[$arr->typeOf];
		}
		$result.= $this->EncodeString($arr->typeOf);
		for($i=0;$i<$count;$i++){
			$result.= $this->Encoding($arr->data[$i]);
		}
		return $result;
	}
	
	private function EncodeArrayList($arr){
		$result = pack("i1",count($arr));
		foreach($arr as $item){
			$result.=$this->Encoding($item);
		}
		return $result;
	}
	
	private function EncodeHashtable($arr){
		$result = pack("i1",count($arr));
		foreach($arr as $key => $value){
			$result.=$this->Encoding($key).$this->Encoding($value);
		}
		return $result;
	}
	
	private function EncodeList($arr){
		$count = count($arr->data);
		$result = pack("i1",$count);
		if(array_key_exists($arr->typeOf, self::$utypes))
			$arr->typeOf = self::$utypes[$arr->typeOf];
		$result .= pack("i1A*",strlen($arr->typeOf),$arr->typeOf);
		foreach ($arr->data as $key=>$objs){
				$result.=$this->Encoding($objs);
		}
		return $result;
	}
	
	private function EncodeDictionary($arr){
		$result = pack("i1",count($arr->data));
		
		if(array_key_exists($arr->keyOf, self::$utypes))
			$arr->keyOf = self::$utypes[$arr->keyOf];
		$result .= $this->EncodeString($arr->keyOf);
		
		if(array_key_exists($arr->valueOf, self::$utypes))
			$arr->valueOf = self::$utypes[$arr->valueOf];
		$result .= $this->EncodeString($arr->valueOf);
		
		foreach($arr->data as $key => $value){
			$result.=$this->Encoding($key).$this->Encoding($value);
		}
		return $result;
	}
	
	private function EncodeObject($eobj){
		$result = '';
		$result = pack('C',200).$this->EncodeString(get_class($eobj));
		$vars = get_object_vars($eobj);
		$countVars = count($vars);
		$result.=$this->EncodeInteger($countVars);
		foreach ($vars as $key => $val){
			if($key!="_explicitType"){
				$unpfield = new UField($key,$val);
				$result.=$this->EncodeField($unpfield);
			}
		}
		return $result;
	}
	
	private function EncodeField($gnpField){
		return $this->EncodeString($gnpField->name).$this->Encoding($gnpField->value);
	}
	
	private function EncodeVector2($vec){
		return pack("f*",$vec->x,$vec->y);
	}
	
	private function EncodeVector3($vec){
		return pack("f*",$vec->x,$vec->y,$vec->z);
	}
	
	private function EncodeVector4($vec){
		return pack("f*",$vec->x,$vec->y,$vec->z,$vec->w);
	}
	
	private function EncodeQuaternion($q){
		return pack("f*",$q->x,$q->y,$q->z,$q->w);
	}
	
	private function EncodeColor($col){
		return pack("f*",$col->r).pack("f*",$col->g).pack("f*",$col->b).pack("f*",$col->a);
	} 
	
	private function EncodeTexture2D($txtr){
		return $this->EncodeInteger($txtr->width).
				$this->EncodeInteger($txtr->height).
				$this->EncodeInteger($txtr->format).
				pack('C1',$txtr->wrapMode).
				$this->EncodeInteger(strlen($txtr->data)).
				$this->EncodeString($txtr->name).
				$txtr->data;
	}
	
	private function EncodePhysicMaterial($mat){
		return $this->EncodeString($mat->name).
			$this->EncodeFloat($mat->dynamicFriction).
			$this->EncodeFloat($mat->staticFriction).
			$this->EncodeFloat($mat->bounciness).
			$this->EncodeFloat($mat->dynamicFriction2).
			$this->EncodeFloat($mat->staticFriction2).
			$this->EncodeVector3($mat->frictionDirection).
			$this->EncodeVector3($mat->frictionDirection2).
			$this->EncodeInteger($mat->frictionCombine).
			$this->EncodeInteger($mat->bounceCombine);
	}
	
	private function EncodeMesh($mesh){
		$vertsCount = count($mesh->vertices->data);
		$uvCount = count($mesh->uv->data);
		$trianglesCount = count($mesh->triangles->data);
			
		$result = $this->EncodeString($mesh->name).
			$this->EncodeInteger($vertsCount);
		for ($i = 0; $i < $vertsCount; $i++) {
			$result.=$this->EncodeVector3($mesh->vertices->data[$i]);
		}
		$result.=$this->EncodeInteger($uvCount);
		for ($i = 0; $i < $uvCount; $i++) {
			$result.=$this->EncodeVector2($mesh->uv->data[$i]);
		}
		$result.=$this->EncodeInteger($trianglesCount);
		for ($i = 0; $i < $trianglesCount; $i++) {
			$result.=$this->EncodeInteger($mesh->triangles->data[$i]);
		}
		return $result;
	}
	
	private function EncodeGameObject(&$go){
		$go->parameters['name'] = $go->name;
		$go->parameters['childcount'] = count($go->childrens);
		$go->parameters['componentscount'] = count($go->components);
		$go->parameters['tag'] = $go->tag;
		$go->parameters['layer'] = $go->layer;
		
		$result = $this->EncodeHashtable($go->parameters);
		
		foreach ($go->childrens as $child) {
			$result.=$this->Encoding($child);
		}
		
		foreach ($go->components as $component) {
			$result.=$this->EncodingComponent($component);
		}
		return $result;
	}
	
	private function EncodeMaterial($mat){
		$mat->parameters['name'] = $mat->name;
		$mat->parameters['shader'] = $mat->shader;
		return $this->EncodeHashtable($mat->parameters);
	}
	
	private function EncodeRect($rect){
		return pack("f*",$rect->x,$rect->y,$rect->width,$rect->height);
	}
	
	private function EncodingComponent(&$component){
		if(!in_array($component, self::$_objectPack)){
			$bytesPack = $this->EncodeComponent($component);
			self::$_bytePackCount++;
			self::$_objectPack[]=$component;
			self::$_objectBytesPack.= $this->EncodeInteger(strlen($bytesPack)).$bytesPack;
		}
		return pack('C1I*',252,array_search($component, self::$_objectPack));
	}
	
	private function EncodeComponent(&$component){
		$cName = $component->GetClassName();
		switch ($cName) {
			case Components::TRANSFORM:
				return pack('C',199).$this->EncodeString($cName).$this->EncodeVector3($component->localPosition).
						$this->EncodeQuaternion($component->localRotation).
						$this->EncodeVector3($component->localScale);
			
			case Components::MESHFILTER:
				if($component->sharedMesh!=null)
					return pack('C',199).$this->EncodeString($cName).pack('C',02).$this->Encoding($component->sharedMesh);
				else
					return pack('C',199).$this->EncodeString($cName).pack('C',01);
			case Components::MESHRENDERER:
				$result = pack('C',199).$this->EncodeString($cName).
						$this->EncodeBoolean($component->castShadows).
						$this->EncodeBoolean($component->receiveShadows).
						//$this->EncodeInteger($component->lightmapIndex).
						//$this->EncodeVector4($component->lightmapTilingOffset).
						$this->EncodeInteger(count($component->sharedMaterials));
				foreach ($component->sharedMaterials->data as $mat) {
					$result.=$this->Encoding($mat);
				}
				return $result;
			case Components::LIGHT:
				return pack('C',199).$this->EncodeString($cName).
						$this->EncodeInteger($component->type).
						$this->EncodeColor($component->color).
						$this->EncodeFloat($component->intensity).
						$this->EncodeInteger($component->shadows).
						$this->EncodeFloat($component->shadowStrength).
						$this->EncodeFloat($component->shadowBias).
						$this->EncodeFloat($component->shadowSoftness).
						$this->EncodeFloat($component->shadowSoftnessFade).
						$this->EncodeFloat($component->range).
						$this->EncodeFloat($component->spotAngle).
						$this->EncodeInteger($component->renderMode).
						$this->EncodeInteger($component->cullingMask);
			case Components::CAMERA:
				return pack('C',199).$this->EncodeString($cName).
						$this->EncodeInteger($component->clearFlags).
						$this->EncodeColor($component->backgroundColor).
						$this->EncodeInteger($component->cullingMask).
						$this->EncodeBoolean($component->orthographic).
						$this->EncodeFloat($component->orthographicSize).
						$this->EncodeFloat($component->fov).
						$this->EncodeFloat($component->near).
						$this->EncodeFloat($component->far).
						$this->EncodeRect($component->rect).
						$this->EncodeFloat($component->depth).
						$this->EncodeInteger($component->renderingPath);	
			case Components::RIGIDBODY:
				return pack('C',199).$this->EncodeString($cName).
						$this->EncodeFloat($component->drag).
						$this->EncodeFloat($component->angularDrag).
						$this->EncodeFloat($component->mass).
						$this->EncodeBoolean($component->isKinematic).
						$this->EncodeBoolean($component->freezeRotation).
						$this->EncodeInteger($component->constraints).
						$this->EncodeInteger($component->collisionDetectionMode).
						$this->EncodeInteger($component->interpolation).
						$this->EncodeBoolean($component->useGravity);
			case Components::PROJECTOR:
				$result = pack('C',199).$this->EncodeString($cName).
						$this->EncodeFloat($component->nearClipPlane).
						$this->EncodeFloat($component->farClipPlane).
						$this->EncodeFloat($component->fieldOfView).
						$this->EncodeFloat($component->aspectRatio).
						$this->EncodeBoolean($component->orthographic).
						$this->EncodeFloat($component->orthographicSize).
						$this->EncodeInteger($component->ignoreLayers);
				if($component->material!=null){
					$result.=$this->EncodeBoolean(true).
					$this->Encoding($component->material);
				}else
					$result.=$this->EncodeBoolean(false);
				return $result;		
			case Components::COLLIDER:
				$vars = $component->GetValues();
				$countVars = count($vars);
				$result=pack('C',199).$this->EncodeString($cName).
						$this->EncodeInteger($countVars);
				foreach ($vars as $key => $val){
					$result.=pack('C',5).$this->EncodeString($key).$this->Encoding($val);
				}
				return $result;
			
			case Components::PARTICLEEMITTER:
				$vars = $component->GetValues();
				$countVars = count($vars);
				$result=pack('C',199).$this->EncodeString($cName).
						$this->EncodeInteger($countVars);
				foreach ($vars as $key => $val){
					$result.=pack('C',5).$this->EncodeString($key).$this->Encoding($val);
				}
				return $result;
			
			default:
				return pack('C',199).$this->EncodeString($cName).$this->EncodeUnknowComponent($component) ;
		}
	}
	
	private function EncodeUnknowComponent(&$component){
		$vars = get_object_vars($component);
		//$vars = $component->GetValues();
		$countVars = count($vars);
		$result=array();
		$result['class']=get_class($component);
		foreach ($vars as $key => $val){//pack("i1A*",strlen($str),$str);
			$result[$key]=$val;
			//$result.=pack('C1I1A*',5).$this->EncodeString($key).$this->Encoding($val);
		}
		return $this->EncodeHashtable($result);
	}
	
	/*ULIB CLasses Encode*/
	private function EncodeULabelModel($model){
		return $this->EncodeInteger($model->id).$this->EncodeString($model->label);
	}
	
	
	private function EncodeUModel($model){
		return $this->EncodeInteger($model->id).
				$this->EncodeString($model->label).
				$this->EncodeString($model->target).
				$this->EncodeString($model->member).
				$this->Encoding($model->parameters).
				$this->Encoding($model->value);
	}
	
	private function EncodeUCommand($model){
		$commandBytes = $this->EncodeInteger($model->id).
						$this->EncodeString($model->label).
						$this->EncodeString($model->target).
						$this->EncodeString($model->member).
						$this->Encoding($model->parameters).
						$this->Encoding($model->value); 
		
		if($model->IsPrevios==false)
			return $commandBytes.pack('C',01);
		else
			return $commandBytes.pack('C',02);
	}
	
	var $CompareToByte = array("<"=>01,"<="=>02,"=="=>03,">="=>04,">"=>05,"!="=>06);
	
	private function EncodeUCompare($model){
		return $this->EncodeInteger($model->id).
				$this->EncodeString($model->label).
				$this->EncodeString($model->target).
				$this->EncodeString($model->member).
				$this->Encoding($model->parameters).
				$this->Encoding($model->value).
				$this->CompareToByte($model->condition);
	}
	
	private function EncodeUValue($model){
		$uValBytes = $this->EncodeInteger($model->id).
				$this->EncodeString($model->label).
				$this->EncodeString($model->target).
				$this->EncodeString($model->member).
				$this->Encoding($model->parameters).
				$this->Encoding($model->value).
				$this->Encoding($model->min).
				$this->Encoding($model->max);
		if($model->changed)
			return $uValBytes.pack('C',02);
		else
			return $uValBytes.pack('C',01);
	}
	
	private function EncodeUMenu($model){
		return $this->EncodeInteger($model->id).
				$this->EncodeString($model->label).
				$this->EncodeInteger($model->parent).
				$this->Encoding($model->commandIds).
				$this->Encoding($model->commands);
	}
	
	/**
	 * -----------------------------------------
	 * 					DECODER					|
	 * -----------------------------------------
	 */
	
	/*private $types = array("string"=>"System.String",
							"integer"=>"System.Int32",
							"UnityColor"=>"UnityEngine.Color",
							"object"=>"System.Object",
							"Vector2"=>"UnityEngine.Vector2",
							"Vector3"=>"UnityEngine.Vector3",
							"Vector4"=>"UnityEngine.Vector4",
							"Texture2D"=>"UnityEngine.Texture2D",
							"ArrayOf"=>"System.Object",
							"ListOf"=>"System.Object");*/
	
	function Decode(&$inBytes){
		$startPos = 0;
		self::$_objectPack = array();
		self::$_bytesPack = array();
		self::$_objectBytesPack = null;
		self::$_bytePackCount = 0;
		$startPos++;//skip version
		$startPos++;//skip head lenght
		return $this->Decoding($inBytes, $startPos);
	}
	
	private function Decoding(&$inBytes, &$startPos) {
		$result = NULL;
		$code = unpack('C1',$inBytes[$startPos]);
		$startPos++;
		if(array_key_exists($code[1], self::$_externalObjectDecoders)){
			include self::$_externalObjectDecoders[$code[1]][0].'.php';
			return call_user_func(self::$_externalObjectDecoders[$code[1]],$this,$inBytes, $startPos);
		}
		switch ($code[1]){
			//Primitive
			case 0://null
            	$result = NULL;
                break;
			case 1://bool-false
                $result = (boolean)false;
                break;
			case 2://bool-true
                $result = (boolean)true;
                break;
			case 3://integer 
				$result = $this->DecodeInteger($inBytes, $startPos);
                break;
			case 4://float
				$result = $this->DecodeFloat($inBytes, $startPos);
                break;
			case 5://string
                $result = "";
				$strLength = $this->DecodeInteger($inBytes, $startPos);
        		for($i=0;$i<$strLength;$i++){
					$result.= $inBytes[$startPos+$i];
				}
				$startPos+=$strLength;
				break;
			//IEnumerable
			 case 10://arrayof
			 	$arrLength = $this->DecodeInteger($inBytes, $startPos);
				$className = $this->DecodeString($inBytes, $startPos);
				$result = array();
				for($i=0;$i<$arrLength;$i++){
					$result[] = $this->Decoding($inBytes,$startPos);
				}
				break;
			case 11://arraylist
               	$arrLength = $this->DecodeInteger($inBytes,$startPos);
				$result = array();
				for($i=0;$i<$arrLength;$i++){
					$result[] = $this->Decoding($inBytes,$startPos);
				}
				break;
			case 12://array with string key=Hashtable
				$arrLength = $this->DecodeInteger($inBytes, $startPos);
				$result = array();
				for($i=0;$i<$arrLength;$i++){
					$key = $this->Decoding($inBytes, $startPos);
					$result[$key] = $this->Decoding($inBytes, $startPos);
				}
                break;
			case 13://list of
				$listLength = $this->DecodeInteger($inBytes, $startPos);
				$className =$this->DecodeString($inBytes, $startPos);
				$result = array();
				for($i=0;$i<$listLength;$i++){
					$result[] = $this->Decoding($inBytes,$startPos);
				}
				break;
			case 14://array with key=Dictionary
				require_once 'Dictionary.php';
				$arrLength = $this->DecodeInteger($inBytes, $startPos);
				$keyClassName = $this->DecodeString($inBytes, $startPos);
				$valClassName = $this->DecodeString($inBytes, $startPos);
				$result = new Dictionary($keyClassName,$valClassName);
				
				for($i=0;$i<$arrLength;$i++){
					$key = $this->Decoding($inBytes,$startPos);
					$result->Add($key,$this->Decoding($inBytes,$startPos));
				}
                break;
                
           	//Unity
			case 16://Vector2
				$result = $this->DecodeVector2($inBytes, $startPos);
                break;
			case 17://Vector3
				$result = $this->DecodeVector3($inBytes, $startPos);
                break;
			case 18://Vector4
				$result = $this->DecodeVector4($inBytes, $startPos);
                break;
            case 19://Quaternion
				$result = $this->DecodeQuaternion($inBytes, $startPos);
                break;
			case 20://color
				$result = $this->DecodeColor($inBytes, $startPos);
				break;
			case 21://Rect
				$result = $this->DecodeRect($inBytes, $startPos);
                break;
				
				
			case 22://Mesh
				$result = $this->DecodeMesh($inBytes, $startPos);
                break;
			case 23://Texture
				require_once 'Texture2D.php';
				$result = new Texture2D();
				
				$result->w = $this->DecodeInteger($inBytes, $startPos);
				$result->h = $this->DecodeInteger($inBytes, $startPos);
				$result->format = $this->DecodeInteger($inBytes, $startPos);
				//$rwrap = $inBytes[$startPos];
				//$result->wrapMode = $rwrap[1];
				$result->wrapMode = $inBytes[$startPos];
				$startPos++;
				$texLength = $this->DecodeInteger($inBytes, $startPos);
				$result->name = $this->DecodeString($inBytes, $startPos);
				$result->data = substr($inBytes, $startPos,$texLength);
				$startPos+=$texLength;							
                break;
             case 24://PhysicMaterial
             	$result = $this->DecodePhysicMaterial($inBytes, $startPos);
                break;  
			case 25://GameObject
				require_once 'GameObject.php';
				
				$arrLength = $this->DecodeInteger($inBytes, $startPos);
				$parameters = array();
				for($i=0;$i<$arrLength;$i++){
					$key = $this->Decoding($inBytes, $startPos);
					$parameters[$key] = $this->Decoding($inBytes, $startPos);
				}
				$result = new GameObject();
				$result->name = $parameters['name'];
				
				$childCount = $parameters['childcount'];
				for ($i = 0; $i < $childCount; $i++) {
					$child = $this->Decoding($inBytes, $startPos);
				}
				
				$componentsCount=$parameters['componentscount'];
				for ($i = 0; $i < $componentsCount; $i++) {
					$this->DecodeComponents($inBytes,$startPos,$result);
				}
				break;
             case 26://Material
				require_once 'Material.php';
				
				$result = new Material();
				$arrLength = $this->DecodeInteger($inBytes, $startPos);
				$parameters = array();
				for($i=0;$i<$arrLength;$i++){
					$key = $this->Decoding($inBytes, $startPos);
					if($key=='name')
						$result->name = $this->Decoding($inBytes, $startPos);
					else if($key=='shader')
						$result->shader = $this->Decoding($inBytes, $startPos);
					else
						$parameters[$key] = $this->Decoding($inBytes, $startPos);
				}
				$result->parameters = $parameters;
                break;   
                
			case 200://object
				$className = $this->DecodeString($inBytes, $startPos);
				$this->RequireClass($className);
                $targetClass = new $className;
				$result = new $targetClass;
				$fieldCount = $this->DecodeInteger($inBytes, $startPos);
				$recivedFields = array();
				for($i=0;$i<$fieldCount;$i++){
					$filedName = $this->DecodeString($inBytes, $startPos);
					$filedVal = $this->Decoding($inBytes,$startPos);//set linf ?? $startPos
					$recivedFields[$filedName] = $filedVal;
				}
				$objVars = get_object_vars($result);
				foreach ($objVars as $key=>$val){
					if(array_key_exists($key, $recivedFields)){
						$result->$key = $recivedFields[$key];
					}
				}
				break;
			case 250:
				$bytePackCount = $this->DecodeInteger($inBytes, $startPos);
				for ($i = 0; $i < $bytePackCount; $i++) {
					$bytePackLenght = $this->DecodeInteger($inBytes, $startPos);
					$bytePack = substr($inBytes, $startPos,$bytePackLenght);
					self::$_bytesPack[]=$bytePack;
					$startPos+=$bytePackLenght;
				}
				return $this->Decoding($inBytes, $startPos);
			case 251:
				$index = $this->DecodeInteger($inBytes, $startPos);
				if(count(self::$_objectBytesPack)<=$index){
					$pos = 0;
					$bpack = self::$_bytesPack[$index];					
					self::$_objectPack[] = $this->Decoding($bpack, $pos);
				}
				$result = self::$_objectPack[$index];
		}
		return $result;
	}

	private function DecodeComponents($inBytes,&$startPos,&$result){
		$startPos++;//pass 251 rescode
		
		$resourceIndex = $this->DecodeInteger($inBytes, $startPos);
		$componentBytes = self::$_bytesPack[$resourceIndex];
		$cStartPos = 1;
		$componentType = $this->DecodeString($componentBytes, $cStartPos);
		
		switch ($componentType) {
			case 'UnityEngine.Transform':
				require_once 'Transform.php';
				$component = $result->components[Components::TRANSFORM];
				$component->localPosition = $this->DecodeVector3($componentBytes, $cStartPos);
				$component->localRotation = $this->DecodeQuaternion($componentBytes, $cStartPos);
				$component->localScale = $this->DecodeVector3($componentBytes, $cStartPos);
				self::$_objectPack[] = $component;
			break;
			
			case 'UnityEngine.MeshFilter':
				require_once 'MeshFilter.php';
				$mesh = null;
				if($this->DecodeBoolean($componentBytes,$cStartPos)){
					$mesh = $this->Decoding($componentBytes, $cStartPos);
				}
				$component = new MeshFilter($mesh);
				$result->components[Components::MESHFILTER] = $component;
				self::$_objectPack[] = $component;
			break;
			
			case 'UnityEngine.MeshRenderer':
				require_once 'MeshRenderer.php';
				$component = new MeshRenderer();
				$component->castShadows = $this->DecodeBoolean($componentBytes, $cStartPos);
				$component->receiveShadows = $this->DecodeBoolean($componentBytes, $cStartPos);
				$matCount = $this->DecodeInteger($componentBytes, $cStartPos);
				for ($i = 0; $i < $matCount; $i++) {
					$component->sharedMaterials->Add($this->Decoding($componentBytes, $cStartPos));
				}
				$result->components[Components::MESHRENDERER] = $component;
				self::$_objectPack[] = $component;//$result->components[Components::MESHRENDERER];//sharedMaterials
			break;
			
			case 'UnityEngine.Collider':
				require_once 'Collider.php';
				$arrLength = $this->DecodeInteger($componentBytes, $cStartPos);
				$parameters = array();
				for($i=0;$i<$arrLength;$i++){
					$key = $this->Decoding($componentBytes, $cStartPos);
					$parameters[$key] = $this->Decoding($componentBytes, $cStartPos);
				}
				$class = explode('.', $parameters['class']);
				$targetClass = new $class[1] ;
				$component = new $targetClass;
				
				$vars = get_object_vars($component);
				foreach ($parameters as $key=>$val) {
					if($key!='class'){
						if(property_exists($component, $key));
							$component->$key = $val;
						$vars[$key] = $val;
					}
				}
				$result->components[Components::COLLIDER] = $component;
				self::$_objectPack[] = $component;//$result->components[Components::COLLIDER];
			break;
			
			case 'UnityEngine.Light':
				require_once 'Light.php';
				$component = new Light(new UnityColor(0, 0, 0, 1));
				$component->type = $this->DecodeInteger($componentBytes, $cStartPos);//4
				$component->color = $this->DecodeColor($componentBytes, $cStartPos);//16
				$component->intensity = $this->DecodeFloat($componentBytes, $cStartPos);//4
				$component->shadows = $this->DecodeInteger($componentBytes, $cStartPos);//4
				$component->shadowStrength = $this->DecodeFloat($componentBytes, $cStartPos);//4
				$component->shadowBias = $this->DecodeFloat($componentBytes, $cStartPos);//4
				$component->shadowSoftness = $this->DecodeFloat($componentBytes, $cStartPos);//4
				$component->shadowSoftnessFade = $this->DecodeFloat($componentBytes, $cStartPos);//4
				$component->range = $this->DecodeFloat($componentBytes, $cStartPos);//4
				$component->spotAngle = $this->DecodeFloat($componentBytes, $cStartPos);//4
				$component->renderMode = $this->DecodeInteger($componentBytes, $cStartPos);//4
				$component->cullingMask = $this->DecodeInteger($componentBytes, $cStartPos);//4
				$result->components[Components::LIGHT] = $component;
				self::$_objectPack[] = $component;
			break;
			case 'UnityEngine.Camera':
				require_once 'Camera.php';
				$component = new Camera();
				$component->clearFlags = $this->DecodeInteger($componentBytes, $cStartPos);
				$component->backgroundColor = $this->DecodeColor($componentBytes, $cStartPos);
				$component->cullingMask = $this->DecodeInteger($componentBytes, $cStartPos);
				$component->orthographic = $this->DecodeBoolean($componentBytes, $cStartPos);
				$component->orthographicSize = $this->DecodeFloat($componentBytes, $cStartPos);
				$component->fov = $this->DecodeFloat($componentBytes, $cStartPos);
				$component->near = $this->DecodeFloat($componentBytes, $cStartPos);
				$component->far = $this->DecodeFloat($componentBytes, $cStartPos);
				$component->rect = $this->DecodeRect($componentBytes, $cStartPos);
				$component->depth = $this->DecodeFloat($componentBytes, $cStartPos);
				$component->renderingPath = $this->DecodeInteger($componentBytes, $cStartPos);
				$result->components[Components::CAMERA] = $component;
				self::$_objectPack[] = $component;
			break;
			case 'UnityEngine.Rigidbody':
				require_once 'Rigidbody.php';
				$component = new Rigidbody();
				$component->drag = $this->DecodeFloat($componentBytes, $cStartPos);
				$component->angularDrag = $this->DecodeFloat($componentBytes, $cStartPos);
				$component->mass = $this->DecodeFloat($componentBytes, $cStartPos);
				$component->isKinematic = $this->DecodeBoolean($componentBytes, $cStartPos);
				$component->freezeRotation = $this->DecodeBoolean($componentBytes, $cStartPos);
				$component->constraints = $this->DecodeInteger($componentBytes, $cStartPos);
				$component->collisionDetectionMode =$this->DecodeInteger($componentBytes, $cStartPos);
				$component->interpolation = $this->DecodeInteger($componentBytes, $cStartPos);
				$component->useGravity = $this->DecodeBoolean($componentBytes, $cStartPos);
				$result->components[Components::RIGIDBODY] = $component;
				self::$_objectPack[] = $component;
			break;
			case 'UnityEngine.Projector':
				require_once 'Projector.php';
				$component = new Projector();
				$component->nearClipPlane = $this->DecodeFloat($componentBytes, $cStartPos);
				$component->farClipPlane = $this->DecodeFloat($componentBytes, $cStartPos);
				$component->fieldOfView = $this->DecodeFloat($componentBytes, $cStartPos);
				$component->aspectRatio = $this->DecodeFloat($componentBytes, $cStartPos);
				$component->orthographic = $this->DecodeBoolean($componentBytes, $cStartPos);
				$component->orthographicSize = $this->DecodeFloat($componentBytes, $cStartPos);
				$component->ignoreLayers = $this->DecodeInteger($componentBytes, $cStartPos);
				if ($this->DecodeBoolean($componentBytes, $cStartPos))
					$component->material = $this->Decoding($componentBytes, $cStartPos);
				$result->components[Components::PROJECTOR] = $component;
				self::$_objectPack[] = $component;
			break;
			default:
				$arrLength = $this->DecodeInteger($componentBytes, $cStartPos);
				$parameters = array();
				for($i=0;$i<$arrLength;$i++){
					$key = $this->Decoding($componentBytes, $cStartPos);
					$parameters[$key] = $this->Decoding($componentBytes, $cStartPos);
				}
				$class = explode('.', $parameters['class']);
				require_once $class.'.php';
				$targetClass = new $class[1] ;
				$component = new $targetClass;
				
				$vars = get_object_vars($component);
				foreach ($parameters as $key=>$val) {
					if($key!='class'){
						$vars[$key] = $val;
					}
				}
				$result->components[$class] = $component;
				self::$_objectPack[] = $component;
			break;
		}
	}
	
	/**
	 * -----------------------------------------
	 * 			Decode Sharp					|
	 * -----------------------------------------
	 */
	
	private function DecodeBoolean(&$inBytes,&$startPos){
		$code = unpack('C1',$inBytes[$startPos]);
		$startPos++;
		if($code[1]==1){
			return false;
		}
		return true;
	}
	
	private function DecodeFloat(&$inBytes,&$startPos){
		$arrFloat = unpack("f*",$inBytes[$startPos].$inBytes[$startPos+1].$inBytes[$startPos+2].$inBytes[$startPos+3]);
		$startPos+=4;
		return round($arrFloat[1],4);
	}
	
	private function DecodeString(&$inBytes, &$startPos){ 
		$result = '';
		$strLength = $this->DecodeInteger($inBytes, $startPos);
		for($i=0;$i<$strLength;$i++){
			$result.= $inBytes[$startPos+$i];
		}
		$startPos+=$strLength;
		return $result;
	}
	
	static function DecodeInteger(&$inBytes, &$startPos){ 
		$result = 0;
		$res = unpack("I*",$inBytes[$startPos].$inBytes[$startPos+1].$inBytes[$startPos+2].$inBytes[$startPos+3]);
		$result = (integer)$res[1];
		$startPos+=4;
		return $result;
	}
	
	function DecodeHashtanle(&$inBytes,&$startPos){
		$arrLength = $this->DecodeInteger($componentBytes, $cStartPos);
		$result = array();
		for($i=0;$i<$arrLength;$i++){
			$key = $this->Decoding($componentBytes, $cStartPos);
			$result[$key] = $this->Decoding($componentBytes, $cStartPos);
		}
		return $result;
	}
	
	/**
	 * -----------------------------------------
	 * 			Decode Unity Struct				|
	 * -----------------------------------------
	 */
	
	private function DecodeVector2(&$inBytes,&$startPos){
		require_once 'Vector2.php';
		$arrFloat = unpack("f*",$inBytes[$startPos].$inBytes[$startPos+1].$inBytes[$startPos+2].$inBytes[$startPos+3]);
		$rx = round($arrFloat[1],4);
		$arrFloat = unpack("f*",$inBytes[$startPos+4].$inBytes[$startPos+5].$inBytes[$startPos+6].$inBytes[$startPos+7]);
		$gy = round($arrFloat[1],4);
		$startPos+=8;
		return new Vector2($rx,$gy);
	}
	
	private function DecodeVector3(&$inBytes,&$startPos){
		require_once 'Vector3.php';
		$arrFloat = unpack("f*",$inBytes[$startPos].$inBytes[$startPos+1].$inBytes[$startPos+2].$inBytes[$startPos+3]);
		$rx = round($arrFloat[1],4);
		$arrFloat = unpack("f*",$inBytes[$startPos+4].$inBytes[$startPos+5].$inBytes[$startPos+6].$inBytes[$startPos+7]);
		$gy = round($arrFloat[1],4);
		$arrFloat = unpack("f*",$inBytes[$startPos+8].$inBytes[$startPos+9].$inBytes[$startPos+10].$inBytes[$startPos+11]);
		$bz = round($arrFloat[1],4);
		$startPos+=12;
		return new Vector3($rx,$gy,$bz);
	}
	
	private function DecodeVector4(&$inBytes,&$startPos){
		require_once 'Vector4.php';
		$arrFloat = unpack("f*",$inBytes[$startPos].$inBytes[$startPos+1].$inBytes[$startPos+2].$inBytes[$startPos+3]);
		$rx = round($arrFloat[1],4);
		$arrFloat = unpack("f*",$inBytes[$startPos+4].$inBytes[$startPos+5].$inBytes[$startPos+6].$inBytes[$startPos+7]);
		$gy = round($arrFloat[1],4);
		$arrFloat = unpack("f*",$inBytes[$startPos+8].$inBytes[$startPos+9].$inBytes[$startPos+10].$inBytes[$startPos+11]);
		$bz = round($arrFloat[1],4);
		$arrFloat = unpack("f*",$inBytes[$startPos+12].$inBytes[$startPos+13].$inBytes[$startPos+14].$inBytes[$startPos+15]);
		$aw = round($arrFloat[1],4);
		$startPos+=16;
		return new Vector4($rx,$gy,$bz,$aw);		
	}
	
	private function DecodeColor(&$inBytes,&$startPos){
		require_once 'UnityColor.php';
		$arrFloat = unpack("f*",$inBytes[$startPos].$inBytes[$startPos+1].$inBytes[$startPos+2].$inBytes[$startPos+3]);
		$rx = round($arrFloat[1],4);
		$arrFloat = unpack("f*",$inBytes[$startPos+4].$inBytes[$startPos+5].$inBytes[$startPos+6].$inBytes[$startPos+7]);
		$gy = round($arrFloat[1],4);
		$arrFloat = unpack("f*",$inBytes[$startPos+8].$inBytes[$startPos+9].$inBytes[$startPos+10].$inBytes[$startPos+11]);
		$bz = round($arrFloat[1],4);
		$arrFloat = unpack("f*",$inBytes[$startPos+12].$inBytes[$startPos+13].$inBytes[$startPos+14].$inBytes[$startPos+15]);
		$aw = round($arrFloat[1],4);
		$startPos+=16;
		return new UnityColor($rx,$gy,$bz,$aw);		
	}
	
	private function DecodeQuaternion(&$inBytes,&$startPos){
		require_once 'Quaternion.php';
		$arrFloat = unpack("f*",$inBytes[$startPos].$inBytes[$startPos+1].$inBytes[$startPos+2].$inBytes[$startPos+3]);
		$rx = round($arrFloat[1],4);
		$arrFloat = unpack("f*",$inBytes[$startPos+4].$inBytes[$startPos+5].$inBytes[$startPos+6].$inBytes[$startPos+7]);
		$gy = round($arrFloat[1],4);
		$arrFloat = unpack("f*",$inBytes[$startPos+8].$inBytes[$startPos+9].$inBytes[$startPos+10].$inBytes[$startPos+11]);
		$bz = round($arrFloat[1],4);
		$arrFloat = unpack("f*",$inBytes[$startPos+12].$inBytes[$startPos+13].$inBytes[$startPos+14].$inBytes[$startPos+15]);
		$aw = round($arrFloat[1],4);
		$startPos+=16;
		return new Quaternion($rx,$gy,$bz,$aw);		
	}
	
	private function DecodeRect(&$inBytes,&$startPos){
		require_once 'Rect.php';
		$arrFloat = unpack("f*",$inBytes[$startPos].$inBytes[$startPos+1].$inBytes[$startPos+2].$inBytes[$startPos+3]);
		$rx = round($arrFloat[1],4);
		$arrFloat = unpack("f*",$inBytes[$startPos+4].$inBytes[$startPos+5].$inBytes[$startPos+6].$inBytes[$startPos+7]);
		$gy = round($arrFloat[1],4);
		$arrFloat = unpack("f*",$inBytes[$startPos+8].$inBytes[$startPos+9].$inBytes[$startPos+10].$inBytes[$startPos+11]);
		$bz = round($arrFloat[1],4);
		$arrFloat = unpack("f*",$inBytes[$startPos+12].$inBytes[$startPos+13].$inBytes[$startPos+14].$inBytes[$startPos+15]);
		$aw = round($arrFloat[1],4);
		$startPos+=16;
		return new Rect($rx,$gy,$bz,$aw);		
	}
	
	/**
	 * -----------------------------------------
	 * 			Decode Unity Objects			|
	 * -----------------------------------------
	 */
	
	private function DecodeMesh(&$inBytes,&$startPos){
		require_once 'Mesh.php';
		require_once 'Vector3.php';
		require_once 'Vector2.php';
		require_once 'ArrayOf.php';
		$mesh = new Mesh();
		$mesh->name = $this->DecodeString($inBytes, $startPos);
				
		$vertecCount = $this->DecodeInteger($inBytes, $startPos);
		$mesh->vertices = new ArrayOf('Vector3');
		for ($i = 0; $i < $vertecCount; $i++) {
			$arrFloat = unpack("f*",$inBytes[$startPos].$inBytes[$startPos+1].$inBytes[$startPos+2].$inBytes[$startPos+3]);
			$rx = round($arrFloat[1],4);
			$arrFloat = unpack("f*",$inBytes[$startPos+4].$inBytes[$startPos+5].$inBytes[$startPos+6].$inBytes[$startPos+7]);
			$gy = round($arrFloat[1],4);
			$arrFloat = unpack("f*",$inBytes[$startPos+8].$inBytes[$startPos+9].$inBytes[$startPos+10].$inBytes[$startPos+11]);
			$bz = round($arrFloat[1],4);
			$vec = new Vector3($rx,$gy,$bz);
			$startPos+=12;
			$mesh->vertices->data[]=$vec;
		}
				
		$uvCount = $this->DecodeInteger($inBytes, $startPos);
		$mesh->uv = new ArrayOf('Vector2');
		for ($i = 0; $i < $uvCount; $i++) {
			$arrFloat = unpack("f*",$inBytes[$startPos].$inBytes[$startPos+1].$inBytes[$startPos+2].$inBytes[$startPos+3]);
			$rx = round($arrFloat[1],4);
			$arrFloat = unpack("f*",$inBytes[$startPos+4].$inBytes[$startPos+5].$inBytes[$startPos+6].$inBytes[$startPos+7]);
			$gy = round($arrFloat[1],4);
			$vec = new Vector2($rx, $gy);
			$startPos+=8;
			$mesh->uv->data[]=$vec;
		}
				
		$triangCount = $this->DecodeInteger($inBytes, $startPos);
		$mesh->triangles = new ArrayOf('integer');
		for ($i = 0; $i < $triangCount; $i++) {
			$trian = $this->DecodeInteger($inBytes, $startPos);
			$mesh->triangles->data[]=$trian;
		}
		return $mesh;
	}
	
	private function DecodePhysicMaterial(&$inBytes,&$startPos){
		require_once 'PhysicMaterial.php';
		$pmaterial = new PhysicMaterial();
		$pmaterial->name = $this->DecodeString($inBytes, $startPos);
				
		$pmaterial->dynamicFriction = unpack("f*",$inBytes[$startPos].$inBytes[$startPos+1].$inBytes[$startPos+2].$inBytes[$startPos+3]);
		$pmaterial->staticFriction = unpack("f*",$inBytes[$startPos].$inBytes[$startPos+1].$inBytes[$startPos+2].$inBytes[$startPos+3]);
		$pmaterial->bounciness = unpack("f*",$inBytes[$startPos].$inBytes[$startPos+1].$inBytes[$startPos+2].$inBytes[$startPos+3]);
		$pmaterial->dynamicFriction2 = unpack("f*",$inBytes[$startPos].$inBytes[$startPos+1].$inBytes[$startPos+2].$inBytes[$startPos+3]);
		$pmaterial->staticFriction2 = unpack("f*",$inBytes[$startPos].$inBytes[$startPos+1].$inBytes[$startPos+2].$inBytes[$startPos+3]);
		$startPos+=20;
		$pmaterial->frictionDirection = $this->DecodeVector3($inBytes, $startPos);
		$pmaterial->frictionDirection2 = $this->DecodeVector3($inBytes, $startPos);
		$pmaterial->frictionCombine = $this->DecodeInteger($inBytes, $startPos);
		$pmaterial->bounceCombine = $this->DecodeInteger($inBytes, $startPos);
		return $pmaterial;
	}
	
	/**
	 * -----------------------------------------
	 * 			Tools							|
	 * -----------------------------------------
	 */
	
	private function RequireClass($className){
		$typesflip = array_flip(self::$utypes);
		if(array_key_exists($className, $typesflip))
			$className =  $typesflip[$className];
		//$className = str_replace("..", "", $className);
		require_once str_replace("..", "", $className).".php";
	}

	private function EncodeToFile($fname,$object){
		$filename = str_replace("..", "", trim($fname,'.unp').'.unp');
		$content = $this->Encode($object);
		$handle = fopen($filename, "wb");
		fwrite($handle, $content);
		fclose($handle);
	}
}

?>