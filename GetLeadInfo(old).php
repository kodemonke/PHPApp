<?php
header('Access-Control-Allow-Origin: https://secure.watchguard.com');
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
	$request->setUrl('https://483-kcw-712.mktorest.com/rest/v1/lead/"'.$mktoLead.'".json');
	$request->setMethod(HTTP_METH_GET);

	$request->setQueryData(array(
	  'fields' => 'lastName,firstName,email,company,title',
	  'access_token' => $token
	));

	$request->setHeaders(array(
	  'cache-control' => 'no-cache'
	));

	try {
	  $response = $request->send();

	  echo $response->getBody();
	} catch (HttpException $ex) {
	  echo "Lookup Error";
	}
  
} catch (HttpException $ex) {
  echo "Authentication Error";
}
?>