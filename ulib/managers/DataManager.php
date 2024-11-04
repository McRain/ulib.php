<?php
	class DataType{
		const DB='DB';
		const FILE='FILE';
	}
	
	class DataManager{
		static $_type = DataType::FILE;
		
		static function Load($name,&$parameters,&$result){
			if(self::$_type==DataType::DB){
				global $mysqli;
			}else{
				if(file_exists($name) && !is_dir($name)){
					$$result=FileManager::LoadObject($name);
				}	
			}
			return $result;
		}
	}


?>