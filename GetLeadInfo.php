<?php
header('Access-Control-Allow-Origin: http://localhost:8000');
$mktoLead = $_GET[leadID];

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "https://483-KCW-712.mktorest.com/identity/oauth/token?grant_type=client_credentials&client_id=5188c509-b6b4-4f86-9c50-17316fc6de72&client_secret=oRG5cQGtU14HQ7leFZxwJCMWZck9PP2r",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET"
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

  $data = json_decode($response);
  $token = $data->access_token;

curl_close($curl);
  
if ($err) {
  echo "cURL Error #1: " . $err;
    } else {

    $curl2 = curl_init();

    curl_setopt_array($curl2, array(
      CURLOPT_URL => 'https://483-kcw-712.mktorest.com/rest/v1/lead/"'.$mktoLead.'".json',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "GET",
      CURLOPT_HTTPHEADER => array(
        'fields' => 'lastName,firstName,email,company,title',
        'access_token' => $token
      ),
    ));

    $response2 = curl_exec($curl2);
    $err2 = curl_error($curl2);

    curl_close($curl2);

    if ($err2) {
      echo "cURL Error #2:" . $err2;
    } else {
      echo "First Response:" . $response . "<br />Final Response" . $response2;
    }


  }
?>