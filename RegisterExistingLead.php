<?php
header('Access-Control-Allow-Origin: https://secure.watchguard.com');
$mktoProgram = $_GET[programID];
$mktoLead = $_GET[leadID];

$identify = new HttpRequest();
$identify->setURL('https://483-KCW-712.mktorest.com/identity/oauth/token');
$identify->setMethod(HTTP_METH_GET);

$identify->setQueryData(array(
	'grant_type' => 'client_credentials',
	'client_id' => '5188c509-b6b4-4f86-9c50-17316fc6de72',
	'client_secret' => 'oRG5cQGtU14HQ7leFZxwJCMWZck9PP2r'
	)

try {
  $identityResponse = $identify->send();
  $data = json_decode($identityResponse);
  $token = $data->access_token;

	$request = new HttpRequest();
	$request->setUrl('https://483-kcw-712.mktorest.com/rest/v1/leads/programs/"'.$mktoProgram.'"/status.json');
	$request->setMethod(HTTP_METH_POST);

	$request->setQueryData(array(
	  'access_token' => '05fe88a3-c9af-44ac-8ac1-f0607af7ac1e:sj'
	));

	$request->setHeaders(array(
	  'cache-control' => 'no-cache',
	  'content-type' => 'application/json'
	));

	$request->setBody('{
	  "input": [
		{"id": $mktoLead}
	  ],
	  "lookupField": "id",
	  "status": "Registered"
	}');

	try {
	  $response = $request->send();

	  echo $response->getBody();
	} catch (HttpException $ex) {
	  echo "Processing Error";
	}
} catch (HttpException $ex) {
  echo "Authentication Error";
}
?>