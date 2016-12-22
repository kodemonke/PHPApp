<?php
//header('Access-Control-Allow-Origin: https://secure.watchguard.com');
$mktoProgram = $_GET[programID];
$mktoLead = $_GET[leadID];
$directory = getcwd();
$payload = array();

if (ctype_digit($mktoLead) && strlen($mktoLead) < 10 && strlen($mktoLead) > 7 && ctype_digit($mktoProgram) && strlen($mktoProgram) == 4){
	
	if (!file_exists($directory.'/programs/'.$mktoProgram.'-Members.json')){echo "Program does not exist.";}
	else{
		$memberList = json_decode(file_get_contents($directory.'/programs/'.$mktoProgram.'-Members.json'));
						
		foreach($memberList as $item){
			if($item->id == $mktoLead){
				$payload = array(
				"programID"=>$mktoProgram,
				"id"=>$mktoLead
				);
				}	
		}
		if (empty($payload)){echo "Not in Program.";}
		else {
			$fileContents = json_decode(FILE_GET_CONTENTS($directory.'/programs/inProcess.json'));
				
			$fileContents[]=$payload;
			
			FILE_PUT_CONTENTS($directory.'/programs/inProcess.json',json_encode($fileContents));
			$test = json_decode(FILE_GET_CONTENTS($directory.'/programs/inProcess.json'));
			foreach($test as $item){
				if($test->id == $mktoLead){
					$success = true;
				}
			}
			if($success != true){echo "Error";}
			else{echo "Success!";}
		}		
	}
}
	

else{echo "Invalid Input";}

?>