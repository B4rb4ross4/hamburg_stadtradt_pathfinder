<?php
/**
 * Created by PhpStorm.
 * User: b4rb4ross4
 * Date: 10/21/17
 * Time: 4:58 AM
 */

$jsonString  = file_get_contents(__DIR__ . "/combinations.json");

$array = json_decode($jsonString, true);

$length = count($array);

print $length;