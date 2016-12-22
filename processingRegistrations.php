<?php
$directory = getcwd();
$Leads = json_decode(file_get_contents($directory.'/programs/inProcess.json'));
$error = "false";

if (empty($Leads)){Echo "Success: No Leads. You're all good!";}
else{
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
		}
		$payload = $tempArray;
		echo json_encode($payload)."<br />";
		
		//API call goes right here. Use $program as the program and $payload as id list
		
		$curl = curl_init();
		curl_setopt_array($curl, array(
		  CURLOPT_URL => "https://483-kcw-712.mktorest.com/identity/oauth/token?grant_type=client_credentials&client_id=".getenv('MARKETO_ID')."&client_secret=".getenv('MARKETO_SECRET'),
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "GET",
		  CURLOPT_HTTPHEADER => array(
			"cache-control: no-cache"
		  ),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		$data = json_decode($response);
		$token = $data->access_token;
		  
		curl_close($curl);
		  
		if ($err) {echo "Authentication Error:";} 
		else {
			$curl2 = curl_init();

			curl_setopt_array($curl2, array(
			  CURLOPT_URL => 'https://483-kcw-712.mktorest.com/rest/v1/leads/programs/'.$program.'/status.json?access_token='.$token,
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => "",
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 30,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => "POST",
			  CURLOPT_POSTFIELDS => array(
			  "input:".$payload,
			  "lookupField: id",
			  "status: Registered"
			  ),
			  CURLOPT_HTTPHEADER => array(
			  "cache-control: no-cache"
			  ),
			));

			$response2 = curl_exec($curl2);
			$err2 = curl_error($curl2);

			curl_close($curl2);
			
			if ($err2){echo "Processing Error".$err2;}
			else{
				if (strpos($response2,'"success":true,"')){
					echo $program." Success!<br />";
					print_r($response2);
					}
				
				else{
					echo "Error";
					print_r($response2);
					$error = "true";
				}
			}
		}
		
	}
	echo json_encode(array(
			  "input:".$payload,
			  "lookupField: id",
			  "status: Registered"
			  ))."<br />";
	//clears out the file when finished. Probably need something more elegant eventually. 
	if($error != "true"){file_put_contents($directory.'/programs/inProcess.json', "");}
}

?>