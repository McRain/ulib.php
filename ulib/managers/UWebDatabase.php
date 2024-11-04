<?php
class UWebDatabase{
	
	private static $_mysqli;
	private static $_db;
	private static $_login;
	private static $_psw;
	
	private static $_stmts = array();
	private static $_currentStmt = 0;
	
	private static $_maxStm = 5;
	
	static function ModuleLoad($p){
		self::$_db = $p[0];
		self::$_login = $p[1];
		self::$_psw = $p[2];
		self::Connect();
		for ($i=0;$i<self::$_maxStm;$i++)
			self::$_stmts[] = self::$_mysqli->stmt_init();
	}
	
	static function Connect(){
		self::$_mysqli = new mysqli('127.0.0.1', self::$_login, self::$_psw, self::$_db);
		if (!mysqli_connect_error()) {
			self::$_mysqli->query("SET NAMES 'utf8'");
			self::$_mysqli->set_charset('UTF8');
		}
		return self::$_mysqli;
	}
	
	static function Call($procedure,$assoc = true){
		
		$parameters = array_slice(func_get_args(),2);
		$callString = 'CALL '.$procedure.'( ';
		$parameterCount = count($parameters);
		for ($i = 0; $i < $parameterCount; $i++)
			$callString.=' ? ,';
		$callString=substr($callString, 0,strlen($callString)-1).' );';
		return self::RunQuery($callString,$assoc,$parameters);
	}
	
	static function CallObject($procedure,$className){
		
		//echo $procedure.'<br/>';
		$parameters = array_slice(func_get_args(),2);
		$callString = 'CALL '.$procedure.'( ';
		$parameterCount = count($parameters);
		for ($i = 0; $i < $parameterCount; $i++)
			$callString.=' ? ,';
		$callString=substr($callString, 0,strlen($callString)-1).' );';
		return self::QueryToObjects($callString,$parameters,$className);
	}
	
	static function Query($sqlString,$assoc=TRUE){
		$parameters = array_slice(func_get_args(),2);
		return self::RunQuery($sqlString, $assoc, $parameters);
	}
	
	private static function RunQuery($sqlString,$assoc,$parameters){
		self::Connect();
		if(self::$_currentStmt>=self::$_maxStm)
			self::$_currentStmt = -1;
		self::$_currentStmt++;
		$result = array();
		if (self::$_stmts[self::$_currentStmt] = self::$_mysqli->prepare($sqlString)){//Prepare
			$parametersCount = count($parameters);
			$types = '';
			
			for ($i=0;$i<$parametersCount;$i++){
				if(is_numeric($parameters[$i]))
					$types.='i';
				else
					$types.='s';
			}
			array_unshift($parameters,$types);
		
			$tmp = array();
			foreach($parameters as $key => $value)
				$tmp[$key] = &$parameters[$key];
			if(count($tmp)>1)
				call_user_func_array(array(self::$_stmts[self::$_currentStmt], 'bind_param'),$tmp);//Bind parameters
			
			self::$_stmts[self::$_currentStmt]->execute();
			
			$meta = self::$_stmts[self::$_currentStmt]->result_metadata();
			if(!$meta)
				return $result;
			$fields = array();
			while ($field = $meta->fetch_field()) {
				$var = $field->name;
				$$var = null;
				$par[] = &$$var;
				$fields[] = $field;
			}
			
			call_user_func_array(array(self::$_stmts[self::$_currentStmt], 'bind_result'),$par);//Bind result
			
			while (self::$_stmts[self::$_currentStmt]->fetch()){
				$row = array();
				$i = 0;
				foreach ($par as $value) {
					if($assoc)
						$row[$fields[$i]->name] = $value;
					else
						$row[] = $value;
					$i++;
				}
				$result[] = $row;
			}
			self::$_stmts[self::$_currentStmt]->close();
		}
		return $result;
	}
	
	static function QueryToObjects($sqlString,$parameters,$className){
		self::Connect();
		if(self::$_currentStmt>=self::$_maxStm)
			self::$_currentStmt = -1;
		self::$_currentStmt++;
		$result = array();
		if (self::$_stmts[self::$_currentStmt] = self::$_mysqli->prepare($sqlString)){//Prepare
			$parametersCount = count($parameters);
			$types = '';
				
			for ($i=0;$i<$parametersCount;$i++){
				if(is_numeric($parameters[$i]))
					$types.='i';
				else
					$types.='s';
			}
			array_unshift($parameters,$types);
		
			$tmp = array();
			foreach($parameters as $key => $value)
				$tmp[$key] = &$parameters[$key];
			if(count($tmp)>1)
				call_user_func_array(array(self::$_stmts[self::$_currentStmt], 'bind_param'),$tmp);//Bind parameters
				
			self::$_stmts[self::$_currentStmt]->execute();
				
			$meta = self::$_stmts[self::$_currentStmt]->result_metadata();
			if(!$meta)
				return $result;
			$fields = array();
			while ($field = $meta->fetch_field()) {
				$var = $field->name;
				$$var = null;
				$par[] = &$$var;
				$fields[] = $field;
			}
				
			call_user_func_array(array(self::$_stmts[self::$_currentStmt], 'bind_result'),$par);//Bind result
				
			while (self::$_stmts[self::$_currentStmt]->fetch()){
				$i = 0;
				$objClass = new $className;
				$obj = new $objClass;
				foreach ($par as $value) {
						$fieldName = $fields[$i]->name;
						$obj->$fieldName = $value;
					$i++;
				}
				$result[] = $obj;
			}
			self::$_stmts[self::$_currentStmt]->close();
		}
		return $result;
	}
	
	
	
	
}