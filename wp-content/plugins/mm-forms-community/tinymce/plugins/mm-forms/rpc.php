<?php

//require_once('Arabic.php');

$action = $_GET['action'];
$param = $_GET['param'];

switch($action){
  case 'hijri':
   /* $ar = new Arabic('ArDate');
    date_default_timezone_set('UTC');
    $time = time();

    $ar->ArDate->setMode(1);*/
    echo "Kaneriya";

    break;
}
?>
