<?php
class ShopManager{
	
	function GetItemList($parameters){
		require_once 'ListOf.php';
		require_once 'UShopItem.php';
		require_once 'Texture2D.php';
		global $mysqli;
		if(!$mysqli) 
			$mysqli = dbconn();
		$result = array();
		$result['list'] = new ListOf('UShopItem');
		$itemsDir = U_APP_PATH.'/'.U_CONTENT_DIR.'/'.U_ITEMS_DIR.'/';
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
	
	function RequestBuy($parameters){
		require_once 'ListOf.php';
		$itemId = $parameters['key'];
		$itemCount = $parameters['count'];
		$userId = $_SESSION['u_uid'];
		$result = array('key'=>0,'count'=>0,'sum'=>0,'param'=>$parameters['param']);
		global $mysqli;
		if(!$mysqli) 
			$mysqli = dbconn();
		if ($stmt = $mysqli->prepare("CALL add_newbuy ( ? , ? , ? ) ;")) {
			$stmt->bind_param("iii", $itemId,$itemCount,$userId);
			$stmt->execute(); 
   			$stmt->bind_result($iid, $icount,$sumcost);
   			if($stmt->fetch()) {
   				$result['key']=$iid;
   				$result['count']=$icount;
   				$result['sum']=$sumcost;
   			}	
   			$stmt->close();
		}
		return $result;
	}
	
	function Sell($parameters){
		
	}
	
}
?>