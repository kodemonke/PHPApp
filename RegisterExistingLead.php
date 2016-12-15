<?php
header('Access-Control-Allow-Origin: https://secure.watchguard.com');
$mktoProgram = $_GET[programID];
$mktoLead = $_GET[leadID];

$identify = new HttpRequest();
$identify->setURL('https://483-KCW-712.mktorest.com/identity/oauth/token');
$identify->setMethod(HTTP_METH_GET);

$identify->setQueryData(array(
	'grant_type' => 'client_credentials',
	)

try {
  $identityResponse = $identify->send();
  $data = json_decode($identityResponse);
  $token = $data->access_token;

	$request = new HttpRequest();
	$request->setUrl('https://483-kcw-712.mktorest.com/rest/v1/leads/programs/"'.$mktoProgram.'"/status.json');
	$request->setMethod(HTTP_METH_POST);

	$request->setQueryData(array(
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