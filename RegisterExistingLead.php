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
			$fileContents = array(json_decode(FILE_GET_CONTENTS($directory.'/programs/inProcess.json')));
			print_r("File contents before:".$fileContents."<br />");
			print_r("File contents before:".$payload."<br />");
			
			if($fileContents = 'null'){
				FILE_PUT_CONTENTS($directory.'/programs/inProcess.json',json_encode(array($payload)));
				
				echo "File contents after, first data:".$fileContents."<br />";
			}
			else{
				array_push($fileContents,$payload);
				FILE_PUT_CONTENTS($directory.'/programs/inProcess.json',json_encode($fileContents));
				
				echo "File contents after, existing data:".$fileContents."<br />";
			}
		}
	}
	
}
else{echo "Invalid Input";}

?>