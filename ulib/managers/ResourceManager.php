<?php
class ResourceManager{
	
	var $resourcePaths = array("assets","assets","sound","assets","gui",
							"icons","assets","movies","music","textures","assets",
							"languages");
	
	var $_resTypes = array('Resource'=>0,'Assets' => 1,'AudioClips' => 2,'GameObjects' => 3,
		'Gui' => 4,'Icons' => 5,'Meshes' => 6,'Movies' => 7,'Music' => 8,'Textures' => 9,
		'Text' => 10,'Translations' => 11);
	
	var $_directResource = array();/*},
									{ResourceSource.Meshes,"assets"},
									{ResourceSource.Movies,"movies"},
									{ResourceSource.Music,"music"},
									{ResourceSource.Textures,"textures"},
									{ResourceSource.Text,"assets"},
									{ResourceSource.Translations,"languages"},
									{ResourceSource.Resource,"assets"});*/
	
	var $_resourceTypes = array('Resource','Assets','AudioClip','GameObject',
		'Gui' ,'Icon','Mesh','Movie' ,'Music' ,'Texture2D', 'Text' ,'Translations' );
	
	public function __construct(){
		$this->_resPaths[6]=U_LANG_DIR;
		$this->_directResource['AudioClip'] = U_SERVER_URL.'/'.'ogg.ogg';
	}
		
	function LoadAudioClip($parameters){
		return "";
	}
	
	/**
	 * key - name of resource file
	 * ext - extension of resource file
	 * path - languages, icons etc.
	 * type - Texture2D, AudioClips, GameObject....
	 * 
	 * 
	 * @param Hashtable $parameter
	 */
	function LoadResource($parameter){
		$resourceName = $parameter['key'];
		$resourceExtension = $parameter['ext'];
		$resType = $this->_resourceTypes[$parameter['type']];
		$path = $parameter['path'];
		if (array_key_exists($resType, $this->_directResource)){
			$parameter['result'] = /*$this->_directResource[$resType];
			$parameter['path']=*/ $this->resourcePaths[$path].'/'.$parameter['key'];
			$parameter['decode'] = false;
		}else{
			require_once $resType.'.php';
			$resourceClass = new $resType;
			$parameter['result'] = new $resourceClass();
			$parameter['result']->GetResource(U_APPS_PATH.U_APP_NAME.'/'.
												U_CONTENT_DIR.'/'.
												$this->resourcePaths[$path].'/'.
												$resourceName.'.'.
												$resourceExtension);
		}
		return $parameter;
	}
	
}
?>