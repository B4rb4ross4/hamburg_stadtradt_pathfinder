<?php
/**
 * Created by PhpStorm.
 * User: b4rb4ross4
 * Date: 10/21/17
 * Time: 4:05 AM
 */

//$url = "http://localhost:8989/route?point=53.5777025,9.9401087&point=53.6035667,9.9524399";
$url = "http://10.80.4.229:8989/route?point=%s,%s&point=%s,%s";

$jsonString  = file_get_contents(__DIR__ . "/combinations.json");

$array = json_decode($jsonString, true);

$length = count($array);
$i = 0;

foreach ($array As &$route) {
  $i++;
  if(($i % 100) == 0)
  {
    print $i ."\n";
  }

  $requestUrl = sprintf($url, $route['a']['lat'], $route['a']['lng'], $route['b']['lat'], $route['b']['lng']);

  $ch = curl_init();

  curl_setopt($ch,CURLOPT_URL,$requestUrl);
  curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);

  $response=curl_exec($ch);

  curl_close($ch);

  $responseArray = json_decode($response, true);

  if(empty($responseArray['paths']))
  {
    continue;
  }

  $route['route'] = [
      'distance' => $responseArray['paths']['0']['distance'],
      'time' => $responseArray['paths']['0']['time'],
      'points' => $responseArray['paths']['0']['points'],
      'instructions' => $responseArray['paths']['0']['instructions'],
  ];

}

file_put_contents("way_matrix.json", json_encode($array));