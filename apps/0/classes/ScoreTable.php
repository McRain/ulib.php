<?php
class ScoreTable{
	
	static $ScoreTable_ufunctions = array('LoadScore');
	
	function LoadScore($parameters){
		require_once 'Dictionary.php';		
		$result = new Dictionary("integer", "ScoreLine");
		$fromDb = $parameters['fromdb'];
		$start =  $parameters['start'];
		$limit =  $parameters['limit'];
		$sort =  $parameters['sort'];
		
		if($fromDb){
			$data = UWebDatabase::Call('example_gethighscore',true,$start,$limit,$sort);
			foreach ($data as $value) {
				$myscore = new ScoreLine();
   				$myscore->position = $value['scoreid'];
				$myscore->Name = $value['playername'];
				$myscore->Value = $value['scoreval'];
				$result->Add($value['scoreid'], $myscore);
			}
		}else{
			for ($i = $start+1; $i < $start+$limit; $i++) {
				$myscore = new ScoreLine();
				$myscore->position = $i;
				$myscore->Name = "Player ".$i;
				$myscore->Value = rand(1, 10000);
				$result->Add($i, $myscore);
			}
			if($sort=='scoreval')
				usort($result->data,array("ScoreTable", "sortval"));
			else 
				sort($result->data);
		}
		return $result;
	}
	
	function sortval($a, $b)
	{
    	if ($a->Value == $b->Value) {
        	return 1;
    	}
    	return ($a->Value < $b->Value) ? -1 : 1;
	}
}

class ScoreLine{
	var $position = 0;
	var $Name = "";
	var $Value = 0;
}
?>