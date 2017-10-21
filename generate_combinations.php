<?php
/**
 * Created by PhpStorm.
 * User: b4rb4ross4
 * Date: 10/21/17
 * Time: 3:50 AM
 */

$jsonString = file_get_contents(__DIR__ . "/raw_stadtrad_output.json");

$array = json_decode($jsonString);

$combinations = [];

foreach ($array As $location)
{
  foreach ($array As $innerLocation)
  {
    if(($location->lat != $innerLocation->lat) && ($location->lng != $innerLocation->lng))
    {
      $combinations[] = ['a' => ['lat' => $location->lat, 'lng' => $location->lng, 'location_id' => $location->hal2option->standort_id], 'b' => ['lat' => $innerLocation->lat, 'lng' => $innerLocation->lng, 'location_id' => $innerLocation->hal2option->standort_id]];
    }
  }
}

file_put_contents('combinations.json', json_encode($combinations));