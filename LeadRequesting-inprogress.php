<?php
$mktoLead = $_GET[leadID];
$mktoProgram = $_GET[programID];
$leadArray = array();
$directory = getcwd();

//Quick check to make sure the numbers are at least valid
if (ctype_digit($mktoLead) && strlen($mktoLead) < 10 && ctype_digit($mktoProgram) && strlen($mktoProgram) < 5 ){
	
	if(file_exists($directory.'/'.$mktoProgram.'-Members.json')){
		$memberList = json_decode(file_get_contents($directory.'/programs/'.$mktoProgram.'-Members.json'));
	}
	else{
		//Running only if not run in the last 5 minutes, then saving the new run time
		 if(time() >= $memberList->lastUpdate + (60 * 5)){
			 
			//Process to get access token (may be unnecessary eventually)
			$curl = curl_init();
			curl_setopt_array($curl, array(
			  CURLOPT_URL => "https://483-kcw-712.mktorest.com/identity/oauth/token?grant_type=client_credentials&client_id=4774cebe-0187-4a08-b19d-8a6c52862bf3&client_secret=yZ0qmfqTV89c0wHqdN2JFIiQb0f2wdBb",
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
			  
			if ($err) {echo "Authentication Error:".$err;} 
			else {
				//Gets the first page of available data
				$curl2 = curl_init();

				curl_setopt_array($curl2, array(
				  CURLOPT_URL => 'https://483-kcw-712.mktorest.com/rest/v1/leads/programs/'.$mktoProgram.'.json?nextPageToken=&fields=id,lastName,firstName,email,company,title&access_token='.$token,
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

				$response2 = curl_exec($curl2);
				$err2 = curl_error($curl2);

				curl_close($curl2);

				if ($err2) {echo "Response Error";} 
				else {  
					$rawData = json_decode($response2);
					$memberData = $rawData->result;
					$pageToken = $rawData->nextPageToken;
					
					while (!empty($pageToken)){
						$curl = curl_init();

						curl_setopt_array($curl, array(
						  CURLOPT_URL => 'https://483-kcw-712.mktorest.com/rest/v1/leads/programs/'.$mktoProgram.'.json?nextPageToken=&fields=id,lastName,firstName,email,company,title&nextPageToken='.$pageToken.'&access_token='.$token,
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
						curl_close($curl);
						
						if ($err){echo "Pagination Error"}
						else {
							$pagedData = json_decode($response);
							$memberData = array_merge($memberData,$pagedData->result);
							$pageToken = $pagedData->nextPageToken;
						}
						
					}
					
					$timeStamp = array(
						"lastUpdate"=>time()
						);
					array_push($memberData,$timeStamp);
					
					$repackData = json_encode($memberData);
					file_put_contents($directory.'/programs/'.$mktoProgram.'-Members.json', $repackData);
						
					$memberList = json_decode(file_get_contents($directory.'/programs/'.$mktoProgram.'-Members.json'));
							
					foreach($memberList as $item){
						if($item->id == $mktoLead){
							$leadArray = array(
								"id"=> $item->id,
								"lastName"=> $item->lastName,
								"firstName"=> $item->firstName,
								"title"=> $item->title,
								"company"=> $item->company,
								"email"=> $item->email,
								"membership"=> $item->membership,
							);	
							$leadInfo = json_encode($leadArray);
						}
					}
				}
				echo $leadInfo;
			}
			}
	
	 else{
		 	
			foreach($memberList->result as $item){
				if($item->id == $mktoLead){
					$leadArray = array(
						"result"=> array(array(
						"id"=> $item->id,
						"lastName"=> $item->lastName,
						"firstName"=> $item->firstName,
						"title"=> $item->title,
						"company"=> $item->company,
						"email"=> $item->email,
						"membership"=> $item->membership,
							))
					);
					$leadInfo = json_encode($leadArray);
				}
			}
			echo $leadInfo;
		}
	}
}
else{
	echo "Error: Invalid Input";
}
?>