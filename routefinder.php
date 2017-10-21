<?php
declare(strict_types = 1);

function getTime(string $from, string $to, array $matrix)
{
  $toParts = explode("##", $to);

  $to = ["lat" => $toParts[0], 'lng' => $toParts[1]];
  foreach ($matrix[$from] As $possibleRoute)
  {
    if(($possibleRoute['b']['lat'] == $to['lat']) && ($possibleRoute['b']['lng'] == $to['lng']))
    {
      return $possibleRoute['route']['time'];
    }
  }

  throw new Exception("If we end here something is fucked up with the paths");
}

function findOptimalPath(array $from, array $to, array &$way_matrix, array $path, int $depth = 5, int $maxJourneyTime , int $totalJourneyTime = 0) : array
{

  $maxTime = null;

  print implode(" ", $path) . "\n";

  if($depth == 0)
  {
    return [];
  }

  foreach ($way_matrix[$from['lat'] . '##' . $from['lng']] As $possibleRoute)
  {
    if(($possibleRoute['b']['lat'] == $to['lat']) && ($possibleRoute['b']['lng'] == $to['lng']))
    {
      $maxTime = $possibleRoute['route']['time'];

      # do something if maxTime < 30 minutes!
      if($maxTime < 600000)
      {
        $path[] = $possibleRoute['b']['lat'] . '##' . $possibleRoute['b']['lng'];
        return [$path];
      }
      break;
    }
  }

  if(($totalJourneyTime + $maxTime) > $maxJourneyTime)
  {
    return [];
  }

  $results = [];

  foreach ($way_matrix[$from['lat'] . '##' . $from['lng']] As $possibleRoute)
  {
    if(in_array($possibleRoute['b']['lat'] . '##' . $possibleRoute['b']['lng'], $path)) {
      continue;
    }

    if(!isset($possibleRoute['route']) || ($possibleRoute['route']['time'] >= $maxTime)) {
      continue;
    }

    # get route from current node
    $currentpath = $path;
    $currentpath[] = $possibleRoute['b']['lat'] . '##' . $possibleRoute['b']['lng'];
    $result = findOptimalPath($possibleRoute['b'], $to, $way_matrix, $currentpath, $depth - 1, $maxJourneyTime, $totalJourneyTime + $possibleRoute['route']['time']);

    $results = array_merge($results, $result);
  }

  return array_filter($results);
}

/*
 * Execution
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

unset($way_matrix);

//"'2950&nbsp;Hugo-Kirchberg-Straße/Tesa'",
$from = ["lat" => "53.654290000000000", 'lng' => "9.985280000000000"];
//"'2441&nbsp;Rahlstedter&nbsp;Weg/Berner&nbsp;Heerweg'",
//$to = ["lat" => "53.606246000000000", "lng" => "10.120210000000000"];
//'2040&nbsp;Ohnhorststraße/Klein&nbsp;Flottbek'
$to = ["lat" => "53.558587000000000", "lng" => "9.862160000000000"];

$output = findOptimalPath($from, $to, $sorted_matrix, [$from["lat"]. "##" . $from["lng"]], 5, 1200000, 0);

$sorted = [];

foreach($output As $route)
{
  $routeCount = count($route) - 1;

  $totalTime = 0;
  for($i = 0 ; $i < $routeCount; $i++)
  {
    $totalTime += getTime($route[$i], $route[$i+1], $sorted_matrix);
  }
  $changes = count($route);

  $sorted[$changes][$totalTime] = $route;
}

$counter = 0;

ksort($sorted);

foreach($sorted As $changes => $routesPerChanges)
{
  ksort($routesPerChanges);

  foreach ($routesPerChanges As $time => $route)
  {
    $i++;

    print "Route 1: " . ($changes - 2) . " Umstiege" . " " . ((int) ($time / 1000 / 60) * 3) . " Minuten werden benötigt\n";

    if($i == 10)
    {
      break 2;
    }
  }
}




$i = 0;