<?php
class UserManager{

	static $_gwFunctions = array('Login','Register','Logout'); 
	
	const UseCaptcha = true;
	
	const KeyLogin = 'login';
	const KeyPassword = 'pass';
	const KeyCaptcha = 'captcha';
	const KeyData = 'data';
	const KeyId = 'id';
	
	/*function Register($param){
		
	}*/
	
	function Login($param){
		require_once 'Texture2D.php';
		$inlogin = $param[self::KeyLogin];
		$inpass =  $param[self::KeyPassword];
		$captcha = $param[self::KeyCaptcha];
		$data = null;
		if(array_key_exists(self::KeyData, $param))
			$data=$param[self::KeyData];
		$result=array(self::KeyId=>-1,self::KeyData=>$data);//set default data
		if(self::UseCaptcha && (isset($_SESSION[GatewayManager::_session_captcha]) && 
					$_SESSION[GatewayManager::_session_captcha]!=trim($captcha))){//Captcha set and wrong
			$result[self::KeyCaptcha]= self::getCaptchaTexture();
		}else{//Captha not set or right or not used
			global $mysqli;
			if(!$mysqli) $mysqli = dbconn();
			if ($stmt = $mysqli->prepare("CALL get_userpass ( ? ) ;")) {
				$stmt->bind_param("s", $inlogin);
				$stmt->execute(); 
   				$stmt->bind_result($uid,$upass);
   				if($stmt->fetch()) {
   					if($upass==$inpass){
   						unset($_SESSION[GatewayManager::_session_captcha]);
						$result[self::KeyId] = $_SESSION[GatewayManager::_session_uid] = $uid;
   					}else if(self::UseCaptcha)
   						$result[self::KeyCaptcha]= self::getCaptchaTexture();
   				}else if(self::UseCaptcha)
   					$result[self::KeyCaptcha]= self::getCaptchaTexture();
				$stmt->close();
			}else if(self::UseCaptcha){//wrong login or password
				$result[self::KeyCaptcha]= self::getCaptchaTexture();
			}
		}
		return  $result;
	}
	
	function Register($param){
		$inlogin = $param[self::KeyLogin];
		$inpass =  $param[self::KeyPassword];
		$dataInfo = $param[self::KeyData];//array
		$mail = $dataInfo['mail'];
		global $mysqli;
		if(!$mysqli) 
			$mysqli = dbconn();
		if ($stmt = $mysqli->prepare("CALL add_newuser ( ? , ? , ? ) ;")) {
			$stmt->bind_param("sss", $inlogin,$inpass,$mail);
			$stmt->execute(); 
   			$stmt->bind_result($uid,$upass);
   			if($stmt->fetch()) {
   				
   			}
   			$stmt->close();
		}
		return  true;
	}
	
	function Logout($param){
		$uid = $param[self::KeyId];
		$data = $param[self::KeyData];
		unset($_SESSION[GatewayManager::_session_uid]);
		unset($_SESSION[GatewayManager::_session_captcha]);
		return array(self::KeyId=>$uid,self::KeyData=>$data);
		
	}
	
	/**
	 * The method returns the captcha in format Texture2D.
	 */
	public static function getCaptchaTexture(){
		require_once 'Texture2D.php';
		$captchaTexture = new Texture2D();
		$captchaTexture->data = self::getCaptcha();
		return $captchaTexture;
	}
	
	/**
	 * 
	 * The method returns the captcha in format PNG
	 */
	public static function getCaptcha(){
 		$width = 256;               		
 		$height = 72;               		
 		$font_size = 24;   					
 		$let_amount = 4;            		//Symbol count
 		$fon_let_amount = 30;       		
 		$path_fonts = dirname(__FILE__)."/brody.ttf";
 
 		$letters = array('a','b','c','d','e','f','g','h','j','k','m','n','p','q','r','s','t','u','v','w','x','y','z','2','3','4','5','6','7','9');
 		$colors = array('10','30','50','70','90','110','130','150','170','190','210');
 
 		$src = imagecreatetruecolor($width,$height);
 		$fon = imagecolorallocate($src,255,255,255);
 		imagefill($src,0,0,$fon);
 		for($i=0;$i<$fon_let_amount;$i++){
   			$color = imagecolorallocatealpha($src,rand(0,255),rand(0,255),rand(0,255),100); 
   			$letter = $letters[rand(0,sizeof($letters)-1)];
   			$size = rand($font_size-2,$font_size+2);
   			imagettftext($src,$size,rand(0,45),rand($width*0.1,$width-$width*0.1),rand($height*0.2,$height),$color,$path_fonts,$letter);
 		}
 
 		for($i=0;$i<$let_amount;$i++){
   			$color = imagecolorallocatealpha($src,$colors[rand(0,sizeof($colors)-1)],$colors[rand(0,sizeof($colors)-1)],$colors[rand(0,sizeof($colors)-1)],rand(20,40)); 
   			$letter = $letters[rand(0,sizeof($letters)-1)];
   			$size = rand($font_size*2.1-2,$font_size*2.1+2);
   			$x = ($i+1)*$font_size + rand(4,7);
   			$y = (($height*2)/3) + rand(0,5);
   			$cod[] = $letter;   
   			imagettftext($src,$size,rand(0,15),$x,$y,$color,$path_fonts,$letter);
 		}
 
 		$_SESSION[GatewayManager::$_session_captcha] = implode('',$cod);
 
		ob_start();
		imagepng( $src );
		$raw = ob_get_clean();
		return $raw;
	}
	
}?>