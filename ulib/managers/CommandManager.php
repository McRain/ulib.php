<?php
/**
 * This service file for ULIB gateway. This file can be changed in future versions.
 * Please do not change it yourself.
 * @author Viktor Abdulov <support@reneos.com>
 * @copyright Copyright (c) 2011, game.reneos.com
 * 
 */
class CommandManager{
	
	function LoadCommands(){
		require_once 'Dictionary.php';
		require_once 'UCommands.php';
		global $mysqli;
		if(!$mysqli) 
			$mysqli = dbconn();
		$result = new Dictionary("integer", "UCommands");
		if ($stmt = $mysqli->prepare("CALL get_shopitems ( ) ;")) {
			//$stmt->bind_param("s", $inlogin);
			$stmt->execute(); 
   			$stmt->bind_result($isid,$iid,$iprice,$ipriceg,$ititle,$igid,$irules);
   			while($stmt->fetch()) {
   				$item = new UShopItem();
   				$item->id = $isid;
   				$item->itemId = $iid;
   				$item->price = floatval($iprice);
   				$item->priceGold = floatval($ipriceg);
   				$item->label = $ititle;
   				$item->Icon = new Texture2D();
   				$item->Icon->LoadFromPNG($itemsDir.$item->id.'.png');
   				$result['list']->Add($item);
   			}	
   			$stmt->close();
		}
		return $result;
	}
	
}
?>