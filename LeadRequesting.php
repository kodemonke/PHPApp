<?php

$mktoLead = $_GET[leadID];
$mktoProgram = $_GET[programID];
$leadArray = array();

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
  
if ($err) {
	echo "System Error";
	} 
	else {
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

		if ($err2) {
		  echo "Response Error";
		} 
		else {  
			$directory = getcwd();
			file_put_contents($directory.'/programMembers.json', $response2);
			echo $directory;
			$memberList = json_decode(file_get_contents($directory.'/programMembers.json;'));
			
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
	
?>