<?php
class ChatManager{
	
	static $ChatManager_ufunctions = array('Entry','Say');
	
	const uidParameter = 'uid';
	const messagesListKey = 'msgs';
	const userListKey = 'usrs';
	const lastMsgId = 'last';
	const entryMessage = 'entry';
	
	const typeMessage = 'Message';
	const typeService = 'Service';
	
	/*
	 * Register user for chat.
	 * Return user list and max message id.
	 * Method closed for call from Gateway.
	 */
	static function Entry($parameters){
		require_once 'ChatMessage.php';
		$result = array();
		
		$userId = $parameters[self::uidParameter];
		$last = 0;
		global $mysqli;
		if(!$mysqli) $mysqli = dbconn();
		if ($stmt = $mysqli->prepare("CALL add_userchat ( ? ) ;")) {
			$stmt->bind_param("i", $userId);
			$stmt->execute();
			$stmt->bind_result($maxmsgid);
			if($stmt->fetch()) {
				$last = $maxmsgid;//0 or >0
			}
			$stmt->close();
		}
		$result[self::userListKey] = self::GetOnlineUsers();
		$result[self::lastMsgId] = $last;
		return $result;
	}
	
	/*
	* Add message to chat table.
	* Return user list and all new messages.
	* Method closed for call from Gateway.
	*/
	static function Say($msg,$targetUserId = 0){
		require_once 'ListOf.php';
		require_once 'Dictionary.php';
		require_once 'ChatUser.php';
		
		$msgtarget = $targetUserId;//0 - to all
		
		$result = array(self::messagesListKey=>new ListOf('ChatMessage'));
		
		$msgtext = str_replace("\r\n",'',$msg->msg);
		$msgtext = str_replace("\n",'',$msgtext);
		
		$msgtype = $msg->messageType;
		$lastid = $msg->id;
		$uid = $msg->userId;
		
		global $mysqli;
		if(!$mysqli) $mysqli = dbconn();
		if ($stmt = $mysqli->prepare(" CALL get_chat( ? , ? , ? , ? )")) { 
			$stmt->bind_param("iiss",$lastid,$uid,$msgtext, $msgtype ); 
   			$stmt->execute(); 
   			$stmt->bind_result($msid , $sndid , $mmsg, $mtype);
   			while($stmt->fetch()) {
   				$message = new ChatMessage();
   				$message->id = $msid;
   				$message->userId = $sndid;
   				$message->msg = $mmsg;
   				$message->messageType = $mtype;
   				$result[self::messagesListKey]->Add($message);
			}
			$stmt->close();
		}
		$result[self::userListKey] = self::GetOnlineUsers();
		return $result;
	}
	
	/*
	* Return user list.
	* Method closed for call from Gateway and from other classes.
	*/
	private static function GetOnlineUsers(){
		require_once 'Dictionary.php';
		require_once 'ChatUser.php';
		$result = new Dictionary('integer', 'ChatUser');
		global $mysqli;
		if(!$mysqli) $mysqli = dbconn();
		if ($stmt = $mysqli->prepare(" CALL get_chatuser()")) {
			$stmt->execute();
			$stmt->bind_result($outuid , $outfio);
			while($stmt->fetch()) {
				$chatUser = new ChatUser();
				$chatUser->id = $outuid;
				$chatUser->name = $outfio;
				$result->Add($chatUser->id,$chatUser);
			}
			$stmt->close();
		}
		return $result;
	}
}
?>