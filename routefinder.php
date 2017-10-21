<?php
/**
 * Created by PhpStorm.
 * User: b4rb4ross4
 * Date: 10/21/17
 * Time: 9:14 AM
 */

$jsonString  = file_get_contents(__DIR__ . "/way_matrix.json");

$way_matrix = json_decode($jsonString, true);

$sorted_matrix = [];
foreach($way_matrix As $key => $relation)
{
  unset($relation['route']['instructions']);
  unset($relation['route']['points']);
  $sorted_matrix[$relation['a']['lat'] . '##' . $relation['a']['lng']][] = $relation;
}


function findOptimalPath($a, $b, &$way_matrix, $depth = 5) {

  foreach($way_matrix[$a['lat'] . '##' . $a['lng']] As $relation) {

  }
}
