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




function findOptimalPath(array $from, array $to, array &$way_matrix, array $path, int $depth = 5) {

  $maxTime = null;

  if($depth == 0)
  {
    return $path;
  }

  foreach ($way_matrix[$from['lat'] . '##' . $from['lng']] As $possibleRoute)
  {
    if(($possibleRoute['b']['lat'] == $to['lat']) && ($possibleRoute['b']['lng'] == $to['lng']))
    {
      $maxTime = $possibleRoute['route']['time'];

      # do something if maxTime < 30 minutes!
      if($maxTime < 30)
      {
        return $path[] = $possibleRoute['b']['lat'] . '##' . $possibleRoute['b']['lng'];
      }
      break;
    }
  }

  $results = [];

  foreach ($way_matrix[$from['lat'] . '##' . $from['lng']] As $possibleRoute)
  {
    if(in_array($possibleRoute['b']['lat'] . '##' . $possibleRoute['b']['lng'], $path)) {
      continue;
    }

    if($possibleRoute['route']['time'] >= $maxTime) {
      continue;
    }

    # get route from current node
    $path[] = $possibleRoute['b']['lat'] . '##' . $possibleRoute['b']['lng'];
    $results[] = array_merge($path, findOptimalPath($possibleRoute['b'], $to, $way_matrix, $path, $depth - 1));
  }

  return $results;
}
