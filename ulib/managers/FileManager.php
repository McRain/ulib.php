<?php
	class FileManager{
		
		static function SaveObject($fileName,$obj){
			$ser = new Serializer();
			$data = $ser->Encode($obj);
			$filename = str_replace('..', '', $fileName);		
			$handle = fopen($filename, "rb+");
			fwrite($handle, $data);
			fclose($handle);
		}

		static function LoadObject($fileName){
			$filename = str_replace('..', '', $fileName);		
			$handle = fopen($filename, "rb");
			$content = fread($handle, filesize($filename));
			fclose($handle);
			$ser = new Serializer();
			return $ser->Decode($content);
		}
	}	
?>