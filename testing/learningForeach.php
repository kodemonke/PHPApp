<?php
$programList=array();

$Leads = json_decode('[{"programID":"7236","id":"100"},{"programID":"7236","id":"101"},{"programID":"7111","id":"103"},{"programID":"7236","id":"105"},{"programID":"7236","id":"107"},{"programID":"7111","id":"122"},{"programID":"7236","id":"144"},{"programID":"7111","id":"153"}]');


foreach($Leads as $item){
	if (!in_array($item->programID,$programList)){
	$programList[] = $item->programID;
	}
}

foreach($programList as $program){			
	$tempArray =array();	
	foreach($Leads as $item){
		if ($item->programID == $program){
			$tempArray[]=array(
				"id"=>$item->id);
		}
	}print_r(json_encode($tempArray));
}
?>