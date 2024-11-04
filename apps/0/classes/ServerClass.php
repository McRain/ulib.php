<?php
class ServerClass{
	
	/*
	 * The list of class methods for closed for call through a gateway.
	 */
	static $ServerClass_ufunctions = array('LoadData','ExternalSave','ExternalLoad','SaveImage','SendData','GetBack','GetBgImage',
			'ChatEntry');
	
	
	function LoadData($funcName){
		return self::$funcName();
	}
	
	function SendData($val){
		if(gettype($val)=='object')
			return 'Type: '.gettype($val).' Class: '.get_class($val);
		return 'Type: '.gettype($val);
	}
	
	function GetBack($val){
		if(gettype($val)=='object' && get_class($val)=='GameObject' && array_key_exists(Components::COLLIDER, $val->components)){
			require_once 'Collider.php';
			$val->components[Components::COLLIDER] = new SphereCollider();					//---Change Collider
			$val->components[Components::COLLIDER]->radius = 1;
			$val->components[Components::COLLIDER]->center = new Vector3(0, 0, 0);
			$val->components[Components::TRANSFORM]->localPosition->x = -1.7;

		}
		return $val;
	}
	
	function LoadInteger(){
		return 125;
	}
	
	function LoadString(){
		return 'Server Test String OK';
	}
	
	function LoadArray(){
		require_once 'ArrayOf.php';
		$arr = new ArrayOf('integer');
		for ($i=0;$i<100;$i+=12){
			$arr->Add($i);
		}
		return $arr;
	}
	
	function LoadArrayString(){
		require_once 'ArrayOf.php';
		$arr = new ArrayOf('string');
		for ($i=0;$i<100;$i+=12){
			$arr->Add('String '.$i);
		}
		return $arr;
	}
	
	function LoadArrayVector(){
		require_once 'ArrayOf.php';
		require_once 'Vector3.php';
		$arr = new ArrayOf('Vector3');
		for ($i=0;$i<100;$i+=12){
			$arr->Add(new Vector3(0, $i, $i*2));
		}
		return $arr;
	}
	
	function LoadArrayList(){
		require_once 'Vector3.php';
		require_once 'Rect.php';
		$arr = array();
		for ($i=0;$i<100;$i+=12){
			$arr[] = new Vector3(0, $i, $i*2);
		}
		for ($i=0;$i<100;$i+=10){
			$arr[] = new Rect($i, $i, $i*2,$i*4);
		}
		return $arr;
	}
	
	function LoadHashtable(){
		require_once 'Vector3.php';
		require_once 'Rect.php';
		$arr = array();
		for ($i=0;$i<100;$i+=12){
			$arr['Vector '.$i] = new Vector3(0, $i, $i*2);
		}
		for ($i=0;$i<100;$i+=10){
			$arr['Rect '.$i] = new Rect($i, $i, $i*2,$i*4);
		}
		return $arr;
	}
	
	function LoadList(){
		require_once 'Vector3.php';
		require_once 'ListOf.php';
		$lst = new ListOf('Vector3');
		for ($i=0;$i<100;$i+=12){
			$lst->Add( new Vector3(0, $i, $i*2));
		}
		return $lst;
	}
	
	function LoadDictionary(){
		require_once 'Vector3.php';
		require_once 'Dictionary.php';
		$dict = new Dictionary('string', 'Vector3');
		for ($i=0;$i<100;$i+=12){
			$dict->Add('Vector '.$i, new Vector3(0, $i, $i*2));
		}
		return $dict;
	}
	
	function LoadMyClass(){
		require_once 'GamePlayer.php';
		$player = new GamePlayer();
		$player->name = 'Player';
		
		return $player;		
	}
	
	function LoadMesh(){
		require_once 'Mesh.php';
		$mesh = new Mesh();
		$mesh->CreatePrimitive(MeshPrimitive::CUBE);
		return $mesh;
	}
	
	function LoadGameObject(){
		require_once 'GameObject.php';
		require_once 'Mesh.php';
		require_once 'MeshFilter.php';
		
		$go = new GameObject();										//---Create GameObject
		$go->name = 'NewServerGo';
		$go->components[Components::TRANSFORM]->localPosition = 
										new Vector3(1.88, 0, 5.25);	//Change Position
		$mesh = new Mesh();											//---Create Mesh
		$mesh->CreatePrimitive(MeshPrimitive::CUBE);
		$go->components[Components::MESHFILTER] = new MeshFilter($mesh);						//---Create MeshFilter
		return $go;
	}
	
	function LoadCube(){
		require_once 'GameObject.php';
		require_once 'Mesh.php';
		require_once 'MeshFilter.php';
		require_once 'MeshRenderer.php';
		require_once 'Material.php';
		require_once 'Collider.php';
		
		$go = new GameObject();										//---Create GameObject
		$go->name = 'NewServerGo';
		$go->layer = 8;
		$go->tag = 'Recived';
		$go->components[Components::TRANSFORM]->localPosition = 
										new Vector3(1.88, 0, 5.25);	//Change Position
		$mesh = new Mesh();											//---Create Mesh
		$mesh->CreatePrimitive(MeshPrimitive::CUBE);
		$meshFilter = new MeshFilter($mesh);						//---Create MeshFilter
		$go->components[Components::MESHFILTER] = $meshFilter;		//---Set MeshFilter to components
		
		$material = new Material();									//---CreateMaterial
		$material->name = 'MyMaterial';
		$material->shader = 'Self-Illumin/Specular';				//---Set shader name
		$material->parameters['_Color'] = 
									new UnityColor(0, 0.5, 1, 1);	//---Set Color
		$meshRenderer = new MeshRenderer();							//---Create renderer
		$meshRenderer->sharedMaterials->Add($material);				//---Add material to renderer
		$go->components[Components::MESHRENDERER] = $meshRenderer;	//--Set Renderer to components
		
		$go->components[Components::COLLIDER] = new BoxCollider();	//---Create collider
		$go->components[Components::COLLIDER]->size = 
											new Vector3(1, 1, 1);	//---Set collider size
		$go->components[Components::COLLIDER]->center = 
											new Vector3(0, 0, 0);
		
		return $go;
	}
	
	function LoadBuild(){
		require_once 'GameObject.php';
		require_once 'MeshFilter.php';
		require_once 'MeshRenderer.php';
		require_once 'Rigidbody.php';
		require_once 'Collider.php';
		require_once 'Mesh.php';
		require_once 'Texture2D.php';
		
		$go = new GameObject();														//---Create GameObject
		$go->name = 'Building';
		$go->layer = 8;
		$go->tag = 'Recived';
		$go->components[Components::TRANSFORM]->localPosition = 
										new Vector3(-0.5, -1.2, -2.25);		//---Change position
		$go->components[Components::TRANSFORM]->localRotation = 
										new Quaternion(-0.75,0,0,0.75);				//---Change rotation
		$go->components[Components::TRANSFORM]->localScale = 
										new Vector3(0.25, 0.25, 0.25);
		
		$mesh = new Mesh();															//---Create mesh
		$mesh->LoadFromFile(U_APP_PATH.U_CONTENT_DIR.'/build_mesh.unp');				//--- Load mesh from previos saved file
		$mf = new MeshFilter($mesh);												//--- Create MeshFilter
		$go->components[Components::MESHFILTER] = $mf;								//--- Add MeshFilter to gameObject components
		
		$mat = new Material();														//---Create Material
		$mat->shader = 'Legacy Shaders/Lightmapped/Bumped Diffuse';					//---Set shader
		$mat->parameters['_Color'] = new UnityColor(1, 1, 1, 1);					//---Set color
		
		$diff = new Texture2D();													//---Create Texture
		$diff->LoadFromPNG(U_APP_PATH.U_CONTENT_DIR.'/diffuse.png');				//--- Load Texture from file
		$mat->parameters['_MainTex'] = $diff;										//--- Set Texture to material
		
		$norm = new Texture2D();													//---Create Texture
		$norm->LoadFromPNG(U_APP_PATH.U_CONTENT_DIR.'/normal.png');					//--- Load Texture from file
		$mat->parameters['_BumpMap'] = $norm;										//--- Set Texture to material
		
		$spec = new Texture2D();													//---Create Texture
		$spec->LoadFromPNG(U_APP_PATH.U_CONTENT_DIR.'/spec.png');					//--- Load Texture from file
		$mat->parameters['_LightMap'] = $spec;										//--- Set Texture to material
		
		$go->components[Components::COLLIDER] = new BoxCollider();					//---Add Collider
		$go->components[Components::COLLIDER]->size = 
												new Vector3(11, 9.25, 11);
		$go->components[Components::COLLIDER]->center = new Vector3(0, 0, 5.6);
		
		$mr = new MeshRenderer();													//---Add Renderer
		$mr->sharedMaterials->Add($mat);											//---Set Material to Renderer
		$go->components[Components::MESHRENDERER] = $mr;
		
		return $go;
	}
	
	function LoadMultiDimentional(){
		$result = array('ListName'=>'Price', 
						array( 'Title' => 'Rose','Price' => 1.25,'Number' => 15 ),
               			array( 'Title' => 'Daisy','Price' => 0.75,'Number' => 25,'Special' => 
               					array('MinPack'=>10,'DiscountPercent'=>10)
                    		),
               			array( 'Title' => 'Orchid','Price' => 1.15,'Number' => 7 )
             		);
		return $result;
	}
	
	function GetBgImage(){
		require_once 'Texture2D.php';
		$texture = new Texture2D();
		$texture->LoadFromPNG(U_APP_PATH.U_CONTENT_DIR.'/bg.png');
		return $texture;
	}
	
	function SaveImage($texture){
		if (!$handle = fopen(U_APP_PATH.U_CONTENT_DIR.'/'.$texture->name.'.png', 'w')) {
			exit;
		}
		if (!fwrite($handle, $texture->data)) {
			exit;
		}
		fclose($handle);
		return 'You screenshoot saved to '.U_APP_PATH.U_CONTENT_DIR.'/'.$texture->name.'.png';
	}
	
	/********************************************************************
	* 								SPAWN LIST
	********************************************************************/
	
	/**
	 * This function require use session.
	 * In test application used session enabled in app_debug.php 
	 */
	function SpawnSave($spawnlist){
		$spawncount = count($spawnlist->data);
		$_SESSION["spawncount"] = $spawncount;
		for ($i=0;$i<$spawncount;$i++){
			$_SESSION["spawn".$i."num"] = $spawnlist->data[$i]->Num;
			$_SESSION["spawn".$i."pos"] = serialize($spawnlist->data[$i]->Pos);
		}
		return $spawncount;
	}
	
	/**
	 * This function require use session.
	 * In test application used session enabled in app_debug.php 
	 */
	function SpawnLoad(){
		require_once 'ListOf.php';
		require_once 'MySavedItem.php';
		require_once 'Vector3.php';
		$spawnList = new ListOf('MySavedItem');
		$spawncount = $_SESSION['spawncount'];
		for ($i=0;$i<$spawncount;$i++){
			$mysp = new MySavedItem();
			$mysp->Num = $_SESSION["spawn".$i."num"];
			$mysp->Pos = unserialize($_SESSION["spawn".$i."pos"]);
			$spawnList->Add($mysp);
		}
		return  $spawnList;
	}
		 
	 /******************************************************************
	  * 				Translations
	  ******************************************************************/
	 function LoadTranslationList(){
	 	require_once 'LanguageManager.php';
	 	//See app_config.php 
	 	//where:
	 	//GatewayManager::set('languages', array('en'=>'English','ru'=>'Russian'));
	 	//and
		//$defaultLanguage=0;//Or 'en' - can be numeric or string
		
	 	return LanguageManager::LoadList();
	 }
	 
	 function LoadTranslations($parameters){
	 	require_once 'LanguageManager.php';
	 	return LanguageManager::LoadTranslation($parameters);
	 }
	 
	 /******************************************************************
	 * 				UserManager example
	 ******************************************************************/
	 
	 function MyRegister($param){
	 	require_once 'UserManager.php';
	 	//any do with parameters or other
	 	return UserManager::Register($param);
	 }
	 
	 
	 function MyLogin($param){
	 	require_once 'UserManager.php';
	 	//any do with parameters or other
	 	return UserManager::Login($param);
	 }
	 
	 function MyLogout($param){
	 	require_once 'UserManager.php';
	 	//any do with parameters or other
	 	return UserManager::Logout($param);
	 }
	
	 
	 /******************************************************************
	 * 				Message example
	 ******************************************************************/
	 function LoadMessages(){
	 	require_once 'UMessage.php';
	 	require_once 'Dictionary.php';
	 	$result = new Dictionary('integer', 'UMessage');
	 	
	 	$msgsFile = U_CONTENT_PATH.'/'.U_LANG_DIR.'/messages'.U_MSG_EXT;
	 	
	 	if(file_exists($filename) && is_file($filename)){
	 		
	 		require_once 'ArrayOf.php';
	 		require_once 'MessageManager.php';
	 		
	 		$section= isset($parameters['section']) ? $section = $parameters['section']:'';
	 		
	 		$msg_array = MessageManager::ParseMessage($filename);
	 		
	 		if($section!=''){
	 			foreach ($msg_array as $tKey=>$tVal) {
	 				if($section==$tKey){
	 					foreach ($tVal as $key=>$val) {
	 						$msg = new UMessage();
	 						$msg->text = (string)trim($val);
	 						$result->Add((integer)trim($key),$msg);
	 					}
	 				}
	 			}
	 		}else{
	 			foreach ($msg_array as $tKey=>$tVal) {
	 				if(is_array($tVal)){
	 					foreach ($tVal as $key=>$val){
	 						$result->Add((integer)trim($key),self::ParseUmsg(U_MSG_DECODER, (string)trim($val)));
	 					}
	 				}
	 				else
	 				$result->Add((integer)trim($tKey),self::ParseUmsg(U_MSG_DECODER, (string)trim($tVal)));
	 			}
	 		}
	 	}
	 }
	 
	 /******************************************************************
	 * 				External example
	 ******************************************************************/
	 
	 function ExternalSave($data){
	 	return true;	
	 }
	 
	 function ExternalLoad(){
	 	 
	 }
}
?>